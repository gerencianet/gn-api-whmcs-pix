<?php

if(!defined('WHMCS')){die();}

require_once ROOTDIR . '/modules/gateways/gerencianetpix/gerencianetpix_lib/handler/exception_handler.php';
require_once ROOTDIR . '/modules/gateways/gerencianetpix/gerencianetpix_lib/api_interaction.php';
require_once ROOTDIR . '/modules/gateways/gerencianetpix/gerencianetpix_lib/functions/gateway_functions.php';

use WHMCS\Database\Capsule;

// defina o método de pagamento 
define('PAYMENT_METHOD', 'gerencianetpix' );

add_hook('CartTotalAdjustment', 1, function($vars) { 

    $cart_adjustments = [];

    // Carrega os valores das configurações do Gateway
    $paramsGateway = getGatewayVariables(PAYMENT_METHOD);
    $pixDiscount = str_replace('%', '', $paramsGateway['pixDiscount']);

    if($vars['paymentmethod'] == PAYMENT_METHOD && $pixDiscount > 0) {

        // Busca o id da moeda BRL
        $result = Capsule::table('tblcurrencies') 
                -> where('code', '=', 'BRL' ) 
                -> get() -> first();
        $idBRL = $result->id;

        if (!empty($idBRL)) {
            
            $products = [];
            foreach ($vars['products'] as $product) {
                // Valor por padrão, é armazenado na coluna 'monthly' da tabela de preço
                if(empty($product['billingcycle']) || $product['billingcycle'] == 'onetime') {
                    $billingcycle = 'monthly';
                } else {
                    $billingcycle = $product['billingcycle'];
                }
                
                // Não verifica o valor do item, se ele for free 
                if($billingcycle != 'freeaccount') {
                    // Busca o preco 
                    $price = Capsule::table('tblpricing') 
                        -> where('type', '=', 'product' ) 
                        -> where('relid', '=', $product['pid'])
                        -> where('currency', '=', $idBRL)
                        -> first($billingcycle);
                    
                    // Por padrão, é salvo -1 em todos os preços do produto que não foi configurado
                    $products[] = (float)$price->$billingcycle > 0 ? (float)$price->$billingcycle : 0;
                }
            }
            // Soma o valor total dos itens
            $invoice_total = array_sum($products);

            if($invoice_total > 0) {
                // Valor do desconto
                $discountValue = (float)(($invoice_total) * $pixDiscount) /100;

                $cart_adjustments = [
                    "description" => "Desconto de $pixDiscount% no pagamento com PIX",
                    "amount" => $discountValue * -1,// Valor do desconto a ser subtraido
                    "taxed" => false,
                ];
            }

        } else {
            showException('Exception', array('A Gerencianet processa apenas transações na moeda brasileira, o Real (código BRL)'));
        }
    }

    return $cart_adjustments;
});

add_hook('AdminAreaPage', 1, function ($vars) {

    $extraVariables = [];
    $url = $_SERVER['REQUEST_URI'];

    if ($_REQUEST['updated'] == 'gerencianetpix') {

        if($_SERVER['HTTPS'] !== 'on' || strpos($_SERVER['HTTP_REFERER'], 'localhost') !== false ||  strpos($_SERVER['HTTP_REFERER'], '127.0.0.1')) {

            $extraVariables['jquerycode'] = '
            $("#Payment-Gateway-Config-gerencianetpix")
            .prepend(`
                <div class="errorbox">
                    <strong>
                        <span class="title">Erro!</span>
                    </strong>
                    <br>
                        Identificamos que o seu domínio não possui certificado de segurança HTTPS ou 
                         não é válido para registrar o Webhook!
                    </br>    
                </div>`);
            $(".successbox").remove();
            ';
        } else {
            // Carrega os valores das configurações do Gateway
            $paramsGateway = getGatewayVariables(PAYMENT_METHOD);
            try {
                // Recebe uma nova instância da API-Gerencianet
                $api_instance = getGerencianetApiInstance($paramsGateway);
                // Registra o webhook
                createWebhook($api_instance, $paramsGateway);

                $extraVariables['jquerycode'] = '
                $("#Payment-Gateway-Config-gerencianetpix")
                .prepend(`
                    <div class="successbox">
                        <strong>
                            <span class="title">Sucesso!</span>
                        </strong>
                        <br>
                        Weebhook Salvo
                        </br>    
                    </div>`);
                ';

            } catch (\Exception $e) {
                logActivity("Erro ao configurar webhook");
                showException($e);
            }
        }
    }

    return $extraVariables;
});