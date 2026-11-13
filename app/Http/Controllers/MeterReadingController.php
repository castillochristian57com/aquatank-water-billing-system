<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\MeterReading;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MeterReadingController extends Controller
{
    /**
     * Display a listing of all meter readings.
     */
    public function index(Request $request): View
    {
        $query = MeterReading::with(['consumer', 'reader', 'bill']);

        // Filter by status (recorded, billed, cancelled)
        if ($status = $request->input('status')) {
            if (in_array($status, ['recorded', 'billed', 'cancelled'])) {
                $query->where('status', $status);
            }
        }

        // Search by consumer name, account #, meter #, or billing period
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('billing_period', 'like', "%{$search}%")
                  ->orWhereHas('consumer', function ($cq) use ($search) {
                      $cq->where('account_number', 'like', "%{$search}%")
                         ->orWhere('meter_number', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('address', 'like', "%{$search}%");
                  });
            });
        }

        $readings = $query->orderBy('reading_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tariffRate = (float) Setting::get('water_rate_per_cbm', 15.00);

        return view('meter_readings.index', compact('readings', 'tariffRate'));
    }

    /**
     * Show the dedicated mobile-friendly meter reading form.
     */
    public function create(Request $request): View
    {
        $consumers = Consumer::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $selectedConsumerId = $request->input('consumer_id');
        $selectedConsumer = $selectedConsumerId ? Consumer::find($selectedConsumerId) : null;

        $tariffRate = (float) Setting::get('water_rate_per_cbm', 15.00);
        $currentPeriod = Carbon::now()->format('F Y');
        $todayDate = Carbon::now()->format('Y-m-d');

        // Recent 5 readings recorded today for quick reference
        $recentReadings = MeterReading::with('consumer')
            ->whereDate('reading_date', Carbon::today())
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('meter_readings.create', compact(
            'consumers',
            'selectedConsumer',
            'tariffRate',
            'currentPeriod',
            'todayDate',
            'recentReadings'
        ));
    }

    /**
     * Store a newly recorded meter reading in the database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'consumer_id' => ['required', 'integer', 'exists:consumers,id'],
            'current_reading' => ['required', 'numeric', 'min:0'],
            'reading_date' => ['required', 'date'],
            'billing_period' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $consumer = Consumer::findOrFail($validated['consumer_id']);

        // Check if consumer is active
        if ($consumer->status !== 'active') {
            $errorMessage = "Cannot record reading for inactive consumer account ({$consumer->account_number}).";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 422);
            }
            return back()->withErrors(['consumer_id' => $errorMessage])->withInput();
        }

        // Retrieve the latest previous reading again on the server (never trust client)
        $hasPrevious = $consumer->hasPreviousReading();
        $previousReading = $consumer->latest_reading;
        $currentReading = round((float) $validated['current_reading'], 2);

        // Validation: Current reading cannot be lower than previous reading
        if ($hasPrevious && $currentReading < (float) $previousReading) {
            $formattedPrev = number_format($previousReading, 2);
            $formattedCurr = number_format($currentReading, 2);
            $errorMessage = "Current meter reading ({$formattedCurr} m³) cannot be lower than the previous reading ({$formattedPrev} m³).";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['current_reading' => [$errorMessage]],
                ], 422);
            }

            return back()->withErrors(['current_reading' => $errorMessage])->withInput();
        }

        // Calculate water consumption backend logic
        $consumption = $hasPrevious
            ? round($currentReading - (float) $previousReading, 2)
            : $currentReading; // For initial reading, consumption equals the initial index

        // Prevent duplicate readings for the same consumer and period
        $existingReading = MeterReading::where('consumer_id', $consumer->id)
            ->where('billing_period', $validated['billing_period'])
            ->first();

        if ($existingReading) {
            $errorMessage = "A meter reading for {$consumer->full_name} has already been recorded for {$validated['billing_period']} ({$existingReading->current_reading} m³ on {$existingReading->reading_date->format('M d, Y')}).";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['current_reading' => [$errorMessage]],
                ], 422);
            }

            return back()->withErrors(['current_reading' => $errorMessage])->withInput();
        }

        // Prevent duplicate bills conflict: check if bill already exists for this period
        $existingBill = Bill::where('consumer_id', $consumer->id)
            ->where('billing_period', $validated['billing_period'])
            ->first();

        if ($existingBill) {
            $errorMessage = "A water bill ({$existingBill->bill_number}) has already been generated for {$consumer->full_name} for {$validated['billing_period']}. Duplicate reading is not allowed.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['billing_period' => [$errorMessage]],
                ], 422);
            }

            return back()->withErrors(['billing_period' => $errorMessage])->withInput();
        }

        // Store reading inside a database transaction
        $reading = DB::transaction(function () use ($consumer, $validated, $previousReading, $currentReading, $consumption) {
            return MeterReading::create([
                'consumer_id' => $consumer->id,
                'user_id' => auth()->id(),
                'previous_reading' => $previousReading,
                'current_reading' => $currentReading,
                'consumption' => $consumption,
                'reading_date' => $validated['reading_date'],
                'billing_period' => $validated['billing_period'],
                'status' => 'recorded',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $tariffRate = (float) Setting::get('water_rate_per_cbm', 15.00);
        $estimatedAmount = round($consumption * $tariffRate, 2);

        $successMessage = "Meter reading of " . number_format($reading->current_reading, 2) . " m³ (Consumption: " . number_format($reading->consumption, 2) . " m³) successfully saved for {$consumer->full_name} ({$consumer->account_number}).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'reading' => [
                    'id' => $reading->id,
                    'consumer_id' => $consumer->id,
                    'consumer_name' => $consumer->full_name,
                    'account_number' => $consumer->account_number,
                    'previous_reading' => (float) $reading->previous_reading,
                    'current_reading' => (float) $reading->current_reading,
                    'consumption' => (float) $reading->consumption,
                    'estimated_amount' => $estimatedAmount,
                    'reading_date' => $reading->reading_date->format('Y-m-d'),
                    'billing_period' => $reading->billing_period,
                ],
            ]);
        }

        return redirect()->route('meter-readings.receipt', $reading)
            ->with('success', $successMessage);
    }

    /**
     * Display and automatically print the receipt for a saved meter reading.
     */
    public function receipt(MeterReading $reading): View
    {
        $reading->load(['consumer', 'bill']);
        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');
        $tariffRate = (float) ($reading->bill?->rate_per_cubic_meter
            ?? Setting::get('water_rate_per_cbm', 15.00));
        $estimatedAmount = round((float) $reading->consumption * $tariffRate, 2);

        return view('meter_readings.receipt', compact(
            'reading',
            'barangayName',
            'systemContact',
            'tariffRate',
            'estimatedAmount'
        ));
    }

    /**
     * API endpoint to retrieve a consumer's details and latest valid previous reading.
     */
    public function getConsumerReading(Consumer $consumer): JsonResponse
    {
        $hasPrevious = $consumer->hasPreviousReading();
        $latestRecord = $consumer->getLatestReadingRecord();
        $previousReading = $consumer->latest_reading;

        return response()->json([
            'id' => $consumer->id,
            'name' => $consumer->full_name,
            'account_number' => $consumer->account_number,
            'meter_number' => $consumer->meter_number,
            'address' => $consumer->address,
            'contact_number' => $consumer->contact_number,
            'status' => $consumer->status,
            'has_previous_reading' => $hasPrevious,
            'previous_reading' => (float) $previousReading,
            'is_initial_reading' => ! $hasPrevious,
            'latest_reading_date' => $latestRecord?->reading_date?->format('M d, Y') ?? ($latestRecord?->billing_date?->format('M d, Y') ?? null),
            'latest_billing_period' => $latestRecord?->billing_period ?? null,
        ]);
    }

    /**
     * API endpoint for live AJAX search of consumers (by name, account, meter, or address).
     */
    public function search(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (mb_strlen($term) < 1) {
            return response()->json([]);
        }

        $parts = array_filter(preg_split('/\s+/', $term));

        $consumers = Consumer::query()
            ->where('status', 'active')
            ->where(function ($q) use ($term, $parts) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('middle_name', 'like', "%{$term}%")
                  ->orWhere('account_number', 'like', "%{$term}%")
                  ->orWhere('meter_number', 'like', "%{$term}%")
                  ->orWhere('address', 'like', "%{$term}%");

                if (count($parts) > 1) {
                    $q->orWhere(function ($sub) use ($parts) {
                        foreach ($parts as $part) {
                            $sub->where(function ($w) use ($part) {
                                $w->where('first_name', 'like', "%{$part}%")
                                  ->orWhere('last_name', 'like', "%{$part}%")
                                  ->orWhere('address', 'like', "%{$part}%");
                            });
                        }
                    });
                }
            })
            ->limit(10)
            ->get()
            ->map(function (Consumer $c) {
                $hasPrev = $c->hasPreviousReading();
                return [
                    'id' => $c->id,
                    'name' => $c->full_name,
                    'account_number' => $c->account_number,
                    'meter_number' => $c->meter_number,
                    'address' => $c->address,
                    'contact_number' => $c->contact_number,
                    'previous_reading' => (float) $c->latest_reading,
                    'has_previous_reading' => $hasPrev,
                    'is_initial_reading' => ! $hasPrev,
                ];
            });

        return response()->json($consumers);
    }
}
