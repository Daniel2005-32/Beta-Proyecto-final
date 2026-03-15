-- ============================================================
-- ARCHIVO: gamer_guild_completo.sql
-- DESCRIPCIÓN: Backup completo de productos y categorías
-- PROYECTO: Soul Guild - Tienda gaming/anime
-- FECHA: 2026-02-25
-- ============================================================

-- ============================================================
-- 1. CATEGORÍAS
-- ============================================================
TRUNCATE TABLE categories;
INSERT INTO categories (id, name, slug, description, image, created_at, updated_at) VALUES
(1, 'Consolas', 'consolas', 'PlayStation, Xbox, Nintendo y consolas de última generación', NULL, NOW(), NOW()),
(2, 'Videojuegos', 'videojuegos', 'Juegos para todas las consolas y plataformas', NULL, NOW(), NOW()),
(3, 'Manga', 'manga', 'Cómics japoneses, novelas ligeras y colecciones', NULL, NOW(), NOW()),
(4, 'Productos Anime', 'productos-anime', 'Figuras, posters, llaveros, ropa y todo tipo de merchandising anime', NULL, NOW(), NOW()),
(5, 'Cosplay', 'cosplay', 'Disfraces, pelucas y accesorios para cosplay', NULL, NOW(), NOW());

-- ============================================================
-- 2. PRODUCTOS REGULARES
-- ============================================================
TRUNCATE TABLE products;

-- ========== CONSOLAS (category_id = 1) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('PlayStation 5', 'ps5', 'La consola de nueva generación con gráficos 4K y SSD ultrarrápido', 499.99, 549.99, 'https://images.unsplash.com/photo-1644571580646-7048372c491a?w=500', 1, 10, 1, 1, 0, NOW(), NOW()),
('Nintendo Switch OLED', 'switch-oled', 'La consola híbrida con pantalla OLED de 7 pulgadas', 339.99, 399.99, 'https://images.unsplash.com/photo-1676261233849-0755de764396?w=500', 1, 5, 1, 1, 0, NOW(), NOW()),
('Xbox Series X', 'xbox-series-x', 'La consola más potente de Microsoft con 12 teraflops', 499.99, 549.99, 'https://images.unsplash.com/photo-1621259182978-fbf931c6f07c?w=500', 1, 3, 0, 1, 0, NOW(), NOW());

-- ========== VIDEOJUEGOS (category_id = 2) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('Elden Ring', 'elden-ring', 'Juego del año. Edición estándar', 59.99, 69.99, 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500', 2, 10, 1, 1, 0, NOW(), NOW()),
('Final Fantasy VII Rebirth', 'ff7-rebirth', 'La esperada continuación', 69.99, 79.99, 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500', 2, 50, 1, 0, 0, NOW(), NOW()),
('The Legend of Zelda: Tears of the Kingdom', 'zelda-totk', 'La aventura épica de Link continúa', 69.99, 79.99, 'https://images.unsplash.com/photo-1616587894289-86480e533129?w=500', 2, 8, 1, 1, 0, NOW(), NOW());

-- ========== MANGA (category_id = 3) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('One Piece Box Set', 'one-piece-box', 'Colección completa tomos 1-23 con estuche especial', 186.99, 219.99, 'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500', 3, 2, 1, 1, 0, NOW(), NOW()),
('Naruto Box Set', 'naruto-box', 'Colección completa tomos 1-27', 199.99, 219.99, 'https://images.unsplash.com/photo-1535311584861-8645c64edc40?w=500', 3, 3, 0, 0, 0, NOW(), NOW()),
('Jujutsu Kaisen Vol. 1-10', 'jujutsu-kaisen', 'Los primeros 10 volúmenes del éxito de ventas', 89.99, 109.99, 'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500', 3, 5, 1, 0, 0, NOW(), NOW());

-- ========== PRODUCTOS ANIME (category_id = 4) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('Figura Goku Ultra Instinct', 'goku-ssj', 'Figura detallada de 20cm con base incluida', 29.99, 42.99, 'https://images.unsplash.com/photo-1765633358966-45a72a11fdaa?w=500', 4, 5, 0, 1, 0, NOW(), NOW()),
('Figura Exclusiva Naruto', 'naruto-exclusive', 'Figura articulada de 25cm Edición limitada', 119.99, 159.99, 'https://images.unsplash.com/photo-1613376023733-0a44915a269c?w=500', 4, 2, 1, 0, 1, NOW(), NOW()),
('Taza Térmica Anime', 'taza-anime', 'Taza con diseño de varios personajes, capacidad 350ml', 14.39, 17.99, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=500', 4, 15, 1, 0, 0, NOW(), NOW()),
('Llavero Anime Surtido', 'llavero-anime', 'Llaveros de diferentes personajes (Pokémon, Dragon Ball, One Piece)', 8.49, 9.99, 'https://images.unsplash.com/photo-1602491453631-e2a5ad90a131?w=500', 4, 30, 0, 0, 0, NOW(), NOW()),
('Almohada Personaje Anime', 'almohada-anime', 'Almohada de 40cm con estampado de personajes', 29.99, 39.99, 'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500', 4, 8, 0, 1, 0, NOW(), NOW());

-- ========== COSPLAY (category_id = 5) ==========
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('Cosplay Anna Yamada', 'annayamada-cosplay', 'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu"', 87.99, 109.99, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTFfFsSLh5Cb-VR-lz16EHcGawo6v0Uo0jUA&s', 5, 3, 1, 1, 0, NOW(), NOW()),
('Cosplay Asuka Langley', 'asuka-cosplay', 'Disfraz de Asuka Langley de Evangelion. Mono de batalla completo', 127.49, 149.99, 'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500', 5, 2, 0, 1, 0, NOW(), NOW());

-- ============================================================
-- 3. PRODUCTOS EXCLUSIVOS (is_exclusive = 1)
-- ============================================================
INSERT INTO products (name, slug, description, price, original_price, image, category_id, stock, featured, trending, is_exclusive, created_at, updated_at) VALUES
('PlayStation 5 Edición 30 Aniversario', 'ps5-30aniversario', 'Edición limitada del 30 aniversario de PlayStation. Incluye consola con diseños exclusivos, mando especial y placa conmemorativa. Solo 100 unidades en Europa.', 299.99, 349.99, 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500', 1, 2, 1, 1, 1, NOW(), NOW()),
('Nintendo Switch Hyrule Edition', 'switch-hyrule', 'Edición especial de The Legend of Zelda. Consola con diseño de Hyrule, dock decorado y funda exclusiva. Incluye el juego Tears of the Kingdom.', 279.99, 329.99, 'https://images.unsplash.com/photo-1616587894289-86480e533129?w=500', 1, 1, 1, 1, 1, NOW(), NOW()),
('Elden Ring Edición Coleccionista', 'elden-ring-coleccionista', 'Incluye estatua de Malenia de 23cm, steelbook, libro de arte y banda sonora. Edición limitada numerada.', 149.99, 179.99, 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500', 2, 1, 1, 1, 1, NOW(), NOW()),
('One Piece Box Set Edición Coleccionista', 'one-piece-coleccionista', 'Caja de coleccionista con los primeros 30 tomos en tapa dura, ilustraciones exclusivas y póster gigante. Incluye número de serie.', 189.99, 229.99, 'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500', 3, 1, 1, 1, 1, NOW(), NOW()),
('Figura Goku Ultra Instinct - Masterlise', 'goku-masterlise', 'Figura de edición limitada de 40cm. Máxima calidad, con base numerada y certificado de autenticidad. Importada directamente de Japón.', 249.99, 299.99, 'https://images.unsplash.com/photo-1608889175123-8ee362201f81?w=500', 4, 1, 1, 1, 1, NOW(), NOW()),
('Estatua Naruto Modo Sabio - Resina', 'naruto-resina', 'Estatua de resina de 35cm pintada a mano. Edición limitada a 500 unidades mundialmente. Incluye base con iluminación LED.', 289.99, 349.99, 'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500', 4, 1, 1, 0, 1, NOW(), NOW()),
('Figura Eren Jaeger Titan Final', 'eren-titan', 'Figura coleccionable de Attack on Titan de 45cm. Incluye cabeza y torso intercambiables. Edición numerada.', 199.99, 239.99, 'https://images.unsplash.com/photo-1541562232579-512a21360020?w=500', 4, 2, 1, 1, 1, NOW(), NOW()),
('Berserk Deluxe Edition Vol. 1-7', 'berserk-deluxe', 'Colección de lujo de Berserk. Incluye los primeros 7 volúmenes en formato gigante, con ilustraciones a color y cubiertas de tela.', 249.99, 299.99, 'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500', 3, 1, 1, 0, 1, NOW(), NOW()),
('Cosplay Armadura de Guts (Berserk)', 'cosplay-guts', 'Disfraz profesional de Guts. Incluye armadura de imitación metal, capa, espada Dragon Slayer y peluca. Hecho a medida bajo pedido.', 299.99, 349.99, 'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500', 5, 1, 1, 1, 1, NOW(), NOW());

-- ============================================================
-- 4. VERIFICAR DATOS INSERTADOS
-- ============================================================
SELECT '✅ CATEGORÍAS:' as '';
SELECT * FROM categories;

SELECT '✅ PRODUCTOS REGULARES:' as '';
SELECT c.name AS categoria, p.name, p.price, p.stock, p.is_exclusive 
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_exclusive = 0
ORDER BY c.name, p.name;

SELECT '🔥 PRODUCTOS EXCLUSIVOS:' as '';
SELECT c.name AS categoria, p.name, p.price, p.stock 
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_exclusive = 1
ORDER BY c.name, p.name;

SELECT '💰 PRODUCTOS CON OFERTA:' as '';
SELECT c.name AS categoria, p.name, p.original_price, p.price, 
       CONCAT(ROUND(((p.original_price - p.price) / p.original_price * 100), 0), '%') AS descuento
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.original_price IS NOT NULL AND p.original_price > p.price
ORDER BY descuento DESC;

SELECT '⚠️ PRODUCTOS CON STOCK BAJO (<=2):' as '';
SELECT c.name AS categoria, p.name, p.stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.stock <= 2 AND p.stock > 0
ORDER BY p.stock;

SELECT '📊 RESUMEN GENERAL:' as '';
SELECT 
    COUNT(*) AS total_productos,
    SUM(CASE WHEN is_exclusive = 1 THEN 1 ELSE 0 END) AS exclusivos,
    SUM(CASE WHEN original_price > price THEN 1 ELSE 0 END) AS ofertas,
    SUM(stock) AS stock_total,
    AVG(price) AS precio_medio
FROM products;

SELECT '🎮 ¡BASE DE DATOS DE Soul GUILD COMPLETADA! 🎮' as '';
