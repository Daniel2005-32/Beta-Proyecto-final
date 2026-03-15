const fs = require('fs');
const path = require('path');

const baseDir = 'c:\\Users\\Daniel\\Documents\\Prueba\\Proyecto_final\\frontend\\src';

const files = [
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

files.forEach(f => {
  const p = path.join(baseDir, f);
  if (!fs.existsSync(p)) {
      console.log("Skipping", p);
      return;
  }
  let content = fs.readFileSync(p, 'utf8');
  // Use Regex to handle trailing spaces safely
  content = content.replace(/const apiBase = 'http:\/\/localhost:8000\/api';\s*/g, "const apiBase = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';\n");
  fs.writeFileSync(p, content, 'utf8');
  console.log("Replaced API URL in:", f);
});

// App.vue
const appP = path.join(baseDir, 'App.vue');
if (fs.existsSync(appP)) {
    let app = fs.readFileSync(appP, 'utf8');
    app = app.replace("'http://localhost:8000/api/logout'", "`\${import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'}/logout`")
    fs.writeFileSync(appP, app, 'utf8');
    console.log("Replaced API URL in: App.vue");
}
