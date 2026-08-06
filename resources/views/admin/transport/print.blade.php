<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengajuan Transportasi - {{ $transportRequest->nomor_pengajuan }}</title>
    <style>
        @media print {
            @page { size: A4; margin: 1cm 1.5cm; }
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #1a1a1a;
            max-width: 21cm;
            margin: 0 auto;
            padding: 8px;
            background: #fff;
        }

        /* Letterhead */
        .letterhead {
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #007774;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .letterhead img { height: 42px; width: auto; }
        .letterhead-info h1 { margin: 0; font-size: 12pt; font-weight: bold; color: #007774; }
        .letterhead-info .sub { margin: 1px 0 0; font-size: 7.5pt; color: #059669; font-weight: 600; }
        .letterhead-info .contact { margin: 1px 0 0; font-size: 7pt; color: #666; }

        /* Document Title */
        .doc-title {
            text-align: center;
            background: #e6f7f6;
            border-left: 3px solid #007774;
            border-right: 3px solid #007774;
            padding: 4px 0;
            margin-bottom: 6px;
        }
        .doc-title h2 { margin: 0; font-size: 10pt; font-weight: bold; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px; }
        .doc-title .doc-num { margin: 1px 0 0; font-size: 7.5pt; color: #047857; font-weight: 600; }

        /* Section */
        .section { margin-bottom: 5px; page-break-inside: avoid; }
        .section-header {
            background: #007774;
            color: white;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 3px;
            border-radius: 2px;
        }
        .section-content { padding: 0 4px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 1.5px 4px; vertical-align: top; font-size: 8.5pt; }
        table td:first-child { width: 33%; color: #4b5563; font-weight: 500; }
        table td:nth-child(2) { width: 2%; color: #6b7280; }
        table td:last-child { width: 65%; color: #1f2937; }

        .val { font-weight: 600; color: #065f46; }
        .badge-cito { display: inline-block; background: #dc2626; color: white; padding: 0 5px; border-radius: 2px; font-size: 7pt; font-weight: bold; }

        /* Two columns */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 4px; }

        /* Info box */
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 2px solid #10b981;
            padding: 4px 6px;
            border-radius: 2px;
        }
        .info-box table td:first-child { width: 38%; }
        .info-box table td:last-child { width: 60%; }

        /* Status badge */
        .status-badge { display: inline-block; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; font-weight: 600; }
        .status-selesai { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }

        /* Signature */
        .signature-section {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            padding: 4px 3px;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sig-box .sig-title { font-weight: bold; font-size: 7.5pt; color: #065f46; margin: 0 0 1px; }
        .sig-space { height: 60px; width: 100%; display: flex; align-items: center; justify-content: center; margin: 2px 0; }
        .sig-space img { width: 58px; height: 58px; object-fit: contain; }
        .sig-bottom { width: 100%; text-align: center; margin-top: auto; }
        .sig-name { font-weight: bold; font-size: 7.5pt; color: #1f2937; margin: 0; border-bottom: 1px solid #1f2937; display: inline-block; padding-bottom: 1px; min-width: 80%; }
        .sig-sub { font-size: 7pt; color: #6b7280; margin: 1px 0 0; }
        .sig-detail { font-size: 6.5pt; color: #6b7280; margin: 1px 0 0; }

        /* Footer */
        .doc-footer { margin-top: 8px; padding-top: 5px; border-top: 1px solid #e5e7eb; text-align: center; }
        .doc-footer p { margin: 0; font-size: 6.5pt; color: #9ca3af; font-style: italic; }

        /* Print button */
        .print-button {
            position: fixed; top: 16px; right: 16px;
            padding: 8px 18px; background: #007774; color: white;
            border: none; border-radius: 6px; cursor: pointer;
            font-size: 12px; font-weight: 600; z-index: 1000;
        }
        .print-button:hover { background: #005a57; }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak</button>

    <!-- Letterhead -->
    <div class="letterhead">
        <img src="{{ asset('images/logo.png') }}" alt="Logo RS Azra">
        <div class="letterhead-info">
            <h1>RS Azra</h1>
            <p class="sub">Rumah Sakit Umum</p>
            <p class="contact">Jl. Pajajaran No. 219, Bogor 16143 | Telp: (0251) 8324054 | info@rsazra.co.id</p>
        </div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h2>Surat Pengajuan Transportasi</h2>
        <p class="doc-num">No: {{ $transportRequest->nomor_pengajuan }}/TRANS/RSA/{{ $transportRequest->created_at->format('m/Y') }}</p>
    </div>

    <!-- Section I & II: Data Pengajuan + Pemohon -->
    <div class="two-col">
        <div class="section">
            <div class="section-header">I. DATA PENGAJUAN</div>
            <div class="section-content">
                <table>
                    <tr>
                        <td>Jenis Transportasi</td><td>:</td>
                        <td><span class="val">{{ strtoupper($transportRequest->jenis) }}</span>
                            @if($transportRequest->jenis === 'ambulance' && $transportRequest->keperluan)
                                <span style="color:#6b7280">({{ ucfirst($transportRequest->keperluan) }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Prioritas</td><td>:</td>
                        <td>@if($transportRequest->prioritas === 'segera')<span class="badge-cito">CITO / SEGERA</span>@else<span class="val">BIASA</span>@endif</td>
                    </tr>
                    <tr>
                        <td>Tgl Pengajuan</td><td>:</td>
                        <td>{{ $transportRequest->created_at->format('d/m/Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Status</td><td>:</td>
                        <td><span class="status-badge status-selesai">{{ strtoupper($transportRequest->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="section">
            <div class="section-header">II. DATA PEMOHON</div>
            <div class="section-content">
                <table>
                    <tr>
                        <td>Nama Pemohon</td><td>:</td>
                        <td class="val">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}</td>
                    </tr>
                    <tr>
                        <td>Unit Kerja</td><td>:</td>
                        <td>{{ $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit }}</td>
                    </tr>
                    @if($transportRequest->user->nip ?? null)
                    <tr>
                        <td>NIP</td><td>:</td>
                        <td>{{ $transportRequest->user->nip }}</td>
                    </tr>
                    @endif
                    @if($transportRequest->jenis === 'umum' && $transportRequest->jumlah_penumpang)
                    <tr>
                        <td>Jml Penumpang</td><td>:</td>
                        <td class="val">{{ $transportRequest->jumlah_penumpang }} orang</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Section III: Data Pasien (ambulance only) -->
    @if($transportRequest->jenis === 'ambulance')
    <div class="section">
        <div class="section-header">III. DATA PASIEN</div>
        <div class="section-content">
            <div class="info-box">
                <div class="two-col">
                    <table>
                        <tr><td>Nama Pasien</td><td>:</td><td class="val">{{ $transportRequest->pasien_nama ?? '-' }}</td></tr>
                        <tr><td>No. Rekam Medis</td><td>:</td><td class="val">{{ $transportRequest->pasien_no_rm ?? '-' }}</td></tr>
                    </table>
                    <table>
                        @if($transportRequest->pendamping_nama)
                        <tr><td>Pendamping</td><td>:</td><td>{{ $transportRequest->pendamping_nama }}</td></tr>
                        @endif
                        @if($transportRequest->alamat_pasien)
                        <tr><td>Alamat Pasien</td><td>:</td><td>{{ $transportRequest->alamat_pasien }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section: Jadwal & Tujuan -->
    <div class="section">
        <div class="section-header">{{ $transportRequest->jenis === 'ambulance' ? 'IV' : 'III' }}. JADWAL & TUJUAN PERJALANAN</div>
        <div class="section-content">
            <div class="two-col">
                <table>
                    <tr><td>Tgl Berangkat</td><td>:</td><td class="val">{{ $transportRequest->tanggal->format('d/m/Y') }}</td></tr>
                    <tr><td>Jam Berangkat</td><td>:</td><td class="val">{{ substr($transportRequest->jam, 0, 5) }} WIB</td></tr>
                    <tr><td>Tgl Kembali</td><td>:</td><td class="val">{{ $transportRequest->tanggal_sampai ? $transportRequest->tanggal_sampai->format('d/m/Y') : '-' }}</td></tr>
                    <tr><td>Jam Kembali</td><td>:</td><td class="val">{{ $transportRequest->jam_sampai ? substr($transportRequest->jam_sampai, 0, 5).' WIB' : '-' }}</td></tr>
                </table>
                <table>
                    <tr><td>Alamat Asal</td><td>:</td><td>{{ $transportRequest->alamat_asal ?? 'RS Azra' }}</td></tr>
                    <tr><td>Alamat Tujuan</td><td>:</td><td class="val">{{ $transportRequest->alamat_tujuan ?? '-' }}</td></tr>
                    @if($transportRequest->jenis === 'umum' && $transportRequest->keperluan)
                    <tr><td>Keperluan</td><td>:</td><td>{{ $transportRequest->keperluan }}</td></tr>
                    @endif
                    @if($transportRequest->keterangan)
                    <tr><td>Keterangan</td><td>:</td><td>{{ $transportRequest->keterangan }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Section: Kendaraan & Pengemudi -->
    <div class="section">
        <div class="section-header">{{ $transportRequest->jenis === 'ambulance' ? 'V' : 'IV' }}. DATA KENDARAAN & PENGEMUDI</div>
        <div class="section-content">
            <div class="info-box">
                <div class="two-col">
                    <table>
                        <tr><td>Unit Kendaraan</td><td>:</td><td class="val">{{ $transportRequest->unit_mobil ?? '-' }}</td></tr>
                        <tr><td>Nomor Polisi</td><td>:</td><td class="val">{{ $transportRequest->plat_nomor ?? '-' }}</td></tr>
                        <tr><td>Nama Pengemudi</td><td>:</td><td class="val">{{ $transportRequest->driver->name ?? '-' }}</td></tr>
                        @if($transportRequest->driver?->phone)
                        <tr><td>Kontak</td><td>:</td><td>{{ $transportRequest->driver->phone }}</td></tr>
                        @endif
                    </table>
                    <table>
                        <tr><td>KM Awal</td><td>:</td><td class="val">{{ $transportRequest->km_awal ? number_format($transportRequest->km_awal, 0, ',', '.') : '-' }} km</td></tr>
                        <tr><td>KM Akhir</td><td>:</td><td class="val">{{ $transportRequest->km_akhir ? number_format($transportRequest->km_akhir, 0, ',', '.') : '-' }} km</td></tr>
                        @if($transportRequest->km_awal && $transportRequest->km_akhir)
                        <tr>
                            <td>Total Jarak</td><td>:</td>
                            <td style="background:#d1fae5;padding:1px 4px;border-radius:2px;font-weight:bold;color:#065f46;">
                                {{ number_format($transportRequest->km_akhir - $transportRequest->km_awal, 0, ',', '.') }} km
                            </td>
                        </tr>
                        @endif
                        @if($transportRequest->jam_kedatangan)
                        <tr><td>Jam Kedatangan</td><td>:</td><td class="val">{{ $transportRequest->jam_kedatangan }} WIB</td></tr>
                        @endif
                        @if($transportRequest->biaya_tol)
                        <tr><td>Biaya Tol</td><td>:</td><td class="val">Rp {{ number_format($transportRequest->biaya_tol, 0, ',', '.') }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Section -->
    @php
        $nipPemohon = $transportRequest->user->nip ?? '-';
        $pengelola1Name = $transportRequest->signature_pengelola_1_name ?? '';
        $pengelola1User = $pengelola1Name ? \App\Models\User::whereRaw("TRIM(CONCAT(first_name, ' ', last_name)) = ?", [$pengelola1Name])->first() : null;
        $nipPengelola1 = $pengelola1User?->nip ?? '-';
        $nipDriver = $transportRequest->driver?->user?->nip ?? $transportRequest->driver?->license_number ?? '-';
        $pengelola2Name = $transportRequest->signature_pengelola_2_name ?: $pengelola1Name;
        $pengelola2User = $pengelola2Name === $pengelola1Name ? $pengelola1User : (\App\Models\User::whereRaw("TRIM(CONCAT(first_name, ' ', last_name)) = ?", [$pengelola2Name])->first());
        $nipPengelola2 = $pengelola2User?->nip ?? '-';
    @endphp

    <div class="signature-section">
        <div class="sig-box">
            <p class="sig-title">Yang Mengajukan</p>
            <div class="sig-space">
                @if($nipPemohon !== '-')
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($nipPemohon) }}" alt="QR">
                @endif
            </div>
            <div class="sig-bottom">
                <p class="sig-name">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}</p>
                <p class="sig-detail">{{ $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit }}</p>
                @if($transportRequest->signature_pemohon_at)
                    <p class="sig-detail">{{ is_string($transportRequest->signature_pemohon_at) ? $transportRequest->signature_pemohon_at : $transportRequest->signature_pemohon_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="sig-box">
            <p class="sig-title">Pengelola Transportasi</p>
            <div class="sig-space">
                @if($nipPengelola1 !== '-')
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($nipPengelola1) }}" alt="QR">
                @endif
            </div>
            <div class="sig-bottom">
                <p class="sig-name">{{ $pengelola1Name ?: '——————' }}</p>
                <p class="sig-sub">Menyetujui</p>
                @if($transportRequest->signature_pengelola_1_at)
                    <p class="sig-detail">{{ is_string($transportRequest->signature_pengelola_1_at) ? $transportRequest->signature_pengelola_1_at : $transportRequest->signature_pengelola_1_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="sig-box">
            <p class="sig-title">Pengemudi</p>
            <div class="sig-space">
                @if(request()->get('from') !== 'driver_active' && $nipDriver !== '-')
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($nipDriver) }}" alt="QR">
                @endif
            </div>
            <div class="sig-bottom">
                @if(request()->get('from') === 'driver_active')
                    <p class="sig-name" style="color:#d1d5db;">——————————</p>
                @else
                    <p class="sig-name">{{ $transportRequest->driver->name ?? '——————' }}</p>
                    @if($transportRequest->driver?->user?->unit_kerja)
                        <p class="sig-detail">{{ $transportRequest->driver->user->unit_kerja }}</p>
                    @endif
                    @if($transportRequest->signature_driver_at)
                        <p class="sig-detail">{{ is_string($transportRequest->signature_driver_at) ? $transportRequest->signature_driver_at : $transportRequest->signature_driver_at->format('d/m/Y H:i') }}</p>
                    @endif
                @endif
            </div>
        </div>

        <div class="sig-box">
            <p class="sig-title">Pengelola Transportasi</p>
            <div class="sig-space">
                @if(request()->get('from') !== 'driver' && $transportRequest->status === 'selesai' && $nipPengelola2 !== '-')
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($nipPengelola2) }}" alt="QR">
                @endif
            </div>
            <div class="sig-bottom">
                @if(request()->get('from') === 'driver')
                    <p class="sig-name" style="color:#d1d5db;">——————————</p>
                    <p class="sig-sub">Mengetahui</p>
                @elseif($transportRequest->status === 'selesai')
                    <p class="sig-name">{{ $pengelola2Name ?: '——————' }}</p>
                    <p class="sig-sub">Mengetahui</p>
                    @if($transportRequest->signature_pengelola_2_at)
                        <p class="sig-detail">{{ is_string($transportRequest->signature_pengelola_2_at) ? $transportRequest->signature_pengelola_2_at : $transportRequest->signature_pengelola_2_at->format('d/m/Y H:i') }}</p>
                    @endif
                @else
                    <p class="sig-name" style="color:#d1d5db;">——————————</p>
                    <p class="sig-sub">Mengetahui</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="doc-footer">
        <p>Dicetak: {{ now()->format('d/m/Y, H:i') }} WIB &nbsp;|&nbsp; RS Azra - Sistem Pengajuan Transportasi</p>
    </div>
</body>
</html>
