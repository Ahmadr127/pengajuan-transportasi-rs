<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Transportasi</title>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 1cm 1.2cm; }
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 7.5pt; color: #1a1a1a; background: #fff; margin: 0; padding: 8px; }

        .header { display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #007774; padding-bottom: 5px; margin-bottom: 6px; }
        .header img { height: 38px; width: auto; }
        .header-info h1 { margin: 0; font-size: 11pt; font-weight: bold; color: #007774; }
        .header-info p { margin: 1px 0 0; font-size: 7pt; color: #555; }

        .doc-title { text-align: center; margin-bottom: 5px; }
        .doc-title h2 { margin: 0; font-size: 9pt; font-weight: bold; text-transform: uppercase; color: #065f46; }
        .doc-title p { margin: 1px 0 0; font-size: 7pt; color: #666; }

        table { width: 100%; border-collapse: collapse; }
        thead tr th { background: #007774; color: white; padding: 3px 4px; font-size: 7pt; font-weight: bold; text-align: left; white-space: nowrap; border: 1px solid #005a57; }
        tbody tr td { padding: 2px 4px; font-size: 7pt; border: 1px solid #e2e8f0; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f0fafa; }
        tbody tr:hover td { background: #e6f7f6; }

        .badge { display: inline-block; padding: 0 4px; border-radius: 2px; font-size: 6.5pt; font-weight: bold; }
        .badge-diajukan { background: #fef3c7; color: #92400e; }
        .badge-diproses { background: #dbeafe; color: #1e40af; }
        .badge-digunakan { background: #cffafe; color: #155e75; }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-tolak { background: #fee2e2; color: #991b1b; }
        .badge-cito { background: #dc2626; color: white; }
        .badge-umum { background: #d1fae5; color: #065f46; }
        .badge-ambulance { background: #fee2e2; color: #991b1b; }

        .footer { margin-top: 8px; border-top: 1px solid #e5e7eb; padding-top: 4px; display: flex; justify-content: space-between; }
        .footer p { margin: 0; font-size: 6.5pt; color: #9ca3af; font-style: italic; }

        .print-btn { position: fixed; top: 16px; right: 16px; padding: 7px 16px; background: #007774; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; z-index: 1000; }
        .print-btn:hover { background: #005a57; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print PDF</button>

    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo RS Azra">
        <div class="header-info">
            <h1>RS Azra</h1>
            <p>Rumah Sakit Umum | Jl. Pajajaran No. 219, Bogor 16143</p>
        </div>
    </div>

    <div class="doc-title">
        <h2>Laporan Pengajuan Transportasi</h2>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }} WIB &nbsp;|&nbsp; Total: {{ $items->count() }} data</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:16px">#</th>
                <th>No. Pengajuan</th>
                <th>Dibuat</th>
                <th>Nama Pemohon</th>
                <th>Unit Kerja</th>
                <th>Jenis</th>
                <th>Keperluan</th>
                <th>Tgl Berangkat</th>
                <th>Jam</th>
                <th>Tujuan</th>
                <th>Unit Kendaraan</th>
                <th>Plat</th>
                <th>Nama Driver</th>
                <th>KM Awal</th>
                <th>KM Akhir</th>
                <th>Jarak</th>
                <th>Tgl Kembali</th>
                <th>Jam Tiba</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
                @php
                    $statusLabel = match($item->status) {
                        'diproses'        => 'Disetujui',
                        'tidak_disetujui' => 'Tdk Disetujui',
                        default           => ucfirst($item->status),
                    };
                    $statusClass = match($item->status) {
                        'diajukan'        => 'badge-diajukan',
                        'diproses'        => 'badge-diproses',
                        'digunakan'       => 'badge-digunakan',
                        'selesai'         => 'badge-selesai',
                        'tidak_disetujui' => 'badge-tolak',
                        default           => '',
                    };
                    $jarak = ($item->km_awal && $item->km_akhir) ? number_format($item->km_akhir - $item->km_awal, 0, ',', '.') : '-';
                    $tglKembali = $item->tanggal_sampai
                        ? $item->tanggal_sampai->format('d/m/Y')
                        : ($item->status === 'selesai' ? $item->updated_at->format('d/m/Y') : '-');
                @endphp
                <tr>
                    <td style="text-align:center;color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-family:monospace;font-weight:600">{{ $item->nomor_pengajuan }}</td>
                    <td style="white-space:nowrap">{{ $item->created_at->format('d/m/Y') }}<br><span style="color:#9ca3af">{{ $item->created_at->format('H:i') }}</span></td>
                    <td>
                        {{ $item->user->full_name ?? $item->pemohon_nama }}
                        @if($item->user && $item->user->isPriority())
                            <span class="badge" style="background:#ede9fe;color:#6d28d9">P</span>
                        @endif
                    </td>
                    <td>{{ $item->user->unit_kerja ?? $item->pemohon_unit ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $item->jenis === 'ambulance' ? 'badge-ambulance' : 'badge-umum' }}">{{ ucfirst($item->jenis) }}</span>
                        @if($item->prioritas === 'segera') <span class="badge badge-cito">CITO</span> @endif
                    </td>
                    <td style="max-width:80px;overflow:hidden">{{ $item->keperluan ?? '-' }}</td>
                    <td style="white-space:nowrap">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td style="white-space:nowrap">{{ substr($item->jam, 0, 5) }}@if($item->jam_sampai)-{{ substr($item->jam_sampai, 0, 5) }}@endif</td>
                    <td style="max-width:90px;overflow:hidden">{{ $item->alamat_tujuan ?? '-' }}</td>
                    <td>{{ $item->unit_mobil ? ucwords(str_replace('_', ' ', $item->unit_mobil)) : '-' }}</td>
                    <td style="font-family:monospace">{{ $item->plat_nomor ?? '-' }}</td>
                    <td>{{ $item->driver->name ?? '-' }}</td>
                    <td style="text-align:right">{{ $item->km_awal ? number_format($item->km_awal, 0, ',', '.') : '-' }}</td>
                    <td style="text-align:right">{{ $item->km_akhir ? number_format($item->km_akhir, 0, ',', '.') : '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#065f46">{{ $jarak }}</td>
                    <td style="white-space:nowrap">{{ $tglKembali }}</td>
                    <td style="white-space:nowrap">{{ $item->jam_kedatangan ?? '-' }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="19" style="text-align:center;padding:16px;color:#9ca3af">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>RS Azra — Sistem Pengajuan Transportasi</p>
        <p>{{ now()->format('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
