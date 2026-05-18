import { reactive } from 'vue';
import axios from 'axios';
import { apiBase } from './api';

export const store = reactive({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
    cart: JSON.parse(localStorage.getItem('cart') || '[]'),
    appliedPoints: 0,
    appliedCoupon: null,
    
    setAuth(user, token) {
        this.user = user;
        this.token = token;
        this.cart = []; // Limpieza de seguridad al entrar
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('token', token);
        localStorage.removeItem('cart'); // Limpiar el almacenamiento local
    },
    
    clearAuth() {
        this.user = null;
        this.token = null;
        this.cart = [];
        this.appliedPoints = 0;
        this.appliedCoupon = null;
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        localStorage.removeItem('cart');
    },
    notifications: [],
    modal: {
        show: false,
        title: '',
        message: '',
        onConfirm: null,
        onCancel: null
    },

    notify(message, type = 'success') {
        const id = Date.now();
        this.notifications.push({ id, message, type });
        setTimeout(() => {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }, 4000);
    },

    confirm(title, message, onConfirm, onCancel = null) {
        this.modal = {
            show: true,
            title,
            message,
            onConfirm: () => {
                this.modal.show = false;
                if (onConfirm) onConfirm();
            },
            onCancel: () => {
                this.modal.show = false;
                if (onCancel) onCancel();
            }
        };
    },

    prompt: {
        show: false,
        title: '',
        message: '',
        value: '',
        onConfirm: null,
        onCancel: null
    },

    ask(title, message, defaultValue, onConfirm, onCancel = null) {
        this.prompt = {
            show: true,
            title,
            message,
            value: defaultValue,
            onConfirm: (val) => {
                this.prompt.show = false;
                if (onConfirm) onConfirm(val);
            },
            onCancel: () => {
                this.prompt.show = false;
                if (onCancel) onCancel();
            }
        };
    },
    
    addToCart(product) {
        const existing = this.cart.find(p => p.id === product.id);
        if (existing) {
            if (existing.quantity < product.stock) {
                existing.quantity += 1;
            } else {
                this.notify('Límite de stock alcanzado para este producto en el carrito', 'error');
            }
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                full_name: product.full_name || product.name,
                price: product.price,
                quantity: 1,
                image_url: product.image_url || product.image,
                stock: product.stock,
                is_censored: product.is_censored || false
            });
        }
        localStorage.setItem('cart', JSON.stringify(this.cart));
    },
    
    updateCart(items) {
        this.cart = items;
        localStorage.setItem('cart', JSON.stringify(items));
    },

    updateCensorship(value) {
        if (this.user) {
            this.user.show_censored_content = value;
            localStorage.setItem('user', JSON.stringify(this.user));
        }
    },
    
    setAppliedPoints(points) {
        this.appliedPoints = points;
    },
    
    updatePoints(points) {
        if (this.user) {
            this.user.points = Number(this.user.points || 0) + Number(points);
            localStorage.setItem('user', JSON.stringify(this.user));
        }
    },

    updateLastGameAt(timestamp, gameId = null) {
        if (this.user) {
            if (gameId === 'soul_memory') {
                this.user.last_memory_at = timestamp;
            } else if (gameId === 'soul_roulette') {
                this.user.last_roulette_at = timestamp;
            } else {
                this.user.last_game_at = timestamp;
            }
            localStorage.setItem('user', JSON.stringify(this.user));
        }
    },

    // Wishlist Logic
    wishlist: [],
    
    async fetchWishlist() {
        if (!this.token) return;
        try {
            const res = await axios.get(`${apiBase}/wishlist`, {
                headers: { Authorization: `Bearer ${this.token}` }
            });
            this.wishlist = res.data.wishlist.map(item => item.product_id);
        } catch (err) {
            console.error("Error fetching wishlist:", err);
        }
    },

    async toggleWishlist(productId) {
        if (!this.token) {
            this.notify("Debes iniciar sesión para guardar favoritos", "warning");
            return;
        }

        const isWishlisted = this.wishlist.includes(productId);
        try {
            if (isWishlisted) {
                await axios.delete(`${apiBase}/wishlist/${productId}`, {
                    headers: { Authorization: `Bearer ${this.token}` }
                });
                this.wishlist = this.wishlist.filter(id => id !== productId);
                this.notify("Eliminado de tu lista de deseos", "info");
            } else {
                await axios.post(`${apiBase}/wishlist`, { product_id: productId }, {
                    headers: { Authorization: `Bearer ${this.token}` }
                });
                this.wishlist.push(productId);
                this.notify("¡Añadido a tu lista de deseos!", "success");
            }
        } catch (err) {
            this.notify("Error al actualizar favoritos", "error");
        }
    },

    async validateCoupon(code, subtotal) {
        try {
            const res = await axios.post(`${apiBase}/validate-coupon`, { code, subtotal });
            if (res.data.valid) {
                this.appliedCoupon = {
                    ...res.data.coupon,
                    discount: res.data.discount
                };
                this.notify("¡Cupón aplicado!", "success");
                return true;
            }
        } catch (err) {
            this.appliedCoupon = null;
            const msg = err.response?.data?.message || "Cupón inválido";
            this.notify(msg, "error");
            return false;
        }
    },

    clearCoupon() {
        this.appliedCoupon = null;
    }
});
