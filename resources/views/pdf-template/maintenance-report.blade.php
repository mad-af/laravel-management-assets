<html>
<head>
    {{-- <title>Generate Laravel TCPDF by codeanddeploy.com</title> --}}
    <style>
        table {
            border-collapse: collapse;
            line-height: normal;
            font-size: 8pt;
        }
        table.table-border, table.table-border th {
            text-align: center;
            border: 1px solid black;
            padding: 2px 4px;
        }
        table.table-border td {
            text-align: left;
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            padding: 2px 4px;
        }
        .center { text-align: center; }
        .title { font-size: 14pt; font-weight: bold; }
        .border { border: 1px solid black; }
        .bold { font-weight: bold; }
        .f-5 { font-size: 5pt; }
        .f-8 { font-size: 8pt; }
        .f-10 { font-size: 10pt; }
        .f-21 { font-size: 21pt; }
        .p-10 { padding: 10px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <table width="100%">
        <tr>
            <!-- Left Header -->
            <td width="50%">
                <table width="100%">
                    <tr><td class="f-21 bold">WORK ORDER</td></tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td class="border f-10">
                                        <table width="100%" style="padding: 3px auto">
                                            <tr><td>No : {{ $data->work_order_no }}</td></tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="30%">To</td>
                                    <td width="5%">:</td>
                                    <td width="65%">{{ $data->workshop->name }}</td>
                                </tr>
                                <tr>
                                    <td></td><td>:</td>
                                    <td>{{ $data->workshop->address }}</td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td>Police Number</td><td>:</td>
                                    <td>{{ $data->vehicle->vehicle_no }}</td>
                                </tr>
                                <tr>
                                    <td>Vehicle Type</td><td>:</td>
                                    <td>{{ $data->vehicle->brand->name }} - {{ $data->vehicle->type }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>  
            </td>

            <!-- Right Header -->
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td align="right"><img src="images/logo/bmr-logo.png" width="140px" /></td>
                                </tr>
                                <tr>
                                    <td align="right" class="f-8 bold">PT. GAYA SUKSES MANDIRI KASEINDO</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" class="f-5">
                                <tr>
                                    <td width="30%">
                                        Surabaya Office | Factory <br>
                                        Jl. Raya Margomulyo 63A <br>
                                        Surabaya <br>
                                        Tel : +62 31 7499 050 <br>
                                        Email : surabaya@safeway.co.id
                                    </td>
                                    <td width="35%">
                                        Jakarta Office <br>
                                        Kompleks Artha Gading Blok F No. 20 <br>
                                        Jakarta <br>
                                        Tel : +62 21 4585 0806 <br>
                                        Email : jakarta@safeway.co.id
                                    </td>
                                    <td width="40%">
                                        Semarang Office | Factory <br>
                                        Kawasan Industri Candi Blok XIC No. 2C <br>
                                        Semarang <br>
                                        Tel : +62 24 7627 600 <br>
                                        Email : semarang@safeway.co.id
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr><td class="bold">Company Contact & Information</td></tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="30%">PIC Name</td><td width="5%">:</td>
                                    <td width="65%">{{ $data->employee->name }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td><td>:</td>
                                    <td>{{ $data->employee->phone }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>    
            </td>
        </tr>
    </table>

    <br><br>

    <!-- SERVICE INSTRUCTION TABLE -->
    <table width="100%">
        <tr>
            <td class="border f-10">
                <table width="100%" align="center" cellpadding="2" cellspacing="0">
                    <thead>
                        <tr class="bold" style="border:1px solid black;">
                            <th width="5%">No.</th>
                            <th width="80%">Instruction of Service</th>
                            <th width="15%">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item as $item)
                        <tr>
                            <td align="center">{{ $loop->iteration }}</td>
                            <td>{{ $item->instruction }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <!-- DATE & NOTE -->
    <table width="100%">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="border f-10">
                            <table width="100%" cellpadding="2">
                                <tr>
                                    <td width="50%">W.O Date</td><td width="5%">:</td>
                                    <td width="45%">{{ \Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Estimation Service End Date</td><td>:</td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp(strtotime($data->estimation_end_date))->format('d-m-Y') }}</td>
                                </tr>
                                <tr><td colspan="3" style="height: 22px"></td></tr>
                            </table>
                        </td>
                    </tr>
                </table>  
            </td>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="border f-10">
                            <table width="100%" cellpadding="2">
                                <tr><td>Note :</td></tr>
                                <tr><td style="height: 30px">{{ $data->note }}</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>  
            </td>
        </tr>
    </table>

    <br><br>

    <!-- SIGNATURE -->
    <table width="100%">
        <tr nobr="true">
            <td>
                <table width="100%">
                    <tr>
                        <th align="center">Created By.</th>
                        <th align="center">Approved By.</th>
                        <th align="center">Recipient By.</th>
                    </tr>
                    <tr><td colspan="3" style="height: 50px"></td></tr>
                    <tr>
                        <th align="center">( Staff GA )</th>
                        <th align="center">( Kepala HR & GA )</th>
                        <th align="center">( --------------------- )</th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
