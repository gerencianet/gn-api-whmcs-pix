<?php

require_once __DIR__ . '/../../../init.php';

include_once 'gerencianetpix/gerencianetpix_lib/database_interaction.php';
include_once 'gerencianetpix/gerencianetpix_lib/handler/exception_handler.php';

App::load_function('gateway');
App::load_function('invoice');

// Detect module name from filename
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

// Hook data retrieving
@ob_clean();
$postData = json_decode(file_get_contents('php://input'));

// Hook validation
if (isset($postData->evento) && isset($postData->data_criacao)) {
    header('HTTP/1.0 200 OK');

    exit();
}

$pixPaymentData = $postData->pix;

// Hook manipulation
if (empty($pixPaymentData)) {
    showException('Exception', array('Pagamento Pix não recebido pelo Webhook.'));

} else {
    header('HTTP/1.0 200 OK');

    $tableName = 'tblgerencianetpix';
    $txID  = $pixPaymentData[0]->txid;
    $e2eID = $pixPaymentData[0]->endToEndId;

    $success = !empty($e2eID);

    // Retrieving Invoice ID from 'tblgerencianetpix'
    $savedInvoice = find($tableName, 'txid', $txID);

    // Checking if the invoice has already been paid
    if (empty($savedInvoice['e2eid'])) {
        // Validate Callback Invoice ID
        $invoiceID = checkCbInvoiceID($savedInvoice['invoiceid'], $gatewayParams['name']);
    
        $conditions = [
            'invoiceid' => $invoiceID,
        ];
        $dataToUpdate = [
            'e2eid' => $e2eID,
        ];
    
        // Saving e2eid in table 'tblgerencianetpix'
        update($tableName, $conditions, $dataToUpdate);
    
        if($success) {
            $paymentFee = '0.00';
            $paymentAmount = $pixPaymentData[0]->valor;
    
            addInvoicePayment($invoiceID, $txID, $paymentAmount, $paymentFee, $gatewayModuleName);
    
        } else {
            showException('Exception', array("Pagamento Pix não efetuado para a Fatura #$invoiceID"));
        }
    }
}