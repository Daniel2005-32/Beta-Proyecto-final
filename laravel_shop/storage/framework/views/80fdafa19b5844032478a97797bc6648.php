<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #<?php echo e($order->id); ?></title>
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
                <?php echo e($order->user->name); ?><br>
                <?php echo e($order->address->street); ?> <?php echo e($order->address->number); ?><br>
                <?php echo e($order->address->city); ?>, <?php echo e($order->address->state); ?><br>
                <?php echo e($order->user->email); ?>

            </td>
        </tr>
    </table>

    <table style="margin-bottom: 20px;">
        <tr>
            <td><strong>Número de Pedido:</strong> #<?php echo e($order->id); ?></td>
            <td style="padding-left: 40px;"><strong>Fecha:</strong> <?php echo e($order->created_at->format('d/m/Y')); ?></td>
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
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->product->name); ?></td>
                <td><?php echo e($item->quantity); ?></td>
                <td><?php echo e(number_format($item->price, 2)); ?>€</td>
                <td style="text-align: right;"><?php echo e(number_format($item->price * $item->quantity, 2)); ?>€</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">Subtotal: <span><?php echo e(number_format($order->subtotal, 2)); ?>€</span></div>
        <?php if($order->discount_amount > 0): ?>
        <div class="totals-row" style="color: #2ecc71;">Descuentos: <span>-<?php echo e(number_format($order->discount_amount, 2)); ?>€</span></div>
        <?php endif; ?>
        <div class="totals-row"><?php echo e($order->tax_type); ?>: <span><?php echo e(number_format($order->tax_amount, 2)); ?>€</span></div>
        <div class="totals-row total-final">TOTAL: <span><?php echo e(number_format($order->total, 2)); ?>€</span></div>
    </div>

    <div class="footer">
        Gracias por su confianza en Soul Arcade. Esta es una factura generada automáticamente.<br>
        Soul Arcade SL - www.soularcade.example.com
    </div>
</body>
</html>
<?php /**PATH /app/resources/views/pdf/invoice.blade.php ENDPATH**/ ?>