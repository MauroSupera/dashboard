<?php

namespace App\Extensions\PaymentGateways;

use Exception;
use MercadoPago\SDK;
use App\Models\Invoice;
use App\Services\Billing\Gateway;

class MercadoPagoGateway implements Gateway
{
    public function __construct()
    {
        SDK::setAccessToken(config('payment.mercadopago.access_token'));
        SDK::setIntegratorId('dev_24c65fb163bf11ea96500242ac130004');
        SDK::setPublicKey('APP_USR-XXXXX');
        SDK::setPrivateKey('APP_USR-XXXXX');
        SDK::setEnv(config('payment.mercadopago.sandbox') ? 'sandbox' : 'production');
    }

    public function charge(Invoice $invoice, array $data = []): bool
    {
        // Código para processar o pagamento com MercadoPago

        return true; // ou false, dependendo do sucesso ou falha do pagamento
    }
}
