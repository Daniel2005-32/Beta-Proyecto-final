-- ============================================================
-- ARCHIVO: tienda_gamer_guild.sql
-- DESCRIPCIÓN: Script completo para crear y poblar la base de datos
--              de la tienda Soul Guild con categorías fusionadas
-- FECHA: 2026-02-25
-- ============================================================

-- ============================================================
-- INSTRUCCIONES DE USO:
-- ============================================================
-- 1. Abre tu terminal
-- 2. Conéctate a MySQL: mysql -u root -p
-- 3. Selecciona tu base de datos: USE laravel;
-- 4. Ejecuta este archivo: SOURCE database/sql/tienda_gamer_guild.sql;
-- 5. Para salir de MySQL: EXIT;
-- ============================================================

-- ============================================================
-- 1. LIMPIAR TABLAS EXISTENTES (opcional - ¡CUIDADO!)
-- ============================================================
-- Descomenta las siguientes líneas SOLO si quieres borrar todo y empezar de cero
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE order_items;
-- TRUNCATE TABLE orders;
-- TRUNCATE TABLE bids;
-- TRUNCATE TABLE auctions;
-- TRUNCATE TABLE products;
-- TRUNCATE TABLE categories;
-- TRUNCATE TABLE users;
-- SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 2. CREAR CATEGORÍAS (VERSIÓN FUSIONADA)
-- ============================================================
-- Insertamos las 5 categorías principales
-- Nota: Productos Anime fusiona Figuras + Merchandising

INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES
('Consolas', 'consolas', 'PlayStation, Xbox, Nintendo y consolas de última generación', NOW(), NOW()),
('Videojuegos', 'videojuegos', 'Juegos para todas las consolas y plataformas', NOW(), NOW()),
('Manga', 'manga', 'Cómics japoneses, novelas ligeras y colecciones', NOW(), NOW()),
('Productos Anime', 'productos-anime', 'Figuras, posters, llaveros, ropa y todo tipo de merchandising anime', NOW(), NOW()),
('Cosplay', 'cosplay', 'Disfraces, pelucas y accesorios para cosplay', NOW(), NOW());

-- Mostrar las categorías creadas
SELECT '✅ CATEGORÍAS CREADAS:' as '';
SELECT * FROM categories ORDER BY id;

-- ============================================================
-- 3. INSERTAR PRODUCTOS POR CATEGORÍA
-- ============================================================

-- ========== 3.1 CONSOLAS ==========
-- Obtenemos el ID de consolas (asumimos que es 1, pero usamos subconsulta por seguridad)
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'PlayStation 5', 
    'ps5', 
    'La consola de nueva generación con gráficos 4K y SSD ultrarrápido', 
    499.99, 
    'https://images.unsplash.com/photo-1644571580646-7048372c491a?w=500',
    id, 
    10, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Nintendo Switch OLED', 
    'switch-oled', 
    'La consola híbrida con pantalla OLED de 7 pulgadas', 
    349.99, 
    'https://images.unsplash.com/photo-1676261233849-0755de764396?w=500',
    id, 
    5, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Xbox Series X', 
    'xbox-series-x', 
    'La consola más potente de Microsoft con 12 teraflops', 
    499.99, 
    'https://images.unsplash.com/photo-1621259182978-fbf931c6f07c?w=500',
    id, 
    3, 
    0, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'consolas';

-- ========== 3.2 VIDEOJUEGOS ==========
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Elden Ring', 
    'elden-ring', 
    'Juego del año. Edición estándar', 
    59.99, 
    'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500',
    id, 
    10, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'videojuegos';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Final Fantasy VII Rebirth', 
    'ff7-rebirth', 
    'La esperada continuación', 
    69.99, 
    'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500',
    id, 
    50, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'videojuegos';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'The Legend of Zelda: Tears of the Kingdom', 
    'zelda-totk', 
    'La aventura épica de Link continúa', 
    69.99, 
    'https://images.unsplash.com/photo-1616587894289-86480e533129?w=500',
    id, 
    8, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'videojuegos';

-- ========== 3.3 MANGA ==========
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'One Piece Box Set', 
    'one-piece-box', 
    'Colección completa tomos 1-23 con estuche especial', 
    189.99, 
    'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500',
    id, 
    2, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Naruto Box Set', 
    'naruto-box', 
    'Colección completa tomos 1-27', 
    199.99, 
    'https://images.unsplash.com/photo-1535311584861-8645c64edc40?w=500',
    id, 
    3, 
    0, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Jujutsu Kaisen Vol. 1-10', 
    'jujutsu-kaisen', 
    'Los primeros 10 volúmenes del éxito de ventas', 
    89.99, 
    'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500',
    id, 
    5, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'manga';

-- ========== 3.4 PRODUCTOS ANIME (FIGURAS + MERCHANDISING) ==========
-- Antiguas figuras
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Figura Goku Ultra Instinct', 
    'goku-ssj', 
    'Figura detallada de 20cm con base incluida', 
    29.99, 
    'https://images.unsplash.com/photo-1765633358966-45a72a11fdaa?w=500',
    id, 
    5, 
    0, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Figura Exclusiva Naruto', 
    'naruto-exclusive', 
    'Edición limitada. Figura articulada de 25cm', 
    120.00, 
    'https://images.unsplash.com/photo-1613376023733-0a44915a269c?w=500',
    id, 
    2, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime';

-- Antiguo merchandising
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Taza Térmica Anime', 
    'taza-anime', 
    'Taza con diseño de varios personajes, capacidad 350ml', 
    14.99, 
    'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=500',
    id, 
    15, 
    1, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Llavero Anime Surtido', 
    'llavero-anime', 
    'Llaveros de diferentes personajes (Pokémon, Dragon Ball, One Piece)', 
    8.99, 
    'https://images.unsplash.com/photo-1602491453631-e2a5ad90a131?w=500',
    id, 
    30, 
    0, 
    0, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Almohada Personaje Anime', 
    'almohada-anime', 
    'Almohada de 40cm con estampado de personajes', 
    29.99, 
    'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500',
    id, 
    8, 
    0, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'productos-anime';

-- ========== 3.5 COSPLAY ==========
INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Cosplay Anna Yamada', 
    'annayamada-cosplay', 
    'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu"', 
    89.99, 
    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTFfFsSLh5Cb-VR-lz16EHcGawo6v0Uo0jUA&s',
    id, 
    3, 
    1, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'cosplay';

INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
SELECT 
    'Cosplay Asuka Langley', 
    'asuka-cosplay', 
    'Disfraz de Asuka Langley de Evangelion. Mono de batalla completo', 
    129.99, 
    'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500',
    id, 
    2, 
    0, 
    1, 
    NOW(), 
    NOW()
FROM categories WHERE slug = 'cosplay';

-- ============================================================
-- 4. VERIFICAR DATOS INSERTADOS
-- ============================================================

SELECT '✅ RESUMEN DE CATEGORÍAS:' as '';
SELECT 
    c.name AS categoria,
    c.slug,
    COUNT(p.id) AS total_productos
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
GROUP BY c.id
ORDER BY c.id;

SELECT '✅ PRODUCTOS POR CATEGORÍA:' as '';
SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.price AS precio,
    p.stock
FROM products p
JOIN categories c ON p.category_id = c.id
ORDER BY c.name, p.name;

SELECT '✅ TOTALES:' as '';
SELECT 
    (SELECT COUNT(*) FROM categories) AS total_categorias,
    (SELECT COUNT(*) FROM products) AS total_productos,
    (SELECT SUM(stock) FROM products) AS stock_total;

-- ============================================================
-- 5. CONSULTAS ÚTILES PARA MANTENIMIENTO
-- ============================================================

-- Productos con stock bajo (< 5)
SELECT '⚠️ PRODUCTOS CON STOCK BAJO:' as '';
SELECT 
    c.name AS categoria,
    p.name AS producto,
    p.stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.stock < 5
ORDER BY p.stock;

-- Productos destacados
SELECT '⭐ PRODUCTOS DESTACADOS:' as '';
SELECT 
    p.name,
    p.price,
    c.name AS categoria
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.featured = 1;

-- Productos en tendencia
SELECT '🔥 PRODUCTOS EN TENDENCIA:' as '';
SELECT 
    p.name,
    p.price,
    c.name AS categoria
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.trending = 1;

-- ============================================================
-- 6. COMANDOS DE MANTENIMIENTO (comentados por seguridad)
-- ============================================================

-- -- ACTUALIZAR PRECIO
-- UPDATE products SET price = 449.99 WHERE slug = 'ps5';

-- -- ACTUALIZAR STOCK
-- UPDATE products SET stock = 8 WHERE slug = 'naruto-exclusive';

-- -- ELIMINAR UN PRODUCTO
-- DELETE FROM products WHERE slug = 'nombre-producto';

-- -- AÑADIR UN NUEVO PRODUCTO
-- INSERT INTO products (name, slug, description, price, image, category_id, stock, featured, trending, created_at, updated_at)
-- VALUES ('Nuevo Producto', 'nuevo-producto', 'Descripción', 19.99, 'url-imagen', 1, 10, 0, 0, NOW(), NOW());

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
SELECT '🎮 ¡BASE DE DATOS DE Soul GUILD CREADA CORRECTAMENTE! 🎮' as '';
