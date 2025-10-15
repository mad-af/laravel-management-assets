<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Pajak Kendaraan Belum Dibayar</title>
</head>

<body style="margin:0; padding:0; background-color:#f0f2f5; font-family:Arial, Helvetica, sans-serif; color:#222;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f0f2f5; padding:24px 0;">
        <tr>
            <td align="center">
                <!-- CARD -->
                <table width="720" border="0" cellspacing="0" cellpadding="0"
                    style="background-color:#ffffff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.08); padding:24px;">
                    <tr>
                        <td>
                            <!-- HEADER -->
                            <h2 style="margin:0 0 8px 0; font-size:22px; color:#111;">Pemberitahuan Pajak Kendaraan
                                Belum Dibayar</h2>
                            <p style="margin:4px 0; font-size:14px; color:#666;">Perusahaan:
                                <strong>{{ $company->name }}</strong></p>
                            <p style="font-size:15px; color:#333; line-height:1.5;">
                                Berikut adalah daftar pajak kendaraan yang belum dibayar pada sistem Anda. Mohon tindak
                                lanjut sesuai kebijakan internal.
                            </p>

                            <!-- SUMMARY -->
                            @php
                                $total = $histories->count();
                                $overdue = $histories->filter(fn($h) => $h->status->value === 'overdue')->count();
                                $dueSoon = $histories->filter(fn($h) => $h->status->value === 'due_soon')->count();
                              @endphp

                            <p style="font-size:15px; margin-top:12px;">
                                Total belum dibayar: <strong>{{ $total }}</strong> |
                                Terlambat: <strong style="color:#c0392b;">{{ $overdue }}</strong> |
                                Jatuh Tempo: <strong style="color:#d68910;">{{ $dueSoon }}</strong>
                            </p>

                            <!-- TABLE -->
                            <table width="100%" cellpadding="8" cellspacing="0" border="0"
                                style="border-collapse:collapse; margin-top:12px;">
                                <thead>
                                    <tr style="background-color:#f8f8f8; border-bottom:2px solid #ddd;">
                                        <th align="left" style="border:1px solid #ddd; font-size:14px;">Kode Aset</th>
                                        <th align="left" style="border:1px solid #ddd; font-size:14px;">Nama Aset</th>
                                        <th align="left" style="border:1px solid #ddd; font-size:14px;">Jenis Pajak</th>
                                        <th align="left" style="border:1px solid #ddd; font-size:14px;">Jatuh Tempo</th>
                                        <th align="left" style="border:1px solid #ddd; font-size:14px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($histories as $history)
                                        @php $status = $history->status; @endphp
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td style="border:1px solid #ddd; font-size:14px;">{{ $history->asset->code }}
                                            </td>
                                            <td style="border:1px solid #ddd; font-size:14px;">{{ $history->asset->name }}
                                            </td>
                                            <td style="border:1px solid #ddd; font-size:14px;">
                                                {{ $history->vehicleTaxType->tax_type->label() }}</td>
                                            <td style="border:1px solid #ddd; font-size:14px;">
                                                {{ optional($history->due_date)->format('d M Y') }}</td>
                                            <td style="border:1px solid #ddd; font-size:14px;">
                                                @if ($status->value === 'overdue')
                                                    <span
                                                        style="display:inline-block; background-color:#f8d7da; color:#721c24; padding:4px 8px; border-radius:12px; font-size:12px;">{{ $status->label() }}</span>
                                                @elseif ($status->value === 'due_soon')
                                                    <span
                                                        style="display:inline-block; background-color:#fff3cd; color:#856404; padding:4px 8px; border-radius:12px; font-size:12px;">{{ $status->label() }}</span>
                                                @elseif ($status->value === 'paid')
                                                    <span
                                                        style="display:inline-block; background-color:#d4edda; color:#155724; padding:4px 8px; border-radius:12px; font-size:12px;">{{ $status->label() }}</span>
                                                @else
                                                    <span
                                                        style="display:inline-block; background-color:#e9f7fe; color:#0c5460; padding:4px 8px; border-radius:12px; font-size:12px;">{{ $status->label() }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- FOOTER -->
                            <p style="font-size:13px; color:#888; margin-top:20px; text-align:center; line-height:1.4;">
                                Email ini dikirim otomatis setiap hari.<br>
                                Jika Anda membutuhkan bantuan, silakan hubungi administrator sistem.
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- END CARD -->
            </td>
        </tr>
    </table>
</body>

</html>