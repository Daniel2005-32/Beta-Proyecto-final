-- ============================================================
-- ARCHIVO: ofertas_seleccionadas.sql
-- DESCRIPCIÓN: Aplicar ofertas SOLO a 3 productos seleccionados
-- PRODUCTOS: Nintendo Switch OLED, Elden Ring, One Piece Box Set
-- FECHA: 2026-02-25
-- ============================================================

-- ============================================================
-- INSTRUCCIONES DE USO:
-- ============================================================
-- 1. Abre tu terminal
-- 2. Conéctate a MySQL: mysql -u root -p
-- 3. Selecciona tu base de datos: USE laravel;
-- 4. Ejecuta este archivo: SOURCE database/sql/ofertas_seleccionadas.sql;
-- 5. Para salir de MySQL: EXIT;
-- ============================================================

-- ============================================================
-- 1. VER ESTADO ACTUAL DE LOS PRODUCTOS (ANTES DE LA OFERTA)
-- ============================================================
SELECT '📦 ESTADO ACTUAL DE LOS PRODUCTOS:' as '';

SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.price AS precio_actual,
    p.original_price AS precio_anterior,
    p.stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.slug IN ('switch-oled', 'elden-ring', 'one-piece-box');

-- ============================================================
-- 2. APLICAR OFERTAS (15% de descuento a cada uno)
-- ============================================================

-- 🎮 Nintendo Switch OLED (15% descuento)
-- Precio original: 399.99€ -> Precio oferta: 339.99€
UPDATE products 
SET 
    original_price = 399.99,
    price = 339.99,
    featured = 1,
    trending = 1,
    updated_at = NOW()
WHERE slug = 'switch-oled';

-- ⚔️ Elden Ring (15% descuento)
-- Precio original: 69.99€ -> Precio oferta: 59.49€
UPDATE products 
SET 
    original_price = 69.99,
    price = 59.49,
    featured = 1,
    trending = 1,
    updated_at = NOW()
WHERE slug = 'elden-ring';

-- 📚 One Piece Box Set (15% descuento)
-- Precio original: 219.99€ -> Precio oferta: 186.99€
UPDATE products 
SET 
    original_price = 219.99,
    price = 186.99,
    featured = 1,
    trending = 1,
    updated_at = NOW()
WHERE slug = 'one-piece-box';

-- ============================================================
-- 3. VERIFICAR QUE LAS OFERTAS SE APLICARON CORRECTAMENTE
-- ============================================================
SELECT '✅ OFERTAS APLICADAS CORRECTAMENTE:' as '';

SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.original_price AS 'Precio original',
    p.price AS 'Precio oferta',
    CONCAT(ROUND(((p.original_price - p.price) / p.original_price * 100), 0), '%') AS descuento,
    p.stock,
    CASE 
        WHEN p.featured = 1 THEN '⭐ Sí' 
        ELSE 'No' 
    END AS destacado
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.slug IN ('switch-oled', 'elden-ring', 'one-piece-box');

-- ============================================================
-- 4. SI ALGÚN PRODUCTO NO EXISTE, CREARLO CON OFERTA
-- ============================================================

-- Nintendo Switch OLED (si no existe)
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Nintendo Switch OLED', 
    'switch-oled', 
    'La consola híbrida con pantalla OLED de 7 pulgadas', 
    339.99, 
    399.99,
    'https://images.unsplash.com/photo-1676261233849-0755de764396?w=500',
    id, 
    5, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'switch-oled');

-- Elden Ring (si no existe)
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Elden Ring', 
    'elden-ring', 
    'Juego del año. Edición estándar', 
    59.49, 
    69.99,
    'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500',
    id, 
    10, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'videojuegos'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'elden-ring');

-- One Piece Box Set (si no existe)
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'One Piece Box Set', 
    'one-piece-box', 
    'Colección completa tomos 1-23 con estuche especial', 
    186.99, 
    219.99,
    'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500',
    id, 
    2, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'one-piece-box');

-- ============================================================
-- 5. RESUMEN FINAL DE OFERTAS
-- ============================================================
SELECT '🔥 RESUMEN DE OFERTAS ACTIVAS (15% DESCUENTO):' as '';

SELECT 
    p.name AS producto,
    CONCAT(p.original_price, '€ → ', p.price, '€') AS oferta,
    CONCAT(ROUND(((p.original_price - p.price) / p.original_price * 100), 0), '%') AS descuento
FROM products p
WHERE p.slug IN ('switch-oled', 'elden-ring', 'one-piece-box');

SELECT '🎮 ¡OFERTAS APLICADAS CORRECTAMENTE! 🎮' as '';
