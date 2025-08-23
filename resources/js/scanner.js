import { BrowserMultiFormatReader, NotFoundException } from '@zxing/library';

class QRBarcodeScanner {
    constructor() {
        this.reader = new BrowserMultiFormatReader();
        this.currentStream = null;
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
        
        this.init();
    }
    
    async init() {
        try {
            await this.getAvailableDevices();
            this.bindEvents();
            this.updateScanHistory();
            this.updateStatus('info', 'Scanner siap digunakan. Klik "Mulai Scan" untuk memulai.');
        } catch (error) {
            console.error('Error initializing scanner:', error);
            this.updateStatus('error', 'Gagal menginisialisasi scanner. Pastikan browser mendukung kamera.');
        }
    }
    
    async getAvailableDevices() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            this.availableDevices = devices.filter(device => device.kind === 'videoinput');
            
            if (this.availableDevices.length === 0) {
                throw new Error('Tidak ada kamera yang tersedia');
            }
            
            // Prefer back camera if available
            const backCamera = this.availableDevices.find(device => 
                device.label.toLowerCase().includes('back') || 
                device.label.toLowerCase().includes('rear')
            );
            
            this.currentDeviceId = backCamera ? backCamera.deviceId : this.availableDevices[0].deviceId;
            
            // Hide switch button if only one camera
            if (this.availableDevices.length <= 1) {
                this.elements.switchBtn.style.display = 'none';
            }
        } catch (error) {
            console.error('Error getting devices:', error);
            throw error;
        }
    }
    
    bindEvents() {
        this.elements.startBtn.addEventListener('click', () => this.startScanning());
        this.elements.stopBtn.addEventListener('click', () => this.stopScanning());
        this.elements.switchBtn.addEventListener('click', () => this.switchCamera());
        
        // Asset action buttons
        document.getElementById('view-asset')?.addEventListener('click', (e) => {
            const assetId = e.target.dataset.assetId;
            if (assetId) {
                window.location.href = `/assets/${assetId}`;
            }
        });
        
        document.getElementById('edit-asset')?.addEventListener('click', (e) => {
            const assetId = e.target.dataset.assetId;
            if (assetId) {
                window.location.href = `/assets/${assetId}/edit`;
            }
        });
        
        document.getElementById('create-asset')?.addEventListener('click', () => {
            const scannedCode = this.elements.scannedCode.textContent;
            if (scannedCode) {
                window.location.href = `/assets/create?code=${encodeURIComponent(scannedCode)}`;
            }
        });
    }
    
    async startScanning() {
        try {
            this.updateStatus('info', 'Memulai kamera...');
            this.elements.startBtn.disabled = true;
            
            // Request camera permission and start stream
            const constraints = {
                video: {
                    deviceId: this.currentDeviceId ? { exact: this.currentDeviceId } : undefined,
                    facingMode: this.currentDeviceId ? undefined : { ideal: 'environment' },
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            };
            
            this.currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.video.srcObject = this.currentStream;
            
            // Wait for video to be ready
            await new Promise((resolve) => {
                this.elements.video.onloadedmetadata = resolve;
            });
            
            this.isScanning = true;
            this.elements.stopBtn.disabled = false;
            this.elements.startBtn.disabled = true;
            
            this.updateStatus('success', 'Kamera aktif. Arahkan ke QR code atau barcode untuk memindai.');
            
            // Start continuous scanning
            this.scanContinuously();
            
        } catch (error) {
            console.error('Error starting scanner:', error);
            this.updateStatus('error', 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.');
            this.elements.startBtn.disabled = false;
        }
    }
    
    stopScanning() {
        this.isScanning = false;
        
        if (this.currentStream) {
            this.currentStream.getTracks().forEach(track => track.stop());
            this.currentStream = null;
        }
        
        this.elements.video.srcObject = null;
        this.elements.startBtn.disabled = false;
        this.elements.stopBtn.disabled = true;
        
        this.updateStatus('info', 'Scanner dihentikan. Klik "Mulai Scan" untuk memulai lagi.');
    }
    
    async switchCamera() {
        if (this.availableDevices.length <= 1) return;
        
        const currentIndex = this.availableDevices.findIndex(device => device.deviceId === this.currentDeviceId);
        const nextIndex = (currentIndex + 1) % this.availableDevices.length;
        this.currentDeviceId = this.availableDevices[nextIndex].deviceId;
        
        if (this.isScanning) {
            this.stopScanning();
            setTimeout(() => this.startScanning(), 500);
        }
    }
    
    async scanContinuously() {
        if (!this.isScanning) return;
        
        try {
            const result = await this.reader.decodeOnceFromVideoDevice(undefined, this.elements.video.id);
            
            if (result) {
                this.handleScanResult(result.getText());
                // Continue scanning after a short delay
                setTimeout(() => this.scanContinuously(), 2000);
            } else {
                // Continue scanning immediately if no result
                requestAnimationFrame(() => this.scanContinuously());
            }
        } catch (error) {
            if (error instanceof NotFoundException) {
                // No QR code found, continue scanning
                requestAnimationFrame(() => this.scanContinuously());
            } else {
                console.error('Scan error:', error);
                // Continue scanning even on error
                setTimeout(() => this.scanContinuously(), 1000);
            }
        }
    }
    
    async handleScanResult(code) {
        console.log('Scanned code:', code);
        
        // Show scan result
        this.elements.scanEmpty.classList.add('hidden');
        this.elements.scanResult.classList.remove('hidden');
        this.elements.scannedCode.textContent = code;
        
        // Add to scan history
        this.addToScanHistory(code);
        
        // Search for asset
        await this.searchAsset(code);
        
        // Play scan sound (optional)
        this.playScanSound();
    }
    
    async searchAsset(code) {
        try {
            this.updateStatus('info', 'Mencari aset...');
            
            // Make API call to search for asset
            const response = await fetch(`/api/assets/search?code=${encodeURIComponent(code)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.found) {
                    this.displayAssetInfo(data.asset);
                    this.updateStatus('success', 'Aset ditemukan!');
                } else {
                    this.displayAssetNotFound();
                    this.updateStatus('warning', 'Aset tidak ditemukan dalam sistem.');
                }
            } else {
                throw new Error('Gagal mencari aset');
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
        
        // Set asset ID for action buttons
        document.getElementById('view-asset').dataset.assetId = asset.id;
        document.getElementById('edit-asset').dataset.assetId = asset.id;
    }
    
    displayAssetNotFound() {
        this.elements.assetInfo.classList.add('hidden');
        this.elements.assetNotFound.classList.remove('hidden');
    }
    
    addToScanHistory(code) {
        const scanEntry = {
            code: code,
            timestamp: new Date().toISOString(),
            id: Date.now()
        };
        
        this.scanHistory.unshift(scanEntry);
        
        // Keep only last 10 scans
        if (this.scanHistory.length > 10) {
            this.scanHistory = this.scanHistory.slice(0, 10);
        }
        
        localStorage.setItem('scanHistory', JSON.stringify(this.scanHistory));
        this.updateScanHistory();
    }
    
    updateScanHistory() {
        if (this.scanHistory.length === 0) {
            this.elements.recentScans.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-base-content/70 py-8">
                        Belum ada riwayat scan
                    </td>
                </tr>
            `;
            return;
        }
        
        this.elements.recentScans.innerHTML = this.scanHistory.map(scan => {
            const date = new Date(scan.timestamp);
            const timeString = date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            return `
                <tr>
                    <td class="text-sm">${timeString}</td>
                    <td class="font-mono text-sm">${scan.code}</td>
                    <td class="text-sm">-</td>
                    <td><span class="badge badge-ghost badge-sm">Unknown</span></td>
                    <td>
                        <button class="btn btn-ghost btn-xs" onclick="navigator.clipboard.writeText('${scan.code}')">
                            <i data-lucide="copy" class="w-3 h-3"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
        
        // Re-initialize Lucide icons for new elements
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
    
    updateStatus(type, message) {
        const alertClasses = {
            info: 'alert-info',
            success: 'alert-success',
            warning: 'alert-warning',
            error: 'alert-error'
        };
        
        const icons = {
            info: 'info',
            success: 'check-circle',
            warning: 'alert-triangle',
            error: 'x-circle'
        };
        
        this.elements.status.innerHTML = `
            <div class="alert ${alertClasses[type]}">
                <i data-lucide="${icons[type]}" class="w-4 h-4"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Re-initialize Lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
    
    playScanSound() {
        // Create a simple beep sound
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
            // Ignore audio errors
            console.log('Could not play scan sound:', error);
        }
    }
    
    destroy() {
        this.stopScanning();
        // Remove event listeners if needed
    }
}

// Make class available globally
window.QRBarcodeScanner = QRBarcodeScanner;