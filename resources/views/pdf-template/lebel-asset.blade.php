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

    @media print {
      body {
        margin: 0;
      }

      .label {
        page-break-inside: avoid;
        page-break-after: always;
      }
    }

    .label {
      width: 5cm;
      /* lebar 5 cm */
      height: 3cm;
      /* tinggi 3 cm */
      border: 0.5px solid #888;
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
      width: auto;
      height: auto;
      display: inline-block;
    }

    .qr-box canvas {
      image-rendering: pixelated;
    }

    .bar-box {
      width: auto;
      height: 0.4cm;
      display: inline-block;
    }
  </style>
</head>

<body>
  @foreach ($assets as $asset)
  <div class="label">
    <table>
      <!-- HEADER -->
      <tr>
        <td colspan="2" class="center">
          <div class="bold" style="font-size:6pt">PT. GAYA SUKSES MANDIRI KASEINDO</div>
          <div class="muted">Jl. Margomulyo 63A Surabaya</div>
          <div class="muted">Telp. 08123456789</div>
          <div class="divider"></div>
        </td>
      </tr>

      <!-- ISI: kolom kiri teks, kolom kanan kode -->
      <tr>
        <!-- KIRI: data aset (pakai tabel supaya rapi) -->
        <td style="width:62%; vertical-align: top; padding-right:0.1cm;">
          <table>
            <tr>
              <td class="muted" style="white-space:nowrap;">Model:</td>
              <td class="muted" style="padding-left:0.08cm;">{{ $asset->model ?? '-' }}</td>
            </tr>
            <tr>
              <td class="muted">Merk:</td>
              <td class="muted" style="padding-left:0.08cm;">{{ $asset->brand ?? '-' }}</td>
            </tr>
            <tr>
              <td class="muted">Category:</td>
              <td class="muted" style="padding-left:0.08cm;">{{ $asset->category->name ?? '-' }}</td>
            </tr>
            <tr>
              <td class="muted">Purchase Date:</td>
              <td class="muted" style="padding-left:0.08cm;">
                @if($asset->purchase_date)
                  {{ $asset->purchase_date->format('d/m/Y') }}
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td class="muted">S/N:</td>
              <td class="muted" style="padding-left:0.08cm;">{{ $asset->serial_number ?? '-' }}</td>
            </tr>
          </table>
        </td>

        <!-- KANAN: QR dan barcode -->
        <td style="width:38%; vertical-align: top; text-align: center;">
          <!-- Ganti DIV di bawah dengan IMG jika sudah ada file QR -->
          <!-- Contoh: <img src="qr.png" style="width:1.8cm;height:1.8cm;" /> -->
          <div class="qr-box"></div>
          <!-- Contoh: <img src="barcode.png" style="width:1.8cm;height:0.65cm;" /> -->
          <div class="bar-box"></div>
          <div style="font-size: 3pt;">{{ $asset->tag_code }}</div>
        </td>
      </tr>
    </table>
  </div>
  @endforeach
</body>

</html>