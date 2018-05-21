<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Libraries\AstroPayStreamline;

class HomeController extends Controller
{
    public function index(AstroPayStreamline $aps, Request $request)
    {
        $price = 1500;
        $status = '';
        $error = '';

        if ($request->input('full_name') && $request->input('email')) {
            $invoice          = rand(1, 1000);
            $amount           = $price;
            $country          = 'BR';
            $bank             = 'TE';
            $currency         = 'USD';
            $iduser           = rand(1, 1000);
            $description      = '';
            $cpf              = '00003456789';
            $name             = $request->input('full_name');
            $email            = $request->input('email');
            $return_url       = 'http://astropay.local/';
            $confirmation_url = 'http://astropay.local/';
            $bdate            = '';
            $address          = '';
            $zip              = '';
            $city             = '';
            $state            = '';

            $response = $aps->newinvoice($invoice, $amount, $bank, $country, $iduser, $cpf, $name, $email, $currency, $description, $bdate, $address, $zip, $city, $state, $return_url, $confirmation_url);
            $decoded_response = json_decode($response);

            if ($decoded_response->status == 0) {
                $url = $decoded_response->link;
                header("Location: $url");
                die();
            } else {
                $error = $decoded_response->desc;
            }
        }

        if ($request->input('x_amount')) {
            $status = $request->input('result');
            $check_amount = $request->input('x_amount');

            if ($price == $check_amount && $status == 9) {
                $status = 'Amount Paid. Transaction successfully concluded.';
            } else if ($price == $check_amount && $status == 8) {
                $status = 'Operation rejected by the bank.';
            } else if ($price == $check_amount && $status == 7) {
                $status = 'Pending transaction awaiting approval.';
            } else {
                $status = 'Payment failed.';
            }
        }

        return view('welcome', [
            'price' => $price,
            'status' => $status,
            'error' => $error
        ]);
    }
}
