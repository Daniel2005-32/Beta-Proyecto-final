<?php
$baseDir = 'c:\\Users\\Daniel\\Documents\\Prueba\\Proyecto_final\\frontend\\src';

$files = [
    'views/profile/ProfileView.vue',
    'views/profile/AddressesView.vue',
    'views/ProductsView.vue',
    'views/HomeView.vue',
    'views/checkout/CheckoutView.vue',
    'views/CartView.vue',
    'views/auth/RegisterView.vue',
    'views/auth/LoginView.vue',
    'views/auctions/AuctionsListView.vue',
    'views/auctions/AuctionDetailView.vue',
    'views/admin/DashboardView.vue'
];

foreach ($files as $f) {
    $p = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    if (!file_exists($p)) {
        echo "Skipping $p\n";
        continue;
    }
    $content = file_get_contents($p);
    $content = preg_replace("/const apiBase = 'http:\/\/localhost:8000\/api';\s*/i", "const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';\n", $content);
    file_put_contents($p, $content);
    echo "Replaced in $f\n";
}

// App.vue
$appP = $baseDir . DIRECTORY_SEPARATOR . 'App.vue';
if (file_exists($appP)) {
    $app = file_get_contents($appP);
    $app = str_replace("'http://localhost:8000/api/logout'", "`\${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/logout`", $app);
    file_put_contents($appP, $app);
    echo "Replaced in App.vue\n";
}
?>
