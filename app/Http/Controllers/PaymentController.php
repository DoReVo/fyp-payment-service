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
        $this->userId = $request->userId;

        $this->httpClient = new Client(
            [
                'base_uri' => getenv('INVOICE_SERVICE_API'),
                'headers' =>
                [
                    // take session_id cookie from request to be used in http call
                    // to invoice-service
                    "Cookie" => 'session_id=' . $request->cookie('session_id'),
                ],
            ]
        );
    }

    // params require:
    // invoice_id
    // amount
    public function makePayment(Request $request)
    {
        // payment model instance
        $payment = new Payment;

        // customer id from request body,
        // it was injected by the SessionAuth middleware.
        $payment->customer_id = $this->userId;
        // invoice id from request body
        $payment->invoice_id = $request->invoice_id;
        // payment amount from request body
        $payment->amount = $request->amount;
        // save payment record to db
        $payment->save();

        try {
            // http request to tell invoice-service to update invoice status
            $invoice = $this->httpClient->patch('invoice/' . $request->invoice_id . '/pay', ['json' => ['payment_id' => $payment->id]]);
        } catch (\Throwable $th) {
            return response()->json(array('error' => $th->getMessage()), 400);
        }

        return response()->json($payment, 200);

    }
}
