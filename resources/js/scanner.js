import { BrowserMultiFormatReader, NotFoundException } from '@zxing/library';

class QRBarcodeScanner {
  constructor() {
    this.reader = new BrowserMultiFormatReader();
    this.isScanning = false;
    this.currentDeviceId = null;
    this.availableDevices = [];
    this.scanHistory = JSON.parse(localStorage.getItem('scanHistory') || '[]');

    this.elements = {
      video: document.getElementById('scanner-video'),
      canvas: document.getElementById('scanner-canvas'),
      overlay: document.getElementById('scanner-overlay'),
      startBtn: document.getElementById('start-scanner'),
      stopBtn: document.getElementById('stop-scanner'),
      switchBtn: document.getElementById('switch-camera'),
      status: document.getElementById('scanner-status'),
      scanResult: document.getElementById('scan-result'),
      scanEmpty: document.getElementById('scan-empty'),
      scannedCode: document.getElementById('scanned-code'),
      assetInfo: document.getElementById('asset-info'),
      assetNotFound: document.getElementById('asset-not-found'),
      recentScans: document.getElementById('recent-scans')
    };

    this._onVisibilityChange = this._onVisibilityChange.bind(this);

    this.init();
  }

  async init() {
    try {
      await this.getAvailableDevices();
      this.bindEvents();
      this.updateScanHistory();
      this.updateStatus('info', 'Scanner siap digunakan. Klik "Mulai Scan" untuk memulai.');
      document.addEventListener('visibilitychange', this._onVisibilityChange);
      window.addEventListener('pagehide', () => this.destroy());
    } catch (error) {
      console.error('Error initializing scanner:', error);
      this.updateStatus('error', 'Gagal menginisialisasi scanner. Pastikan browser mendukung kamera.');
    }
  }

  async getAvailableDevices() {
    const devices = await navigator.mediaDevices.enumerateDevices();
    this.availableDevices = devices.filter(d => d.kind === 'videoinput');
    if (this.availableDevices.length === 0) throw new Error('Tidak ada kamera yang tersedia');

    // Prefer back/rear camera on mobile
    const back = this.availableDevices.find(d => (d.label || '').toLowerCase().includes('back') || (d.label || '').toLowerCase().includes('rear'));
    this.currentDeviceId = back ? back.deviceId : this.availableDevices[0].deviceId;

    if (this.availableDevices.length <= 1) {
      this.elements.switchBtn.style.display = 'none';
    }
  }

  bindEvents() {
    this.elements.startBtn.addEventListener('click', () => this.startScanning());
    this.elements.stopBtn.addEventListener('click', () => this.stopScanning());
    this.elements.switchBtn.addEventListener('click', () => this.switchCamera());

    // Asset action buttons
    document.getElementById('view-asset')?.addEventListener('click', (e) => {
      const assetId = e.target.dataset.assetId;
      if (assetId) window.location.href = `/dashboard/assets/${assetId}`;
    });

    // Status update buttons
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('update-status-btn') || e.target.closest('.update-status-btn')) {
        const button = e.target.classList.contains('update-status-btn') ? e.target : e.target.closest('.update-status-btn');
        const status = button.dataset.status;
        const assetId = this.currentAsset?.id;
        if (assetId && status) {
          this.updateAssetStatus(assetId, status);
        }
      }
    });

    // Checkout and Checkin drawer buttons
    document.getElementById('checkout-btn')?.addEventListener('click', () => {
      this.openCheckoutDrawer();
    });

    document.getElementById('checkin-btn')?.addEventListener('click', () => {
      this.openCheckinDrawer();
    });

    // Form submissions
    document.getElementById('checkout-form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleCheckoutSubmit();
    });

    document.getElementById('checkin-form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleCheckinSubmit();
    });
  }

  async startScanning() {
    if (this.isScanning) return;
    try {
      this.updateStatus('info', 'Memulai kamera...');
      this.elements.startBtn.disabled = true;

      // Let ZXing manage the camera stream so that .reset() fully releases it
      // Passing explicit deviceId prevents ZXing from opening a second stream
      this.isScanning = true;
      this.elements.stopBtn.disabled = false;

      await this.reader.decodeFromVideoDevice(this.currentDeviceId, this.elements.video, (result, err) => {
        if (!this.isScanning) return; // guard when stop is pressed mid-callback

        if (result) {
          this.handleScanResult(result.getText());
        } else if (err && !(err instanceof NotFoundException)) {
          console.error('Scan error:', err);
        }
      });

      this.updateStatus('success', 'Kamera aktif. Arahkan ke QR code atau barcode untuk memindai.');
    } catch (error) {
      console.error('Error starting scanner:', error);
      this.updateStatus('error', 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.');
      this.elements.startBtn.disabled = false;
      this.isScanning = false;
    }
  }

  async stopScanning() {
    if (!this.isScanning) return;
    this.isScanning = false;

    try {
      // This stops ZXing's internal decode loop and closes any stream it owns
      this.reader.reset();
    } catch (e) {
      console.warn('Reader reset warning:', e);
    }

    // Additionally, hard stop any stream bound to the <video> element (belt & suspenders)
    const video = this.elements.video;
    const stream = video.srcObject;
    if (stream && typeof stream.getTracks === 'function') {
      try { stream.getTracks().forEach(t => t.stop()); } catch (_) {}
    }
    video.srcObject = null;
    try { video.pause(); } catch (_) {}
    try { video.load(); } catch (_) {}

    this.elements.startBtn.disabled = false;
    this.elements.stopBtn.disabled = true;
    this.updateStatus('info', 'Scanner dihentikan. Klik "Mulai Scan" untuk memulai lagi.');
  }

  async switchCamera() {
    if (this.availableDevices.length <= 1) return;
    const idx = this.availableDevices.findIndex(d => d.deviceId === this.currentDeviceId);
    const nextIdx = (idx + 1) % this.availableDevices.length;
    this.currentDeviceId = this.availableDevices[nextIdx].deviceId;

    // To ensure the previous stream is fully released, stop then start
    await this.stopScanning();
    // small delay to allow hardware LED/state to settle on some devices
    setTimeout(() => this.startScanning(), 200);
  }

  async handleScanResult(code) {
    // Show scan result
    this.elements.scanEmpty.classList.add('hidden');
    this.elements.scanResult.classList.remove('hidden');
    this.elements.scannedCode.textContent = code;

    this.addToScanHistory(code);
    await this.searchAsset(code);
    this.playScanSound();
  }

  async searchAsset(code) {
    try {
      this.updateStatus('info', 'Mencari aset...');
      const response = await fetch(`/api/assets/search?code=${encodeURIComponent(code)}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (!response.ok) throw new Error('Gagal mencari aset');

      const data = await response.json();
      if (data.found) {
        this.displayAssetInfo(data.asset);
        this.updateStatus('success', 'Aset ditemukan!');
      } else {
        this.displayAssetNotFound();
        this.updateStatus('warning', 'Aset tidak ditemukan dalam sistem.');
      }
    } catch (error) {
      console.error('Error searching asset:', error);
      this.displayAssetNotFound();
      this.updateStatus('error', 'Terjadi kesalahan saat mencari aset.');
    }
  }

  displayAssetInfo(asset) {
    this.currentAsset = asset; // Store current asset for status updates
    this.elements.assetNotFound.classList.add('hidden');
    this.elements.assetInfo.classList.remove('hidden');

    document.getElementById('asset-name').textContent = asset.name || '-';
    document.getElementById('asset-code').textContent = asset.code || '-';
    document.getElementById('asset-category').textContent = asset.category?.name || '-';
    document.getElementById('asset-location').textContent = asset.location?.name || '-';

    const statusBadge = document.getElementById('asset-status');
    statusBadge.textContent = asset.status || '-';
    statusBadge.className = `badge ${asset.status_badge_color || 'badge-ghost'}`;

    document.getElementById('view-asset').dataset.assetId = asset.id;
    
    // Show/hide buttons based on asset status
    const maintenanceCheckoutContainer = document.getElementById('maintenance-checkout-button-container');
    const checkinContainer = document.getElementById('checkin-button-container');
    
    // Hide maintenance and checkout buttons if status is maintenance or checked_out
    if (asset.status === 'maintenance' || asset.status === 'checked_out') {
      maintenanceCheckoutContainer.classList.add('hidden');
    } else {
      maintenanceCheckoutContainer.classList.remove('hidden');
    }
    
    // Show check-in button only if status is checked_out
    if (asset.status === 'checked_out') {
      checkinContainer.classList.remove('hidden');
    } else {
      checkinContainer.classList.add('hidden');
    }
  }

  displayAssetNotFound() {
    this.elements.assetInfo.classList.add('hidden');
    this.elements.assetNotFound.classList.remove('hidden');
  }

  async updateAssetStatus(assetId, status) {
    try {
      const response = await fetch(`/api/assets/${assetId}/status`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        // Update the current asset status
        this.currentAsset.status = data.data.new_status;
        
        // Update status badge
        const statusBadge = document.getElementById('asset-status');
        statusBadge.textContent = data.data.new_status;
        statusBadge.className = `badge ${badgeColors[data.data.asset.status_badge_color] || 'badge-ghost'}`;
        
        // Update button visibility based on new status
        const checkinContainer = document.getElementById('checkin-button-container');
        const maintenanceCheckoutContainer = document.getElementById('maintenance-checkout-button-container');
        
        // Hide maintenance and checkout buttons if status is maintenance or checked_out
        if (data.data.new_status === 'maintenance' || data.data.new_status === 'checked_out') {
          maintenanceCheckoutContainer.classList.add('hidden');
        } else {
          maintenanceCheckoutContainer.classList.remove('hidden');
        }
        
        // Show check-in button only if status is checked_out
        if (data.data.new_status === 'checked_out') {
          checkinContainer.classList.remove('hidden');
        } else {
          checkinContainer.classList.add('hidden');
        }
        
        // Show success message
        this.updateStatus('success', data.message);
        
      } else {
        this.updateStatus('error', data.message || 'Gagal mengubah status aset');
      }
    } catch (error) {
      console.error('Error updating asset status:', error);
      this.updateStatus('error', 'Terjadi kesalahan saat mengubah status aset');
    }
  }

  addToScanHistory(code) {
    const scanEntry = { code, timestamp: new Date().toISOString(), id: Date.now() };
    this.scanHistory.unshift(scanEntry);
    if (this.scanHistory.length > 10) this.scanHistory = this.scanHistory.slice(0, 10);
    localStorage.setItem('scanHistory', JSON.stringify(this.scanHistory));
    this.updateScanHistory();
  }

  updateScanHistory() {
    if (this.scanHistory.length === 0) {
      this.elements.recentScans.innerHTML = `
        <tr>
          <td colspan="5" class="py-8 text-center text-base-content/70">Belum ada riwayat scan</td>
        </tr>`;
      return;
    }

    this.elements.recentScans.innerHTML = this.scanHistory.map(scan => {
      const date = new Date(scan.timestamp);
      const timeString = date.toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
      return `
        <tr>
          <td class="text-sm">${timeString}</td>
          <td class="font-mono text-sm">${scan.code}</td>
          <td class="text-sm">-</td>
          <td><span class="badge badge-ghost badge-sm">Unknown</span></td>
          <td>
            <button class="btn btn-ghost btn-xs" onclick="navigator.clipboard.writeText('${scan.code.replace(/'/g, "&#39;")}')">
              <i data-lucide="copy" class="w-3 h-3"></i>
            </button>
          </td>
        </tr>`;
    }).join('');

    if (window.lucide) window.lucide.createIcons();
  }

  updateStatus(type, message) {
    const alertClasses = { info: 'alert-info', success: 'alert-success', warning: 'alert-warning', error: 'alert-error' };
    const icons = { info: 'info', success: 'check-circle', warning: 'alert-triangle', error: 'x-circle' };

    this.elements.status.innerHTML = `
      <div class="alert ${alertClasses[type]}">
        <i data-lucide="${icons[type]}" class="w-4 h-4"></i>
        <span>${message}</span>
      </div>`;

    if (window.lucide) window.lucide.createIcons();
  }

  playScanSound() {
    try {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      oscillator.frequency.value = 800;
      oscillator.type = 'sine';
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.1);
    } catch (error) {
      console.log('Could not play scan sound:', error);
    }
  }

  openCheckoutDrawer() {
    if (!this.currentAsset) {
      this.updateStatus('error', 'Tidak ada asset yang dipilih');
      return;
    }

    // Set default values
    const now = new Date();
    const checkoutDate = now.toISOString().slice(0, 16);
    const dueDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16); // 7 days from now

    document.getElementById('checkout-date').value = checkoutDate;
    document.getElementById('checkout-due-date').value = dueDate;

    // Clear borrower name field
    document.getElementById('checkout-borrower').value = '';

    // Open drawer
    const drawerToggle = document.getElementById('checkout-drawer-toggle');
    if (drawerToggle) {
      drawerToggle.checked = true;
    }
  }

  openCheckinDrawer() {
    if (!this.currentAsset) {
      this.updateStatus('error', 'Tidak ada asset yang dipilih');
      return;
    }

    // Set default values
    const now = new Date();
    const checkinDate = now.toISOString().slice(0, 16);

    document.getElementById('checkin-date').value = checkinDate;

    // Open drawer
    const drawerToggle = document.getElementById('checkin-drawer-toggle');
    if (drawerToggle) {
      drawerToggle.checked = true;
    }
  }

  // loadBorrowers function removed - now using direct text input for borrower name

  async handleCheckoutSubmit() {
    // Clear previous error messages
    this.clearFormErrors('checkout-form');

    const formData = {
      asset_id: this.currentAsset.id,
      borrower_name: document.getElementById('checkout-borrower').value,
      checkout_at: document.getElementById('checkout-date').value,
      due_at: document.getElementById('checkout-due-date').value,
      condition_out: document.getElementById('checkout-condition').value,
      notes: document.getElementById('checkout-notes').value
    };

    // Client-side validation
    const validationErrors = this.validateCheckoutForm(formData);
    if (validationErrors.length > 0) {
      this.displayFormErrors('checkout-form', validationErrors);
      return;
    }

    try {
      const response = await fetch('/api/assets/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (response.ok) {
        this.updateStatus('success', result.message || 'Asset berhasil di-checkout');
        document.getElementById('checkout-drawer-toggle').checked = false;
        document.getElementById('checkout-form').reset();
        
        // Update asset info display
        if (result.data) {
          this.currentAsset = result.data;
          this.displayAssetInfo(this.currentAsset);
        }
      } else {
        // Handle validation errors from server
        if (result.errors) {
          this.displayFormErrors('checkout-form', result.errors);
        } else {
          this.updateStatus('error', result.message || 'Gagal checkout asset');
        }
      }
    } catch (error) {
      console.error('Error during checkout:', error);
      this.updateStatus('error', 'Terjadi kesalahan saat checkout asset');
    }
  }

  async handleCheckinSubmit() {
    // Clear previous error messages
    this.clearFormErrors('checkin-form');

    const formData = {
      asset_id: this.currentAsset.id,
      checkin_at: document.getElementById('checkin-date').value,
      condition_in: document.getElementById('checkin-condition').value,
      notes: document.getElementById('checkin-notes').value
    };

    // Client-side validation
    const validationErrors = this.validateCheckinForm(formData);
    if (validationErrors.length > 0) {
      this.displayFormErrors('checkin-form', validationErrors);
      return;
    }

    try {
      const response = await fetch('/api/assets/checkin', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (response.ok) {
        this.updateStatus('success', result.message || 'Asset berhasil di-checkin');
        document.getElementById('checkin-drawer-toggle').checked = false;
        document.getElementById('checkin-form').reset();
        
        // Update asset info display
        if (result.data) {
          this.currentAsset = result.data;
          this.displayAssetInfo(this.currentAsset);
        }
      } else {
        // Handle validation errors from server
        if (result.errors) {
          this.displayFormErrors('checkin-form', result.errors);
        } else {
          this.updateStatus('error', result.message || 'Gagal checkin asset');
        }
      }
    } catch (error) {
      console.error('Error during checkin:', error);
      this.updateStatus('error', 'Terjadi kesalahan saat checkin asset');
    }
  }

  _onVisibilityChange() {
    if (document.hidden) {
      // Auto-stop when tab is hidden to ensure camera is released
      this.stopScanning();
    }
  }

  destroy() {
    this.stopScanning();
    document.removeEventListener('visibilitychange', this._onVisibilityChange);
  }

  // Form validation methods
  validateCheckoutForm(formData) {
    const errors = [];

    if (!formData.borrower_name || formData.borrower_name.trim() === '') {
      errors.push({ field: 'borrower_name', message: 'Nama peminjam harus diisi' });
    }

    if (!formData.checkout_at) {
      errors.push({ field: 'checkout_at', message: 'Tanggal checkout harus diisi' });
    }

    if (!formData.due_at) {
      errors.push({ field: 'due_at', message: 'Tanggal jatuh tempo harus diisi' });
    }

    if (formData.checkout_at && formData.due_at) {
      const checkoutDate = new Date(formData.checkout_at);
      const dueDate = new Date(formData.due_at);
      if (dueDate <= checkoutDate) {
        errors.push({ field: 'due_at', message: 'Tanggal jatuh tempo harus setelah tanggal checkout' });
      }
    }

    if (!formData.condition_out) {
      errors.push({ field: 'condition_out', message: 'Kondisi asset harus dipilih' });
    }

    return errors;
  }

  validateCheckinForm(formData) {
    const errors = [];

    if (!formData.checkin_at) {
      errors.push({ field: 'checkin_at', message: 'Tanggal checkin harus diisi' });
    }

    if (!formData.condition_in) {
      errors.push({ field: 'condition_in', message: 'Kondisi asset harus dipilih' });
    }

    return errors;
  }

  clearFormErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Remove existing error messages
    const errorElements = form.querySelectorAll('.error-message');
    errorElements.forEach(el => el.remove());

    // Remove error classes from inputs
    const inputs = form.querySelectorAll('.input-error, .select-error');
    inputs.forEach(input => {
      input.classList.remove('input-error', 'select-error');
    });
  }

  displayFormErrors(formId, errors) {
    const form = document.getElementById(formId);
    if (!form) return;

    errors.forEach(error => {
      let fieldName = error.field;
      let message = error.message;

      // Handle server validation errors format
      if (typeof error === 'object' && !error.field) {
        fieldName = Object.keys(error)[0];
        message = error[fieldName][0] || error[fieldName];
      }

      // Map field names to actual input IDs
      const fieldMap = {
        'borrower_id': 'checkout-borrower',
        'checkout_at': 'checkout-date',
        'due_at': 'checkout-due-date',
        'condition_out': 'checkout-condition',
        'checkin_at': 'checkin-date',
        'condition_in': 'checkin-condition'
      };

      const inputId = fieldMap[fieldName] || fieldName;
      const input = document.getElementById(inputId);

      if (input) {
        // Add error class to input
        if (input.tagName === 'SELECT') {
          input.classList.add('select-error');
        } else {
          input.classList.add('input-error');
        }

        // Create and insert error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-error text-sm mt-1';
        errorDiv.textContent = message;

        // Insert after the input's parent (form-control)
        const formControl = input.closest('.form-control');
        if (formControl) {
          formControl.appendChild(errorDiv);
        } else {
          input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
      }
    });
  }
}

// Make class available globally
window.QRBarcodeScanner = QRBarcodeScanner;
