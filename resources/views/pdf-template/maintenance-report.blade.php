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
    .mb-3 { margin-bottom: 12px; }
    .mb-3 { margin-bottom: 8px; }
    .mb-2 { margin-bottom: 6px; }
    .mb-1 { margin-bottom: 4px; }
    .mb-0.5 { margin-bottom: 2px; }
    .p-1 { padding: 2px; }
    .p-2 { padding: 4px; }
    .p-4 { padding: 8px; }
    .border { border: 1px solid #000; }
    .bg-gray { background: #f3f4f6; }
    .italic { font-style: italic; color: #4b5563; }
    .lh-tight { line-height: 1.2; }
    .title { font-size: 24px; font-weight: 800; letter-spacing: 1px; }

    /* Footer fixed ala DOMPDF: akan muncul di setiap halaman, lengkapi ruang via @page */
    .footer { position: fixed; left: 0; right: 0; bottom: 0mm; }
  </style>
</head>
<body>

  <!-- Header -->
  <table class="mb-3">
    <tr>
      <td style="width:70%; vertical-align: top;">
        {{-- <div class="mb-2">
          <div class="uppercase bold" style="font-size:16px; line-height:1.2; text-transform: uppercase;">{{ $data->company_name }}</div>
        </div>
        <div class="mb-0.5 small lh-tight">
          <div class="semibold">Surabaya Office | Factory</div>
          <div>Jl. Raya Margomulyo 63A, Surabaya</div>
          <div>Tel: +62 31 7499 050 · surabaya@safeway.co.id</div>
        </div>
        <div class="mb-0.5 small lh-tight">
          <div class="semibold">Jakarta Office</div>
          <div>Kompleks Artha Gading Blok F No. 20, Jakarta</div>
          <div>Tel: +62 21 4585 0806 · jakarta@safeway.co.id</div>
        </div>
        <div class="mb-0.5 small lh-tight">
          <div class="semibold">Semarang Office | Factory</div>
          <div>Kawasan Industri Candi, Blok XIC No. 2C, Semarang</div>
          <div>Tel: +62 24 7627 600 · semarang@safeway.co.id</div>
        </div> --}}
      </td>
      <td class="right" style="width:30%; vertical-align: top;">
        <div class="uppercase title bold">WORK</div>
        <div class="uppercase title bold">ORDER</div>
      </td>
    </tr>
  </table>

  <!-- WO note & number -->
  <table class="mb-2 small">
    <tr>
      <td colspan="2" style="padding-bottom:6px;">
        Nomor berikut harus dicantumkan pada semua korespondensi, dokumen pengiriman, dan faktur yang terkait:
      </td>
    </tr>
    <tr>
      <td style="width:50%; vertical-align: top;">
        <span class="bold">Nomor W.O.:</span>
        <span class="uppercase">{{ $data->work_order_no ?? 'WO-000000' }}</span>
      </td>
      <td style="width:50%; vertical-align: top;">
        <table>
          <tr>
            <span class="bold">Tanggal W.O.:</span>
            <span style="margin-left: 3px;">{{ $data->start_date ? $data->start_date->format('d F Y') : 'Belum ditentukan' }}</span>
            {{-- <td style="width:55%;"><span class="semibold">Tanggal W.O.:</span></td>
            <td>{{ $data->start_date ? $data->start_date->format('d F Y') : 'Belum ditentukan' }}</td> --}}
          </tr>
          <tr>
            <span class="bold">Estimasi Tanggal Selesai:</span>
            <span style="margin-left: 3px;">{{ $data->estimation_end_date ? $data->estimation_end_date->format('d F Y') : 'Belum ditentukan' }}</span>
            {{-- <td><span class="semibold">Estimasi Tanggal Selesai:</span></td>
            <td>{{ $data->estimation_end_date ? $data->estimation_end_date->format('d F Y') : 'Belum ditentukan' }}</td> --}}
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Vendor / Asset / PIC (dipisah) -->
  <table class="mb-3 small">
    <tr>
      <td style="width:50%; vertical-align: top;">
        <!-- Vendor -->
        <table class="">
          <tr>
            <td class="semibold">Kepada:</td>
          </tr>
          <tr>
            <td class="lh-tight">
              <div>{{ $data->vendor_name ?? 'Tidak tersedia' }}</div>
              <div>{{ $data->workshop_address ?? '' }}</div>
            </td>
          </tr>
        </table>
        <!-- Asset -->
        <table class="">
          <tr>
            <td class="semibold">Informasi Aset:</td>
          </tr>
          <tr>
            <td class="lh-tight">
              <div>Nama: {{ $data->asset->name ?? 'Tidak tersedia' }}</div>
              <div>Jenis: {{ $data->asset->brand ?? 'Tidak tersedia' }} - {{ $data->asset->type ?? 'Tidak tersedia' }}</div>
              <div>Kode Tag: {{ $data->asset->tag_code ?? 'Tidak tersedia' }}</div>
            </td>
          </tr>
        </table>
      </td>
      <td style="width:50%; vertical-align: top;">
        <!-- PIC -->
        <table class="">
          <tr>
            <td class="semibold">Kontak PIC Perusahaan:</td>
          </tr>
          <tr>
            <td class="lh-tight">
              <div>Nama: {{ $data->employee->name ?? 'Tidak tersedia' }}</div>
              <div>Telepon: {{ $data->employee->phone ?? 'Tidak tersedia' }}</div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>


  <!-- Service Instructions Table -->
  <table class="mb-3 border small">
    <thead>
      <tr class="bg-gray">
        <th class="p-1 border center" style="width:30px;">No.</th>
        <th class="p-1 border center">Instruksi Pekerjaan</th>
        <th class="p-1 border center" style="width:100px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @if(isset($data->maintenance->service_tasks) && is_array($data->maintenance->service_tasks) && count($data->maintenance->service_tasks) > 0)
        @foreach($data->maintenance->service_tasks as $index => $task)
          <tr>
            <td class="p-1 border center">{{ $index + 1 }}</td>
            <td class="p-1 border">{{ $task['task'] }}</td>
            <td class="p-1 border">&nbsp;</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td class="p-1 border center">1</td>
          <td class="p-1 border">{{ $data->maintenance->title ?? 'Maintenance umum' }}</td>
          <td class="p-1 border">&nbsp;</td>
        </tr>
      @endif
    </tbody>
  </table>

  <!-- Notes -->
  <table class="mb-3 small">
    <tr>
      <td class="semibold" style="padding-bottom:4px;">Catatan:</td>
    </tr>
    <tr>
      <td class="p-2 border" style="height:60px; vertical-align: top;">
        @if($data->note && $data->note !== 'No notes available')
          {{ $data->note }}
        @else
          <span class="italic">Tidak ada catatan tambahan</span>
        @endif
      </td>
    </tr>
  </table>

  <!-- Signatures -->
  <table class="mb-3 small" style="table-layout: fixed;">
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
          Diterbitkan oleh <span class="capitalize bold">{{ $data->company_name }}</span> · Dokumen Rahasia – Untuk penggunaan internal dan klien.
        </td>
      </tr>
    </table>
  </div>

</body>
</html>