<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $letter->title }}</title>
    <style>
        @page {
            margin: 40px 50px;
        }
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; 
            font-size: 14px; 
            color: #000; 
            line-height: 1.6; 
        }
        
        /* HEADER KOP SURAT */
        .kop-surat {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 5px;
            position: relative;
            display: table;
        }
        .kop-logo {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: center;
        }
        .kop-logo img {
            max-width: 90px;
            max-height: 90px;
        }
        .kop-logo svg {
            width: 80px;
            height: 80px;
            fill: #d32f2f;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }
        .kop-org-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .kop-address {
            font-size: 12px;
            margin: 5px 0 0 0;
            padding: 0;
            line-height: 1.4;
        }
        
        /* TAGLINE */
        .tagline-container {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 25px;
            text-align: center;
        }
        .tagline {
            font-size: 11px;
            font-style: italic;
            font-weight: bold;
            color: #8B0000;
        }

        /* CONTENT */
        .date-right {
            text-align: right;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .meta-table td {
            vertical-align: top;
            padding-bottom: 5px;
        }
        .meta-label {
            width: 70px;
        }
        .meta-colon {
            width: 15px;
        }
        
        .recipient {
            margin-bottom: 30px;
            line-height: 1.4;
        }
        
        .body {
            margin-bottom: 40px;
            color: #000;
            text-align: justify;
        }
        .body * { 
            color: #000 !important; 
        }
        
        /* SIGNATURE */
        .signature-area {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            float: right;
            width: 300px;
            text-align: center;
        }
        .signature-img {
            max-height: 100px;
            max-width: 250px;
            margin-top: 15px;
            margin-bottom: 15px;
            object-fit: contain;
        }
        .signature-line {
            margin-top: 15px;
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 80%;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <div class="kop-logo">
            @php $logoPath = $letter->logoAsset?->absolutePath(); @endphp
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo">
            @else
                <!-- Placeholder SVG Stupa -->
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 10 L45 30 L55 30 Z" fill="#d32f2f"/>
                    <path d="M40 30 L60 30 L65 50 L35 50 Z" fill="#d32f2f"/>
                    <path d="M30 50 L70 50 L75 70 L25 70 Z" fill="#d32f2f"/>
                    <path d="M20 70 L80 70 L80 80 L20 80 Z" fill="#d32f2f"/>
                    <path d="M15 80 L85 80 L85 90 L15 90 Z" fill="#d32f2f"/>
                    <rect x="48" y="5" width="4" height="15" fill="#d32f2f"/>
                </svg>
            @endif
        </div>
        <div class="kop-text">
            <div class="kop-org-name">{{ $orgSettings['org_name'] ?? 'CETIYA DHAMMA MANGGALA' }}</div>
            <div class="kop-address">
                {{ $orgSettings['org_address'] ?? 'Jl. Taman Sunter Indah A3/33 Sunter Jaya, Jakarta Utara 14350 – Indonesia' }}<br>
                {{ $orgSettings['org_phone'] ?? 'Telp. (021) 65300211 Fax. (021) 65300211 WA. 085103728801' }}
            </div>
        </div>
    </div>

    <!-- TAGLINE -->
    <div class="tagline-container">
        <span class="tagline">{{ $orgSettings['org_tagline'] ?? '"SELALU BERUSAHA BERBUAT KEBAJIKAN SEBANYAK MUNGKIN UNTUK DIRI SENDIRI DAN ORANG LAIN"' }}</span>
    </div>

    <!-- TANGGAL (Kanan Atas) -->
    <div class="date-right">
        {{ $letter->display_city_date }}
    </div>

    <!-- META INFO (Nomor, Hal) -->
    <table class="meta-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="meta-label">Nomor</td>
            <td class="meta-colon">:</td>
            <td>{{ $letter->letter_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Perihal</td>
            <td class="meta-colon">:</td>
            <td>{{ $letter->title }}</td>
        </tr>
    </table>

    <!-- RECIPIENT -->
    <div class="recipient">
        Kepada Yth.<br>
        <strong>{{ $letter->recipient_name }}</strong><br>
        @if($letter->recipient_phone)
            {{ $letter->recipient_phone }}<br>
        @endif
        Di tempat
    </div>

    <!-- ISI SURAT -->
    <div class="body">
        {!! $letter->body !!}
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature-area">
        <div class="signature-box">
            @if($letter->sig_text_above)
                <div style="margin-bottom: 5px;">{{ $letter->sig_text_above }}</div>
            @endif
            
            <div style="position:relative; width:220px; height:120px; margin:0 auto 10px auto;">
                @if($kopPath = $letter->kopAsset?->absolutePath())
                    @if(file_exists($kopPath))
                    <img src="{{ $kopPath }}" style="position:absolute; top:0; left:10px; width:150px; opacity:0.85; z-index:1;" alt="Kop">
                    @endif
                @endif
                
                @if($ttdPath = $letter->ttdAsset?->absolutePath())
                    @if(file_exists($ttdPath))
                    <img src="{{ $ttdPath }}" style="position:absolute; top:10px; left:35px; width:150px; z-index:2;" alt="Signature">
                    @endif
                @endif
                
                @if(!$letter->kopAsset && !$letter->ttdAsset && !$letter->signature_path)
                    <br><br><br><br>
                @endif
                
                <!-- Fallback for old signatures -->
                @if($letter->signature_path && !$letter->ttdAsset)
                    @php 
                        $oldSigPath = storage_path('app/public/' . $letter->signature_path); 
                    @endphp
                    @if(file_exists($oldSigPath))
                        <img src="{{ $oldSigPath }}" style="position:absolute; top:10px; left:35px; width:150px; z-index:2;" alt="Signature">
                    @endif
                @endif
            </div>

            <div class="signature-line"></div>
            <div style="font-weight:bold;">{{ $letter->sig_name ?: 'Panitia ' . $event->name }}</div>
            @if($letter->sig_position)
                <div style="font-size:12px;">{{ $letter->sig_position }}</div>
            @endif
        </div>
    </div>

</body>
</html>
