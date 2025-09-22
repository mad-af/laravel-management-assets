@vite('resources/js/scanner.js')

<script type="module">
  // Initialize scanner when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    console.log("heheh")
    if (window.QRBarcodeScanner) {
      console.log("heheh1")
      window.scanner = new window.QRBarcodeScanner();
      console.log("heheh2")
    }
  });

  // Cleanup on page unload
  window.addEventListener('beforeunload', () => {
    console.log("abc")
    if (window.scanner) {
      console.log("abc1")
      window.scanner.destroy();
      console.log("abc2")
    }
  });

</script>