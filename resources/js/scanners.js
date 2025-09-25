import { BrowserMultiFormatReader, NotFoundException } from "@zxing/library";

class QRBarcodeScanner {
    constructor() {
        this.videoEl = document.querySelector("#scanner-video");
        this.stream = null;
        this.availableDevices = [];
        this.currentDeviceId = null;
        this.isSwitchCamera = true;
        this.isScanning = false;
        this.scanHistory = JSON.parse(
            localStorage.getItem("scanHistory") || "[]"
        );

        this.BrowserMultiFormatReader = new BrowserMultiFormatReader();

        this.syncHistory();
        window.addEventListener("scanner:start", () => this.start());
        window.addEventListener("scanner:stop", () => this.stop());
        window.addEventListener("scanner:switch", () => this.switchCamera());
        window.addEventListener("scanner:history", () => this.syncHistory());
    }

    async start() {
        this.dispatchUpdateCameraAttributes({
            cameraStatus: "preparing",
            ...this.setAlert(
                "warning",
                "Menyiapkan Kamera",
                "Mohon izinkan akses kamera."
            ),
        });

        this.syncHistory();

        try {
            if (!this.currentDeviceId) {
                await this.getAvailableDevices();
            }

            this.isScanning = true;
            // await this.startStream(this.currentDeviceId);

            await this.runningScanner();

            this.dispatchUpdateCameraAttributes({
                cameraStatus: "on",
                isSwitchCamera: this.isSwitchCamera,
                ...this.setAlert(
                    "success",
                    "Kamera Aktif",
                    "Arahkan ke QR code atau barcode untuk memindai."
                ),
            });
        } catch (e) {
            this.isScanning = false;
            this.dispatchUpdateCameraAttributes({
                ...this.setAlert(
                    "error",
                    "Gagal Mengakses Kamera",
                    e.message ||
                        "Gagal mengakses kamera. Pastikan HTTPS & izin kamera diberikan."
                ),
            });
        }
    }

    async stop() {
        if (!this.isScanning) return;
        this.isScanning = false;

        try {
            // This stops ZXing's internal decode loop and closes any stream it owns
            this.BrowserMultiFormatReader.reset();
        } catch (e) {
            console.warn("Reader reset warning:", e);
        }

        if (this.stream) {
            this.stream.getTracks().forEach((t) => t.stop());
            this.stream = null;
        }
        if (this.videoEl) {
            this.videoEl.srcObject = null;
            try {
                this.videoEl.pause();
            } catch (_) {}
            try {
                this.videoEl.load();
            } catch (_) {}
        }
        this.dispatchUpdateCameraAttributes({
            cameraStatus: "off",
            ...this.setAlert(
                "info",
                "Aktifkan Kamera",
                'Klik "Mulai Scan" untuk mengaktifkan kamera.'
            ),
        });
    }

    async getAvailableDevices() {
        // MINTA IZIN dulu agar label device terisi di sebagian browser
        try {
            const temp = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false,
            });
            temp.getTracks().forEach((t) => t.stop());
        } catch (e) {
            throw new Error("Permission preflight gagal/ditolak:", e);
        }

        const devices = await navigator.mediaDevices.enumerateDevices();
        this.availableDevices = devices.filter((d) => d.kind === "videoinput");
        if (this.availableDevices.length === 0)
            throw new Error("Tidak ada kamera yang tersedia");

        // Prefer kamera belakang di mobile
        const back = this.availableDevices.find(
            (d) =>
                (d.label || "").toLowerCase().includes("back") ||
                (d.label || "").toLowerCase().includes("rear")
        );

        this.isSwitchCamera = this.availableDevices.length > 1;
        this.currentDeviceId = back
            ? back.deviceId
            : this.availableDevices[0].deviceId;
    }

    async switchCamera() {
        if (this.availableDevices.length <= 1) return;
        const idx = this.availableDevices.findIndex(
            (d) => d.deviceId === this.currentDeviceId
        );
        const next =
            this.availableDevices[(idx + 1) % this.availableDevices.length];
        this.currentDeviceId = next.deviceId;
        await this.start();
    }

    async startStream(deviceId) {
        const constraints = {
            video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                // Sedikit tuning untuk kamera belakang di ponsel
                facingMode: deviceId ? undefined : { ideal: "environment" },
                width: { ideal: 1280 },
                height: { ideal: 720 },
            },
            audio: false,
        };

        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
        this.videoEl.srcObject = this.stream;
        await this.videoEl.play();
    }

    async runningScanner() {
        if (!this.isScanning) return;

        this.BrowserMultiFormatReader.decodeFromVideoDevice(
            this.currentDeviceId,
            this.videoEl,
            (result, _) => {
                if (!this.isScanning) return; // guard when stop is pressed mid-callback

                if (result) {
                    this.handleScanResult(result.getText());
                }
            }
        );
    }

    async handleScanResult(text) {
        this.dispatchUpdateResultAttributes({
            scanStatus: "loading",
        });
        try {
            const assetResponse = await fetch(
                `/api/assets/search?code=${encodeURIComponent(text)}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                }
            );

            if (!assetResponse.ok) throw new Error("Gagal mencari aset");
            const assetData = await assetResponse.json();
            this.dispatchUpdateResultAttributes({
                scanStatus: "success",
                tagScanned: text,
                assetScanned: assetData.asset ?? null,
            });
            this.addToScanHistory(text, assetData.asset ?? null);
            this.dispatchUpdateHistoryAttributes({ rows: this.scanHistory });
            this.playScanSound();
        } catch (error) {
            console.error("Error searching asset:", error);
        }
    }

    addToScanHistory(tag, payload) {
        const scanEntry = {
            time: new Intl.DateTimeFormat("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            }).format(Date.now()),
            tag: tag,
            id: payload?.id ?? null,
            asset_name: payload?.name ?? "-",
            category: payload?.category.name ?? "-",
            location: payload?.location.name ?? "-",
            status: payload?.status ?? "-",
        };
        this.scanHistory = JSON.parse(
            localStorage.getItem("scanHistory") || "[]"
        );
        this.scanHistory.unshift(scanEntry);
        if (this.scanHistory.length > 10)
            this.scanHistory = this.scanHistory.slice(0, 10);
        localStorage.setItem("scanHistory", JSON.stringify(this.scanHistory));
    }

    playScanSound() {
        try {
            const audioContext = new (window.AudioContext ||
                window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.frequency.value = 800;
            oscillator.type = "sine";
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(
                0.01,
                audioContext.currentTime + 0.1
            );
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            console.log("Could not play scan sound:", error);
        }
    }

    syncHistory() {
        this.dispatchUpdateHistoryAttributes({ rows: this.scanHistory });
    }

    setAlert(type, title, message) {
        return { alert: { type, title, message } };
    }

    dispatchUpdateCameraAttributes(payload) {
        Livewire.dispatch("scanCamera:updateAttributes", { payload });
    }

    dispatchUpdateResultAttributes(payload) {
        Livewire.dispatch("scanResult:updateAttributes", { payload });
    }

    dispatchUpdateHistoryAttributes(payload) {
        Livewire.dispatch("scanHistory:updateAttributes", { payload });
    }
}

window.QRBarcodeScanner = QRBarcodeScanner;
