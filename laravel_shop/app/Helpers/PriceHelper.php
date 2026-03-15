<?php

namespace App\Helpers;

class PriceHelper
{
    const IVA_PENINSULA = 21; // 21% para Península
    const IGIC_CANARIAS = 7;   // 7% para Canarias

    /**
     * Obtener el tipo de impuesto según la provincia
     * Busca "GC" (Gran Canaria) y "TF" (Tenerife)
     */
    public static function getTaxRate($province)
    {
        $provinceUpper = strtoupper(trim($province));
        
        // Buscar si la provincia contiene "GC" o "TF"
        if (str_contains($provinceUpper, 'GC') || str_contains($provinceUpper, 'TF')) {
            return self::IGIC_CANARIAS;
        }
        
        // Si no es Canarias, asumimos Península (IVA)
        return self::IVA_PENINSULA;
    }

    /**
     * Calcular precio con impuesto según provincia
     */
    public static function withTax($price, $province)
    {
        $rate = self::getTaxRate($province);
        return $price * (1 + $rate / 100);
    }

    /**
     * Calcular solo el impuesto según provincia
     */
    public static function taxAmount($price, $province)
    {
        $rate = self::getTaxRate($province);
        return $price * ($rate / 100);
    }

    /**
     * Formatear precio con impuesto incluido según provincia
     */
    public static function formatWithTax($price, $province)
    {
        return number_format(self::withTax($price, $province), 2) . '€';
    }

    /**
     * Obtener el nombre del impuesto según provincia
     */
    public static function getTaxName($province)
    {
        $rate = self::getTaxRate($province);
        if ($rate == self::IGIC_CANARIAS) {
            return 'IGIC ' . $rate . '%';
        }
        return 'IVA ' . $rate . '%';
    }

    /**
     * Obtener el texto del impuesto para mostrar
     */
    public static function getTaxText($province)
    {
        $rate = self::getTaxRate($province);
        if ($rate == self::IGIC_CANARIAS) {
            return 'IGIC 7% (Canarias)';
        }
        return 'IVA 21% (Península)';
    }

    // ============================================
    // MÉTODOS PARA COMPATIBILIDAD (con nombres antiguos)
    // ============================================
    
    /**
     * @deprecated Usar getTaxRate() en su lugar
     */
    public static function getTaxRateFromProvince($province)
    {
        return self::getTaxRate($province);
    }
}
