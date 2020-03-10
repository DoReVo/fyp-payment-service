<?php

namespace App\Http\Controllers;

use App\Helpers\JWTHelper;
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
    private $decodedToken;
    private $httpClient;
    public function __construct(Request $request)
    {
        $jwtHandler = new JWTHelper;
        $token = $request->bearerToken();
        $this->decodedToken = $jwtHandler->decode($token);
        $this->userId = $this->decodedToken['uid'];

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
