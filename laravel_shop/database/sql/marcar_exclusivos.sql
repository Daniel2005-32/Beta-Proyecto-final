-- ============================================================
-- MARCAR PRODUCTOS COMO EXCLUSIVOS
-- ============================================================

-- Ver productos antes de marcar
SELECT '📦 PRODUCTOS QUE SERÁN EXCLUSIVOS:' as '';
SELECT name, price, stock FROM products WHERE slug IN (
    'ps5-30aniversario',
    'switch-hyrule',
    'goku-masterlise',
    'naruto-resina',
    'eren-titan',
    'one-piece-coleccionista',
    'berserk-deluxe',
    'cosplay-guts'
);

-- Marcar como exclusivos
UPDATE products SET 
    is_exclusive = true,
    featured = true,
    updated_at = NOW()
WHERE slug IN (
    'ps5-30aniversario',
    'switch-hyrule',
    'goku-masterlise',
    'naruto-resina',
    'eren-titan',
    'one-piece-coleccionista',
    'berserk-deluxe',
    'cosplay-guts'
);

-- Verificar
SELECT '✅ PRODUCTOS MARCADOS COMO EXCLUSIVOS:' as '';
SELECT name, price, stock, is_exclusive FROM products WHERE is_exclusive = true;
