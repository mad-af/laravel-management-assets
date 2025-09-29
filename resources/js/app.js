import "./bootstrap";
import "./scanners";
import QRCode from "qrcode";
import JsBarcode from "jsbarcode";

// Simple Theme Management
function changeTheme(theme) {
    // Save to localStorage
    localStorage.setItem("theme", theme);

    // Apply theme
    document.documentElement.setAttribute("data-theme", theme);

    // Update active state using the centralized function
    updateThemeActiveState(theme);
}

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);

    // Update active state when DOM is ready
    updateThemeActiveState(savedTheme);
}

// Update theme active state
function updateThemeActiveState(theme) {
    // Remove active class from all theme options
    document.querySelectorAll(".theme-option").forEach((option) => {
        option.classList.remove("active");
    });

    // Find and mark active theme
    const activeOption = document.querySelector(
        `[onclick="changeTheme('${theme}')"]`
    );
    if (activeOption) {
        activeOption.classList.add("active");
    }
}

// Enhanced theme initialization with observers
function enhancedInitTheme() {
    // Apply theme immediately
    initTheme();

    // Watch for DOM changes and reapply theme state
    const observer = new MutationObserver(() => {
        const savedTheme = localStorage.getItem("theme") || "light";
        // Ensure theme is still applied
        if (
            document.documentElement.getAttribute("data-theme") !== savedTheme
        ) {
            document.documentElement.setAttribute("data-theme", savedTheme);
        }
        // Update active states if theme options are present
        if (document.querySelector('[onclick*="changeTheme"]')) {
            updateThemeActiveState(savedTheme);
        }
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Initialize theme with enhanced features
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", enhancedInitTheme);
} else {
    enhancedInitTheme();
}

// Make functions globally available
window.changeTheme = changeTheme;

// Print QR/Barcode function
function printQRBarcode(tagCode, assetName, assetCode, purchaseYear) {
    // Create a new window for printing
    const printWindow = window.open("", "_blank", "width=800,height=600");

    // Create the HTML content for the print window
    const printContent = `<!DOCTYPE html>
    <html>
    <head>
    <title>Asset Tag - ${tagCode}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root {
        --fg:#222; --muted:#666; --line:#ddd; --brand:#0A66C2;
        }
        * { box-sizing: border-box; }
        html,body { margin:0; padding:0; }
        body {
        font-family: Arial, sans-serif;
        color: var(--fg);
        background:#fff;
        }
        .wrap {
        max-width: 720px;
        margin: 16px auto;
        padding: 16px;
        }

        /* Kartu ringkas untuk cetak */
        .card {
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 16px;
        }
        .hdr {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-bottom:12px;
        }
        .title {
        margin:0;
        font-size:18px;
        letter-spacing:.2px;
        }
        .badge {
        font: 12px/1 Arial, sans-serif;
        padding:6px 10px;
        color:#fff;
        background:var(--brand);
        border-radius:999px;
        white-space:nowrap;
        }

        .meta {
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap:8px 16px;
        font-size:14px;
        color:var(--muted);
        margin-bottom:12px;
        }
        .meta b { color:var(--fg); font-weight:600; }

        .codes {
        border-top:1px dashed var(--line);
        padding-top:12px;
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap:16px;
        align-items:center;
        }
        .codeBox {
        border:1px solid var(--line);
        border-radius:8px;
        padding:12px;
        text-align:center;
        }
        .codeBox h4 {
        margin:0 0 8px 0;
        font-size:14px;
        font-weight:600;
        color:var(--muted);
        letter-spacing:.3px;
        text-transform:uppercase;
        }
        /* ukuran kanvas & svg agar konsisten */
        #qrcode { width: 160px; height: 160px; display:inline-block; }
        #barcode { width: 260px; height: 90px; display:inline-block; }

        .actions {
        margin-top:16px;
        display:flex; gap:8px; justify-content:flex-end;
        }
        .btn {
        padding:10px 14px; border:0; border-radius:8px; cursor:pointer;
        color:#fff; background:var(--brand);
        }
        .btn.secondary { background:#6c757d; }

        @media (max-width:640px) {
        .meta { grid-template-columns: 1fr; }
        .codes { grid-template-columns: 1fr; }
        .actions { justify-content:center; }
        }

        @media print {
        body { background:#fff; }
        .wrap { padding:0; margin:0; }
        .card { border:0; border-radius:0; padding:0; }
        .actions { display:none !important; }
        .badge { background:#000; }
        /* pastikan dua kolom tetap muat pada kertas lebar; jika tidak, otomatis single column */
        .codes { grid-template-columns: 1fr 1fr; gap:12px; }
        #qrcode { width: 140px; height: 140px; }
        #barcode { width: 220px; height: 80px; }
        }
    </style>
    </head>
    <body>
    <div class="wrap">
        <div class="card">
        <div class="hdr">
            <h1 class="title">Asset Tag</h1>
            <span class="badge">${tagCode}</span>
        </div>

        <div class="meta">
            <div><b>Name:</b> ${assetName}</div>
            <div><b>Code:</b> ${assetCode}</div>
            ${
                purchaseYear
                    ? `<div><b>Purchase Year:</b> ${purchaseYear}</div>`
                    : ""
            }
        </div>

        <div class="codes">
            <div class="codeBox">
            <h4>QR Code</h4>
            <canvas id="qrcode" aria-label="QR Code for ${tagCode}"></canvas>
            </div>
            <div class="codeBox">
            <h4>Barcode</h4>
            <svg id="barcode" role="img" aria-label="Barcode for ${tagCode}"></svg>
            </div>
        </div>
        </div>
        <div class="actions no-print">
            <button class="btn" onclick="window.print()">Print</button>
            <button class="btn secondary" onclick="window.close()">Close</button>
        </div>
    </div>
    </body>
    </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();

    // Wait for the window to load, then generate codes
    printWindow.onload = function () {
        // Generate QR Code using tag_code
        const qrCanvas = printWindow.document.getElementById("qrcode");
        QRCode.toCanvas(
            qrCanvas,
            tagCode,
            {
                width: 200,
                height: 200,
                margin: 2,
            },
            function (error) {
                if (error) console.error("QR Code generation error:", error);
            }
        );

        // Generate Barcode using tag_code
        const barcodeSvg = printWindow.document.getElementById("barcode");
        JsBarcode(barcodeSvg, tagCode, {
            format: "CODE128",
            width: 2,
            height: 100,
            displayValue: true,
            fontSize: 14,
            margin: 10,
        });
    };
}

// Make printQRBarcode globally available
window.printQRBarcode = printQRBarcode;

function initScannerIfPresent() {
    if (document.body.dataset.route != "scanners.index") return;

    // hindari double init
    if (!window._cameraScanner) {
        window._cameraScanner = new window.QRBarcodeScanner();
    }
}
