-- ============================================
-- SQL PARA SEMBRAR LA BASE DE DATOS
-- BASE DE DATOS: laravel
-- ============================================

-- 1. LIMPIAR DATOS EXISTENTES (OPCIONAL)
-- Descomenta si quieres limpiar antes de insertar
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE product_reviews;
-- TRUNCATE TABLE order_items;
-- TRUNCATE TABLE orders;
-- TRUNCATE TABLE raffle_entries;
-- TRUNCATE TABLE raffles;
-- TRUNCATE TABLE chat_messages;
-- TRUNCATE TABLE bans;
-- DELETE FROM products;
-- DELETE FROM categories;
-- DELETE FROM users WHERE email != 'danielsmartin2005@gmail.com';
-- SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 2. CATEGORÍAS
-- ============================================
INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES
('Consolas', 'consolas', 'Las mejores consolas de videojuegos', NOW(), NOW()),
('Videojuegos', 'videojuegos', 'Todos los juegos para tus plataformas favoritas', NOW(), NOW()),
('Manga', 'manga', 'Cómics y novelas gráficas japonesas', NOW(), NOW()),
('Productos Anime', 'productos-anime', 'Figuras, accesorios y merchandising de tus animes favoritos', NOW(), NOW()),
('Cosplay', 'cosplay', 'Disfraces y accesorios para cosplay', NOW(), NOW());

-- ============================================
-- 3. USUARIO SUPER ADMINISTRADOR (DANIEL)
-- ============================================
-- Contraseña: luigi2005 (hash generado con bcrypt)
-- El hash corresponde a: password_hash('luigi2005', PASSWORD_BCRYPT)
INSERT INTO users (name, email, password, is_admin, is_super_admin, email_verified_at, created_at, updated_at) VALUES
('Daniel', 'danielsmartin2005@gmail.com', '$2y$12$FQjY7QFUvQ7QFUvQ7QFUvO7QFUvQ7QFUvQ7QFUvQ7QFUvQ7QFUvQ7Q', 1, 1, NOW(), NOW(), NOW());

-- ============================================
-- 4. PRODUCTOS
-- ============================================

-- ============================================
-- CONSOLAS (category_id = 1)
-- ============================================
INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('PlayStation 5', 'ps5', 1, 499.99, NULL, 10, 'https://m.media-amazon.com/images/I/51NbBH89m1L._AC_UF1000,1000_QL80_.jpg', 'La consola de nueva generación con gráficos 4K y SSD ultrarrápido', 1, 1, 0, 0, NOW(), NOW()),
('Nintendo Switch OLED', 'switch-oled', 1, 339.99, 399.99, 5, 'https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcSNQUxZEb2QMrQoU3tCjDU3DVBGY1IivMQITShZ2BnGfeM29MPCvAPZbiBbusvJD4Nxp5nxtMlfbRtmncXPEmXCh7pDkP9YeTD2FyJTiA44W4q6OHoXGRzp0baxfb7o0RJaZAF4jw&usqp=CAc', 'La consola híbrida con pantalla OLED de 7 pulgadas', 1, 1, 0, 0, NOW(), NOW()),
('Nintendo Switch Hyrule Edition', 'switch-hyrule', 1, 220.00, 243.00, 1, 'https://img.pccomponentes.com/articles/1084/10843258/1913-nintendo-switch-lite-hyrule-edition-12-meses-nintendo-switch-online-caracteristicas.jpg', 'Edición especial de The Legend of Zelda. Consola con diseño de Hyrule, dock decorado y funda exclusiva. Incluye el juego Tears of the Kingdom.', 1, 1, 1, 0, NOW(), NOW()),
('PlayStation 5 Edición 30 Aniversario', 'ps5-30aniversario', 1, 850.00, 900.00, 1, 'https://i.ytimg.com/vi/BWr2P9Udz7M/maxresdefault.jpg', 'Edición limitada del 30 aniversario de PlayStation. Incluye consola con diseños exclusivos, mando especial y placa conmemorativa.', 1, 1, 1, 0, NOW(), NOW());

-- ============================================
-- VIDEOJUEGOS (category_id = 2)
-- ============================================
INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('Elden Ring', 'elden-ring', 2, 59.49, 69.99, 10, 'https://periodismo.ull.es/wp-content/uploads/2022/04/Se-rumorea-que-Elden-Ring-realizara-proximamente-una-nueva-prueba.jpg', 'Juego del año. Edición estándar.', 1, 1, 0, 0, NOW(), NOW()),
('Final Fantasy VII Rebirth', 'ff7-rebirth', 2, 69.99, NULL, 50, 'https://cdn1.epicgames.com/offer/e9a679451d094c1ba3d008b6a01adec5/EGS_FINALFANTASYVIIREBIRTH_SquareEnix_S1_2560x1440-e254f978084058f898118dc49728d04c', 'La esperada continuación.', 1, 1, 0, 0, NOW(), NOW()),
('Super Mario Galaxy 1 + 2', 'mario-galaxy', 2, 69.99, NULL, 5, 'https://m.media-amazon.com/images/I/810UZa3-MpL._UF1000,1000_QL80_.jpg', 'Los dos juegos de las mejores sagas de Super Mario', 1, 1, 0, 0, NOW(), NOW());

-- ============================================
-- MANGA (category_id = 3)
-- ============================================
INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('Dragon Ball Box Set', 'dragon-ball-z-box', 3, 200.00, 239.99, 5, 'https://jumpichiban.com/cdn/shop/files/DragonBall-Complete42VolumeSetDoubleCoverBox_JumpComics_3.jpg?v=1746966724&width=1000', 'Colección completa tomos 1-42 del manga de Dragon Ball original', 1, 1, 1, 0, NOW(), NOW()),
('Berserk Deluxe Edition Vol. 1-7', 'berserk-deluxe', 3, 220.00, 253.00, 1, 'https://m.media-amazon.com/images/I/917hnLqEpTL._AC_UF1000,1000_QL80_.jpg', 'Colección de lujo de Berserk. Incluye los primeros 7 volúmenes en formato gigante, con ilustraciones a color y cubiertas de tela.', 1, 1, 1, 0, NOW(), NOW()),
('10 tomos de Inazuma Eleven', 'inazuma', 3, 70.00, 85.00, 10, 'https://www.hit-japan.com/book24/190405020.JPG', '10 tomos completos de Inazuma Eleven el mejor anime/manga de fútbol, Blue lock es una mierda', 0, 0, 0, 0, NOW(), NOW()),
('Pack Manga Starter Set Super Mario Volumen 1 a 3', 'mario-manga', 3, 20.00, 25.00, 10, 'https://kurogami.com/med/img/productos/35/20/portada-del-pack-manga-super-mario.png', 'Tres tomos del manga de Super Mario', 1, 0, 0, 0, NOW(), NOW());

-- ============================================
-- PRODUCTOS ANIME (category_id = 4)
-- ============================================
INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('Figura Goku SSJ', 'goku-ssj', 4, 90.00, NULL, 5, 'https://www.raccoongames.es/img/productos/2020/11/25/dragon-ball-z-goku-ss-diorama-the-brush-overseas-super-master-stars-piece-banpresto%201.jpg', 'Figura detallada de 20cm de la famosa escena de la primera vez que se transforma Goku en SSJ', 1, 1, 0, 0, NOW(), NOW()),
('Figura de Naruto modo Sabio', 'naruto-sabio', 4, 70.00, NULL, 5, 'https://comicstores.es/imagenes_grandes/4983164/498316428940.JPG', 'Figura de Naruto de 22cm en su estado Sabio', 1, 1, 0, 0, NOW(), NOW()),
('Taza Térmica de JJK', 'taza-anime-jjk', 4, 13.00, NULL, 15, 'https://encrypted-tbn0.gstatic.com/shopping?q=tbn:ANd9GcRx24t0g1HGcuHk3JrlyB2WYXvsz47TaVpMEcOCPnhimcDV3HR7yPiWIGSlIbXFwvwlppIsljXYQCROG1kgFCpMpKgEA5wRCkLqhXOFNqDP4sh86iRVaSO15l_TkTSpwLdtUuuLG_5-fg&usqp=CAc', 'Taza con diseño de Itadori y Sukuna, capacidad 350ml', 0, 1, 0, 0, NOW(), NOW()),
('Llavero Surtido de Danger in my Heart', 'llavero-anime-danger', 4, 3.59, NULL, 30, 'https://ae01.alicdn.com/kf/S09b5357451c343f58e66de13b8cb8c02Y.jpg', 'Llaveros de los personajes de Ichikawa y Yamada del anime Danger in my Heart', 0, 0, 0, 0, NOW(), NOW()),
('Figura Goku Ultra Instinct - Masterlise', 'goku-masterlise', 4, 70.00, 80.00, 1, 'https://tienda-dragon-ball.com/wp-content/uploads/2024/08/figura-de-goku-ultra-instinto-1.webp', 'Figura de edición limitada de 33cm. Máxima calidad, con base numerada y certificado de autenticidad. Importada directamente de Japón.', 1, 1, 1, 0, NOW(), NOW()),
('Figura de Ichikawa y Yamada', 'figura-ichikawa-yamada', 4, 50.00, 60.00, 1, 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhWMXtqfWCVfSoVGDKn8gjm08qEyZZLB_62-jwzCA8G3r5_mPEvtXLXFmhSGLivbbM5ZR2cmjtcFfmnYXJBp_gTk-WDEIG6KavB7FZ67_MBmt57BMwjZIgjTfXYw-GxTzSr2TEmq0yydkt8/s16000-rw/27.jpg', 'Figura de Ichikawa y Yamada de uno de los momentos mas emotivos del anime', 1, 1, 1, 0, NOW(), NOW()),
('Figura Eren Jaeger', 'eren', 4, 800.00, 860.00, 1, 'https://yardratshop.com/wp-content/uploads/2025/04/eren-jaeger-diorama-hertz-studio-attack-on-titan4.jpg', 'Figura coleccionable de Attack on Titan de Eren Jaegar de 90cm.', 1, 1, 1, 0, NOW(), NOW());

-- ============================================
-- COSPLAY (category_id = 5)
-- ============================================
INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('Cosplay Anna Yamada', 'annayamada-cosplay', 5, 89.99, NULL, 3, 'https://i.redd.it/my-anna-yamada-cosplay-from-the-dangers-in-my-heart-v0-7r1cex3e94re1.jpg?width=2316&format=pjpg&auto=webp&s=377119061f21d14b352b460166e3d5df9bad4ff7', 'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu"', 1, 1, 0, 0, NOW(), NOW()),
('Cosplay Armadura de Guts (Berserk)', 'cosplay-guts', 5, 299.99, 349.99, 1, 'https://i.redd.it/wi6l7goktixa1.jpg', 'Disfraz profesional de Guts. Incluye armadura de imitación metal, capa, espada Dragon Slayer y peluca. Hecho a medida bajo pedido.', 1, 1, 1, 0, NOW(), NOW()),
('Cosplay zero two', 'zero-two', 5, 100.00, NULL, 3, 'https://i.pinimg.com/736x/bc/a3/80/bca380011a5a682a9e7766c1d7c2db82.jpg', 'Cosplay de zero two del anime de Darling in the Franx', 1, 1, 0, 0, NOW(), NOW()),
('Cosplay de Igris', 'igris', 5, 200.00, 250.00, 5, 'https://i.redd.it/my-blood-red-commander-igris-cosplay-game-version-v0-zrx40f3kpcrc1.jpg?width=4016&format=pjpg&auto=webp&s=8665949fe51b172e02ec01b74f059a0cc460cbcf', 'Cosplay de Igris una de las sombras mas fuertes de Sung Jin-Woo de Solo Leveling', 1, 1, 0, 0, NOW(), NOW()),
('Cosplay de Luigi', 'cosplay-luigi', 5, 45.00, 50.00, 5, 'https://raw.githubusercontent.com/Daniel2005-32/Beta-Proyecto-final/main/unnamed.jpg', 'Cosplay de Luigi version realista del fontanero', 1, 0, 0, 0, NOW(), NOW());

-- ============================================
-- PELUCHE (category_id = 6 - Productos)
-- ============================================
-- Primero insertamos la categoría Productos si no existe
INSERT INTO categories (name, slug, description, created_at, updated_at) 
SELECT 'Productos', 'productos', 'Todo tipo de productos relacionados con el mundo gamer', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'productos');

INSERT INTO products (name, slug, category_id, price, original_price, stock, image, description, featured, trending, is_exclusive, is_in_auction, created_at, updated_at) VALUES
('Peluche de Luigi', 'peluche-luigi', (SELECT id FROM categories WHERE slug = 'productos'), 20.00, NULL, 20, 'https://dam.elcorteingles.es/producto/www-001008341136573-00.jpg?impolicy=Resize&width=1200&height=1200', 'Peluche Luigi 30cm Super Mario Bros Nintendo', 1, 1, 0, 0, NOW(), NOW());

-- ============================================
-- 5. VERIFICACIÓN DE DATOS
-- ============================================
-- Mostrar resumen de la inserción
SELECT 'CATEGORÍAS' AS 'TIPO', COUNT(*) AS 'TOTAL' FROM categories
UNION ALL
SELECT 'PRODUCTOS', COUNT(*) FROM products
UNION ALL
SELECT 'USUARIOS (TOTAL)', COUNT(*) FROM users
UNION ALL
SELECT 'SUPER ADMIN', COUNT(*) FROM users WHERE is_super_admin = 1;

# 1. Guardar el archivo
nano seed_data.sql
# (Pega todo el contenido y guarda con Ctrl+O, luego Ctrl+X)

# 2. Importar a la base de datos
mysql -u root -p laravel < seed_data.sql
# (Te pedirá contraseña, en tu .env está vacía, solo presiona Enter)

# 3. Verificar que se insertó correctamente
mysql -u root -p laravel -e "SELECT COUNT(*) FROM products; SELECT COUNT(*) FROM categories; SELECT name, email, is_super_admin FROM users WHERE email = 'danielsmartin2005@gmail.com';"


# Entrar a MySQL
mysql -u root -p

# Crear la base de datos (si no existe)
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Salir
EXIT;

# Desde la raíz de tu proyecto
php artisan migrate

# Asegúrate de tener el archivo seed_data.sql en la raíz del proyecto
mysql -u root -p laravel < seed_data.sql

php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear