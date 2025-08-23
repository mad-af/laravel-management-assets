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
      if (assetId) window.location.href = `/assets/${assetId}`;
    });
    document.getElementById('edit-asset')?.addEventListener('click', (e) => {
      const assetId = e.target.dataset.assetId;
      if (assetId) window.location.href = `/assets/${assetId}/edit`;
    });
    document.getElementById('create-asset')?.addEventListener('click', () => {
      const scannedCode = this.elements.scannedCode.textContent;
      if (scannedCode) window.location.href = `/assets/create?code=${encodeURIComponent(scannedCode)}`;
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
    this.elements.assetNotFound.classList.add('hidden');
    this.elements.assetInfo.classList.remove('hidden');

    document.getElementById('asset-name').textContent = asset.name || '-';
    document.getElementById('asset-code').textContent = asset.code || '-';
    document.getElementById('asset-category').textContent = asset.category?.name || '-';
    document.getElementById('asset-location').textContent = asset.location?.name || '-';

    const statusBadge = document.getElementById('asset-status');
    statusBadge.textContent = asset.status || '-';
    statusBadge.className = `badge ${
      asset.status === 'available' ? 'badge-success' :
      asset.status === 'in_use' ? 'badge-warning' :
      asset.status === 'maintenance' ? 'badge-error' :
      'badge-neutral'
    }`;

    document.getElementById('view-asset').dataset.assetId = asset.id;
    document.getElementById('edit-asset').dataset.assetId = asset.id;
  }

  displayAssetNotFound() {
    this.elements.assetInfo.classList.add('hidden');
    this.elements.assetNotFound.classList.remove('hidden');
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
}

// Make class available globally
window.QRBarcodeScanner = QRBarcodeScanner;
