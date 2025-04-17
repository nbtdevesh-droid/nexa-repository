<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StripeWebhookController extends Controller
{
    
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $payload = @file_get_contents('php://input');
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        Log::info("webhook start.");

        // Verify the webhook signature to ensure it came from Stripe
        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            Log::error("Invalid payload received.");
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error("Invalid signature.");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                Log::info("payment success.");
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;

            default:
                Log::warning('Received unexpected event type: ' . $event->type);
                return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'success']);
    }

    // Handle successful payment
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Fetch the first order with matching transaction_id
        // $order = Order::select(['user_id', 'order_id'])->where('transaction_id', $paymentIntent->id)->first();

        // if (!$order) {
        //     Log::error("Order not found for transaction ID: {$paymentIntent->id}");
        //     return response()->json(['error' => 'Order not found'], 404);
        // }

        // Store payment information
        Payment::create([
            // 'user_id'         => $order->user_id,
            // 'order_id'        => $order->order_id,
            'transaction_id'  => $paymentIntent->id,
            'amount'          => $paymentIntent->amount_received / 100,
            'currency'        => $paymentIntent->currency,
            'payment_method'  => $paymentIntent->payment_method_types[0],
            'payment_status'  => $paymentIntent->status,
            'payment_details' => json_encode($paymentIntent),
        ]);
        Log::info("Payment for Order {$paymentIntent->id} succeeded.");
    }

    // Handle failed payment
    private function handlePaymentIntentFailed($paymentIntent)
    {
        // Your logic for failed payment, e.g., notify the user
        Log::error("Payment for Order {$paymentIntent->id} failed.");
    }
}
