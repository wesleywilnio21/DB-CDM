<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Envelope - {{ $letterDocument->letter_number }}</title>

    <style>
        /* Ukuran standar amplop DL mode Portrait untuk feed printer */
        @page {
            size: 110mm 230mm portrait;
            margin: 0;
        }

        body {
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Container amplop: aktual potret (berdiri) */
        .envelope-portrait {
            width: 110mm;
            height: 230mm;
            background-color: white;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Wrapper desain landscape yang di-rotate agar tercetak melintang dari kiri bawah ke kiri atas */
        .landscape-wrapper {
            width: 230mm;
            height: 110mm;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            padding: 12mm 15mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        /* Bagian Pengirim (Kop) */
        .sender-section {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #de2424; 
            padding-bottom: 3.5mm;
            margin-bottom: 12mm;
        }

        .logo-wrapper {
            width: 30mm;
            margin-right: 5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        .kop-text h1 {
            color: #de2424;
            font-size: 26pt;
            font-weight: 900;
            margin: 0;
            line-height: 1.1;
            letter-spacing: 0.05em;
        }

        .kop-text h2 {
            color: #de2424;
            font-size: 19pt;
            font-weight: 900;
            margin: 0;
            line-height: 1.1;
            letter-spacing: 0.02em;
        }

        .kop-text .address {
            font-size: 11.5pt;
            margin: 1.5mm 0 1mm 0;
            color: #111;
        }

        .kop-text .contact {
            font-size: 11pt;
            margin: 0;
            color: #111;
            display: flex;
            align-items: center;
        }

        .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5mm;
        }

        .icon-wrap-fb {
            margin-left: 2.5mm;
        }

        /* Bagian Penerima */
        .receiver-section {
            display: flex;
            justify-content: space-between;
            padding-left: 30mm; 
        }

        .receiver-address p {
            margin: 0 0 1.5mm 0;
            font-size: 13pt;
            color: #000;
        }

        .receiver-address .name {
            font-size: 14pt;
            font-weight: bold;
        }

        .i-mark {
            width: 3.5mm;
            height: 10mm;
            background-color: #333;
            margin-right: 15mm;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            z-index: 50;
        }
        .print-btn:hover { background: #1d4ed8; }

        @media print {
            body {
                background: none;
                padding: 0;
                display: block;
            }
            .envelope-portrait {
                box-shadow: none;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Print Amplop</button>

    <div class="envelope-portrait">
        <!-- Wrapper desain horizontal yang dirotasi 90 derajat -->
        <div class="landscape-wrapper">
            
            <div class="sender-section">
                <div class="logo-wrapper">
                    <img src="{{ asset('Logo CDM.png') }}" alt="Logo Cetiya">
                </div>
                
                <div class="kop-text">
                    <h1>CETIYA</h1>
                    <h2>DHAMMA MANGGALA</h2>
                    <p class="address">Jl. Taman Sunter Indah Blok A3 No. 33, Jakarta Utara 14350</p>
                    <p class="contact">
                        Tel. (021) 6530 0211 
                        <span class="icon-wrap icon-wrap-fb" style="margin-left: 2mm;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span> 
                        dhamma.manggala@gmail.com 
                        <span class="icon-wrap icon-wrap-fb">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-3 7h-1.924c-.615 0-1.076.252-1.076.889v1.111h3l-.239 3h-2.761v8h-3v-8h-2v-3h2v-1.923c0-2.022 1.064-3.077 3.461-3.077h2.539v3z"/>
                            </svg>
                        </span> 
                        Dhamma Manggala
                    </p>
                </div>
            </div>
            
            <div class="receiver-section">
                <div class="receiver-address">
                    <p>Kepada Yth.</p>
                    @if($letterDocument->contact)
                        <p class="name">{{ $letterDocument->contact->name }} @if($letterDocument->contact->name) @endif</p>
                        <p>Di tempat</p>
                    @else
                        @php $penerima = $letterDocument->variables['penerima'] ?? '...........................'; @endphp
                        <p class="name">{{ $penerima }}</p>
                        <p>Di tempat</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</body>
</html>