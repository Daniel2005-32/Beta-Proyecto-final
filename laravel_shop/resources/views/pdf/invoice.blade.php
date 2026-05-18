<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $order->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { border-bottom: 2px solid #7000ff; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #7000ff; text-transform: uppercase; }
        .invoice-title { text-align: right; font-size: 30px; font-weight: 100; color: #999; }
        .details-table { width: 100%; margin-bottom: 40px; }
        .details-table td { vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { background: #f8f8f8; padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 12px; text-transform: uppercase; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 14px; }
        .totals { float: right; width: 300px; }
        .totals-row { display: block; width: 100%; padding: 5px 0; font-size: 14px; }
        .totals-row span { float: right; font-weight: bold; }
        .total-final { border-top: 2px solid #7000ff; margin-top: 10px; padding-top: 10px; font-size: 18px; color: #7000ff; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%">
            <tr>
                <td class="logo">Soul Arcade</td>
                <td class="invoice-title">FACTURA</td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <td style="width: 50%">
                <strong>De:</strong><br>
                Soul Arcade SL<br>
                Calle Gamer, 13<br>
                28001 Madrid, España<br>
                NIF: B12345678
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>Para:</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->address->street }} {{ $order->address->number }}<br>
                {{ $order->address->city }}, {{ $order->address->state }}<br>
                {{ $order->user->email }}
            </td>
        </tr>
    </table>

    <table style="margin-bottom: 20px;">
        <tr>
            <td><strong>Número de Pedido:</strong> #{{ $order->id }}</td>
            <td style="padding-left: 40px;"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio Unit.</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }}€</td>
                <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 2) }}€</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">Subtotal: <span>{{ number_format($order->subtotal, 2) }}€</span></div>
        @if($order->discount_amount > 0)
        <div class="totals-row" style="color: #2ecc71;">Descuentos: <span>-{{ number_format($order->discount_amount, 2) }}€</span></div>
        @endif
        <div class="totals-row">{{ $order->tax_type }}: <span>{{ number_format($order->tax_amount, 2) }}€</span></div>
        <div class="totals-row total-final">TOTAL: <span>{{ number_format($order->total, 2) }}€</span></div>
    </div>

    <div class="footer">
        Gracias por su confianza en Soul Arcade. Esta es una factura generada automáticamente.<br>
        Soul Arcade SL - www.soularcade.example.com
    </div>
</body>
</html>
