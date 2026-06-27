<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates donation form submissions from public frontend
 */
class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Anyone can donate (public endpoint)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:10000',
            'currency' => 'required|in:USD,EUR,GBP,CAD,AUD',
            'frequency' => 'required|in:one-time,monthly,yearly',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_anonymous' => 'boolean',
            'tribute_type' => 'nullable|in:memory,honor',
            'tribute_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:500',
            'cover_fees' => 'boolean', // Donor covers processing fees
            'stripe_token' => 'required_if:payment_method,card',
            'payment_method' => 'required|in:card,paypal,bank_transfer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Minimum donation amount is $1.',
            'amount.max' => 'Maximum donation amount is $10,000.',
            'donor_email.email' => 'Please provide a valid email address.',
            'stripe_token.required_if' => 'Payment information is required.',
        ];
    }

    /**
     * Prepare data for insertion into database
     */
    public function getDonationData(): array
    {
        $validated = $this->validated();
        
        // Calculate final amount including fees if donor chooses to cover them
        if ($this->input('cover_fees')) {
            $feeRate = 0.029 + 0.30; // Stripe standard fee: 2.9% + $0.30
            $validated['amount'] = $validated['amount'] / (1 - 0.029);
            $validated['processing_fee'] = $validated['amount'] * 0.029 + 0.30;
        } else {
            $validated['processing_fee'] = $validated['amount'] * 0.029 + 0.30;
        }

        $validated['net_amount'] = $validated['amount'] - $validated['processing_fee'];
        
        return $validated;
    }
}
