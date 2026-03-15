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
    
    // Safety fallback logic if string doesn't end with /api
    $safeString = "const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';";

    // Match previous edit
    $content = preg_replace("/const apiBase = import\.meta\.env\.VITE_API_URL \|\| 'http:\/\/localhost:8000\/api';\s*/i", "$safeString\n", $content);
    file_put_contents($p, $content);
    echo "Replaced in $f\n";
}

// App.vue
$appP = $baseDir . DIRECTORY_SEPARATOR . 'App.vue';
if (file_exists($appP)) {
    $app = file_get_contents($appP);
    
    // Replace the templating in logout method
    $safeReplace = "const base = import.meta.env.VITE_API_URL; const apiBase = base ? (base.endsWith('/api') ? base : base + '/api') : 'http://localhost:8000/api';\n        await axios.post(`\${apiBase}/logout`";
    
    $app = preg_replace("/await axios\.post\(`\\\${import\.meta\.env\.VITE_API_URL \|\| 'http:\/\/localhost:8000\/api'}\/logout`/i", $safeReplace, $app);
    file_put_contents($appP, $app);
    echo "Replaced in App.vue\n";
}
?>
