<?php

require 'gerencianetpix/gerencianet-sdk/autoload.php';
include_once 'gerencianetpix/gerencianetpix_lib/api_interaction.php';
include_once 'gerencianetpix/gerencianetpix_lib/database_interaction.php';
include_once 'gerencianetpix/gerencianetpix_lib/handler/exception_handler.php';
include_once 'gerencianetpix/gerencianetpix_lib/functions/gateway_functions.php';

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @see https://developers.whmcs.com/payment-gateways/configuration/
 *
 * @return array
 */
function gerencianetpix_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Gerencianet via Pix',
        ),
        'clientIdProd' => array(
            'FriendlyName' => 'Client_ID de Produção (obrigatório)',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => '',
        ),
        'clientSecretProd' => array(
            'FriendlyName' => 'Client_Secret de Produção (obrigatório)',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => '',
        ),
        'clientIdSandbox' => array(
            'FriendlyName' => 'Client_ID de Sandbox (obrigatório)',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => '',
        ),
        'clientSecretSandbox' => array(
            'FriendlyName' => 'Client_Secret de Sandbox (obrigatório)',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => '',
        ),
        'sandbox' => array(
            'FriendlyName' => 'Sandbox',
            'Type' => 'yesno',
            'Description' => 'Habilita o modo Sandbox da Gerencianet',
        ),
        'debug' => array(
            'FriendlyName' => 'Debug',
            'Type' => 'yesno',
            'Description' => 'Habilita o modo Debug',
        ),
        'pixKey' => array(
            'FriendlyName' => 'Chave Pix (obrigatório)',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => 'Insira sua chave Pix padrão para recebimentos',
        ),
        'pixCert' => array(
            'FriendlyName' => 'Certificado Pix',
            'Type' => 'text',
            'Size' => '350',
            'Default' => '/var/certs/cert.pem',
            'Description' => 'Insira o caminho do seu certificado .pem',
        ),
        'pixDiscount' => array(
            'FriendlyName' => 'Desconto do Pix (porcentagem %)',
            'Type' => 'text',
            'Size' => '3',
            'Default' => '0%',
            'Description' => 'Preencha um valor caso queira dar um desconto para pagamentos via Pix',
        ),
        'pixDays' => array(
            'FriendlyName' => 'Validade da Cobrança em Dias',
            'Type' => 'text',
            'Size' => '3',
            'Default' => '1',
            'Description' => 'Tempo em dias de validade da cobrança',
        ),
        'mtls' => array(
            'FriendlyName' => 'Validar mTLS',
            'Type' => 'yesno',
            'Default' => true,
            'Description' => 'Entenda os riscos de não configurar o mTLS acessando o link https://gnetbr.com/rke4baDVyd',
        )
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $gatewayParams Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function gerencianetpix_link($gatewayParams)
{
    //  Validate if required parameters are empty
    validateRequiredParams($gatewayParams);

    // Getting API Instance
    $api_instance = getGerencianetApiInstance($gatewayParams);

    // Creating table 'tblgerencianetpix'
    createGerencianetPixTable();

    // Verifying if exists a Pix Charge for current invoiceId
    $existingPixCharge = getPixCharge($gatewayParams['invoiceid']);
    
    if (empty($existingPixCharge)) {
        // Creating a new Pix Charge
        $newPixCharge = createPixCharge($api_instance, $gatewayParams);
    
        if (isset($newPixCharge['txid'])) {
            // Storing Pix Charge Infos on table 'tblgerencianetpix' for later use
            storePixChargeInfo($newPixCharge, $gatewayParams);
        }
    }

    // Generating QR Code
    $locId = $existingPixCharge ? $existingPixCharge['locid'] : $newPixCharge['loc']['id'];
    return createQRCode($api_instance, $locId);
}

/**
 * Refund transaction
 *
 * Called when a refund is requested for a previously successful transaction
 *
 * @param array $gatewayParams Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array Transaction response status
 */
function gerencianetpix_refund($gatewayParams)
{
    //  Validating if required parameters are empty
    validateRequiredParams($gatewayParams);

    // Getting API Instance
    $api_instance = getGerencianetApiInstance($gatewayParams);

    // Refunding Pix Charge
    $responseData = refundCharge($api_instance, $gatewayParams);
    
    return array(
        'status' => $responseData['rtrId'] ? 'success' : 'error',
        'rawdata' => $responseData,
        'transid' => $responseData['rtrId'] ? $responseData['rtrId'] : 'Not Refunded',
    );
}