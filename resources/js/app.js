import './bootstrap';

// Animation libraries
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import AOS from 'aos';
import 'aos/dist/aos.css';

// Swiper
import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

gsap.registerPlugin(ScrollTrigger);

// Alpine.js Core
import Alpine from 'alpinejs';

// Alpine.js Plugins
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

// Register plugins
Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.plugin(intersect);

// Make Alpine available globally
window.Alpine = Alpine;

// ========================================
// Global Utilities
// ========================================

/**
 * Format currency (INR by default)
 */
window.formatCurrency = function(amount, currency) {
    const code     = currency || window.__currencyCode || 'INR';
    const symbol   = window.__currencySymbol || '₹';
    const position = window.__currencyPosition || 'before';
    const rate     = window.__exchangeRate || 0;

    let num = Number(amount ?? 0);

    // Convert from INR (base) to display currency
    if (code !== 'INR' && rate > 0) {
        num = num / rate;
    }

    const formatted = (code === 'INR' && num % 1 === 0)
        ? num.toLocaleString('en-IN')
        : num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    return position === 'after' ? formatted + symbol : symbol + formatted;
};

/**
 * Debounce function
 */
window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Throttle function
 */
window.throttle = function(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// ========================================
// Alpine.js Global Data/Stores
// ========================================

/**
 * Toast notification store
 */
Alpine.store('toast', {
    items: [],

    show(message, type = 'info', duration = 3000) {
        const id = Date.now();
        this.items.push({ id, message, type });

        if (duration > 0) {
            setTimeout(() => this.remove(id), duration);
        }

        return id;
    },

    success(message, duration = 3000) {
        return this.show(message, 'success', duration);
    },

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    },

    warning(message, duration = 4000) {
        return this.show(message, 'warning', duration);
    },

    info(message, duration = 3000) {
        return this.show(message, 'info', duration);
    },

    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    },

    clear() {
        this.items = [];
    }
});

/**
 * Cart store
 */
Alpine.store('cart', {
    items: [],
    recommendations: [],
    itemCount: 0,
    isOpen: false,
    isLoading: false,

    get count() {
        return this.itemCount;
    },

    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },

    _updateCount() {
        this.itemCount = this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    async fetch() {
        this.isLoading = true;
        try {
            const response = await axios.get('/cart/data');
            this.items = response.data.items || [];
            this.recommendations = response.data.recommendations || [];
            this.itemCount = response.data.cart_count || this.items.reduce((sum, item) => sum + item.quantity, 0);
        } catch (error) {
            console.error('Failed to fetch cart:', error);
        } finally {
            this.isLoading = false;
        }
    },

    async add(productId, quantity = 1, variantId = null) {
        this.isLoading = true;
        try {
            const response = await axios.post('/cart/add', {
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            });
            // Update count immediately from response
            if (response.data.cart_count !== undefined) {
                this.itemCount = response.data.cart_count;
            }
            Alpine.store('toast').success(response.data.message || 'Added to cart!');
            // GA4: add_to_cart
            if (typeof gtag !== 'undefined' && response.data.ga4_item) {
                gtag('event', 'add_to_cart', {
                    currency: 'INR',
                    value: response.data.ga4_item.price * response.data.ga4_item.quantity,
                    items: [response.data.ga4_item]
                });
            }
            // Facebook Pixel: AddToCart
            if (typeof fbq !== 'undefined' && response.data.fb_event) {
                fbq('track', 'AddToCart', {
                    content_ids: response.data.fb_event.content_ids,
                    content_name: response.data.fb_event.content_name,
                    content_type: response.data.fb_event.content_type,
                    value: response.data.fb_event.value,
                    currency: response.data.fb_event.currency,
                }, {eventID: response.data.fb_event.event_id});
            }
            await this.fetch();
            this.open();
        } catch (error) {
            const status = error.response?.status || 0;
            const serverMsg = error.response?.data?.error
                || error.response?.data?.message;
            if (!error.response) {
                Alpine.store('toast').error('Network error — check if the server is running');
            } else if (status === 419) {
                Alpine.store('toast').info('Refreshing session...');
                setTimeout(() => window.location.reload(), 500);
                return;
            } else if (status === 422) {
                // Stock limit — show inline red message, not a toast
                window.dispatchEvent(new CustomEvent('cart-stock-error', {
                    detail: { message: serverMsg || 'Stock limit reached' }
                }));
            } else {
                Alpine.store('toast').error(serverMsg || 'Something went wrong (HTTP ' + status + ')');
            }
            console.error('Cart add failed:', status, error.response?.data || error.message);
        } finally {
            this.isLoading = false;
        }
    },

    async update(itemId, quantity) {
        this.isLoading = true;
        try {
            const response = await axios.put(`/cart/${itemId}`, {
                quantity: quantity
            });
            if (response.data.cart_count !== undefined) {
                this.itemCount = response.data.cart_count;
            }
            await this.fetch();
        } catch (error) {
            const msg = error.response?.data?.error
                || error.response?.data?.message
                || 'Failed to update cart';
            Alpine.store('toast').error(msg);
            console.error('Failed to update cart:', error);
        } finally {
            this.isLoading = false;
        }
    },

    async remove(itemId) {
        this.isLoading = true;
        try {
            const response = await axios.delete(`/cart/${itemId}`);
            // GA4: remove_from_cart
            if (typeof gtag !== 'undefined' && response.data.ga4_removed_item) {
                gtag('event', 'remove_from_cart', {
                    currency: 'INR',
                    value: response.data.ga4_removed_item.price * response.data.ga4_removed_item.quantity,
                    items: [response.data.ga4_removed_item]
                });
            }
            Alpine.store('toast').info('Item removed from cart');
            await this.fetch();
        } catch (error) {
            const msg = error.response?.data?.error
                || error.response?.data?.message
                || 'Failed to remove item';
            Alpine.store('toast').error(msg);
            console.error('Failed to remove from cart:', error);
        } finally {
            this.isLoading = false;
        }
    },

    toggle() {
        this.isOpen = !this.isOpen;
    },

    open() {
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
    }
});

/**
 * Wishlist store
 */
Alpine.store('wishlist', {
    items: [],
    isLoading: false,

    get count() {
        return this.items.length;
    },

    has(productId) {
        return this.items.some(item => item.product_id === productId);
    },

    async fetch() {
        this.isLoading = true;
        try {
            const response = await axios.get('/wishlist', {
                headers: { 'Accept': 'application/json' }
            });
            this.items = response.data.items || [];
        } catch (error) {
            console.error('Failed to fetch wishlist:', error);
        } finally {
            this.isLoading = false;
        }
    },

    async toggle(productId) {
        // Show login modal if not authenticated
        if (document.body.dataset.authenticated !== 'true') {
            Alpine.store('authModal').setPendingWishlist(productId);
            try { localStorage.setItem('wishlist_pending_product', productId); } catch (e) {}
            Alpine.store('authModal').open();
            return;
        }

        this.isLoading = true;
        try {
            if (this.has(productId)) {
                await axios.delete(`/wishlist/${productId}`);
                this.items = this.items.filter(item => item.product_id !== productId);
                Alpine.store('toast').info('Removed from wishlist');
            } else {
                const response = await axios.post(`/wishlist/${productId}`);
                this.items.push({ product_id: productId });
                Alpine.store('toast').success('Added to wishlist');
                // Facebook Pixel: AddToWishlist
                if (typeof fbq !== 'undefined' && response.data.fb_event) {
                    fbq('track', 'AddToWishlist', {
                        content_ids: response.data.fb_event.content_ids,
                        content_name: response.data.fb_event.content_name,
                        content_type: response.data.fb_event.content_type,
                        value: response.data.fb_event.value,
                        currency: response.data.fb_event.currency,
                    }, {eventID: response.data.fb_event.event_id});
                }
            }
        } catch (error) {
            if (error.response && error.response.status === 401) {
                Alpine.store('authModal').open();
                return;
            }
            const msg = error.response?.data?.error
                || error.response?.data?.message
                || 'Failed to update wishlist';
            Alpine.store('toast').error(msg);
            console.error('Failed to toggle wishlist:', error);
        } finally {
            this.isLoading = false;
        }
    },

    async addAfterLogin(productId) {
        if (!productId) return;
        this.isLoading = true;
        try {
            if (!this.items.length) {
                await this.fetch();
            }
            if (this.has(productId)) {
                Alpine.store('toast').info('Already in wishlist');
                return;
            }
            const response = await axios.post(`/wishlist/${productId}`);
            this.items.push({ product_id: productId });
            Alpine.store('toast').success('Added to wishlist');
            if (typeof fbq !== 'undefined' && response.data?.fb_event) {
                fbq('track', 'AddToWishlist', {
                    content_ids: response.data.fb_event.content_ids,
                    content_name: response.data.fb_event.content_name,
                    content_type: response.data.fb_event.content_type,
                    value: response.data.fb_event.value,
                    currency: response.data.fb_event.currency,
                }, {eventID: response.data.fb_event.event_id});
            }
        } catch (error) {
            const msg = error.response?.data?.error
                || error.response?.data?.message
                || 'Failed to update wishlist';
            Alpine.store('toast').error(msg);
            console.error('Failed to add wishlist after login:', error);
        } finally {
            this.isLoading = false;
        }
    }
});

/**
 * Auth Modal store
 */
Alpine.store('authModal', {
    isOpen: false,
    isLoading: false,
    mode: 'login',
    errors: {},
    message: '',
    pendingWishlistProductId: null,

    setPendingWishlist(productId) {
        this.pendingWishlistProductId = productId;
    },

    clearPendingWishlist() {
        this.pendingWishlistProductId = null;
        try { localStorage.removeItem('wishlist_pending_product'); } catch (e) {}
    },

    async handlePendingWishlist() {
        const pendingFromStore = this.pendingWishlistProductId;
        let pending = pendingFromStore;
        if (!pending) {
            try {
                const stored = localStorage.getItem('wishlist_pending_product');
                if (stored) pending = parseInt(stored, 10);
            } catch (e) {}
        }
        if (pending) {
            await Alpine.store('wishlist').addAfterLogin(pending);
            this.clearPendingWishlist();
            return true;
        }
        return false;
    },

    intendedUrl: null,

    open(mode = 'login', intendedUrl = null) {
        this.mode = mode;
        this.errors = {};
        this.message = '';
        this.intendedUrl = intendedUrl;
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.isOpen = false;
        this.errors = {};
        this.message = '';
        document.body.style.overflow = '';
    },

    switchMode(mode) {
        this.mode = mode;
        this.errors = {};
        this.message = '';
    },

    async login(email, password, remember) {
        this.isLoading = true;
        this.errors = {};
        try {
            await axios.post('/login', { email, password, remember });
            document.body.dataset.authenticated = 'true';
            const handled = await this.handlePendingWishlist();
            const dest = this.intendedUrl || '/account';
            this.close();
            if (!handled) window.location.href = dest;
        } catch (error) {
            if (error.response?.status === 422) {
                this.errors = error.response.data.errors || {};
                this.message = error.response.data.message || '';
            } else {
                this.message = 'Something went wrong. Please try again.';
            }
        } finally {
            this.isLoading = false;
        }
    },

    async register(name, email, password, passwordConfirmation) {
        this.isLoading = true;
        this.errors = {};
        try {
            await axios.post('/register', {
                full_name: name, email, password,
                password_confirmation: passwordConfirmation, terms: true
            });
            document.body.dataset.authenticated = 'true';
            const handled = await this.handlePendingWishlist();
            const dest = this.intendedUrl || '/account';
            this.close();
            if (!handled) window.location.href = dest;
        } catch (error) {
            if (error.response?.status === 422) {
                this.errors = error.response.data.errors || {};
                this.message = error.response.data.message || '';
            } else {
                this.message = 'Something went wrong. Please try again.';
            }
        } finally {
            this.isLoading = false;
        }
    }
});

// ========================================
// Alpine.js Reusable Components
// ========================================

/**
 * Dropdown component
 */
Alpine.data('dropdown', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    }
}));

/**
 * Modal component
 */
Alpine.data('modal', (nameOrOpen = false) => ({
    isOpen: typeof nameOrOpen === 'boolean' ? nameOrOpen : false,

    open() {
        this.isOpen = true;
        document.body.classList.add('overflow-hidden');
    },

    show() {
        this.open();
    },

    close() {
        this.isOpen = false;
        document.body.classList.remove('overflow-hidden');
    },

    hide() {
        this.close();
    },

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
}));

/**
 * Tabs component
 */
Alpine.data('tabs', (initialTab = null) => ({
    activeTab: initialTab,

    isActive(tab) {
        return this.activeTab === tab;
    },

    select(tab) {
        this.activeTab = tab;
    }
}));

/**
 * Accordion component
 */
Alpine.data('accordion', (allowMultiple = false) => ({
    openItems: [],
    allowMultiple: allowMultiple,

    isOpen(item) {
        return this.openItems.includes(item);
    },

    toggle(item) {
        if (this.isOpen(item)) {
            this.openItems = this.openItems.filter(i => i !== item);
        } else {
            if (this.allowMultiple) {
                this.openItems.push(item);
            } else {
                this.openItems = [item];
            }
        }
    }
}));

/**
 * Quantity selector component
 */
Alpine.data('quantitySelector', (initialValue = 1, min = 1, max = 99) => ({
    quantity: initialValue,
    min: min,
    max: max,
    stockMsg: '',
    _stockTimer: null,

    increment() {
        if (this.quantity < this.max) {
            this.quantity++;
            this.stockMsg = '';
        } else {
            this.stockMsg = `Only ${this.max} in stock`;
            clearTimeout(this._stockTimer);
            this._stockTimer = setTimeout(() => { this.stockMsg = ''; }, 2500);
        }
    },

    decrement() {
        if (this.quantity > this.min) {
            this.quantity--;
            this.stockMsg = '';
        }
    },

    set(value) {
        const num = parseInt(value) || this.min;
        this.quantity = Math.max(this.min, Math.min(this.max, num));
    }
}));

/**
 * Image gallery component
 */
Alpine.data('imageGallery', (images = []) => ({
    images: images,
    currentIndex: 0,

    get currentImage() {
        return this.images[this.currentIndex] || null;
    },

    get hasMultiple() {
        return this.images.length > 1;
    },

    select(index) {
        if (index >= 0 && index < this.images.length) {
            this.currentIndex = index;
        }
    },

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    }
}));

/**
 * Search component with debounce
 */
Alpine.data('search', (endpoint = '/api/search') => ({
    query: '',
    results: [],
    isLoading: false,
    isOpen: false,
    selectedIndex: -1,
    endpoint: endpoint,

    async search() {
        if (this.query.length < 2) {
            this.results = [];
            this.isOpen = false;
            return;
        }

        this.isLoading = true;
        this.isOpen = true;

        try {
            const response = await axios.get(this.endpoint, {
                params: { q: this.query }
            });
            this.results = response.data.results || [];
        } catch (error) {
            console.error('Search failed:', error);
            this.results = [];
        } finally {
            this.isLoading = false;
        }
    },

    clear() {
        this.query = '';
        this.results = [];
        this.isOpen = false;
        this.selectedIndex = -1;
    },

    close() {
        this.isOpen = false;
        this.selectedIndex = -1;
    },

    selectNext() {
        if (this.selectedIndex < this.results.length - 1) {
            this.selectedIndex++;
        }
    },

    selectPrev() {
        if (this.selectedIndex > 0) {
            this.selectedIndex--;
        }
    },

    selectCurrent() {
        if (this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
            window.location.href = this.results[this.selectedIndex].url;
        }
    }
}));

// ========================================
// Initialize on page load
// ========================================

function initStores() {
    // Always fetch cart (works for both guests and authenticated users)
    Alpine.store('cart').fetch();

    // Wishlist only for authenticated users
    if (document.body.dataset.authenticated === 'true') {
        Alpine.store('wishlist').fetch();
        try {
            const pending = localStorage.getItem('wishlist_pending_product');
            if (pending) {
                Alpine.store('wishlist').addAfterLogin(parseInt(pending, 10));
                localStorage.removeItem('wishlist_pending_product');
            }
        } catch (e) {}
    }
}

// Handle timing: if DOM already loaded (module scripts can run late), init immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStores);
} else {
    initStores();
}

// ========================================
// Start Alpine.js (MUST be after all stores and components are registered)
// ========================================

// Fix Alpine 3.15 transition bug + show other errors on screen
window.onerror = function(msg, src, line) {
    if (msg && msg.includes("reading 'after'")) return true;
    console.error('JS Error:', msg, src, line);
};

Alpine.start();

// ========================================
// GSAP + AOS Animations (run after Alpine)
// ========================================

const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ---- AOS: Animate on Scroll (all pages) ----
AOS.init({
    duration: 650,
    easing: 'ease-out-cubic',
    once: true,
    offset: 50,
    disable: prefersReduced,
});

// ---- GSAP ScrollTrigger: Header scroll state ----
(function () {
    const header = document.getElementById('main-header');
    if (!header) return;
    ScrollTrigger.create({
        start: 'top -80px',
        end: 'max',
        toggleClass: { targets: header, className: 'header-scrolled' },
    });
})();

// ---- Page Navigation Loader ----
(function () {
    let navigating = false;
    document.addEventListener('click', (e) => {
        if (navigating) return;
        const link = e.target.closest('a[href]');
        if (!link || e.defaultPrevented) return;

        const href = link.href || '';
        if (!href || /^(javascript|mailto|tel):/.test(href)) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

        let url;
        try { url = new URL(href); } catch { return; }
        if (url.hostname !== window.location.hostname) return;
        if (url.pathname === window.location.pathname && url.hash && !url.search) return;

        // Show loader spinner on navigation
        navigating = true;
        let loader = document.getElementById('page-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'page-loader';
            loader.innerHTML = '<div class="loader-spinner"></div>';
            document.body.appendChild(loader);
        }
        loader.classList.remove('hide');
    }, true);
})();

// ---- Fly to Cart Animation ----
window.flyToCart = function (buttonEl) {
    if (prefersReduced) return;
    const cartIcon = document.getElementById('cart-icon');
    if (!cartIcon) return;

    const card = buttonEl.closest('[data-product-card]');
    const imgEl = card ? card.querySelector('img') : null;
    if (!imgEl) return;

    const imgRect = imgEl.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();
    const size = Math.min(imgRect.width, imgRect.height, 72);

    const clone = document.createElement('div');
    clone.style.cssText = [
        `position:fixed`,
        `left:${imgRect.left + imgRect.width / 2 - size / 2}px`,
        `top:${imgRect.top + imgRect.height / 2 - size / 2}px`,
        `width:${size}px`,
        `height:${size}px`,
        `background-image:url('${imgEl.currentSrc || imgEl.src}')`,
        `background-size:cover`,
        `background-position:center`,
        `border-radius:50%`,
        `z-index:99998`,
        `pointer-events:none`,
        `box-shadow:0 8px 32px rgba(0,0,0,0.22)`,
        `border:2px solid #fff`,
    ].join(';');
    document.body.appendChild(clone);

    const destX = cartRect.left + cartRect.width / 2;
    const destY = cartRect.top + cartRect.height / 2;

    gsap.to(clone, {
        left: destX - 8,
        top: destY - 8,
        width: 16,
        height: 16,
        opacity: 0,
        duration: 0.7,
        ease: 'power3.in',
        onComplete() {
            clone.remove();
            gsap.fromTo(cartIcon,
                { scale: 1.55 },
                { scale: 1, duration: 0.5, ease: 'elastic.out(1, 0.45)' }
            );
        },
    });
};

// ── Pendant Sets swiper ───────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('.pendant-swiper');
    if (!el) return;
    new Swiper(el, {
        modules: [Navigation],
        slidesPerView: 5,
        spaceBetween: 16,
        navigation: { nextEl: '.pendant-next', prevEl: '.pendant-prev' },
        breakpoints: {
            0:    { slidesPerView: 1.5, spaceBetween: 12 },
            640:  { slidesPerView: 3,   spaceBetween: 14 },
            1024: { slidesPerView: 5,   spaceBetween: 16 },
        },
    });
});
