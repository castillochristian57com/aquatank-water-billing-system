<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsumerController extends Controller
{
    /**
     * Standard Barangay Caruhayon purok distribution.
     */
    protected array $purokList = [
        'Purok 1',
        'Purok 2',
        'Purok 3',
        'Purok 4',
        'Purok 5',
        'Purok 6',
        'Purok 7',
    ];

    /**
     * Normalize Philippine phone number to 09XXXXXXXXX format.
     */
    protected function normalizePhone(?string $phone): string
    {
        if (!$phone) return '';
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '639') && strlen($clean) === 12) {
            return '0' . substr($clean, 2);
        }

        if (str_starts_with($clean, '9') && strlen($clean) === 10) {
            return '0' . $clean;
        }

        return $clean;
    }

    /**
     * List consumers with search, status filtering, and purok organization.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $purokFilter = $request->input('purok');
        $search = trim((string) $request->input('search', ''));

        // Support viewing archived/soft-deleted consumers
        if ($status === 'archived') {
            $query = Consumer::onlyTrashed()->withCount('bills');
        } else {
            $query = Consumer::query()->withCount('bills');
        }

        // Search by account number, meter number, name, address, contact, or purok
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhere('meter_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('purok', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Filter by active/inactive status
        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        // Filter by purok/zone
        if (!empty($purokFilter)) {
            $query->where('purok', $purokFilter);
        }

        $consumers = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $purokList = $this->purokList;

        return view('consumers.index', compact('consumers', 'purokList'));
    }

    /**
     * Consumer registration form.
     */
    public function create(): View
    {
        // Suggest next account and meter numbers based on latest registered record
        $lastConsumer = Consumer::withTrashed()->latest('id')->first();
        $nextId = $lastConsumer ? ($lastConsumer->id + 1) : 1;
        $suggestedAccountNo = sprintf('CRH-%s-%04d', date('Y'), $nextId);
        $suggestedMeterNo = sprintf('MTR-CAR-%04d', 1000 + $nextId);
        $purokList = $this->purokList;

        return view('consumers.create', compact('suggestedAccountNo', 'suggestedMeterNo', 'purokList'));
    }

    /**
     * Store newly registered consumer.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize contact number prior to validation
        if ($request->has('contact_number')) {
            $request->merge([
                'contact_number' => $this->normalizePhone($request->contact_number)
            ]);
        }

        $validated = $request->validate([
            'account_number' => ['required', 'string', 'max:50', 'unique:consumers,account_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'purok' => ['nullable', 'string', 'max:100'],
            'meter_number' => ['required', 'string', 'max:50', 'unique:consumers,meter_number'],
            'connection_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'contact_number.regex' => 'Please enter a valid 11-digit Philippine mobile number (e.g., 09171234567).',
            'account_number.unique' => 'This account number is already registered in the system.',
            'meter_number.unique' => 'This meter number is already assigned to another consumer.',
        ]);

        // Duplicate consumer prevention: check matching name and address
        $existingDuplicate = Consumer::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('address', $validated['address'])
            ->first();

        if ($existingDuplicate) {
            throw ValidationException::withMessages([
                'first_name' => "A consumer named '{$existingDuplicate->full_name}' is already registered at this service address ({$existingDuplicate->account_number}).",
            ]);
        }

        $consumer = Consumer::create($validated);

        return redirect()->route('consumers.show', $consumer)
            ->with('success', "Consumer {$consumer->full_name} ({$consumer->account_number}) registered successfully.");
    }

    /**
     * View individual consumer details.
     */
    public function show(Consumer $consumer): View
    {
        $consumer->load([
            'bills' => function ($q) {
                $q->with('payments')->orderBy('billing_date', 'desc');
            },
            'payments' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            },
            'meterReadings' => function ($q) {
                $q->orderBy('reading_date', 'desc');
            },
            'smsLogs' => function ($q) {
                $q->orderBy('sent_at', 'desc');
            }
        ]);

        return view('consumers.show', compact('consumer'));
    }

    /**
     * Edit consumer form.
     */
    public function edit(Consumer $consumer): View
    {
        $purokList = $this->purokList;
        return view('consumers.edit', compact('consumer', 'purokList'));
    }

    /**
     * Update existing consumer record.
     */
    public function update(Request $request, Consumer $consumer): RedirectResponse
    {
        // Normalize contact number prior to validation
        if ($request->has('contact_number')) {
            $request->merge([
                'contact_number' => $this->normalizePhone($request->contact_number)
            ]);
        }

        $validated = $request->validate([
            'account_number' => ['required', 'string', 'max:50', Rule::unique('consumers')->ignore($consumer->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'purok' => ['nullable', 'string', 'max:100'],
            'meter_number' => ['required', 'string', 'max:50', Rule::unique('consumers')->ignore($consumer->id)],
            'connection_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'contact_number.regex' => 'Please enter a valid 11-digit Philippine mobile number (e.g., 09171234567).',
            'account_number.unique' => 'This account number is already registered in the system.',
            'meter_number.unique' => 'This meter number is already assigned to another consumer.',
        ]);

        // Duplicate conflict check with other records
        $existingDuplicate = Consumer::where('id', '!=', $consumer->id)
            ->where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('address', $validated['address'])
            ->first();

        if ($existingDuplicate) {
            throw ValidationException::withMessages([
                'first_name' => "Another consumer record exists with the same name and address ({$existingDuplicate->account_number}).",
            ]);
        }

        $consumer->update($validated);

        return redirect()->route('consumers.show', $consumer)
            ->with('success', "Consumer {$consumer->full_name} details updated successfully.");
    }

    /**
     * Safely delete or archive consumer record.
     * If the consumer has historical billing, meter reading, or payment records,
     * they are soft-deleted and deactivated rather than destroyed to protect history.
     */
    public function destroy(Consumer $consumer): RedirectResponse
    {
        $name = $consumer->full_name;
        $accountNo = $consumer->account_number;

        if ($consumer->hasFinancialHistory()) {
            // Soft-delete to preserve billing and collection audit logs
            $consumer->update(['status' => 'inactive']);
            $consumer->delete();

            return redirect()->route('consumers.index')
                ->with('success', "Consumer {$name} ({$accountNo}) has associated billing or payment records. The account has been safely archived to preserve historical data.");
        }

        // If no transactions exist, safe deletion
        $consumer->forceDelete();

        return redirect()->route('consumers.index')
            ->with('success', "Consumer {$name} ({$accountNo}) record has been permanently deleted.");
    }

    /**
     * Restore an archived consumer.
     */
    public function restore(int $id): RedirectResponse
    {
        $consumer = Consumer::onlyTrashed()->findOrFail($id);
        $consumer->restore();
        $consumer->update(['status' => 'active']);

        return redirect()->route('consumers.show', $consumer)
            ->with('success', "Consumer {$consumer->full_name} has been restored successfully.");
    }

    /**
     * Toggle active/inactive connection status.
     */
    public function toggleStatus(Consumer $consumer): RedirectResponse
    {
        $newStatus = $consumer->status === 'active' ? 'inactive' : 'active';
        $consumer->update(['status' => $newStatus]);

        return back()->with('success', "Consumer {$consumer->full_name} connection status changed to " . ucfirst($newStatus) . ".");
    }

    /**
     * Live search autocomplete endpoint for consumers.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));

        if (mb_strlen($term) < 1) {
            return response()->json([]);
        }

        $parts = array_filter(preg_split('/\s+/', $term));

        $consumers = Consumer::query()
            ->where(function ($q) use ($term, $parts) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('middle_name', 'like', "%{$term}%")
                  ->orWhere('account_number', 'like', "%{$term}%")
                  ->orWhere('meter_number', 'like', "%{$term}%")
                  ->orWhere('contact_number', 'like', "%{$term}%")
                  ->orWhere('address', 'like', "%{$term}%")
                  ->orWhere('purok', 'like', "%{$term}%");

                if (count($parts) > 1) {
                    $q->orWhere(function ($sub) use ($parts) {
                        foreach ($parts as $part) {
                            $sub->where(function ($w) use ($part) {
                                $w->where('first_name', 'like', "%{$part}%")
                                  ->orWhere('last_name', 'like', "%{$part}%")
                                  ->orWhere('middle_name', 'like', "%{$part}%")
                                  ->orWhere('address', 'like', "%{$part}%");
                            });
                        }
                    });
                }
            })
            ->limit(10)
            ->get(['id', 'account_number', 'first_name', 'middle_name', 'last_name', 'address', 'contact_number', 'meter_number', 'status', 'purok'])
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->full_name,
                    'account_number' => $c->account_number,
                    'meter_number' => $c->meter_number,
                    'contact_number' => $c->contact_number,
                    'address' => $c->address,
                    'purok' => $c->purok,
                    'status' => $c->status,
                    'view_url' => route('consumers.show', $c),
                    'bill_url' => route('bills.create', ['consumer_id' => $c->id]),
                ];
            });

        return response()->json($consumers);
    }
}
