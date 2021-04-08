/**
 * Copy QR Code
 * 
 * @param {string} textToCopy 
 */
function copyQrCode(textToCopy) {
    const button = document.getElementById("copyButton");
    const textarea = document.createElement("textarea");

    textarea.value = textToCopy;

    document.body.appendChild(textarea);

    textarea.select();
    document.execCommand("copy");

    document.body.removeChild(textarea);

    button.innerHTML = "Copiado &#10004";
    setTimeout(() => (button.innerHTML = "Copiar QR Code"), 1e3);
}
// Displays the QRCode in PDF printing
$( ".payment-btn-container" ).removeClass( "d-print-none" );
