<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class PaystackWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Paystack Webhook start');
        // Retrieve the Paystack signature from the header
        $paystackSignature = $request->header('x-paystack-signature');

        // Validate the webhook signature
        $secretKey = env('PAYSTACK_SECRET');
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);

        if ($paystackSignature !== $computedSignature) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Log the webhook payload (useful for debugging)
        Log::info('Paystack Webhook Received:', $request->all());

        // Extract event type
        $event = $request->input('event');

        if ($event === 'charge.success') {
            return $this->handleSuccessfulPayment($request->input('data'));
        }
        return response()->json(['status' => 'ignored']);
    }

    private function handleSuccessfulPayment($data)
    {
        Log::info('Payment processed successfully:', [
            'transaction_id' => $data['id'],
            'amount' => $data['amount'] / 100,
            'amount1' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'],
            'reference' => $data['reference']
        ]);

        // Save payment to the database
        Payment::create([
            'transaction_id'  => $data['id'],
            'amount'          => $data['amount'] / 100,// Convert kobo to Naira
            'currency'        => $data['currency'],
            'payment_method'  => $data['channel'],
            'payment_status'  => $data['status'],
            'reference' => $data['reference'],
            'payment_details' => json_encode($data),
        ]);

        return response()->json(['status' => 'success']);
    }
}
