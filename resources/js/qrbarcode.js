// Keep QR code logic EXACTLY as your original. Only barcode sizing is changed.
// Recommended CSS on the print page (you likely already have something similar):
// .bar-box { width: 1.8cm; height: 0.65cm; display:block; overflow:hidden; }
// .qr-box { width: 1cm; height: 1cm; display:block; }

import QRCode from "qrcode";
import JsBarcode from "jsbarcode";

// Listen for the print-qrbarcode event from Livewire (unchanged)
document.addEventListener("livewire:init", () => {
    Livewire.on("print-qrbarcode", (event) => {
        const { tagCode, url, html } = event;

        const printWindow = window.open("", "_blank", "width=800,height=600");
        printWindow.document.write(html);
        printWindow.document.close();

        printWindow.onload = function () {
            generateQRAndBarcode(printWindow, url, tagCode);
        };
    });
});

function generateQRAndBarcode(printWindow, url, tagCode) {
    const doc = printWindow.document;

    // ---------------------- QR (UNCHANGED) ----------------------
    const qrPlaceholder = doc.querySelector(".qr-box");
    if (qrPlaceholder && url) {
        const qrCanvas = doc.createElement("canvas");
        qrCanvas.style.width = "1cm";
        qrCanvas.style.height = "1cm";

        QRCode.toCanvas(
            qrCanvas,
            url,
            {
                width: 38, // ~1cm @ ~96dpi
                height: 38,
                margin: 0,
            },
            function (error) {
                if (!error) {
                    qrPlaceholder.innerHTML = "";
                    qrPlaceholder.appendChild(qrCanvas);
                }
            }
        );
    }

    // ---------------------- BARCODE (FIXED) ----------------------
    const barcodePlaceholder = doc.querySelector(".bar-box");
    if (barcodePlaceholder && tagCode) {
        const barcodeSvg = doc.createElementNS(
            "http://www.w3.org/2000/svg",
            "svg"
        );

        // Measure the placeholder to derive a sensible height in px
        const { height: boxH } = barcodePlaceholder.getBoundingClientRect();
        const targetH = Math.max(18, Math.round(boxH)); // keep it legible

        // 1) Generate barcode with narrow modules & zero margin (denser code)
        JsBarcode(barcodeSvg, tagCode, {
            format: "CODE128",
            width: 0.45, // tweak 0.35–0.55 to fit density
            height: targetH, // in px
            margin: 0,
            displayValue: false,
        });

        // 2) Convert to viewBox + scale to container without distortion
        const wAttr = parseFloat(barcodeSvg.getAttribute("width")) || 0;
        const hAttr = parseFloat(barcodeSvg.getAttribute("height")) || 0;
        if (wAttr && hAttr) {
            barcodeSvg.setAttribute("viewBox", `0 0 ${wAttr} ${hAttr}`);
            barcodeSvg.removeAttribute("width");
            barcodeSvg.removeAttribute("height");
        }
        barcodeSvg.style.width = "100%";
        barcodeSvg.style.height = "100%";
        barcodeSvg.setAttribute("preserveAspectRatio", "xMidYMid meet");

        // 3) Mount
        barcodePlaceholder.innerHTML = "";
        barcodePlaceholder.appendChild(barcodeSvg);
    }

    // Optional auto print
    // setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
}
