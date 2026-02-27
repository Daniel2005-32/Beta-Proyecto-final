-- ============================================================
-- RESETEAR STOCK DE TODOS LOS PRODUCTOS
-- ============================================================

-- Ver stock actual antes de resetear
SELECT '📦 STOCK ACTUAL:' as '';
SELECT name, stock FROM products ORDER BY stock;

-- Resetear stocks a valores predeterminados
UPDATE products SET stock = 
    CASE 
        -- Consolas
        WHEN slug = 'ps5' THEN 10
        WHEN slug = 'switch-oled' THEN 5
        WHEN slug = 'xbox-series-x' THEN 3
        WHEN slug = 'ps5-30aniversario' THEN 2
        WHEN slug = 'switch-hyrule' THEN 1
        
        -- Videojuegos
        WHEN slug = 'elden-ring' THEN 10
        WHEN slug = 'ff7-rebirth' THEN 50
        WHEN slug = 'zelda-totk' THEN 8
        WHEN slug = 'elden-ring-coleccionista' THEN 1
        
        -- Manga
        WHEN slug = 'one-piece-box' THEN 2
        WHEN slug = 'naruto-box' THEN 3
        WHEN slug = 'jujutsu-kaisen' THEN 5
        WHEN slug = 'one-piece-coleccionista' THEN 1
        WHEN slug = 'berserk-deluxe' THEN 1
        
        -- Productos Anime
        WHEN slug = 'goku-ssj' THEN 5
        WHEN slug = 'naruto-exclusive' THEN 2
        WHEN slug = 'taza-anime' THEN 15
        WHEN slug = 'llavero-anime' THEN 30
        WHEN slug = 'almohada-anime' THEN 8
        WHEN slug = 'goku-masterlise' THEN 1
        WHEN slug = 'naruto-resina' THEN 1
        WHEN slug = 'eren-titan' THEN 2
        
        -- Cosplay
        WHEN slug = 'annayamada-cosplay' THEN 3
        WHEN slug = 'asuka-cosplay' THEN 2
        WHEN slug = 'cosplay-guts' THEN 1
        
        ELSE 10 -- Valor por defecto para cualquier otro producto
    END;

-- Verificar que se actualizó
SELECT '✅ STOCK RESETEADO:' as '';
SELECT name, stock FROM products ORDER BY stock;
