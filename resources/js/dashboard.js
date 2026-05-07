/**
 * Trendimus Admin Dashboard — GSAP Animation System
 * Dark-theme first. Brand: #BCCE3A (dark) / #697416 (light).
 * Requires: gsap, gsap/ScrollTrigger (already installed via premium.js deps)
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Respect user's motion preference throughout
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ─── 1. THEME TOGGLE ────────────────────────────────────────────────────────

export function initThemeToggle() {
  const root = document.documentElement;

  // Restore persisted preference
  const stored = localStorage.getItem('dash-theme');
  if (stored === 'light') {
    root.dataset.theme = 'light';
  } else {
    delete root.dataset.theme;
  }

  // Sync all toggle buttons on page
  function syncToggleIcons(theme) {
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      const sunIcon  = btn.querySelector('.icon-sun');
      const moonIcon = btn.querySelector('.icon-moon');
      if (sunIcon)  sunIcon.style.display  = theme === 'light' ? 'none'  : 'block';
      if (moonIcon) moonIcon.style.display = theme === 'light' ? 'block' : 'none';
    });
  }
  syncToggleIcons(stored || 'dark');

  function toggleTheme() {
    const current = root.dataset.theme === 'light' ? 'light' : 'dark';
    const next    = current === 'light' ? 'dark' : 'light';

    // Create or reuse overlay
    let overlay = document.getElementById('theme-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'theme-overlay';
      overlay.style.cssText = [
        'position:fixed', 'inset:0', 'z-index:999998',
        'pointer-events:none', 'background:var(--bg-base)',
        'opacity:0', 'will-change:opacity',
      ].join(';');
      document.body.appendChild(overlay);
    }

    if (reduced) {
      // Skip animation, just switch
      if (next === 'light') root.dataset.theme = 'light';
      else delete root.dataset.theme;
      localStorage.setItem('dash-theme', next);
      syncToggleIcons(next);
      // Notify Alpine
      window.dispatchEvent(new CustomEvent('dash-theme-changed', { detail: { theme: next } }));
      return;
    }

    const tl = gsap.timeline();
    tl.to(overlay, { opacity: 1, duration: 0.18, ease: 'power2.in' })
      .add(() => {
        if (next === 'light') root.dataset.theme = 'light';
        else delete root.dataset.theme;
        localStorage.setItem('dash-theme', next);
        syncToggleIcons(next);
        window.dispatchEvent(new CustomEvent('dash-theme-changed', { detail: { theme: next } }));
      })
      .to(overlay, { opacity: 0, duration: 0.28, ease: 'power2.out' });
  }

  // Expose globally
  window.toggleTheme = toggleTheme;

  // Delegate clicks on all toggle buttons
  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-theme-toggle]')) toggleTheme();
  });
}

// ─── 2. SIDEBAR ANIMATIONS ──────────────────────────────────────────────────

export function initSidebarAnimations() {
  const sidebar = document.querySelector('.dash-sidebar');
  if (!sidebar) return;

  if (!reduced) {
    // Slide sidebar in from left
    gsap.from(sidebar, {
      x: -260,
      opacity: 0,
      duration: 0.6,
      ease: 'power4.out',
      clearProps: 'x,opacity',
    });

    // Stagger nav items
    const navItems = sidebar.querySelectorAll('.sidebar-nav-item');
    if (navItems.length) {
      gsap.from(navItems, {
        x: -20,
        opacity: 0,
        stagger: 0.045,
        duration: 0.5,
        delay: 0.25,
        ease: 'power3.out',
        clearProps: 'x,opacity',
      });
    }

    // Active item: draw the ::before left bar via scaleY
    const activeItem = sidebar.querySelector('.sidebar-nav-item.active');
    if (activeItem) {
      // We animate via a CSS custom property override trick on the pseudo-element
      // by toggling a class that CSS transitions handle. GSAP drives the timing.
      activeItem.classList.remove('active');
      gsap.delayedCall(0.55, () => {
        activeItem.classList.add('active');
      });
    }
  }

  // Hover: x-shift brightness via event delegation
  sidebar.addEventListener('mouseover', (e) => {
    const item = e.target.closest('.sidebar-nav-item');
    if (!item || item.classList.contains('active') || reduced) return;
    gsap.to(item, { x: 3, duration: 0.2, ease: 'power2.out', overwrite: 'auto' });
  });

  sidebar.addEventListener('mouseout', (e) => {
    const item = e.target.closest('.sidebar-nav-item');
    if (!item || item.classList.contains('active') || reduced) return;
    gsap.to(item, { x: 0, duration: 0.3, ease: 'power2.out', overwrite: 'auto' });
  });
}

// ─── 3. NAVBAR ANIMATIONS ───────────────────────────────────────────────────

export function initNavbarAnimations() {
  const navbar = document.querySelector('.dash-navbar');
  if (!navbar) return;

  if (!reduced) {
    gsap.from(navbar, {
      y: -60,
      opacity: 0,
      duration: 0.5,
      ease: 'power3.out',
      delay: 0.1,
      clearProps: 'y,opacity',
    });
  }

  // Shrink navbar on scroll down, restore on scroll up
  let lastY = 0;
  ScrollTrigger.create({
    start: 'top top',
    onUpdate(self) {
      const y = self.scroller ? self.scroller.scrollTop : window.scrollY;
      const scrollingDown = y > lastY;
      lastY = y;

      if (y > 80) {
        if (scrollingDown) {
          gsap.to(navbar, {
            height: 50,
            paddingTop: '0.25rem',
            paddingBottom: '0.25rem',
            duration: 0.3,
            ease: 'power2.out',
            overwrite: 'auto',
          });
        } else {
          gsap.to(navbar, {
            height: 'var(--navbar-height)',
            paddingTop: '',
            paddingBottom: '',
            duration: 0.35,
            ease: 'power2.out',
            overwrite: 'auto',
          });
        }
      } else {
        gsap.to(navbar, {
          height: 'var(--navbar-height)',
          duration: 0.35,
          ease: 'power2.out',
          overwrite: 'auto',
        });
      }
    },
  });
}

// ─── 4. CARD ANIMATIONS ─────────────────────────────────────────────────────

export function initCardAnimations(container = document) {
  if (reduced) return;

  const cards = container.querySelectorAll('.card, .card-stat, .card-glass, .card-accent');
  if (!cards.length) return;

  // Group by proximity for stagger
  gsap.from(cards, {
    y: 24,
    opacity: 0,
    scale: 0.98,
    stagger: 0.08,
    duration: 0.55,
    ease: 'power3.out',
    clearProps: 'y,opacity,scale',
    scrollTrigger: {
      trigger: cards[0].closest('section, .dash-content') || cards[0],
      start: 'top 88%',
      once: true,
    },
  });

  // Per-card hover lift (skip stat cards which have their own CSS hover)
  container.querySelectorAll('.card, .card-glass').forEach((card) => {
    card.addEventListener('mouseenter', () => {
      if (reduced) return;
      gsap.to(card, { y: -3, duration: 0.25, ease: 'power2.out', overwrite: 'auto' });
    });
    card.addEventListener('mouseleave', () => {
      if (reduced) return;
      gsap.to(card, { y: 0, duration: 0.35, ease: 'power2.out', overwrite: 'auto' });
    });
  });
}

// ─── 5. BUTTON ANIMATIONS ───────────────────────────────────────────────────

export function initButtonAnimations(container = document) {
  // Scale press + elastic release
  container.querySelectorAll('.btn').forEach((btn) => {
    btn.addEventListener('mousedown', () => {
      if (reduced) return;
      gsap.to(btn, { scale: 0.97, duration: 0.1, ease: 'power2.in', overwrite: 'auto' });
    });

    const release = () => {
      if (reduced) return;
      gsap.to(btn, {
        scale: 1,
        duration: 0.45,
        ease: 'elastic.out(1, 0.4)',
        overwrite: 'auto',
      });
    };
    btn.addEventListener('mouseup', release);
    btn.addEventListener('mouseleave', release);

    // Ripple on click
    btn.addEventListener('click', (e) => {
      if (reduced) return;
      const rect   = btn.getBoundingClientRect();
      const x      = e.clientX - rect.left;
      const y      = e.clientY - rect.top;
      const ripple = document.createElement('span');
      ripple.style.cssText = [
        `position:absolute`,
        `left:${x}px`,
        `top:${y}px`,
        `width:6px`,
        `height:6px`,
        `margin-left:-3px`,
        `margin-top:-3px`,
        `border-radius:50%`,
        `background:rgba(255,255,255,0.45)`,
        `pointer-events:none`,
        `transform:scale(0)`,
        `opacity:1`,
      ].join(';');
      btn.appendChild(ripple);

      gsap.to(ripple, {
        scale: 40,
        opacity: 0,
        duration: 0.6,
        ease: 'power2.out',
        onComplete() { ripple.remove(); },
      });
    });
  });

  // Primary button glow pulse on hover
  container.querySelectorAll('.btn-primary').forEach((btn) => {
    btn.addEventListener('mouseenter', () => {
      if (reduced) return;
      gsap.to(btn, {
        filter: 'brightness(1.12) drop-shadow(0 0 8px var(--accent-glow))',
        duration: 0.25,
        ease: 'power2.out',
        overwrite: 'auto',
      });
    });
    btn.addEventListener('mouseleave', () => {
      if (reduced) return;
      gsap.to(btn, {
        filter: 'brightness(1) drop-shadow(0 0 0px transparent)',
        duration: 0.35,
        ease: 'power2.out',
        overwrite: 'auto',
      });
    });
  });
}

// ─── 6. METRIC COUNTERS ─────────────────────────────────────────────────────

export function initMetricCounters() {
  document.querySelectorAll('.dash-metric[data-count]').forEach((el) => {
    const target    = parseFloat(el.dataset.count);
    const format    = el.dataset.format || '';   // e.g. '₹' or '%' or ''
    const isRupee   = format.includes('₹');
    const isPercent = format.includes('%');
    const proxy     = { val: 0 };

    ScrollTrigger.create({
      trigger: el,
      start: 'top 90%',
      once: true,
      onEnter() {
        if (reduced) {
          el.textContent = formatMetric(target, isRupee, isPercent);
          return;
        }
        gsap.to(proxy, {
          val: target,
          duration: 1.4,
          ease: 'power3.out',
          onUpdate() {
            el.textContent = formatMetric(proxy.val, isRupee, isPercent);
          },
          onComplete() {
            el.textContent = formatMetric(target, isRupee, isPercent);
          },
        });
      },
    });
  });
}

function formatMetric(val, isRupee, isPercent) {
  const rounded = isPercent ? val.toFixed(1) : Math.round(val);
  const str     = Number(rounded).toLocaleString('en-IN');
  if (isRupee)   return `₹${str}`;
  if (isPercent) return `${str}%`;
  return str;
}

// ─── 7. TABLE ANIMATIONS ────────────────────────────────────────────────────

export function initTableAnimations() {
  document.querySelectorAll('.dash-table tbody').forEach((tbody) => {
    const rows = tbody.querySelectorAll('tr');
    if (!rows.length) return;

    if (!reduced) {
      gsap.from(rows, {
        y: 16,
        opacity: 0,
        stagger: 0.06,
        duration: 0.4,
        ease: 'power3.out',
        clearProps: 'y,opacity',
        scrollTrigger: {
          trigger: tbody,
          start: 'top 90%',
          once: true,
        },
      });
    }

    // Row hover: accent left border via class
    rows.forEach((row) => {
      row.addEventListener('mouseenter', () => row.classList.add('row-hover-accent'));
      row.addEventListener('mouseleave', () => row.classList.remove('row-hover-accent'));
    });
  });

  // Sort header flash
  document.querySelectorAll('.dash-table th[data-sort]').forEach((th) => {
    th.style.cursor = 'pointer';
    th.addEventListener('click', () => {
      if (reduced) return;
      gsap.fromTo(
        th,
        { backgroundColor: 'var(--accent-muted)' },
        { backgroundColor: 'transparent', duration: 0.5, ease: 'power2.out' }
      );
    });
  });
}

// ─── 8. FORM ANIMATIONS ─────────────────────────────────────────────────────

export function initFormAnimations() {
  // Label float on focus (GSAP version for inputs without pure-CSS float)
  document.querySelectorAll('.form-group .form-input, .form-group .form-textarea').forEach((input) => {
    const label = input.closest('.form-group')?.querySelector('.form-label');

    if (label) {
      input.addEventListener('focus', () => {
        if (reduced) return;
        gsap.to(label, {
          color: 'var(--accent-primary)',
          duration: 0.2,
          ease: 'power2.out',
          overwrite: 'auto',
        });
      });
      input.addEventListener('blur', () => {
        if (reduced) return;
        gsap.to(label, {
          color: 'var(--text-secondary)',
          duration: 0.2,
          ease: 'power2.out',
          overwrite: 'auto',
        });
      });
    }
  });

  // Error shake: observe class changes on form inputs
  const shakeObserver = new MutationObserver((mutations) => {
    mutations.forEach(({ target, oldValue }) => {
      const el = target;
      if (
        !reduced &&
        el.classList.contains('error') &&
        !(oldValue || '').includes('error')
      ) {
        gsap.to(el, {
          keyframes: [
            { x: -8, duration: 0.07 },
            { x:  8, duration: 0.07 },
            { x: -6, duration: 0.07 },
            { x:  6, duration: 0.07 },
            { x: -3, duration: 0.07 },
            { x:  0, duration: 0.07 },
          ],
          ease: 'none',
          clearProps: 'x',
        });
      }
    });
  });

  document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach((el) => {
    shakeObserver.observe(el, { attributes: true, attributeOldValue: true, attributeFilter: ['class'] });
  });

  // Success checkmark animation
  document.querySelectorAll('.form-success-icon').forEach((icon) => {
    if (reduced) return;
    gsap.from(icon, {
      scale: 0,
      rotate: -45,
      opacity: 0,
      duration: 0.4,
      ease: 'back.out(1.7)',
    });
  });
}

// ─── 9. PAGE TRANSITION ─────────────────────────────────────────────────────

export function initPageTransition() {
  // Uses #page-curtain if it exists (defined in premium.layout)
  const curtain = document.getElementById('page-curtain');
  if (!curtain) return;

  // Override curtain style for dashboard brand color
  curtain.style.background = 'linear-gradient(135deg, #0B0D16 0%, #1A1F2E 100%)';

  // Reveal on first load
  if (!reduced) {
    gsap.fromTo(
      curtain,
      { scaleY: 1, transformOrigin: 'bottom center' },
      {
        scaleY: 0,
        duration: 0.7,
        ease: 'power4.inOut',
        delay: 0.05,
        clearProps: 'transform',
      }
    );
  } else {
    curtain.style.transform = 'scaleY(0)';
  }

  // Intercept same-origin link navigations for curtain-out transition
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link) return;
    const href = link.getAttribute('href');
    if (
      !href ||
      href.startsWith('#') ||
      href.startsWith('javascript') ||
      href.startsWith('mailto') ||
      link.target === '_blank' ||
      e.metaKey || e.ctrlKey || e.shiftKey || reduced
    ) return;

    try {
      const url = new URL(href, window.location.href);
      if (url.origin !== window.location.origin) return;
    } catch {
      return;
    }

    e.preventDefault();
    gsap.fromTo(
      curtain,
      { scaleY: 0, transformOrigin: 'top center' },
      {
        scaleY: 1,
        duration: 0.45,
        ease: 'power4.in',
        onComplete() { window.location.href = href; },
      }
    );
  });
}

// ─── 10. MASTER INIT ────────────────────────────────────────────────────────

export function initDashboard() {
  initThemeToggle();
  initSidebarAnimations();
  initNavbarAnimations();
  initCardAnimations();
  initButtonAnimations();
  initMetricCounters();
  initTableAnimations();
  initFormAnimations();
  initPageTransition();

  // Refresh ScrollTrigger after layout settles
  if (!reduced) {
    gsap.delayedCall(0.1, () => ScrollTrigger.refresh());
  }
}

// ─── AUTO-INIT ──────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', initDashboard);

// ─── WINDOW EXPORTS (for Barba afterEnter + inline scripts) ─────────────────

window.initDashboard       = initDashboard;
window.initCardAnimations  = initCardAnimations;
window.initButtonAnimations = initButtonAnimations;
window.initMetricCounters  = initMetricCounters;
window.initTableAnimations = initTableAnimations;
window.initFormAnimations  = initFormAnimations;
