<?php

namespace App\Http\Controllers;

use App\Payment;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userId;
    private $httpClient;
    public function __construct(Request $request)
    {

        $this->httpClient = new Client(
            [
                'base_uri' => getenv('INVOICE_SERVICE_API'),
                'headers' =>
                [
                    // JWT token from this request
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );
    }

    // params require:
    // invoice_id
    // amount
    public function makePayment(Request $request)
    {

        // make http calls to get invoice detail
        $client = new Client;
        $response = $client->get(getenv('Invoice_Service_API') . 'invoice/' . $request->invoice_id);

        // check for invoice ownership, return failed if user does not own

        // make payment
        $payment = new Payment;

        // get customer id from request bearer token or cookies
        $payment->customer_id = $this->userId;
        // invoice id from request body
        $payment->invoice_id = $request->invoice_id;
        // payment amount from request body
        $payment->amount = $request->amount;
        // save to payment record to db
        $payment->save();

        // http request to tell invoice-service to update invoice status
        try {
            $invoice = $this->httpClient->patch('invoice/' . $request->invoice_id . '/pay', ['json' => ['payment_id' => $payment->id]]);
        } catch (\Throwable $th) {
            return response()->json(array('error' => $th->getMessage()), 400);
        }

        return response()->json($payment, 200);

    }
}
