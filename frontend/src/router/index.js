import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
const ProductsView = () => import('../views/ProductsView.vue');
const ProductDetailView = () => import('../views/ProductDetailView.vue');
const LoginView = () => import('../views/auth/LoginView.vue');
const RegisterView = () => import('../views/auth/RegisterView.vue');
const CartView = () => import('../views/CartView.vue');
const ProfileView = () => import('../views/profile/ProfileView.vue');
const AddressesView = () => import('../views/profile/AddressesView.vue');
const AuctionsListView = () => import('../views/auctions/AuctionsListView.vue');
const AuctionDetailView = () => import('../views/auctions/AuctionDetailView.vue');
const RaffleListView = () => import('../views/raffles/RaffleListView.vue');
const RaffleDetailView = () => import('../views/raffles/RaffleDetailView.vue');
const CheckoutView = () => import('../views/checkout/CheckoutView.vue');
const CreateAuctionView = () => import('../views/auctions/CreateAuctionView.vue');
const DashboardView = () => import('../views/admin/DashboardView.vue');
const GamesView = () => import('../views/games/GamesView.vue');
const WishlistView = () => import('../views/WishlistView.vue');

const SupportView = () => import('../views/SupportView.vue');
const ForgotPasswordView = () => import('../views/auth/ForgotPasswordView.vue');
const ResetPasswordView = () => import('../views/auth/ResetPasswordView.vue');


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { title: 'Inicio' }
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsView,
      meta: { title: 'Catálogo de Productos' }
    },
    {
      path: '/products/:id',
      name: 'product-detail',
      component: ProductDetailView,
      meta: { title: 'Detalle del Producto' }
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { title: 'Iniciar Sesión' }
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { title: 'Registro' }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPasswordView,
      meta: { title: 'Recuperar Contraseña' }
    },
    {
      path: '/reset-password/:token',
      name: 'reset-password',
      component: ResetPasswordView,
      meta: { title: 'Nueva Contraseña' }
    },
    {
      path: '/cart',
      name: 'cart',
      component: CartView,
      meta: { title: 'Tu Carrito' }
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
      meta: { title: 'Tu Perfil' }
    },
    {
      path: '/profile/addresses',
      name: 'addresses',
      component: AddressesView,
      meta: { title: 'Tus Direcciones' }
    },
    {
      path: '/auctions',
      name: 'auctions',
      component: AuctionsListView,
      meta: { title: 'Subastas Activas' }
    },
    {
      path: '/auctions/:id',
      name: 'auction-detail',
      component: AuctionDetailView,
      meta: { title: 'Detalle de Subasta' }
    },
    {
      path: '/auctions/create',
      name: 'auction-create',
      component: CreateAuctionView,
      meta: { title: 'Crear Subasta' }
    },
    {
      path: '/raffles',
      name: 'raffles',
      component: RaffleListView,
      meta: { title: 'Sorteos' }
    },
    {
      path: '/raffles/:id',
      name: 'raffle-detail',
      component: RaffleDetailView,
      meta: { title: 'Detalle de Sorteo' }
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: CheckoutView,
      meta: { title: 'Finalizar Compra' }
    },
    {
      path: '/admin',
      name: 'admin',
      component: DashboardView,
      meta: { title: 'Panel de Administración' }
    },
    {
      path: '/games',
      name: 'games',
      component: GamesView,
      meta: { title: 'Soul Guild | Games' }
    },
    {
      path: '/wishlist',
      name: 'wishlist',
      component: WishlistView,
      meta: { title: 'Lista de Deseos', requiresAuth: true }
    },

    {
      path: '/support',
      name: 'support',
      component: SupportView,
      meta: { title: 'Centro de Soporte', requiresAuth: true }
    },

    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFoundView.vue'),
      meta: { title: '404 - Not Found' }
    }
  ]
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  
  if (to.meta.requiresAuth && !token) {
    next('/register');
  } else {
    const title = to.meta.title ? `${to.meta.title} | Soul Guild` : 'Soul Guild';
    document.title = title;
    next();
  }
});

export default router;
