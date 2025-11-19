<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>QR Code - {{ $equipment->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            padding: 20px;
        }
        .qrcode-container {
            margin: 0 auto;
            text-align: center;
            max-width: 100%;
        }
        .qrcode {
            margin: 20px 0;
        }
        .qrcode img {
            max-width: 200px;
            height: auto;
        }
        .equipment-info {
            margin: 20px 0;
        }
        .equipment-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .equipment-serial {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .qrcode-url {
            font-size: 12px;
            color: #666;
            word-break: break-all;
            max-width: 80%;
            margin: 10px auto;
        }
    </style>
</head>
<body>
    <div class="qrcode-container">
        <div class="equipment-info">
            <div class="equipment-name">{{ $equipment->name }}</div>
            @if($equipment->serial_number)
                <div class="equipment-serial">N° de série: {{ $equipment->serial_number }}</div>
            @endif
        </div>
        
        <div class="qrcode">
            <img src="{{ $qrCodeImage }}" alt="QR Code">
            <div class="qrcode-url">{{ route('equipment.show', $equipment) }}</div>
        </div>
        
        <div class="footer">
            <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
