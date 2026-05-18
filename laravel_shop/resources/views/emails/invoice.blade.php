<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { text-align: center; border-bottom: 2px solid #7000ff; padding-bottom: 10px; }
        .content { margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 0.8em; color: #777; }
        .button { display: inline-block; padding: 10px 20px; background-color: #7000ff; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #7000ff;">¡Gracias por tu compra, {{ $order->user->name }}!</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Confirmamos que hemos recibido tu pedido <strong>#{{ $order->id }}</strong> correctamente.</p>
            <p>Adjunto a este correo encontrarás la factura oficial en formato PDF con todos los detalles de tu compra.</p>
            
            <h3>Resumen del Pedido:</h3>
            <ul>
                @foreach($order->items as $item)
                    <li>{{ $item->quantity }}x {{ $item->product->name }} - {{ $item->price }}€</li>
                @endforeach
            </ul>
            
            <p><strong>Total pagado: {{ $order->total }}€</strong></p>
            
            <p>Si tienes alguna duda, puedes contactar con nosotros a través de la sección de soporte en la web.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Soul Shop. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
