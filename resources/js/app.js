import './bootstrap';
import QRCode from 'qrcode';
import JsBarcode from 'jsbarcode';

// Initialize Lucide icons after DOM and CDN are loaded
function initializeLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized successfully');
    } else {
        console.log('Waiting for Lucide library to load...');
        // Retry after a short delay
        setTimeout(initializeLucideIcons, 200);
    }
}

// Wait for window load to ensure all external scripts are loaded
window.addEventListener('load', function() {
    // Give a small delay to ensure Lucide CDN is fully loaded
    setTimeout(initializeLucideIcons, 100);
});

// Also try to initialize when DOM is ready (fallback)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeLucideIcons, 300);
    });
} else {
    setTimeout(initializeLucideIcons, 300);
}

// Expose function globally for manual re-initialization if needed
window.initLucide = initializeLucideIcons;

// Simple Theme Management
function changeTheme(theme) {
    // Save to localStorage
    localStorage.setItem('theme', theme);
    
    // Apply theme
    document.documentElement.setAttribute('data-theme', theme);
    
    // Update active state
    document.querySelectorAll('.theme-option').forEach(option => {
        option.classList.remove('active');
    });
    
    // Find and mark active theme
    const activeOption = document.querySelector(`[onclick="changeTheme('${theme}')"]`);
    if (activeOption) {
        activeOption.classList.add('active');
    }
}

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update active state after DOM is ready
    setTimeout(() => {
        const activeOption = document.querySelector(`[onclick="changeTheme('${savedTheme}')"]`);
        if (activeOption) {
            activeOption.classList.add('active');
        }
    }, 100);
}

// Initialize theme
initTheme();

// Make changeTheme globally available
window.changeTheme = changeTheme;

// Print QR/Barcode function
function printQRBarcode(assetCode, assetName) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    // Create the HTML content for the print window
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code & Barcode - ${assetCode}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    text-align: center;
                }
                .print-container {
                    max-width: 600px;
                    margin: 0 auto;
                }
                .asset-info {
                    margin-bottom: 30px;
                }
                .asset-info h2 {
                    margin: 0 0 10px 0;
                    color: #333;
                }
                .asset-info p {
                    margin: 5px 0;
                    color: #666;
                }
                .code-section {
                    margin: 30px 0;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                }
                .code-section h3 {
                    margin-top: 0;
                    color: #333;
                }
                #qrcode, #barcode {
                    margin: 20px 0;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-container">
                <div class="asset-info">
                    <h2>Asset Information</h2>
                    <p><strong>Code:</strong> ${assetCode}</p>
                    <p><strong>Name:</strong> ${assetName}</p>
                </div>
                
                <div class="code-section">
                    <h3>QR Code</h3>
                    <canvas id="qrcode"></canvas>
                </div>
                
                <div class="code-section">
                    <h3>Barcode</h3>
                    <svg id="barcode"></svg>
                </div>
                
                <div class="no-print" style="margin-top: 30px;">
                    <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Print</button>
                    <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Close</button>
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for the window to load, then generate codes
    printWindow.onload = function() {
        // Generate QR Code
        const qrCanvas = printWindow.document.getElementById('qrcode');
        QRCode.toCanvas(qrCanvas, assetCode, {
            width: 200,
            height: 200,
            margin: 2
        }, function(error) {
            if (error) console.error('QR Code generation error:', error);
        });
        
        // Generate Barcode
        const barcodeSvg = printWindow.document.getElementById('barcode');
        JsBarcode(barcodeSvg, assetCode, {
            format: "CODE128",
            width: 2,
            height: 100,
            displayValue: true,
            fontSize: 14,
            margin: 10
        });
    };
}

// Make printQRBarcode globally available
window.printQRBarcode = printQRBarcode;