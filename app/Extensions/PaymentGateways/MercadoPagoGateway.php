<?php

namespace App\Services\Billing\Gateway;

use App\Services\Billing\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use MercadoPago\SDK;
use MercadoPago\Payment;
use MercadoPago\Payer;

class MercadoPagoGateway extends Gateway
{
    public function __construct()
    {
        SDK::setAccessToken(Config::get('billing.gateways.mercadopago.access_token'));
    }

    public function charge($amount, $currency, $description, $metadata = [], $options = [])
    {
        $payment = new Payment();
        $payment->transaction_amount = $amount;
        $payment->description = $description;
        $payment->currency_id = $currency;
        $payment->metadata = $metadata;

        $payer = new Payer();
        $payer->email = $options['email'];
        $payment->payer = $payer;

        $payment->save();

        $transaction = new Transaction();
        $transaction->setIdentifier($payment->id);
        $transaction->setStatus(Transaction::STATUS_PENDING);
        $transaction->setAmount($amount);
        $transaction->setCurrency($currency);
        $transaction->setDescription($description);

        return $transaction;
    }

    public function verify(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $payment = Payment::find_by_id($transactionId);

        $transaction = new Transaction();
        $transaction->setIdentifier($transactionId);
        $transaction->setAmount($payment->transaction_amount);
        $transaction->setCurrency($payment->currency_id);
        $transaction->setDescription($payment->description);

        if ($payment->status == 'approved') {
            $transaction->setStatus(Transaction::STATUS_COMPLETED);
        } else if ($payment->status == 'pending') {
            $transaction->setStatus(Transaction::STATUS_PENDING);
        } else {
            $transaction->setStatus(Transaction::STATUS_FAILED);
        }

        return $transaction;
    }
}
