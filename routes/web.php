<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ConsumerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

// public landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// section redirects
Route::get('/about', fn() => redirect('/#about'))->name('about');
Route::get('/services', fn() => redirect('/#services'))->name('services');
Route::get('/contact', fn() => redirect('/#contact'))->name('contact');

// consumer portal
Route::get('/my-water-bill', [ConsumerPortalController::class, 'index'])->name('portal.my-water-bill');
Route::get('/my-water-bill/statement/{bill}', [ConsumerPortalController::class, 'statement'])->name('portal.statement');

// evaluation survey for respondents
Route::get('/system-evaluation', [EvaluationController::class, 'create'])->name('evaluation.create');
Route::post('/system-evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');
Route::get('/evaluate', fn() => redirect()->route('evaluation.create'))->name('evaluate.alias');

// admin routes
Route::middleware(['auth'])->group(function () {
    // admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // consumers management
    Route::get('/consumers/search/autocomplete', [ConsumerController::class, 'autocomplete'])->name('consumers.autocomplete');
    Route::resource('consumers', ConsumerController::class);
    Route::patch('/consumers/{consumer}/toggle-status', [ConsumerController::class, 'toggleStatus'])->name('consumers.toggle-status');
    Route::post('/consumers/{id}/restore', [ConsumerController::class, 'restore'])->name('consumers.restore');

    // dedicated mobile-friendly meter reading feature
    Route::get('/meter-readings', [MeterReadingController::class, 'index'])->name('meter-readings.index');
    Route::get('/meter-readings/create', [MeterReadingController::class, 'create'])->name('meter-readings.create');
    Route::post('/meter-readings', [MeterReadingController::class, 'store'])->name('meter-readings.store');
    Route::get('/meter-readings/{reading}/receipt', [MeterReadingController::class, 'receipt'])->name('meter-readings.receipt');
    Route::get('/meter-readings/search', [MeterReadingController::class, 'search'])->name('meter-readings.search');
    Route::get('/meter-readings/consumer/{consumer}', [MeterReadingController::class, 'getConsumerReading'])->name('meter-readings.consumer');
    Route::get('/meter-reading', fn() => redirect()->route('meter-readings.create'))->name('meter-reading.alias');

    // meter reading and billing
    Route::get('/bills', [BillingController::class, 'index'])->name('bills.index');
    Route::get('/bills/create', [BillingController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillingController::class, 'store'])->name('bills.store');
    Route::get('/bills/{bill}/statement', [BillingController::class, 'statement'])->name('bills.statement');
    Route::get('/api/consumers/{consumer}/reading', [BillingController::class, 'getConsumerReading'])->name('bills.consumer-reading');

    // billing aliases
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');

    // payments and receipts
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // sms alerts and gateway
    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms/billing-notice/{bill}', [SmsController::class, 'sendBillingNotice'])->name('sms.send-billing-notice');
    Route::post('/sms/bill-reminder/{bill}', [SmsController::class, 'sendBillReminder'])->name('sms.send-bill-reminder');
    Route::post('/sms/overdue-alert/{bill}', [SmsController::class, 'sendOverdueAlert'])->name('sms.send-overdue-alert');
    Route::post('/sms/batch-reminders', [SmsController::class, 'batchReminders'])->name('sms.batch-reminders');
    Route::post('/sms/batch-overdue', [SmsController::class, 'batchOverdueAlerts'])->name('sms.batch-overdue');
    Route::post('/sms/send-test', [SmsController::class, 'sendTestSms'])->name('sms.send-test');
    Route::post('/sms/update-settings', [SmsController::class, 'updateSettings'])->name('sms.update-settings');

    // operational reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // respondent evaluation report
    Route::get('/admin/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/admin/evaluations/print', [EvaluationController::class, 'printReport'])->name('evaluations.print');

    // profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// auth routes
require __DIR__.'/auth.php';
