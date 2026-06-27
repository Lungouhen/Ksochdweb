<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;

/**
 * Handles public donation processing with Stripe integration
 */
class DonationController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display donation form for a specific campaign
     */
    public function create(Request $request, Campaign $campaign = null)
    {
        $campaigns = Campaign::active()->with('category')->get();
        
        return view('public.donate', compact('campaign', 'campaigns'));
    }

    /**
     * Process donation payment
     */
    public function store(StoreDonationRequest $request)
    {
        try {
            DB::beginTransaction();

            // Get validated donation data
            $donationData = $request->getDonationData();

            // Create Stripe customer if doesn't exist
            $stripeCustomerId = $this->paymentService->createOrFindCustomer(
                $request->input('donor_email'),
                $request->input('donor_name'),
                $request->input('donor_phone')
            );

            // Process payment based on frequency
            if ($request->input('frequency') === 'one-time') {
                $charge = $this->processOneTimePayment($donationData, $stripeCustomerId);
            } else {
                $subscription = $this->processRecurringPayment($donationData, $stripeCustomerId);
                $donationData['stripe_subscription_id'] = $subscription->id;
            }

            // Create donation record
            $donation = Donation::create([
                'campaign_id' => $donationData['campaign_id'] ?? null,
                'donor_name' => $request->input('is_anonymous') ? 'Anonymous' : $donationData['donor_name'],
                'donor_email' => $donationData['donor_email'],
                'donor_phone' => $donationData['donor_phone'] ?? null,
                'amount' => $donationData['amount'],
                'net_amount' => $donationData['net_amount'],
                'processing_fee' => $donationData['processing_fee'],
                'currency' => $donationData['currency'],
                'frequency' => $donationData['frequency'],
                'payment_method' => $donationData['payment_method'],
                'stripe_charge_id' => $charge->id ?? null,
                'stripe_customer_id' => $stripeCustomerId,
                'is_anonymous' => $request->boolean('is_anonymous'),
                'tribute_type' => $donationData['tribute_type'] ?? null,
                'tribute_name' => $donationData['tribute_name'] ?? null,
                'message' => $donationData['message'] ?? null,
                'status' => 'completed',
                'metadata' => [
                    'cover_fees' => $request->boolean('cover_fees'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            // Update campaign total if associated
            if ($donation->campaign) {
                $donation->campaign->increment('total_raised', $donation->net_amount);
            }

            // Send confirmation email
            // Mail::to($donation->donor_email)->send(new DonationConfirmationMail($donation));

            DB::commit();

            return redirect()->route('public.donation.success', $donation)
                ->with('success', 'Thank you for your generous donation!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Donation processing failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['payment' => 'Payment processing failed. Please try again or contact support.']);
        }
    }

    /**
     * Process one-time payment via Stripe
     */
    protected function processOneTimePayment(array $data, string $customerId): Charge
    {
        return Charge::create([
            'amount' => (int) ($data['amount'] * 100), // Convert to cents
            'currency' => $data['currency'],
            'customer' => $customerId,
            'description' => 'One-time donation',
            'receipt_email' => $data['donor_email'],
            'metadata' => [
                'donation_type' => 'one-time',
                'campaign_id' => $data['campaign_id'] ?? 'general',
            ],
        ]);
    }

    /**
     * Setup recurring payment subscription via Stripe
     */
    protected function processRecurringPayment(array $data, string $customerId)
    {
        // Map frequency to Stripe interval
        $intervalMap = [
            'monthly' => 'month',
            'yearly' => 'year',
        ];

        $interval = $intervalMap[$data['frequency']] ?? 'month';

        // Create or get price ID for recurring donations
        $priceId = $this->paymentService->getOrCreateDonationPrice(
            (int) ($data['amount'] * 100),
            $data['currency'],
            $interval
        );

        return \Stripe\Subscription::create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'billing_cycle_anchor' => 'now',
            'metadata' => [
                'donation_type' => 'recurring',
                'campaign_id' => $data['campaign_id'] ?? 'general',
            ],
        ]);
    }

    /**
     * Show donation success page
     */
    public function success(Donation $donation)
    {
        return view('public.donation-success', compact('donation'));
    }

    /**
     * Webhook handler for Stripe events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'charge.succeeded':
                $charge = $event->data->object;
                // Update donation status if needed
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                // Mark recurring donation as cancelled
                Donation::where('stripe_subscription_id', $subscription->id)
                    ->update(['status' => 'cancelled']);
                break;

            default:
                // Unhandled event type
                return response('Unhandled event type', 200);
        }

        return response('Webhook handled', 200);
    }
}
