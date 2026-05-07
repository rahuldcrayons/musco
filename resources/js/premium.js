import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';
import SplitType from 'split-type';
import { animate, stagger, inView } from 'motion';
import barba from '@barba/core';
import prefetch from '@barba/prefetch';
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade, FreeMode, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';
import lottie from 'lottie-web';

gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const lenis = new Lenis({ lerp: 0.08, smoothWheel: true });
lenis.on('scroll', ScrollTrigger.update);
gsap.ticker.add((t) => lenis.raf(t * 1000));
gsap.ticker.lagSmoothing(0);
window.lenis = lenis;

export function initSplitText(container = document) {
  if (reduced) return;

  container.querySelectorAll('[data-split="chars"]').forEach((el) => {
    const split = new SplitType(el, { types: 'chars' });
    gsap.from(split.chars, {
      y: '110%',
      rotateX: -60,
      opacity: 0,
      stagger: 0.035,
      duration: 0.9,
      ease: 'power4.out',
      transformOrigin: '50% 50% -30px',
    });
  });

  container.querySelectorAll('[data-split="words"]').forEach((el) => {
    const split = new SplitType(el, { types: 'words' });
    gsap.from(split.words, {
      y: 30,
      opacity: 0,
      stagger: 0.05,
      duration: 0.8,
      ease: 'power3.out',
      scrollTrigger: { trigger: el, start: 'top 85%' },
    });
  });

  container.querySelectorAll('[data-split="lines"]').forEach((el) => {
    const split = new SplitType(el, { types: 'lines' });
    split.lines.forEach((line) => {
      const wrapper = document.createElement('div');
      wrapper.style.overflow = 'hidden';
      line.parentNode.insertBefore(wrapper, line);
      wrapper.appendChild(line);
    });
    gsap.from(split.lines, {
      y: '100%',
      opacity: 0,
      stagger: 0.06,
      duration: 0.85,
      ease: 'power3.out',
      scrollTrigger: { trigger: el, start: 'top 88%' },
    });
  });
}

export function initScrollAnimations(container = document) {
  if (reduced) return;

  container.querySelectorAll('[data-gsap="fade-up"]').forEach((el) => {
    gsap.from(el, {
      y: 60,
      opacity: 0,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: { trigger: el, start: 'top 88%' },
    });
  });

  container.querySelectorAll('[data-gsap="stagger-grid"]').forEach((grid) => {
    const items = grid.querySelectorAll('[data-gsap="stagger-item"]');
    gsap.from(items, {
      y: 50,
      opacity: 0,
      duration: 0.8,
      ease: 'power3.out',
      stagger: { each: 0.1 },
      scrollTrigger: { trigger: grid, start: 'top 85%' },
    });
  });

  container.querySelectorAll('[data-parallax]').forEach((el) => {
    const speed = parseFloat(el.dataset.parallax) || 20;
    gsap.to(el, {
      yPercent: speed,
      ease: 'none',
      scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true },
    });
  });

  container.querySelectorAll('[data-gsap="clip-reveal"]').forEach((el) => {
    gsap.fromTo(
      el,
      { clipPath: 'inset(100% 0 0 0)' },
      {
        clipPath: 'inset(0% 0 0 0)',
        duration: 1,
        ease: 'power4.out',
        scrollTrigger: { trigger: el, start: 'top 85%' },
      }
    );
  });

  container.querySelectorAll('[data-gsap="scale-in"]').forEach((el) => {
    gsap.from(el, {
      scale: 0.88,
      opacity: 0,
      duration: 1,
      ease: 'power4.out',
      scrollTrigger: { trigger: el, start: 'top 88%' },
    });
  });
}

export function initMicroInteractions(container = document) {
  container.querySelectorAll('[data-magnetic]').forEach((el) => {
    const onMove = (e) => {
      if (reduced) return;
      const rect = el.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      animate(el, { x: (e.clientX - cx) * 0.35, y: (e.clientY - cy) * 0.35 }, { duration: 0.4, easing: 'ease-out' });
    };
    const onLeave = () => animate(el, { x: 0, y: 0 }, { duration: 0.4, easing: 'ease-out' });
    el.addEventListener('mousemove', onMove);
    el.addEventListener('mouseleave', onLeave);
  });

  container.querySelectorAll('[data-hover="scale"]').forEach((el) => {
    el.addEventListener('mouseenter', () => { if (!reduced) animate(el, { scale: 1.05 }, { duration: 0.25, easing: 'ease-out' }); });
    el.addEventListener('mouseleave', () => animate(el, { scale: 1 }, { duration: 0.25, easing: 'ease-out' }));
  });

  container.querySelectorAll('[data-hover="lift"]').forEach((el) => {
    el.addEventListener('mouseenter', () => {
      if (!reduced) animate(el, { y: -4, boxShadow: '0 12px 32px rgba(183,110,121,0.18)' }, { duration: 0.3, easing: 'ease-out' });
    });
    el.addEventListener('mouseleave', () => animate(el, { y: 0, boxShadow: '0 0px 0px rgba(0,0,0,0)' }, { duration: 0.3, easing: 'ease-out' }));
  });

  container.querySelectorAll('[data-float-label]').forEach((wrapper) => {
    const input = wrapper.querySelector('input, textarea');
    const label = wrapper.querySelector('label');
    if (!input || !label) return;
    const float = () => animate(label, { y: -20, scale: 0.8, color: '#B76E79' }, { duration: 0.2, easing: 'ease-out' });
    const sink = () => { if (!input.value) animate(label, { y: 0, scale: 1, color: '#6b7280' }, { duration: 0.2, easing: 'ease-out' }); };
    input.addEventListener('focus', float);
    input.addEventListener('blur', sink);
    if (input.value) float();
  });
}

export function initHeroVideo(container = document) {
  container.querySelectorAll('[data-hero-video]').forEach((video) => {
    video.play().catch(() => {});
    if (reduced) return;
    gsap.to(video, {
      opacity: 0.3,
      scale: 1.08,
      ease: 'none',
      scrollTrigger: { trigger: video, start: 'top top', end: 'bottom top', scrub: true },
    });
  });
}

export function initImageGallery(container = document) {
  container.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');
    const thumbs = gallery.querySelectorAll('[data-thumb]');
    if (!main || !thumbs.length) return;

    thumbs.forEach((thumb) => {
      thumb.addEventListener('click', () => {
        const src = thumb.dataset.thumb;
        thumbs.forEach((t) => t.classList.remove('ring-2', 'ring-[#B76E79]'));
        thumb.classList.add('ring-2', 'ring-[#B76E79]');

        if (reduced) { main.src = src; return; }

        gsap.to(main, {
          opacity: 0,
          scale: 0.97,
          duration: 0.2,
          ease: 'power2.in',
          onComplete: () => {
            main.src = src;
            gsap.to(main, { opacity: 1, scale: 1, duration: 0.35, ease: 'power2.out' });
          },
        });
      });
    });
  });
}

export function initSwipers(container = document) {
  const productEl = container.querySelector ? container.querySelector('.swiper-products') : document.querySelector('.swiper-products');
  if (productEl) {
    new Swiper(productEl, {
      modules: [Navigation, Pagination, FreeMode],
      slidesPerView: 1.2,
      spaceBetween: 16,
      freeMode: true,
      navigation: { nextEl: '.swiper-products .swiper-button-next', prevEl: '.swiper-products .swiper-button-prev' },
      pagination: { el: '.swiper-products .swiper-pagination', clickable: true },
      breakpoints: { 640: { slidesPerView: 2.2 }, 1024: { slidesPerView: 3.5 }, 1280: { slidesPerView: 4.2 } },
    });
  }

  const testimonialEl = container.querySelector ? container.querySelector('.swiper-testimonials') : document.querySelector('.swiper-testimonials');
  if (testimonialEl) {
    new Swiper(testimonialEl, {
      modules: [Navigation, Pagination, Autoplay],
      slidesPerView: 1,
      spaceBetween: 24,
      autoplay: { delay: 5000, disableOnInteraction: false },
      navigation: { nextEl: '.swiper-testimonials .swiper-button-next', prevEl: '.swiper-testimonials .swiper-button-prev' },
      pagination: { el: '.swiper-testimonials .swiper-pagination', clickable: true },
      breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
    });
  }

  const heroEl = container.querySelector ? container.querySelector('.swiper-hero') : document.querySelector('.swiper-hero');
  if (heroEl) {
    new Swiper(heroEl, {
      modules: [Autoplay, EffectFade, Pagination],
      effect: 'fade',
      fadeEffect: { crossFade: true },
      autoplay: { delay: 6000, disableOnInteraction: false },
      loop: true,
      pagination: { el: '.swiper-hero .swiper-pagination', clickable: true },
    });
  }
}

export function initLottie(container = document) {
  window.lottieInstances = window.lottieInstances || [];
  container.querySelectorAll('[data-lottie]').forEach((el) => {
    const path = el.dataset.lottiePath;
    if (!path) return;
    try {
      const instance = lottie.loadAnimation({
        container: el,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path,
      });
      window.lottieInstances.push(instance);
    } catch (e) {
      console.warn('[Trendimus] Lottie failed to load:', path, e);
    }
  });
}

export function flyToCart(buttonEl) {
  const card = buttonEl.closest('[data-product-card]');
  const cartIcon = document.querySelector('#cart-icon');
  const cartCount = document.querySelector('#cart-count');
  if (!card || !cartIcon) return;

  const img = card.querySelector('img');
  if (!img) return;

  const imgRect = img.getBoundingClientRect();
  const cartRect = cartIcon.getBoundingClientRect();

  const clone = document.createElement('div');
  clone.style.cssText = `
    position:fixed;
    top:${imgRect.top}px;
    left:${imgRect.left}px;
    width:${imgRect.width}px;
    height:${imgRect.height}px;
    background-image:url(${img.src});
    background-size:cover;
    background-position:center;
    border-radius:8px;
    pointer-events:none;
    z-index:9999;
  `;
  document.body.appendChild(clone);

  gsap.to(clone, {
    x: cartRect.left - imgRect.left + cartRect.width / 2 - imgRect.width / 2,
    y: cartRect.top - imgRect.top + cartRect.height / 2 - imgRect.height / 2,
    scale: 0.15,
    opacity: 0,
    duration: 0.7,
    ease: 'power3.in',
    onComplete: () => {
      clone.remove();
      gsap.fromTo(cartIcon, { scale: 1 }, { scale: 1.35, duration: 0.25, ease: 'elastic.out(1.5,0.4)', yoyo: true, repeat: 1 });
      if (cartCount) {
        const current = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = current + 1;
        gsap.fromTo(cartCount, { scale: 1.5 }, { scale: 1, duration: 0.35, ease: 'elastic.out(1.2,0.5)' });
      }
    },
  });
}

export function initNavbar() {
  const navbar = document.querySelector('#premium-navbar');
  if (!navbar) return;

  ScrollTrigger.create({
    start: '-80px top',
    onEnter: () => navbar.classList.add('navbar--scrolled'),
    onLeaveBack: () => navbar.classList.remove('navbar--scrolled'),
  });

  let lastScroll = 0;
  lenis.on('scroll', ({ scroll }) => {
    const delta = scroll - lastScroll;
    lastScroll = scroll;
    if (reduced) return;
    if (delta > 10) {
      gsap.to(navbar, { y: '-100%', duration: 0.35, ease: 'power3.in', overwrite: true });
    } else if (delta < -10) {
      gsap.to(navbar, { y: '0%', duration: 0.4, ease: 'power3.out', overwrite: true });
    }
  });
}

export function killPageAnimations() {
  ScrollTrigger.getAll().forEach((st) => st.kill());
}

function getCurtain() {
  let curtain = document.querySelector('#page-curtain');
  if (!curtain) {
    curtain = document.createElement('div');
    curtain.id = 'page-curtain';
    curtain.style.cssText = `
      position:fixed;inset:0;background:#111111;
      z-index:9998;transform:scaleY(0);transform-origin:top;pointer-events:none;
    `;
    document.body.appendChild(curtain);
  }
  return curtain;
}

export function initBarba() {
  barba.use(prefetch);

  barba.init({
    transitions: [
      {
        name: 'premium-curtain',
        leave({ current }) {
          const curtain = getCurtain();
          return new Promise((resolve) => {
            gsap.set(curtain, { scaleY: 0, transformOrigin: 'top' });
            gsap.to(curtain, {
              scaleY: 1,
              duration: 0.5,
              ease: 'power4.inOut',
              onComplete: resolve,
            });
            gsap.to(current.container, { opacity: 0, duration: 0.3 });
          });
        },
        enter({ next }) {
          const curtain = getCurtain();
          gsap.set(next.container, { opacity: 0 });
          return new Promise((resolve) => {
            gsap.set(curtain, { transformOrigin: 'bottom' });
            gsap.to(curtain, {
              scaleY: 0,
              duration: 0.7,
              ease: 'power4.inOut',
              onComplete: resolve,
            });
            gsap.to(next.container, { opacity: 1, duration: 0.4, delay: 0.2 });
          });
        },
        afterEnter({ next }) {
          lenis.scrollTo(0, { immediate: true });
          if (window.Alpine) {
            window.Alpine.initTree(next.container);
          }
          initPageAnimations(next.container);
        },
        beforeLeave() {
          killPageAnimations();
        },
      },
    ],
  });
}

export function initPageAnimations(container = document) {
  initSplitText(container);
  initScrollAnimations(container);
  initMicroInteractions(container);
  initHeroVideo(container);
  initImageGallery(container);
  initSwipers(container);
  initLottie(container);
}

window.initPageAnimations = initPageAnimations;
window.flyToCart = flyToCart;
window.killPageAnimations = killPageAnimations;

document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initPageAnimations(document);
  initBarba();
});
