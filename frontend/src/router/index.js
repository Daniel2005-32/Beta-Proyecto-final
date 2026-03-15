import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import ProductsView from '../views/ProductsView.vue';
import LoginView from '../views/auth/LoginView.vue';
import RegisterView from '../views/auth/RegisterView.vue';
import CartView from '../views/CartView.vue';
import ProfileView from '../views/profile/ProfileView.vue';
import AddressesView from '../views/profile/AddressesView.vue';
import AuctionsListView from '../views/auctions/AuctionsListView.vue';
import AuctionDetailView from '../views/auctions/AuctionDetailView.vue';
import CheckoutView from '../views/checkout/CheckoutView.vue';
import DashboardView from '../views/admin/DashboardView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/cart',
      name: 'cart',
      component: CartView
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView
    },
    {
      path: '/profile/addresses',
      name: 'addresses',
      component: AddressesView
    },
    {
      path: '/auctions',
      name: 'auctions',
      component: AuctionsListView
    },
    {
      path: '/auctions/:id',
      name: 'auction-detail',
      component: AuctionDetailView
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: CheckoutView
    },
    {
      path: '/admin',
      name: 'admin',
      component: DashboardView
    }
  ]
});

export default router;
