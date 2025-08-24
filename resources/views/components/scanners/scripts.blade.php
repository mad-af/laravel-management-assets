@vite('resources/js/scanner.js')

<script type="module">
  // Initialize scanner when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    if (window.QRBarcodeScanner) {
      window.scanner = new window.QRBarcodeScanner();
    }
  });

  // Cleanup on page unload
  window.addEventListener('beforeunload', () => {
    if (window.scanner) {
      window.scanner.destroy();
    }
  });

</script>