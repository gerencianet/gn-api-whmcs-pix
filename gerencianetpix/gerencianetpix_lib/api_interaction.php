<?php

use Gerencianet\Gerencianet;
use Gerencianet\Exception\GerencianetException;

/**
 * Retrieve Genrencianet API Instance
 * 
 * @param array $gatewayParams Payment Gateway Module Parameters
 * 
 * @return \Gerencianet\Endpoints $api_instance Gerencianet API Instance
 */
function getGerencianetApiInstance($gatewayParams)
{
    // Pix Parameters
    $pixCert = $gatewayParams['pixCert'];

    // Boolean Parameters
    $mtls    = ($gatewayParams['mtls'] == 'on');
    $debug   = ($gatewayParams['debug'] == 'on');
    $sandbox = ($gatewayParams['sandbox'] == 'on');

    // Client Authentication Parameters
    $clientIdSandbox     = $gatewayParams['clientIdSandbox'];
    $clientIdProd        = $gatewayParams['clientIdProd'];
    $clientSecretSandbox = $gatewayParams['clientSecretSandbox'];
    $clientSecretProd    = $gatewayParams['clientSecretProd'];

    // Getting API Instance
    $api_instance = Gerencianet::getInstance(
        array(
            'client_id' => $sandbox ? $clientIdSandbox : $clientIdProd,
            'client_secret' => $sandbox ? $clientSecretSandbox : $clientSecretProd,
            'pix_cert' => $pixCert,
            'sandbox' => $sandbox,
            'debug' => $debug,
            'headers' => [
                'x-skip-mtls-checking' => $mtls ? 'false' : 'true' // Needs to be string
            ]
        )
    );

    return $api_instance;
}

/**
 * Create Immediate Charge
 * 
 * @param \Gerencianet\Endpoints $api_instance Gerencianet API Instance
 * @param array $requestBody Request Body
 * 
 * @return array Generated Charge
 */
function createImmediateCharge($api_instance, $requestBody)
{

    try {
        $responseData = $api_instance->pixCreateImmediateCharge([], $requestBody);

        return $responseData;

    } catch (GerencianetException $e) {
        showException('Gerencianet Exception', array($e));

    } catch (Exception $e) {
        showException('Exception', array($e));
    }
}

/**
 * Configure WebhookUrl
 * 
 * @param \Gerencianet\Endpoints $api_instance Gerencianet API Instance
 * @param array $requestParams Request Params
 * @param array $requestBody Request Body
 */
function configWebhook($api_instance, $requestParams, $requestBody)
{
    try {
        $api_instance->pixConfigWebhook($requestParams, $requestBody);

    } catch (GerencianetException $e) {
        showException('Gerencianet Exception', array($e));

    } catch (Exception $e) {
        showException('Exception', array($e));
    }
}

/**
 * Generate QR Code for a Pix Charge
 * 
 * @param \Gerencianet\Endpoints $api_instance Gerencianet API Instance
 * @param array $requestParams Request Params
 * 
 * @return array QR Code Infos
 */
function generateQRCode($api_instance, $requestParams)
{
    try {
        $qrcode = $api_instance->pixGenerateQRCode($requestParams);

        return $qrcode;

    } catch (GerencianetException $e) {
        showException('Gerencianet Exception', array($e));

    } catch (Exception $e) {
        showException('Exception', array($e));
    }
}

/**
 * Refund Pix Charge
 * 
 * @param \Gerencianet\Endpoints $api_instance Gerencianet API Instance
 * @param array $requestParams Request Params
 * @param array $requestBody Request Body
 * 
 * @return array Charge Refund Infos
 */
function devolution($api_instance, $requestParams, $requestBody)
{
    try {
        $responseData = $api_instance->pixDevolution($requestParams, $requestBody);

        return $responseData;

    } catch (GerencianetException $e) {
        showException('Gerencianet Exception', array($e));

    } catch (Exception $e) {
        showException('Exception', array($e));
    }
}