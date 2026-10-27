/* Mars Pillow — shared site behaviour: starfield, nav, cart drawer, bundle picker, consent + tracking, motion */
(() => {
  'use strict';
  const MP = window.MP || {};
  const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const FINE = window.matchMedia('(pointer: fine)').matches;
  const hasGsap = typeof gsap !== 'undefined';
  const hasST = hasGsap && typeof ScrollTrigger !== 'undefined';
  if (hasST) gsap.registerPlugin(ScrollTrigger);
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => [...el.querySelectorAll(s)];
  const fmt = n => '$' + Number(n).toLocaleString('en-US', { maximumFractionDigits: 2 });
  const OFFERS = MP.offers || [];
  const offerById = id => OFFERS.find(o => o.id === id);
  const savings = o => o && o.compare ? { usd: o.compare - o.price, pct: Math.round((o.compare - o.price) / o.compare * 100) } : null;
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const store = {
    get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set(k, v) { try { localStorage.setItem(k, v); } catch (e) {} },
  };

  /* ---------------- Starfield (whole page) with warp on fast scroll ---------------- */
  const sky = $('#sky');
  if (sky) {
    const sctx = sky.getContext('2d');
    let stars = [], sw = 0, sh = 0, lastY = window.scrollY, vel = 0, shooting = null, running = true;
    const resize = () => {
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      sw = window.innerWidth; sh = window.innerHeight;
      sky.width = sw * dpr; sky.height = sh * dpr; sctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      const n = Math.round(sw * sh / (sw < 700 ? 3200 : 2300));
      stars = Array.from({ length: n }, () => ({ x: Math.random() * sw, y: Math.random() * sh, r: Math.random() * 1.3 + .2, z: Math.random(), p: Math.random() * 6.28 }));
    };
    const draw = now => {
      const t = now / 1000, y = window.scrollY, dy = y - lastY; lastY = y;
      vel += (Math.max(-90, Math.min(90, dy)) - vel) * .18;
      sctx.clearRect(0, 0, sw, sh);
      const neb = sctx.createRadialGradient(sw * .2, sh * .25, 0, sw * .2, sh * .25, sw * .7);
      neb.addColorStop(0, 'rgba(91,75,138,.22)'); neb.addColorStop(1, 'rgba(91,75,138,0)');
      sctx.fillStyle = neb; sctx.fillRect(0, 0, sw, sh);
      const streak = Math.abs(vel) < 6 ? 0 : Math.min(70, (Math.abs(vel) - 6) * 1.3);
      sctx.strokeStyle = '#F5F3FF'; sctx.fillStyle = '#F5F3FF';
      for (const s of stars) {
        if (!REDUCED) {
          s.y -= vel * (0.08 + s.z * .3); s.x -= .03 + s.z * .05;
          if (s.y < -20) s.y += sh + 40; if (s.y > sh + 20) s.y -= sh + 40; if (s.x < -2) s.x = sw + 2;
        }
        sctx.globalAlpha = REDUCED ? .7 : .4 + .6 * Math.sin(t * (0.6 + s.z) + s.p) ** 2;
        const L = streak * (0.3 + s.z);
        if (L > 3) { sctx.lineWidth = s.r * 1.1; sctx.beginPath(); sctx.moveTo(s.x, s.y); sctx.lineTo(s.x, s.y + Math.sign(vel) * L); sctx.stroke(); }
        else { sctx.beginPath(); sctx.arc(s.x, s.y, s.r, 0, 6.29); sctx.fill(); }
      }
      sctx.globalAlpha = 1;
      if (!REDUCED) {
        if (!shooting && Math.random() < .004) shooting = { x: Math.random() * sw * .7 + sw * .25, y: Math.random() * sh * .35, life: 0 };
        if (shooting) {
          shooting.life += .016; const L = shooting.life, x = shooting.x - L * 560, yy = shooting.y + L * 240;
          const gr = sctx.createLinearGradient(x, yy, x + 110, yy - 46); gr.addColorStop(0, 'rgba(245,243,255,.95)'); gr.addColorStop(1, 'rgba(245,243,255,0)');
          sctx.strokeStyle = gr; sctx.lineWidth = 1.4; sctx.beginPath(); sctx.moveTo(x, yy); sctx.lineTo(x + 110, yy - 46); sctx.stroke();
          if (L > .9) shooting = null;
        }
        if (running) requestAnimationFrame(draw);
      }
    };
    resize(); requestAnimationFrame(draw);
    window.addEventListener('resize', () => { resize(); if (REDUCED) requestAnimationFrame(draw); });
    document.addEventListener('visibilitychange', () => { const was = running; running = !document.hidden; if (running && !was && !REDUCED) requestAnimationFrame(draw); });
  }

  /* ---------------- Nav, progress, mobile menu ---------------- */
  const nav = $('#nav'), progress = $('#progress');
  const onScroll = () => {
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 40);
    if (progress) { const max = document.documentElement.scrollHeight - window.innerHeight; progress.style.transform = `scaleX(${max > 0 ? window.scrollY / max : 0})`; }
  };
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
  const menuBtn = $('#menuBtn'), menu = $('#mobileMenu');
  if (menuBtn && menu) {
    menuBtn.addEventListener('click', () => {
      const open = menu.hidden; menu.hidden = !open; menuBtn.setAttribute('aria-expanded', open);
      menuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      if (open) nav.classList.add('scrolled');
    });
    $$('a', menu).forEach(a => a.addEventListener('click', () => { menu.hidden = true; menuBtn.setAttribute('aria-expanded', 'false'); }));
  }

  /* ---------------- Analytics (only after consent) ---------------- */
  const track = {
    ready: false,
    load() {
      if (this.ready) return; this.ready = true;
      const t = MP.tracking || {};
      if (t.ga4 || t.ads) {
        const s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(t.ga4 || t.ads); s.nonce = MP.nonce; document.head.appendChild(s);
        window.dataLayer = window.dataLayer || []; window.gtag = function () { dataLayer.push(arguments); };
        gtag('js', new Date());
        if (t.ga4) gtag('config', t.ga4);
        if (t.ads) gtag('config', t.ads, { allow_enhanced_conversions: true });
      }
      if (t.pixel) {
        !function (f, b, e, v, n, t2, s2) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t2 = b.createElement(e); t2.async = !0; t2.src = v; t2.nonce = MP.nonce; s2 = b.getElementsByTagName(e)[0]; s2.parentNode.insertBefore(t2, s2); }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', t.pixel); fbq('track', 'PageView');
      }
    },
    addToCart(o, qty) {
      if (!this.ready) return;
      if (window.gtag) gtag('event', 'add_to_cart', { currency: 'USD', value: o.price * qty, items: [{ item_id: o.id, item_name: o.name, price: o.price, quantity: qty }] });
      if (window.fbq) fbq('track', 'AddToCart', { currency: 'USD', value: o.price * qty, content_ids: [o.id], content_type: 'product' });
    },
    purchase(p) {
      if (!this.ready || !p) return;
      const key = 'mp_tracked_' + p.id; if (store.get(key)) return; store.set(key, '1');
      const t = MP.tracking || {};
      if (window.gtag) {
        gtag('event', 'purchase', { transaction_id: p.id, currency: 'USD', value: p.value, items: p.items.map(i => ({ item_id: i.id, item_name: i.name, price: i.price, quantity: i.qty })) });
        if (t.ads && t.adsLabel) { // Google Ads purchase with enhanced conversions (Google hashes the email)
          if (p.email) gtag('set', 'user_data', { email: p.email.trim().toLowerCase() });
          gtag('event', 'conversion', { send_to: t.ads + '/' + t.adsLabel, value: p.value, currency: 'USD', transaction_id: p.id });
        }
      }
      // same event id as the server event, so Meta counts the purchase once
      if (window.fbq) fbq('track', 'Purchase', { currency: 'USD', value: p.value, content_ids: p.items.map(i => i.id), content_type: 'product' }, { eventID: p.id });
    },
  };
  const consent = $('#consent');
  const setConsent = v => {
    store.set('mp_consent', v);
    // a cookie too, so the server knows whether it may send the purchase to Meta
    document.cookie = 'mp_consent=' + v + ';path=/;max-age=31536000;SameSite=Lax' + (location.protocol === 'https:' ? ';Secure' : '');
    if (consent) consent.hidden = true;
    if (v === 'yes') track.load();
    document.dispatchEvent(new Event('mp:consent'));
  };
  const c = store.get('mp_consent');
  if (c === 'yes') track.load(); else if (!c && consent) consent.hidden = false;
  $$('[data-consent]').forEach(b => b.addEventListener('click', () => setConsent(b.dataset.consent)));
  const cs = $('#cookieSettings'); if (cs && consent) cs.addEventListener('click', () => { consent.hidden = false; consent.querySelector('button').focus(); });

  /* ---------------- Toast ---------------- */
  const toastEl = $('#toast'); let tT;
  const toast = msg => { if (!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('show'); clearTimeout(tT); tT = setTimeout(() => toastEl.classList.remove('show'), 2600); };

  /* ---------------- Cart (server is the source of truth) ---------------- */
  const api = async (body) => {
    const res = await fetch('/api/cart', {
      method: body ? 'POST' : 'GET', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': MP.csrf || '', 'Accept': 'application/json' },
      body: body ? JSON.stringify(body) : undefined,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || 'Something went wrong. Please try again.');
    return data;
  };
  const countEl = $('#cartCount'), cartBtn = $('#cartBtn'), drawer = $('#drawer'), scrim = $('#scrim'), lines = $('#lines');
  const thumb = '<svg viewBox="0 0 64 64" aria-hidden="true"><use href="#planet"/></svg>';
  let cartState = null;
  function renderCart(c) {
    cartState = c;
    if (countEl) { countEl.textContent = c.count; countEl.dataset.zero = c.count === 0; }
    if (cartBtn) cartBtn.setAttribute('aria-label', `Open cart, ${c.count} item${c.count === 1 ? '' : 's'}`);
    if (!lines) return;
    lines.innerHTML = !c.lines.length
      ? `<li class="empty"><b style="font-family:var(--font-display);font-size:1.6rem;font-weight:400">Your cart is empty</b>Pick a bundle to start your trial.<a class="btn btn-primary" href="/product/mars-pillow">Choose a bundle</a></li>`
      : c.lines.map(l => `<li class="line" data-id="${esc(l.id)}"><div class="thumb">${thumb}</div>
          <div><div class="ln">${esc(l.name)}</div><div class="lp">${fmt(l.price)} each</div>
          <div class="qty"><button type="button" aria-label="Decrease quantity of ${esc(l.name)}" data-a="dec">−</button><span aria-live="polite">${l.qty}</span><button type="button" aria-label="Increase quantity of ${esc(l.name)}" data-a="inc">+</button></div></div>
          <div style="display:grid;justify-items:end;gap:6px"><strong style="font-variant-numeric:tabular-nums">${fmt(l.line)}</strong><button type="button" class="remove" data-a="rm">Remove<span class="sr-only"> ${esc(l.name)}</span></button></div></li>`).join('');
    $('#subtotal').textContent = fmt(c.total);
    const sl = $('#subtotalLabel'); if (sl) sl.textContent = c.coupon ? `Total with ${c.coupon.code}` : 'Subtotal';
    const co = $('#checkoutBtn'); if (co) co.hidden = !c.lines.length;
  }
  let lastFocus = null;
  function openCart(open) {
    if (!drawer) return;
    if (open) lastFocus = document.activeElement;
    drawer.classList.toggle('open', open); scrim.hidden = !open; requestAnimationFrame(() => scrim.classList.toggle('open', open));
    drawer.setAttribute('aria-hidden', !open); drawer.inert = !open; cartBtn.setAttribute('aria-expanded', open);
    if (open) $('#closeCart').focus(); else if (lastFocus) lastFocus.focus();
  }
  if (cartBtn) {
    cartBtn.addEventListener('click', () => { openCart(true); api().then(renderCart).catch(() => {}); });
    $('#closeCart').addEventListener('click', () => openCart(false));
    scrim.addEventListener('click', () => openCart(false));
    document.addEventListener('keydown', e => {
      if (!drawer.classList.contains('open')) return;
      if (e.key === 'Escape') openCart(false);
      if (e.key === 'Tab') { // keep focus inside the drawer
        const f = $$('a[href],button:not([disabled])', drawer).filter(x => !x.hidden && x.offsetParent !== null);
        if (!f.length) return;
        if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
        else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
      }
    });
    lines.addEventListener('click', async e => {
      const btn = e.target.closest('button[data-a]'); if (!btn) return;
      const li = btn.closest('.line'), id = li.dataset.id, cur = cartState.lines.find(l => l.id === id);
      const a = btn.dataset.a;
      const qty = a === 'inc' ? cur.qty + 1 : a === 'dec' ? cur.qty - 1 : 0;
      try {
        if (qty === 0 && hasGsap && !REDUCED) await new Promise(r => gsap.to(li, { x: 60, opacity: 0, height: 0, paddingTop: 0, paddingBottom: 0, duration: .35, ease: 'power2.in', onComplete: r }));
        renderCart(await api({ action: 'update', offer: id, qty }));
      } catch (err) { toast(err.message); }
    });
    if (countEl && countEl.textContent !== '0') countEl.dataset.zero = 'false';
  }

  function burst(x, y) {
    for (let i = 0; i < 14; i++) {
      const s = document.createElement('i'); s.className = 'spark'; document.body.appendChild(s);
      const a = (i / 14) * Math.PI * 2, d = 30 + Math.random() * 30;
      gsap.fromTo(s, { x: x - 3, y: y - 3, scale: 1, opacity: 1 }, { x: x - 3 + Math.cos(a) * d, y: y - 3 + Math.sin(a) * d, scale: 0, opacity: 0, duration: .7, ease: 'power2.out', onComplete: () => s.remove() });
    }
  }
  function flyToCart(fromEl, done) {
    if (!hasGsap || REDUCED || !cartBtn || !window.MP3D) { done(); return; }
    const from = fromEl.getBoundingClientRect(), to = cartBtn.getBoundingClientRect();
    const fly = document.createElement('div'); fly.className = 'flyer';
    fly.innerHTML = `<svg viewBox="0 0 ${MP3D.W} 170" aria-hidden="true"><path d="${MP3D.slab(0, 6 / 14)}" fill="#7C6CB4"/><path d="${MP3D.slab(6 / 14, 8 / 14)}" fill="#E8743B"/><path d="${MP3D.slab(8 / 14, 1)}" fill="#E4DDF8"/></svg>`;
    document.body.appendChild(fly);
    const sx = from.left + from.width / 2 - 35, sy = from.top - 20, ex = to.left + to.width / 2 - 35, ey = to.top + to.height / 2 - 14;
    gsap.set(fly, { x: sx, y: sy, scale: 1.4, rotate: 0 });
    gsap.to(fly, { x: ex, duration: .85, ease: 'power1.inOut' });
    gsap.to(fly, { y: Math.min(sy, ey) - 140, duration: .42, ease: 'power2.out', onComplete: () => gsap.to(fly, { y: ey, duration: .43, ease: 'power2.in' }) });
    gsap.to(fly, { scale: .35, rotate: -25, duration: .85, ease: 'power1.in', onComplete: () => {
      fly.remove(); done();
      burst(to.left + to.width / 2, to.top + to.height / 2);
      gsap.fromTo(cartBtn, { y: 0 }, { y: -8, duration: .15, yoyo: true, repeat: 3, ease: 'power1.out' });
      gsap.fromTo(countEl, { scale: 1.7 }, { scale: 1, duration: .5, ease: 'back.out(3)' });
    } });
  }

  /* ---------------- Bundle picker (home + product) ---------------- */
  const nights = () => (document.querySelector('[data-nights]') || {}).dataset?.nights;
  $$('form[data-picker]').forEach(form => {
    const S = k => form.querySelector(`[data-s="${k}"]`);
    const priceEl = S('price');
    let shown = offerById((form.querySelector('input[name=offer]:checked') || {}).value)?.price || 0;
    const extras = [...S('inc').querySelectorAll('li')].slice(-2).map(li => li.outerHTML).join('');
    const qtyInput = form.querySelector('input[name=qty][type=number]');
    const qty = () => Math.max(1, Math.min(10, parseInt(qtyInput ? qtyInput.value : 1, 10) || 1));
    function update(animate) {
      const id = (form.querySelector('input[name=offer]:checked') || {}).value, o = offerById(id); if (!o) return;
      $$('.bundle', form).forEach(b => b.classList.toggle('is-selected', b.dataset.id === id));
      S('name').textContent = o.name;
      S('compare').textContent = o.compare ? fmt(o.compare) : '';
      const s = savings(o); S('save').hidden = !s; if (s) S('save').textContent = `You save ${fmt(s.usd)} (${s.pct}%)`;
      S('inc').innerHTML = o.inc.map(i => `<li>${esc(i)}</li>`).join('') + extras;
      S('add').textContent = `Add to cart · ${fmt(o.price * qty())}`;
      const am = S('amazon'); if (am) { am.hidden = !o.amazon; if (o.amazon) am.href = o.amazon; }
      if (animate && hasGsap && !REDUCED) {
        const obj = { v: shown };
        gsap.to(obj, { v: o.price, duration: .6, ease: 'power2.out', onUpdate: () => priceEl.textContent = fmt(Math.round(obj.v)) });
        gsap.fromTo([S('name'), S('save')], { y: 10, opacity: .2 }, { y: 0, opacity: 1, duration: .45, stagger: .06, ease: 'power2.out' });
      } else priceEl.textContent = fmt(o.price);
      shown = o.price;
      document.dispatchEvent(new CustomEvent('mp:offer', { detail: o }));
      if (form.dataset.context === 'product' && history.replaceState) history.replaceState(null, '', '/product/' + o.slug);
    }
    form.addEventListener('change', e => { if (e.target.name === 'offer') update(true); if (e.target.name === 'qty') update(false); });
    $$('[data-q]', form).forEach(b => b.addEventListener('click', () => { qtyInput.value = Math.max(1, Math.min(10, qty() + Number(b.dataset.q))); update(false); }));
    if (qtyInput) qtyInput.addEventListener('input', () => update(false));
    if (FINE && hasGsap && !REDUCED) $$('.bundle', form).forEach(b => {
      b.addEventListener('pointermove', e => { const r = b.getBoundingClientRect(); gsap.to(b, { rotateY: ((e.clientX - r.left) / r.width - .5) * 10, rotateX: -((e.clientY - r.top) / r.height - .5) * 10, transformPerspective: 700, duration: .4 }); });
      b.addEventListener('pointerleave', () => gsap.to(b, { rotateY: 0, rotateX: 0, duration: .6, ease: 'elastic.out(1,.5)' }));
    });
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const id = (form.querySelector('input[name=offer]:checked') || {}).value, o = offerById(id), q = qty();
      const btn = S('add'); btn.disabled = true;
      try {
        const cart = await api({ action: 'add', offer: id, qty: q });
        track.addToCart(o, q);
        flyToCart(btn, () => renderCart(cart));
        toast(`${o.name} added to cart`);
      } catch (err) { toast(err.message); } finally { btn.disabled = false; }
    });
    update(false);
  });
  window.MPUI = { toast, track, api, renderCart, openCart, fmt, REDUCED, FINE, hasGsap, hasST };

  /* ---------------- Email capture: popup + quiz form ---------------- */
  const post = async (url, body) => {
    const res = await fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': MP.csrf || '' }, body: JSON.stringify(body) });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || 'Something went wrong. Please try again.');
    return data;
  };
  $$('form[data-subscribe]').forEach(f => f.addEventListener('submit', async e => {
    e.preventDefault();
    const msg = f.querySelector('.sub-msg'), btn = f.querySelector('button[type=submit]'), email = f.querySelector('input[name=email]');
    btn.disabled = true; msg.textContent = '';
    try {
      const d = await post('/api/subscribe', { email: email.value, source: f.dataset.subscribe, quiz: f.dataset.quiz || '', website: (f.querySelector('input[name=website]') || {}).value || '' });
      store.set('mp_popup_seen', '1');
      f.innerHTML = `<p class="code-box">Your code: <b>${esc(d.code)}</b></p><p class="fine">We also sent it to your email.</p><button type="button" class="btn btn-primary btn-block" data-apply>Add the code to my cart</button><p class="fine sub-msg" role="status"></p>`;
      f.querySelector('[data-apply]').addEventListener('click', async ev => {
        try { renderCart(await post('/api/coupon', { code: d.code })); ev.target.textContent = 'Code added ✓'; ev.target.disabled = true; } catch (err) { f.querySelector('.sub-msg').textContent = err.message; }
      });
      f.querySelector('[data-apply]').focus();
    } catch (err) { msg.textContent = err.message; email.setAttribute('aria-invalid', 'true'); btn.disabled = false; }
  }));
  const popup = $('#popup');
  if (popup && !store.get('mp_popup_seen')) {
    let shown = false, prevFocus = null;
    const close = () => { popup.hidden = true; store.set('mp_popup_seen', '1'); if (prevFocus) prevFocus.focus(); };
    const show = () => {
      if (shown || store.get('mp_popup_seen') || (consent && !consent.hidden) || (drawer && drawer.classList.contains('open'))) return;
      shown = true; prevFocus = document.activeElement; popup.hidden = false;
      setTimeout(() => popup.querySelector('input[type=email]').focus(), 50);
      if (hasGsap && !REDUCED) gsap.from('.popup-card', { y: 30, opacity: 0, scale: .96, duration: .5, ease: 'power3.out' });
    };
    setTimeout(show, 25000);
    if (FINE) document.addEventListener('mouseout', e => { if (!e.relatedTarget && e.clientY < 10) show(); });
    document.addEventListener('mp:consent', () => setTimeout(show, 12000));
    $$('[data-popup-close]', popup).forEach(b => b.addEventListener('click', close));
    popup.addEventListener('click', e => { if (e.target === popup) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !popup.hidden) close(); });
  }

  /* ---------------- Full cart page: animate removals (forms still work without JS) ---------------- */
  // (handled by normal form posts)

  if (!hasGsap || !hasST || REDUCED) return;

  /* ---------------- Motion ---------------- */
  const GLYPHS = '01/<>*#+=:';
  function scramble(el) {
    const final = el.textContent; let f = 0; const total = 22;
    el.setAttribute('aria-label', final);
    const tick = () => {
      f++;
      el.textContent = final.split('').map((ch, i) => ch === ' ' ? ' ' : (i < (f / total) * final.length ? ch : GLYPHS[(Math.random() * GLYPHS.length) | 0])).join('');
      if (f < total) requestAnimationFrame(tick); else { el.textContent = final; el.removeAttribute('aria-label'); }
    };
    tick();
  }
  $$('.scramble').forEach(el => ScrollTrigger.create({ trigger: el, start: 'top 92%', once: true, onEnter: () => scramble(el) }));

  gsap.utils.toArray('.section-head h2, .benefit, .spine-card, .bundle, .bundles-layout > .summary, .table-wrap, .slot, .faq details, .final .wrap > *, .cta-box').forEach(el => {
    gsap.from(el, { y: 30, opacity: .25, duration: .9, ease: 'power3.out', scrollTrigger: { trigger: el, start: 'top 92%', once: true } });
  });

  const track2 = $('#marquee');
  if (track2) {
    let mx = 0, lastSY = window.scrollY, mv = 0;
    gsap.ticker.add(() => {
      const dy = window.scrollY - lastSY; lastSY = window.scrollY;
      mv += (dy - mv) * .1; mx -= .6 + Math.abs(mv) * .9;
      const w = track2.children[0].offsetWidth; if (-mx > w) mx += w;
      track2.style.transform = `translate3d(${mx}px,0,0) skewX(${Math.max(-12, Math.min(12, -mv * .4))}deg)`;
    });
  }

  if (FINE) {
    $$('.magnetic').forEach(b => {
      b.addEventListener('pointermove', e => { const r = b.getBoundingClientRect(); gsap.to(b, { x: (e.clientX - r.left - r.width / 2) * .28, y: (e.clientY - r.top - r.height / 2) * .35, duration: .4, ease: 'power3.out' }); });
      b.addEventListener('pointerleave', () => gsap.to(b, { x: 0, y: 0, duration: .7, ease: 'elastic.out(1,.4)' }));
    });
    const glow = $('#glow');
    if (glow) {
      const qx = gsap.quickTo(glow, 'x', { duration: .6, ease: 'power3' }), qy = gsap.quickTo(glow, 'y', { duration: .6, ease: 'power3' });
      window.addEventListener('pointermove', e => { glow.classList.add('on'); qx(e.clientX); qy(e.clientY); }, { passive: true });
    }
  }
})();
