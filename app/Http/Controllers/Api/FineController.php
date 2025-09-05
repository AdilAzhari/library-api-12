<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Services\FineService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class FineController extends Controller
{
    use ApiMessages;

    public function __construct(
        protected FineService $fineService
    ) {}

    /**
     * Get fine summary for authenticated user
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $summary = [
                'total_outstanding' => $user->getOutstandingFineAmount(),
                'unpaid_count' => $user->unpaidFines()->count(),
                'paid_count' => $user->fines()->where('status', 'paid')->count(),
                'total_lifetime_fines' => $user->fines()->sum('amount'),
                'can_borrow' => $user->getOutstandingFineAmount() < config('library.settings.max_fine_amount', 50.00),
            ];

            return $this->successResponse('Fine summary retrieved successfully', $summary);

        } catch (Exception $e) {
            Log::error('Failed to get fine summary: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get fine summary');
        }
    }

    /**
     * Get outstanding fines for authenticated user
     */
    public function outstanding(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $outstandingFines = $user->fines()
                ->where('status', 'outstanding')
                ->with(['borrow.book'])
                ->orderBy('created_at')
                ->get()
                ->map(function ($fine) {
                    $fine->days_outstanding = now()->diffInDays($fine->created_at);

                    return $fine;
                });

            return $this->successResponse('Outstanding fines retrieved successfully', $outstandingFines);

        } catch (Exception $e) {
            Log::error('Failed to get outstanding fines: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get outstanding fines');
        }
    }

    /**
     * Get available payment methods
     */
    public function paymentMethods(Request $request): JsonResponse
    {
        try {
            $paymentMethods = [
                [
                    'id' => 'card',
                    'name' => 'Credit/Debit Card',
                    'description' => 'Pay with Visa, MasterCard, or American Express',
                    'enabled' => true,
                    'icon' => 'credit-card',
                ],
                [
                    'id' => 'paypal',
                    'name' => 'PayPal',
                    'description' => 'Pay securely with your PayPal account',
                    'enabled' => config('services.paypal.enabled', false),
                    'icon' => 'paypal',
                ],
                [
                    'id' => 'bank_transfer',
                    'name' => 'Bank Transfer',
                    'description' => 'Transfer funds directly from your bank',
                    'enabled' => true,
                    'icon' => 'bank',
                ],
                [
                    'id' => 'cash',
                    'name' => 'Cash Payment',
                    'description' => 'Pay in person at the library counter',
                    'enabled' => true,
                    'icon' => 'money',
                ],
            ];

            return $this->successResponse('Payment methods retrieved successfully', $paymentMethods);

        } catch (Exception $e) {
            Log::error('Failed to get payment methods: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get payment methods');
        }
    }

    /**
     * Get payment status for a specific fine
     */
    public function paymentStatus(Request $request, Fine $fine): JsonResponse
    {
        try {
            if ($fine->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $paymentStatus = [
                'fine_id' => $fine->id,
                'status' => $fine->status,
                'amount' => $fine->amount,
                'paid_at' => $fine->paid_at,
                'payment_method' => $fine->payment_method,
                'payment_reference' => $fine->payment_reference,
                'can_pay' => $fine->status === 'outstanding',
            ];

            if ($fine->status === 'processing') {
                $paymentStatus['estimated_completion'] = 'Payment is being processed. This usually takes 1-2 business days.';
            }

            return $this->successResponse('Payment status retrieved successfully', $paymentStatus);

        } catch (Exception $e) {
            Log::error('Failed to get payment status: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get payment status');
        }
    }
}
