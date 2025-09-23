import { BrowserMultiFormatReader, NotFoundException } from "@zxing/library";

class QRBarcodeScanner {
    constructor() {
        this.videoEl = document.querySelector("#scanner-video");
        this.stream = null;
        this.availableDevices = [];
        this.currentDeviceId = null;
        this.isSwitchCamera = true;

        window.addEventListener("scanner:start", () => this.start());
        window.addEventListener("scanner:stop", () => this.stop());
        window.addEventListener("scanner:switch", () => this.switchCamera());
    }

    async start() {
        this.dispatchUpdateAttributes({
            ...this.setAlert(
                "warning",
                "Menyiapkan Kamera",
                "Mohon izinkan akses kamera."
            ),
        });
        try {
            if (!this.currentDeviceId) {
                await this.getAvailableDevices();
            }

            await this.startStream(this.currentDeviceId);

            this.dispatchUpdateAttributes({
                isCameraActive: true,
                isSwitchCamera: this.isSwitchCamera,
                ...this.setAlert(
                    "success",
                    "Kamera Aktif",
                    "Arahkan ke QR code atau barcode untuk memindai."
                ),
            });
        } catch (e) {
            console.error(e);
            this.dispatchUpdateAttributes({
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
        if (this.stream) {
            this.stream.getTracks().forEach((t) => t.stop());
            this.stream = null;
        }
        if (this.videoEl) {
            this.videoEl.srcObject = null;
        }
        this.dispatchUpdateAttributes({
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

    setAlert(type, title, message) {
        return { alert: { type, title, message } };
    }

    dispatchUpdateAttributes(payload) {
        Livewire.dispatch("scanner:updateAttributes", { payload });
    }
}

window.QRBarcodeScanner = QRBarcodeScanner;
