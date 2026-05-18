<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return response()->json(['coupons' => $coupons]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Capar porcentaje al 10% si es tipo percentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 10) {
            $validated['value'] = 10;
        }

        // Forzar un solo uso y sin mínimo de compra
        $validated['usage_limit'] = 1;
        $validated['min_purchase'] = 0;
        $validated['used_count'] = 0;
        $validated['is_active'] = true;

        $coupon = Coupon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cupón creado correctamente',
            'coupon' => $coupon
        ], 201);
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return response()->json([
            'success' => true,
            'message' => 'Estado del cupón actualizado',
            'coupon' => $coupon
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cupón eliminado'
        ]);
    }
}
