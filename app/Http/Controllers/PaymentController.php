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
    private $userId;
    private $httpClient;
    public function __construct(Request $request)
    {
        $this->userId = $request->userId;

        $this->httpClient = new Client(
            [
                'headers' =>
                [
                    // take session_id cookie from request to be used in http call
                    // to invoice-service
                    "Cookie" => 'session_id=' . $request->cookie('session_id'),
                ],
            ]
        );
    }

    public function handleRequest()
    {
        try {
            $url = getenv('INVOICE_SERVICE_API');
            // http request to tell invoice-service to update invoice status
            $invoice = $this->httpClient->get($url)->getBody();
            return response($invoice, 200)->header('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            return response()->json(array('error' => $th->getMessage()), 400);
        }

    }
}
