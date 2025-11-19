<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Code-barres - {{ $equipment->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            padding: 20px;
        }
        .barcode-container {
            margin: 0 auto;
            text-align: center;
            max-width: 100%;
        }
        .barcode {
            margin: 20px 0;
        }
        .barcode-number {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
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
        }
    </style>
</head>
<body>
    <div class="barcode-container">
        <div class="equipment-info">
            <div class="equipment-name">{{ $equipment->name }}</div>
            @if($equipment->serial_number)
                <div class="equipment-serial">N° de série: {{ $equipment->serial_number }}</div>
            @endif
        </div>
        
        <div class="barcode">
            <img src="{{ $barcodeImage }}" alt="Code-barres">
            <div class="barcode-number">{{ $equipment->barcode }}</div>
        </div>
        
        <div class="footer">
            <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
