<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function makePayment(Request $request)
    {

        // make http calls to get invoice detail
        $client = new Client;
        $response = $client->get(getenv('Invoice_Service_API') . 'invoice/' . $request->invoice_id);

        // check for invoice ownership, return failed if user does not own

        // make payment

        // $payment = new Payment;

        // // get customer id from request bearer token or cookies
        // $payment->customer_id = 0;
        // // invoice id from request body
        // $payment->invoice_id = $request->invoice_id;
        // $payment->amount = $request->amount;
        // $payment->save();

        return $response->getBody();

    }
}
