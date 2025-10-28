<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Label Aset 3x5cm</title>
  <style>
    /* Ukuran fisik label */
    @page {
      size: 5cm 3cm;
      margin: 0;
    }

    html,
    body {
      margin: 0;
      padding: 0;
    }

    .label {
      width: 5cm;
      /* lebar 5 cm */
      height: 3cm;
      /* tinggi 3 cm */
      border: 1px solid #000;
      /* garis tepi label */
      box-sizing: border-box;
      padding: 0.15cm;
      /* ruang dalam label */
      font-family: Arial, Helvetica, sans-serif;
      line-height: 1.15;
      font-size: 8pt;
      /* ukuran teks kecil agar muat */
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    .center {
      text-align: center;
    }

    .bold {
      font-weight: 700;
    }

    .muted {
      font-size: 5pt;
    }

    .divider {
      border-top: 1px solid #000;
      height: 0;
      margin: 0.06cm 0 0.08cm;
    }

    /* Placeholder kotak QR & barcode bila belum ada gambar */
    .qr-box {
      width: 1.8cm;
      height: 1.8cm;
      border: 1px solid #000;
      display: inline-block;
    }

    .bar-box {
      width: 1.8cm;
      height: 0.4cm;
      border: 1px solid #000;
      display: inline-block;
      overflow: hidden;
    }
  </style>
</head>

<body>
  <div class="label">
    <table>
      <!-- HEADER -->
      <tr>
        <td colspan="2">
          <div class="bold" style="font-size:5pt">PT. GAYA SUKSES MANDIRI KASEINDO</div>
        </td>
      </tr>

      <!-- ISI: kolom kiri teks, kolom kanan kode -->
      <tr>
        <!-- KIRI: data aset (pakai tabel supaya rapi) -->
        <td style="width:62%; vertical-align: top; padding-right:0.05cm;">
          <table>
            <tr>
              <td class="muted" style="white-space:nowrap;">Model:</td>
              <td class="muted" style="padding-left:0.05cm;">Thinkpad X13</td>
            </tr>
            <tr>
              <td class="muted">Merk:</td>
              <td class="muted" style="padding-left:0.05cm;">Lenovo</td>
            </tr>
            <tr>
              <td class="muted">Kategori:</td>
              <td class="muted" style="padding-left:0.05cm;">Laptop</td>
            </tr>
            <tr>
              <td class="muted">Tgl. Beli:</td>
              <td class="muted" style="padding-left:0.05cm;">08/09/2025</td>
            </tr>
            <tr>
              <td class="muted">S/N:</td>
              <td class="muted" style="padding-left:0.05cm;">12345</td>
            </tr>
          </table>
        </td>

        <!-- KANAN: QR dan barcode -->
        <td style="width:38%; vertical-align: top; text-align: center;">
          <!-- Ganti DIV di bawah dengan IMG jika sudah ada file QR -->
          <div class="qr-box"></div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="bar-box"></div>
        </td>
        <td>
          <div style="font-size: 5pt; text-align: right;">01K747S86C53</div>
        </td>
      </tr>
    </table>
  </div>
</body>

</html>