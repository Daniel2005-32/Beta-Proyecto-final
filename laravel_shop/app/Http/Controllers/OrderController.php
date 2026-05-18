<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PriceHelper;

use App\Models\Coupon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Verificar si el usuario está baneado
     */
    private function checkBanned()
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return response()->json(['error' => 'No puedes realizar esta acción mientras estás baneado.'], 403);
        }
        return null;
    }

    public function validateCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string', 'subtotal' => 'required|numeric']);
        $coupon = Coupon::where('code', $request->code)->first();
        
        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Cupón no encontrado'], 404);
        }
        
        if (!$coupon->isValid($request->subtotal)) {
            return response()->json(['valid' => false, 'message' => 'El cupón no es válido para este pedido'], 400);
        }
        
        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($request->subtotal)
        ]);
    }

    public function checkout(Request $request)
    {
        // RASTREADOR DE ENTRADA PROTEGIDO
        try {
            $logDir = storage_path('logs');
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
            file_put_contents($logDir . '/resend_debug.log', "[" . date('Y-m-d H:i:s') . "] INICIO CHECKOUT: Petición recibida de " . (auth()->user()->email ?? 'Anónimo') . "\n", FILE_APPEND);
        } catch (\Exception $e) {}

        $check = $this->checkBanned();
        if ($check) return $check;

        if (!auth()->check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'points_used' => 'nullable|integer|min:0',
            'coupon_code' => 'nullable|string',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1'
        ]);

        $user = auth()->user();
        $address = Address::find($request->address_id);
        if ($address->user_id != $user->id) {
            return response()->json(['error' => 'Dirección inválida'], 403);
        }

        $cart = $request->cart;
        $subtotal = 0;
        // Limitar puntos a un máximo de 15% del total (150 puntos máximo por pedido según la lógica de 10pts=1%)
        $maxTotalPoints = 150; 

        // Validar puntos solicitados
        $pointsToUse = $request->input('points_used', 0);
        if ($pointsToUse > $user->points) {
            return response()->json(['error' => 'No tienes suficientes puntos.'], 400);
        }
        if ($pointsToUse > $maxTotalPoints) {
            $pointsToUse = $maxTotalPoints;
        }

        // Verificar stock de todos los productos y calcular el total real basado en la BD
        foreach ($cart as $item) {
            $product = Product::withoutGlobalScope('parent_only')->find($item['id']);
            
            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'error' => "Stock insuficiente para {$product->name}. Disponible: {$product->stock}"
                ], 400);
            }

            $subtotal += ($product->price * $item['quantity']);
        }

        // Calcular Descuento por Puntos (1 punto = 0.1%)
        $pointsDiscountAmount = $subtotal * ($pointsToUse * 0.001);
        
        // Calcular Descuento por Cupón
        $couponDiscountAmount = 0;
        $couponId = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $couponDiscountAmount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;
            }
        }

        $totalDiscount = $pointsDiscountAmount + $couponDiscountAmount;
        $discountedSubtotal = $subtotal - $totalDiscount;

        // Calcular Impuestos (IVA 21% por defecto, IGIC 7% para Canarias) sobre el subtotal descontado
        $taxRate = 0.21;
        $taxType = 'IVA (21%)';
        
        $canaryIslands = ['GC', 'TF', 'LP', 'LZ', 'FV', 'EH'];
        if (in_array(strtoupper($address->state), $canaryIslands)) {
            $taxRate = 0.07;
            $taxType = 'IGIC (7%)';
        }
        
        $taxAmount = max(0, $discountedSubtotal * $taxRate);
        $totalWithTax = max(0, $discountedSubtotal + $taxAmount);

        $order = DB::transaction(function () use ($user, $address, $subtotal, $totalDiscount, $taxAmount, $taxType, $totalWithTax, $pointsToUse, $cart, $couponId) {
            // Deduct points
            if ($pointsToUse > 0) {
                $user->points -= $pointsToUse;
                $user->save();
            }

            // Increment coupon usage
            if ($couponId) {
                Coupon::where('id', $couponId)->increment('used_count');
            }

            // Crear el pedido con desglose
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'points_used' => $pointsToUse,
                'coupon_id' => $couponId,
                'discount_amount' => $totalDiscount,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_type' => $taxType,
                'total' => $totalWithTax,
                'status' => 'pending'
            ]);

            foreach ($cart as $item) {
                $product = Product::withoutGlobalScope('parent_only')->find($item['id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                if (!$product->decreaseStock($item['quantity'])) {
                    throw new \Exception("Error al actualizar el stock de {$product->name}");
                }
            }

            // Generar entradas de sorteo
            if (method_exists($order, 'generateRaffleEntries')) {
                $order->generateRaffleEntries();
            }

            return $order;
        });

        // Cargar todas las relaciones necesarias para la factura (PDF y Email)
        $order->load(['items.product', 'user', 'address']);

        // Enviar factura por email (VERSIÓN BLINDADA A PRUEBA DE FALLOS)
        try {
            $apiKey = trim(env('MAIL_PASSWORD'));
            $fromAddress = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $recipient = $user->email;

            // Generar PDF con protección contra errores
            $pdfBase64 = null;
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
                $pdfBase64 = base64_encode($pdf->output());
            } catch (\Exception $pdfError) {
                file_put_contents(storage_path('logs/resend_debug.log'), "[" . date('Y-m-d H:i:s') . "] Error PDF: " . $pdfError->getMessage() . "\n", FILE_APPEND);
            }

            // Diseño Premium
            $html = "
            <div style='background-color: #0a0a0c; color: #ffffff; font-family: sans-serif; padding: 40px; border-radius: 20px; max-width: 600px; margin: auto; border: 1px solid #1a1a1c;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #00d2ff; text-transform: uppercase; font-style: italic; letter-spacing: -1px; margin: 0;'>Soul <span style='color: #ffffff;'>Guild</span></h1>
                </div>
                <div style='background: #121214; padding: 30px; border-radius: 15px; border-left: 4px solid #7000ff;'>
                    <h2 style='margin-top: 0; color: #ffffff;'>¡Confirmado! Pedido #{$order->id}</h2>
                    <p style='color: #a0a0a0; line-height: 1.6;'>Tu arsenal está en camino. " . ($pdfBase64 ? "Adjuntamos tu factura oficial." : "Puedes ver tu factura en tu perfil de usuario.") . "</p>
                    <p style='color: #00d2ff; font-weight: bold; font-size: 18px;'>Total: {$order->total}€</p>
                </div>
            </div>";

            $resendData = [
                'from' => "Soul Guild <$fromAddress>",
                'to' => [$recipient],
                'subject' => "Soul Guild - Pedido #{$order->id}",
                'html' => $html,
            ];

            // Añadir adjunto solo si se generó correctamente
            if ($pdfBase64) {
                $resendData['attachments'] = [
                    [
                        'filename' => "factura_{$order->id}.pdf",
                        'content' => $pdfBase64,
                    ]
                ];
            }

            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->post('https://api.resend.com/emails', $resendData);

            // Si falla con adjunto, reintentar sin él (Fallback de seguridad)
            if (!$response->successful() && $pdfBase64) {
                unset($resendData['attachments']);
                $resendData['subject'] .= " (Confirmación)";
                $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                    ->post('https://api.resend.com/emails', $resendData);
            }

            // LOG DE DEPURACIÓN CRÍTICO (PROTEGIDO)
            try {
                $logMsg = "[" . date('Y-m-d H:i:s') . "] Envío a: $recipient | Status: " . $response->status() . " | Resp: " . $response->body() . "\n";
                file_put_contents(storage_path('logs/resend_debug.log'), $logMsg, FILE_APPEND);
            } catch (\Exception $e) {}

        } catch (\Exception $e) {
            try {
                file_put_contents(storage_path('logs/resend_debug.log'), "[" . date('Y-m-d H:i:s') . "] ERROR CRÍTICO: " . $e->getMessage() . "\n", FILE_APPEND);
            } catch (\Exception $ex) {}
            \Log::error("Error en envío de factura: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado correctamente. Se ha enviado la factura a su correo.',
            'order' => $order
        ], 201);
    }

    public function downloadInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $order->load(['user', 'address', 'items.product']);
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);
        
        return $pdf->download("factura_{$order->id}.pdf");
    }

    public function myOrders()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $orders = Order::where('user_id', auth()->id())
                    ->where('status', '!=', 'cancelled')
                    ->with('items.product', 'address')
                    ->latest()
                    ->paginate(10);
                    
        return response()->json(['orders' => $orders]);
    }

    public function show(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No tienes permiso para ver este pedido'], 403);
        }
        
        return response()->json([
            'order' => $order->load('items.product', 'address')
        ]);
    }

    public function resendInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $order->load(['items.product', 'user', 'address']);
        
        try {
            $apiKey = trim(env('MAIL_PASSWORD'));
            if (empty($apiKey)) {
                throw new \Exception("La API Key de Resend (MAIL_PASSWORD) está vacía en el archivo .env");
            }

            $fromAddress = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $recipient = $order->user->email;

            // Generar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
            $pdfBase64 = base64_encode($pdf->output());

            $html = "
            <div style='background-color: #0a0a0c; color: #ffffff; font-family: sans-serif; padding: 40px; border-radius: 20px; max-width: 600px; margin: auto;'>
                <h1 style='color: #00d2ff;'>Pedido #{$order->id} (Reenvío)</h1>
                <p>Aquí tienes tu factura de Soul Guild.</p>
            </div>";

            $resendData = [
                'from' => "Soul Guild <$fromAddress>",
                'to' => [$recipient],
                'subject' => "Reenvío: Soul Guild - Pedido #{$order->id}",
                'html' => $html,
                'attachments' => [
                    [
                        'filename' => "factura_{$order->id}.pdf",
                        'content' => $pdfBase64,
                    ]
                ]
            ];

            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->post('https://api.resend.com/emails', $resendData);

            // Log del resultado
            $logMsg = "[" . date('Y-m-d H:i:s') . "] REENVÍO a: $recipient | Status: " . $response->status() . " | Body: " . $response->body() . "\n";
            file_put_contents(storage_path('logs/resend_debug.log'), $logMsg, FILE_APPEND);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Correo reenviado con éxito']);
            } else {
                return response()->json(['success' => false, 'error' => $response->json()], $response->status());
            }

        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/resend_debug.log'), "[" . date('Y-m-d H:i:s') . "] ERROR REENVÍO: " . $e->getMessage() . "\n", FILE_APPEND);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
