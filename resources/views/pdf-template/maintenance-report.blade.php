<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Work Order</title>
  <style>
    /* KOMPATIBEL DOMPDF: hindari flex/grid/absolute untuk layout; gunakan fixed khusus footer */
    @page { size: A5 portrait; margin: 10mm 10mm 22mm 10mm; }
    /* ↑ tambah margin-bottom untuk ruang footer tetap */

    body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: #000; margin: 0; }
    table { width: 100%; border-collapse: collapse; }

    /* Utilitas sederhana */
    .small { font-size: 10px; }
    .xs { font-size: 9px; }
    .uppercase { text-transform: uppercase; }
    .bold { font-weight: 700; }
    .semibold { font-weight: 600; }
    .center { text-align: center; }
    .right { text-align: right; }
    .mb-4 { margin-bottom: 12px; }
    .mb-2 { margin-bottom: 6px; }
    .mb-1 { margin-bottom: 4px; }
    .p-2 { padding: 4px; }
    .p-4 { padding: 8px; }
    .border { border: 1px solid #000; }
    .bg-gray { background: #f3f4f6; }
    .italic { font-style: italic; color: #4b5563; }
    .lh-tight { line-height: 1.2; }
    .title { font-size: 24px; font-weight: 800; letter-spacing: 1px; }

    /* Footer fixed ala DOMPDF: akan muncul di setiap halaman, lengkapi ruang via @page */
    .footer { position: fixed; left: 0; right: 0; bottom: 10mm; }
  </style>
</head>
<body>

  <!-- Header -->
  <table class="mb-4">
    <tr>
      <td style="width:70%; vertical-align: top;">
        <div class="mb-2">
          <div class="uppercase bold" style="font-size:16px; line-height:1.2;">PT. GAYA SUKSES MANDIRI KASEINDO</div>
        </div>
        <div class="small lh-tight">
          <div class="semibold">Surabaya Office | Factory</div>
          <div>Jl. Raya Margomulyo 63A, Surabaya</div>
          <div>Tel: +62 31 7499 050 · surabaya@safeway.co.id</div>
        </div>
        <div class="small lh-tight">
          <div class="semibold">Jakarta Office</div>
          <div>Kompleks Artha Gading Blok F No. 20, Jakarta</div>
          <div>Tel: +62 21 4585 0806 · jakarta@safeway.co.id</div>
        </div>
        <div class="small lh-tight">
          <div class="semibold">Semarang Office | Factory</div>
          <div>Kawasan Industri Candi, Blok XIC No. 2C, Semarang</div>
          <div>Tel: +62 24 7627 600 · semarang@safeway.co.id</div>
        </div>
      </td>
      <td class="right" style="width:30%; vertical-align: top;">
        <div class="uppercase title bold">WORK</div>
        <div class="uppercase title bold">ORDER</div>
      </td>
    </tr>
  </table>

  <!-- WO note & number -->
  <table class="mb-4 small">
    <tr>
      <td colspan="2" style="padding-bottom:6px;">
        Nomor berikut harus dicantumkan pada semua korespondensi, dokumen pengiriman, dan faktur yang terkait:
      </td>
    </tr>
    <tr>
      <td style="width:50%; vertical-align: top;">
        <span class="bold">Nomor W.O.:</span>
        <span class="uppercase">WO-2025-001</span>
      </td>
      <td style="width:50%; vertical-align: top;">
        <table>
          <tr>
            <td style="width:55%;"><span class="semibold">Tanggal W.O.:</span></td>
            <td>01 September 2025</td>
          </tr>
          <tr>
            <td><span class="semibold">Estimasi Tanggal Selesai:</span></td>
            <td>19 Januari 2025</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- To / PIC -->
  <table class="mb-4 small">
    <tr>
      <td style="width:50%; vertical-align: top;">
        <div class="mb-1 semibold">Kepada:</div>
        <div>PT Bengkel Jaya</div>
        <div>Jl. Industri No. 123 Surabaya</div>
        <div>L 1234 AB</div>
        <div>Toyota - Avanza</div>
      </td>
      <td style="width:50%; vertical-align: top;">
        <div class="mb-1 semibold">Kontak PIC Perusahaan:</div>
        <div>Budi Santoso</div>
        <div>08123456789</div>
      </td>
    </tr>
  </table>

  <!-- Service Instructions Table -->
  <table class="mb-4 border small">
    <thead>
      <tr class="bg-gray">
        <th class="p-2 border center" style="width:30px;">No.</th>
        <th class="p-2 border center">Instruksi Pekerjaan</th>
        <th class="p-2 border center" style="width:100px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="p-2 border center">1</td>
        <td class="p-2 border">Ganti oli mesin</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
      <tr>
        <td class="p-2 border center">2</td>
        <td class="p-2 border">Cek rem dan kampas</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
      <tr>
        <td class="p-2 border center">3</td>
        <td class="p-2 border">Service AC dan isi freon</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
      <tr>
        <td class="p-2 border center">3</td>
        <td class="p-2 border">Service AC dan isi freon</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
      <tr>
        <td class="p-2 border center">3</td>
        <td class="p-2 border">Service AC dan isi freon</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
      <tr>
        <td class="p-2 border center">3</td>
        <td class="p-2 border">Service AC dan isi freon</td>
        <td class="p-2 border">&nbsp;</td>
      </tr>
    </tbody>
  </table>

  <!-- Notes -->
  <table class="mb-4 small">
    <tr>
      <td class="semibold" style="padding-bottom:4px;">Catatan:</td>
    </tr>
    <tr>
      <td class="p-2 border" style="height:60px; vertical-align: top;">
        <span class="italic">Tuliskan catatan tambahan atau instruksi khusus di sini...</span>
      </td>
    </tr>
  </table>

  <!-- Signatures -->
  <table class="mb-4 small" style="table-layout: fixed;">
    <tr>
      <td class="border" style="width:33%; vertical-align: top;">
        <table style="width:100%; height:60px; border-collapse: collapse;">
          <tr>
            <td class="semibold center" style="padding:4px; vertical-align: top;">Dibuat Oleh:</td>
          </tr>
          <tr>
            <td class="center" style="padding:4px; height:40px; vertical-align: bottom;">( Staff GA )</td>
          </tr>
        </table>
      </td>
      <td class="border" style="width:33%; vertical-align: top;">
        <table style="width:100%; height:60px; border-collapse: collapse;">
          <tr>
            <td class="semibold center" style="padding:4px; vertical-align: top;">Disetujui Oleh:</td>
          </tr>
          <tr>
            <td class="center" style="padding:4px; height:40px; vertical-align: bottom;">( Kepala HR &amp; GA )</td>
          </tr>
        </table>
      </td>
      <td class="border" style="width:34%; vertical-align: top;">
        <table style="width:100%; height:60px; border-collapse: collapse;">
          <tr>
            <td class="semibold center" style="padding:4px; vertical-align: top;">Diterima Oleh:</td>
          </tr>
          <tr>
            <td class="center" style="padding:4px; height:40px; vertical-align: bottom;">( _________________ )</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Footer fixed (akan selalu di bagian bawah halaman) -->
  <div class="footer">
    <table class="xs">
      <tr>
        <td>
          Diterbitkan oleh <span class="bold">PT. Gaya Sukses Mandiri Kaseindo</span> · Dokumen Rahasia – Untuk penggunaan internal dan klien.
        </td>
      </tr>
    </table>
  </div>

</body>
</html>