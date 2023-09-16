var html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", {
    fps: 2,
    qrbox: 250,
    aspectRatio: 1.0,
    formatsToSupport: [Html5QrcodeSupportedFormats.ITF]
},false);

function onScanSuccess(qrCodeMessage) {
    openItemInfoDetails(qrCodeMessage);
    console.log(`Scan result: ${qrCodeMessage}`);

}

function onScanError(errorMessage) {
    console.warn(`Code scan error = ${errorMessage}`);
}

function openScanner() {
    html5QrcodeScanner.render(onScanSuccess, onScanError);
}
