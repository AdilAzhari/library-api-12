<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Services\FineService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class FineController extends Controller
{
    public function __construct(
        protected FineService $fineService
    ) {}

    /**
     * Display user's fines
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $status = $request->input('status', 'outstanding'); // outstanding, paid, all
            $perPage = $request->input('per_page', 10);

            $user = Auth::user();
            $fines = $this->fineService->getUserFines($user->id, $status, $perPage);

            $summary = [
                'total_outstanding' => $user->getOutstandingFineAmount(),
                'total_paid' => $this->fineService->getUserTotalPaidFines($user->id),
                'count_unpaid' => $user->unpaidFines()->count(),
            ];

            return Inertia::render('Fines/Index', [
                'fines' => $fines,
                'summary' => $summary,
                'filters' => [
                    'status' => $status,
                ],
                'paymentMethods' => config('library.payment_methods', ['card', 'bank_transfer']),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load fines: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to load fines. Please try again.');
        }
    }

    /**
     * Show payment form for a fine
     */
    public function showPayment(Fine $fine): Response|RedirectResponse
    {
        try {
            Gate::authorize('pay', $fine);

            return Inertia::render('Fines/Payment', [
                'fine' => $fine->load('borrow.book'),
                'paymentMethods' => config('library.payment_methods', ['card', 'bank_transfer']),
                'minimumPayment' => config('library.minimum_fine_payment', 5.00),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load payment form: '.$e->getMessage());

            return redirect()
                ->route('fines.index')
                ->with('error', 'Unable to process payment. Please contact the library.');
        }
    }

    /**
     * Process fine payment
     */
    public function processPayment(Request $request, Fine $fine): RedirectResponse
    {
        try {
            Gate::authorize('pay', $fine);

            $validatedData = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|in:card,bank_transfer,cash',
                'payment_reference' => 'nullable|string|max:255',
            ]);

            $payment = $this->fineService->processPayment(
                $fine,
                $validatedData['amount'],
                $validatedData['payment_method'],
                $validatedData['payment_reference'] ?? null
            );

            $message = $fine->fresh()->status->value === 'paid'
                ? 'Fine paid in full successfully!'
                : 'Partial payment of $'.number_format($validatedData['amount'], 2).' processed successfully.';

            return redirect()
                ->route('fines.index')
                ->with('success', $message);

        } catch (Exception $e) {
            Log::error('Payment processing failed: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Payment processing failed: '.$e->getMessage());
        }
    }

    /**
     * Show fine details
     */
    public function show(Fine $fine): Response|RedirectResponse
    {
        try {
            Gate::authorize('view', $fine);

            return Inertia::render('Fines/Show', [
                'fine' => $fine->load(['borrow.book', 'payments']),
                'paymentHistory' => $fine->payments()->latest()->get(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load fine details: '.$e->getMessage());

            return redirect()
                ->route('fines.index')
                ->with('error', 'Unable to load fine details.');
        }
    }
}
