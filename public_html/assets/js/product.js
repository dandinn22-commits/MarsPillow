/* Mars Pillow — product page: 3D viewer, gallery, tabs, sticky add-to-cart bar */
(() => {
  'use strict';
  const D = window.MP3D, UI = window.MPUI || {};
  const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = s => document.querySelector(s), $$ = s => [...document.querySelectorAll(s)];

  /* ---------- 3D viewer: drag to turn, press to dent the foam ---------- */
  const cv = $('#pdpStage');
  if (cv && D && D.webgl) {
    const renderer = D.makeRenderer(cv);
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(30, 1, .1, 100);
    camera.position.set(0, 3.4, 15); camera.lookAt(0, .3, 0);
    D.stdLights(scene);
    const pillow = new THREE.Mesh(D.pillowGeometry({ N: 110, M: 70 }), D.pillowMaterial());
    const spin = new THREE.Group(); spin.add(pillow); scene.add(spin);
    const shadow = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), new THREE.MeshBasicMaterial({ map: D.glowTex('8,6,30'), transparent: true, depthWrite: false }));
    shadow.rotation.x = -Math.PI / 2; shadow.scale.set(7, 9, 1); shadow.position.y = -.3; scene.add(shadow);
    const foam = D.foam(pillow, camera, cv, { fine: window.matchMedia('(pointer: fine)').matches });
    let rotY = -1.1, velY = REDUCED ? 0 : .0025, dragging = false, lastX = 0, idle = 0;
    cv.addEventListener('pointerdown', e => {
      if (foam.press(e)) return;
      dragging = true; lastX = e.clientX; cv.setPointerCapture(e.pointerId);
    });
    cv.addEventListener('pointermove', e => {
      if (dragging) { const dx = e.clientX - lastX; lastX = e.clientX; rotY += dx * .01; velY = dx * .0008; idle = 0; }
      else foam.move(e);
    });
    const up = () => { dragging = false; foam.release(); };
    cv.addEventListener('pointerup', up); cv.addEventListener('pointercancel', up); cv.addEventListener('pointerleave', () => foam.leave());
    cv.addEventListener('keydown', e => { if (e.key === 'ArrowLeft') rotY -= .2; if (e.key === 'ArrowRight') rotY += .2; });
    cv.tabIndex = 0;
    const size = () => { const r = cv.getBoundingClientRect(); renderer.setSize(r.width, r.height, false); camera.aspect = r.width / r.height; camera.updateProjectionMatrix(); };
    size(); window.addEventListener('resize', size);
    let visible = true; new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(cv);
    const clock = new THREE.Clock(); let t = 0;
    (function frame() {
      const dt = Math.min(clock.getDelta(), .05); t += dt; idle += dt;
      if (visible && !document.hidden && !cv.closest('[hidden]')) {
        foam.update(dt);
        if (!dragging) { rotY += velY; velY += ((REDUCED ? 0 : .0025) - velY) * (idle > 1.5 ? .02 : .005); }
        spin.rotation.set(.12, rotY, 0);
        spin.position.y = REDUCED ? 0 : Math.sin(t * 1.1) * .08;
        renderer.render(scene, camera);
      }
      requestAnimationFrame(frame);
    })();
  } else if (cv && D) {
    const fb = $('#pdpFallback'); fb.hidden = false; fb.innerHTML = D.svgPillow(); cv.hidden = true;
    const tip = $('.g-tip'); if (tip) tip.hidden = true;
  }

  /* ---------- Gallery thumbnails ---------- */
  const thumbs = $$('.g-thumb'), slides = $$('.g-slide');
  const go = i => {
    thumbs.forEach((t, k) => t.setAttribute('aria-selected', k === i));
    slides.forEach((s, k) => { s.hidden = k !== i; s.classList.toggle('is-active', k === i); });
    if (UI.hasGsap && !REDUCED) gsap.fromTo(slides[i], { opacity: 0, scale: 1.02 }, { opacity: 1, scale: 1, duration: .5, ease: 'power2.out' });
  };
  thumbs.forEach((t, i) => {
    t.addEventListener('click', () => go(i));
    t.addEventListener('keydown', e => {
      if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
      e.preventDefault(); const n = (i + (e.key === 'ArrowRight' ? 1 : -1) + thumbs.length) % thumbs.length; go(n); thumbs[n].focus();
    });
  });

  /* ---------- Tabs (all panels show when JavaScript is off) ---------- */
  const tabs = $$('.tablist [role=tab]');
  const panels = tabs.map(t => document.getElementById(t.getAttribute('aria-controls')));
  const select = i => {
    tabs.forEach((t, k) => { t.setAttribute('aria-selected', k === i); t.tabIndex = k === i ? 0 : -1; });
    panels.forEach((p, k) => { p.hidden = k !== i; });
  };
  tabs.forEach((t, i) => {
    t.addEventListener('click', () => select(i));
    t.addEventListener('keydown', e => {
      const map = { ArrowRight: 1, ArrowLeft: -1, Home: -i, End: tabs.length - 1 - i };
      if (!(e.key in map)) return;
      e.preventDefault(); const n = (i + map[e.key] + tabs.length) % tabs.length; select(n); tabs[n].focus();
    });
  });
  if (tabs.length) select(0);

  /* ---------- Sticky add-to-cart bar (mobile) ---------- */
  const bar = $('#stickyAtc'), form = $('form[data-picker]');
  if (bar && form) {
    const addBtn = form.querySelector('[data-s="add"]');
    const sName = bar.querySelector('[data-sticky="name"]'), sPrice = bar.querySelector('[data-sticky="price"]'), sBtn = bar.querySelector('[data-sticky="add"]');
    document.addEventListener('mp:offer', e => { sName.textContent = e.detail.name; sPrice.textContent = UI.fmt ? UI.fmt(e.detail.price) : '$' + e.detail.price; });
    sBtn.addEventListener('click', () => form.requestSubmit ? form.requestSubmit() : addBtn.click());
    new IntersectionObserver(es => {
      const hiddenBtn = !es[0].isIntersecting && es[0].boundingClientRect.top < window.innerHeight;
      bar.classList.toggle('show', hiddenBtn);
      bar.setAttribute('aria-hidden', !hiddenBtn); sBtn.tabIndex = hiddenBtn ? 0 : -1;
    }, { threshold: 0 }).observe(addBtn);
  }
})();
