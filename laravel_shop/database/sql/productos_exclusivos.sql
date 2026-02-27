-- ============================================================
-- ARCHIVO: productos_exclusivos.sql
-- DESCRIPCIÓN: Añadir productos EXCLUSIVOS con stock limitado
--              y precios entre 100€ y 300€
-- FECHA: 2026-02-25
-- ============================================================

-- ============================================================
-- INSTRUCCIONES DE USO:
-- ============================================================
-- 1. Abre tu terminal
-- 2. Conéctate a MySQL: mysql -u root -p
-- 3. Selecciona tu base de datos: USE laravel;
-- 4. Ejecuta este archivo: SOURCE database/sql/productos_exclusivos.sql;
-- 5. Para salir de MySQL: EXIT;
-- ============================================================

-- ============================================================
-- 1. PRODUCTOS EXCLUSIVOS POR CATEGORÍA
-- ============================================================

-- ========== CONSOLAS (Ediciones especiales) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'PlayStation 5 Edición 30 Aniversario', 
    'ps5-30aniversario', 
    'Edición limitada del 30 aniversario de PlayStation. Incluye consola con diseños exclusivos, mando especial y placa conmemorativa. Solo 100 unidades en Europa.',
    299.99, 
    349.99,
    'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500',
    id, 
    2, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'ps5-30aniversario');

INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Nintendo Switch Hyrule Edition', 
    'switch-hyrule', 
    'Edición especial de The Legend of Zelda. Consola con diseño de Hyrule, dock decorado y funda exclusiva. Incluye el juego Tears of the Kingdom.',
    279.99, 
    329.99,
    'https://images.unsplash.com/photo-1616587894289-86480e533129?w=500',
    id, 
    1, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'switch-hyrule');

-- ========== COLECCIONISTAS (Figuras de alta gama) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Figura Goku Ultra Instinct - Masterlise', 
    'goku-masterlise', 
    'Figura de edición limitada de 40cm. Máxima calidad, con base numerada y certificado de autenticidad. Importada directamente de Japón.',
    249.99, 
    299.99,
    'https://images.unsplash.com/photo-1608889175123-8ee362201f81?w=500',
    id, 
    1, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'goku-masterlise');

INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Estatua Naruto Modo Sabio - Resina', 
    'naruto-resina', 
    'Estatua de resina de 35cm pintada a mano. Edición limitada a 500 unidades mundialmente. Incluye base con iluminación LED.',
    289.99, 
    349.99,
    'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500',
    id, 
    1, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'naruto-resina');

INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Figura Eren Jaeger Titan Final', 
    'eren-titan', 
    'Figura coleccionable de Attack on Titan de 45cm. Incluye cabeza y torso intercambiables. Edición numerada.',
    199.99, 
    239.99,
    'https://images.unsplash.com/photo-1541562232579-512a21360020?w=500',
    id, 
    2, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'eren-titan');

-- ========== MANGA (Ediciones de coleccionista) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'One Piece Box Set Edición Coleccionista', 
    'one-piece-coleccionista', 
    'Caja de coleccionista con los primeros 30 tomos en tapa dura, ilustraciones exclusivas y póster gigante. Incluye número de serie.',
    189.99, 
    229.99,
    'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500',
    id, 
    1, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'one-piece-coleccionista');

INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Berserk Deluxe Edition Vol. 1-7', 
    'berserk-deluxe', 
    'Colección de lujo de Berserk. Incluye los primeros 7 volúmenes en formato gigante, con ilustraciones a color y cubiertas de tela.',
    249.99, 
    299.99,
    'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500',
    id, 
    1, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'berserk-deluxe');

-- ========== COSPLAY (Disfraces premium) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Cosplay Armadura de Guts (Berserk)', 
    'cosplay-guts', 
    'Disfraz profesional de Guts. Incluye armadura de imitación metal, capa, espada Dragon Slayer y peluca. Hecho a medida bajo pedido.',
    299.99, 
    349.99,
    'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500',
    id, 
    1, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'cosplay'
AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'cosplay-guts');

-- ============================================================
-- 2. VERIFICAR PRODUCTOS EXCLUSIVOS AÑADIDOS
-- ============================================================
SELECT '🔥 PRODUCTOS EXCLUSIVOS AÑADIDOS (STOCK LIMITADO):' as '';

SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.price AS precio,
    CASE 
        WHEN p.original_price IS NOT NULL THEN CONCAT('Antes: ', p.original_price, '€')
        ELSE 'Sin oferta'
    END AS precio_anterior,
    p.stock AS 'Stock (limitado)',
    CASE 
        WHEN p.stock = 1 THEN '⚠️ ÚLTIMA UNIDAD'
        WHEN p.stock <= 2 THEN '⭐ Pocas unidades'
        ELSE 'Disponible'
    END AS estado
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.price >= 100 
  AND p.stock <= 2
ORDER BY p.price DESC;

SELECT '💰 PRODUCTOS CON PRECIO > 100€:' as '';

SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.price AS precio,
    p.stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.price >= 100
ORDER BY p.price DESC;

-- ============================================================
-- 3. RESUMEN DE EXCLUSIVIDAD
-- ============================================================
SELECT '📊 RESUMEN DE COLECCIÓN EXCLUSIVA:' as '';

SELECT 
    COUNT(*) AS total_exclusivos,
    MIN(price) AS precio_min,
    MAX(price) AS precio_max,
    AVG(price) AS precio_medio,
    SUM(stock) AS stock_total
FROM products
WHERE price >= 100;

SELECT '🎮 ¡PRODUCTOS EXCLUSIVOS AÑADIDOS CORRECTAMENTE! 🎮' as '';
