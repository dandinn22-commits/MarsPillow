/* Mars Pillow — home page: 3D hero, exploded layers, spine diagram, night calendar */
(() => {
  'use strict';
  const D = window.MP3D, UI = window.MPUI || {};
  const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const FINE = window.matchMedia('(pointer: fine)').matches;
  const hasGsap = typeof gsap !== 'undefined', hasST = hasGsap && typeof ScrollTrigger !== 'undefined';
  const $ = s => document.querySelector(s);

  /* ================= HERO: pillow between the lines, orbit ring, Mars horizon ================= */
  const hero = $('#top'), stageCv = $('#stage'), display = $('.hero-display'), pressHint = $('#pressHint');
  let heroAPI = null;
  if (D && D.webgl && hero) {
    const renderer = D.makeRenderer(stageCv);
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(32, 1, .1, 400);
    camera.position.set(0, .4, 14); camera.lookAt(0, 0, 0);
    D.stdLights(scene);

    const MR = 26;
    const marsMap = D.marsTex(); marsMap.encoding = THREE.sRGBEncoding;
    const mars = new THREE.Mesh(new THREE.SphereGeometry(MR, 96, 64), new THREE.MeshStandardMaterial({ map: marsMap, roughness: 1, metalness: 0, color: 0x8F5C58 }));
    const atm = new THREE.Mesh(new THREE.SphereGeometry(MR * 1.035, 96, 64), new THREE.ShaderMaterial({
      transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
      uniforms: { c: { value: new THREE.Color(0xE8743B) }, p: { value: 3.4 }, k: { value: 1.05 } },
      vertexShader: 'varying vec3 vN; varying vec3 vV; void main(){ vec4 mv = modelViewMatrix * vec4(position,1.); vN = normalize(normalMatrix * normal); vV = normalize(-mv.xyz); gl_Position = projectionMatrix * mv; }',
      fragmentShader: 'uniform vec3 c; uniform float p; uniform float k; varying vec3 vN; varying vec3 vV; void main(){ float f = pow(1. - max(dot(vN, vV), 0.), p) * k; gl_FragColor = vec4(c * f, f); }',
    }));
    const marsGroup = new THREE.Group(); marsGroup.add(mars, atm); scene.add(marsGroup);
    const sunrise = new THREE.DirectionalLight(0xFFD2B0, 1.7); sunrise.position.set(-6, 14, -18); scene.add(sunrise);

    const pillow = new THREE.Mesh(D.pillowGeometry({ N: 110, M: 70 }), D.pillowMaterial());
    pillow.position.y = -.55;
    const spin = new THREE.Group(); spin.add(pillow);
    const squash = new THREE.Group(); squash.add(spin);

    const orbit = new THREE.Group();
    const ellipse = (r, n = 240) => { const a = []; for (let i = 0; i <= n; i++) { const t = i / n * Math.PI * 2; a.push(new THREE.Vector3(Math.cos(t) * r, 0, Math.sin(t) * r)); } return new THREE.BufferGeometry().setFromPoints(a); };
    const ring1 = new THREE.Line(ellipse(5.4), new THREE.LineBasicMaterial({ color: 0xF2A07A, transparent: true, opacity: .55 }));
    const ring2 = new THREE.Line(ellipse(6.6), new THREE.LineDashedMaterial({ color: 0xF5F3FF, transparent: true, opacity: .28, dashSize: .18, gapSize: .22 }));
    ring2.computeLineDistances();
    const moon = new THREE.Mesh(new THREE.SphereGeometry(.24, 32, 24), new THREE.MeshStandardMaterial({ color: 0xF5F3FF, emissive: 0xF2A07A, emissiveIntensity: .35, roughness: .6 }));
    const moonGlow = new THREE.Sprite(new THREE.SpriteMaterial({ map: D.glowTex('242,160,122'), transparent: true, depthWrite: false, blending: THREE.AdditiveBlending }));
    moonGlow.scale.set(1.6, 1.6, 1); moon.add(moonGlow);
    const sat = new THREE.Mesh(new THREE.SphereGeometry(.09, 16, 12), new THREE.MeshBasicMaterial({ color: 0xF5F3FF }));
    orbit.add(ring1, ring2, moon, sat);
    orbit.rotation.set(1.28, 0, -.32);

    const halo = new THREE.Sprite(new THREE.SpriteMaterial({ map: D.glowTex('91,75,138'), transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, opacity: .85 }));
    halo.scale.set(13, 9, 1); halo.position.set(0, 0, -3);
    const holder = new THREE.Group(); holder.add(halo, squash, orbit); scene.add(holder);

    const foam = D.foam(pillow, camera, stageCv, { fine: FINE });
    const pointerN = { x: 0, y: 0 };
    const isUI = e => e.target.closest('a,button,input,label');
    hero.addEventListener('pointerdown', e => { if (!isUI(e) && foam.press(e)) pressHint.classList.add('gone'); });
    hero.addEventListener('pointermove', e => {
      pointerN.x = (e.clientX / window.innerWidth) * 2 - 1; pointerN.y = (e.clientY / window.innerHeight) * 2 - 1;
      const p = foam.move(e);
      hero.style.cursor = p && !isUI(e) ? (foam.pressing ? 'grabbing' : 'grab') : '';
    });
    window.addEventListener('pointerup', () => foam.release()); window.addEventListener('pointercancel', () => foam.release());
    hero.addEventListener('pointerleave', () => foam.leave());

    const base = { holder: new THREE.Vector3(), mars: new THREE.Vector3() };
    function layout() {
      const r = stageCv.getBoundingClientRect(), cw = r.width, ch = r.height;
      renderer.setSize(cw, ch, false); camera.aspect = cw / ch; camera.updateProjectionMatrix(); camera.updateMatrixWorld(true);
      const l1 = display.querySelector('.l1').getBoundingClientRect(), l2 = display.querySelector('.l2').getBoundingClientRect();
      const gy = (l1.bottom + l2.top) / 2;
      base.holder.copy(D.worldAt(camera, 0, -((gy - r.top) / ch) * 2 + 1, 0));
      const upp = (2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.position.z) / ch;
      const targetPx = Math.min(cw * (cw < 600 ? .66 : .48), 620);
      holder.scale.setScalar((targetPx * upp) / 6.4);
      const top = D.worldAt(camera, 0, cw < 600 ? -.93 : -.76, -30);
      base.mars.set(top.x, top.y - MR, -30);
      if (cw >= 860) { pressHint.style.left = `${cw / 2 + targetPx * .52}px`; pressHint.style.top = `${gy - r.top - 16}px`; pressHint.style.transform = 'none'; }
      else { pressHint.style.left = '50%'; pressHint.style.top = `${l2.bottom - r.top + 12}px`; pressHint.style.transform = 'translate(-50%, 0)'; }
    }
    layout();
    let lt; window.addEventListener('resize', () => { clearTimeout(lt); lt = setTimeout(layout, 120); });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(layout);

    const intro = { drop: REDUCED || !hasGsap ? 0 : 1, rise: REDUCED || !hasGsap ? 0 : 1, sq: 0, ring: REDUCED || !hasGsap ? 1 : 0 };
    let visible = true, scrollP = 0, tAcc = 0, spinY = -1.15;
    new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(hero);
    const clock = new THREE.Clock();
    (function frame() {
      const dt = Math.min(clock.getDelta(), .05); tAcc += dt;
      if (visible && !document.hidden) {
        foam.update(dt);
        const m = REDUCED ? 0 : 1;
        if (!foam.pressing) spinY += dt * .16 * m;
        spin.rotation.set(.16 + pointerN.y * .05 * m + scrollP * .6, spinY + pointerN.x * .25 * m, Math.sin(tAcc * .7) * .03 * m);
        holder.position.set(base.holder.x, base.holder.y + intro.drop * 9 + Math.sin(tAcc * 1.1) * .12 * m + scrollP * 2.6, base.holder.z);
        squash.scale.set(1 + intro.sq * .12, 1 - intro.sq * .22, 1 + intro.sq * .12);
        orbit.rotation.y = tAcc * .12 * m;
        orbit.scale.setScalar(.6 + .4 * intro.ring + scrollP * .5);
        ring1.material.opacity = .55 * intro.ring; ring2.material.opacity = .28 * intro.ring;
        const a = tAcc * .55 * m + 1.2; moon.position.set(Math.cos(a) * 5.4, 0, Math.sin(a) * 5.4); moon.visible = intro.ring > .2;
        const b = -tAcc * .3 * m; sat.position.set(Math.cos(b) * 6.6, 0, Math.sin(b) * 6.6); sat.visible = intro.ring > .2;
        marsGroup.position.set(base.mars.x, base.mars.y - intro.rise * 10 + scrollP * 7, base.mars.z);
        mars.rotation.y += dt * .012 * m;
        camera.position.x += ((pointerN.x * .7 * m) - camera.position.x) * .04;
        camera.position.y += ((.4 - pointerN.y * .4 * m) - camera.position.y) * .04;
        camera.lookAt(0, 0, 0);
        renderer.render(scene, camera);
      }
      requestAnimationFrame(frame);
    })();
    heroAPI = {
      setScroll: p => { scrollP = p; },
      intro: () => gsap.timeline({ delay: .25 })
        .to(intro, { rise: 0, duration: 1.8, ease: 'power3.out' }, 0)
        .to(intro, { drop: 0, duration: .95, ease: 'power3.in' }, .35)
        .to(intro, { sq: 1, duration: .1, ease: 'power2.out' }, 1.3)
        .to(intro, { sq: 0, duration: .9, ease: 'elastic.out(1, .35)' }, 1.4)
        .to(intro, { ring: 1, duration: 1.2, ease: 'power3.out' }, 1.35),
    };
  } else if (hero && D) {
    // lite mode / no WebGL: a drawing of the pillow, placed in the gap between the two headline lines
    const fb = $('#heroFallback'); fb.hidden = false; fb.innerHTML = D.svgPillow(); if (pressHint) pressHint.hidden = true;
    const place = () => {
      const r = hero.getBoundingClientRect(), l1 = display.querySelector('.l1').getBoundingClientRect(), l2 = display.querySelector('.l2').getBoundingClientRect();
      const gap = Math.max(60, l2.top - l1.bottom);
      fb.style.width = `${Math.min(r.width * .62, gap * 2.6, 520)}px`;
      fb.style.top = `${(l1.bottom + l2.top) / 2 - r.top}px`;
    };
    place(); window.addEventListener('resize', place);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(place);
  }

  /* ================= INSIDE: exploded 3D layers ================= */
  const LF = D ? D.LAYER_F : { soft: [8 / 14, 1], fine: [6 / 14, 8 / 14], firm: [0, 6 / 14] };
  const LAYERS = [
    { name: 'COVER', f: [0, 1], color: 0xF5F3FF, wire: true },
    { name: 'SOFT LAYER', f: LF.soft, color: 0xEDE6FF },
    { name: 'FINE-TUNE LAYER', f: LF.fine, color: 0xF2A07A, emissive: 0x6a2a10 },
    { name: 'FIRM LAYER', f: LF.firm, color: 0x6E5FA8 },
  ];
  const label = i => (i === 0 ? LAYERS[0].name : `L/0${i} ${LAYERS[i].name}`);
  const OFF3 = [2.75, 1.3, .62, 0];
  const exState = { p: 0 };
  const lis = [...document.querySelectorAll('#layerList li')];
  const setOn = n => lis.forEach((li, i) => li.classList.toggle('on', i < n));
  const exEl = $('#exploded'), exCv = $('#exCanvas'), callouts = $('#callouts');
  if (exEl && D && D.webgl) {
    const renderer = D.makeRenderer(exCv);
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(30, 1, .1, 100);
    camera.position.set(0, 4.6, 15.5); camera.lookAt(0, 1.05, 0);
    D.stdLights(scene);
    const group = new THREE.Group(); scene.add(group); group.rotation.set(.18, -.75, 0);
    const meshes = LAYERS.map(L => {
      const geo = D.pillowGeometry({ f1: L.f[0], f2: L.f[1], N: L.wire ? 30 : 64, M: L.wire ? 40 : 64, inflate: L.wire ? .05 : 0 });
      const mat = L.wire ? new THREE.MeshBasicMaterial({ color: L.color, wireframe: true, transparent: true, opacity: .32 })
        : new THREE.MeshStandardMaterial({ color: L.color, roughness: .8, emissive: L.emissive || 0x000000, emissiveIntensity: .35 });
      const m = new THREE.Mesh(geo, mat); group.add(m);
      m.userData.anchor = new THREE.Vector3(D.DIM.Wd / 2, (L.f[0] + L.f[1]) / 2 * D.DIM.H, D.DIM.D * .25);
      return m;
    });
    const poreArr = new Float32Array(900 * 3);
    for (let k = 0; k < poreArr.length; k += 3) { const t = .04 + Math.random() * .92, v = .04 + Math.random() * .92; poreArr[k] = (t - .5) * D.DIM.Wd; poreArr[k + 1] = D.topY(t, v, LF.soft[0], LF.soft[1]) + .012; poreArr[k + 2] = (v - .5) * D.DIM.D; }
    const poreGeo = new THREE.BufferGeometry(); poreGeo.setAttribute('position', new THREE.BufferAttribute(poreArr, 3));
    meshes[1].add(new THREE.Points(poreGeo, new THREE.PointsMaterial({ color: 0x5B4B8A, size: .045, transparent: true, opacity: .7 })));
    let cw = 1, ch = 1, visible = false;
    const size = () => { const r = exEl.getBoundingClientRect(); cw = r.width; ch = r.height; renderer.setSize(cw, ch, false); camera.aspect = cw / ch; camera.fov = camera.aspect < 1 ? THREE.MathUtils.radToDeg(2 * Math.atan(.255 / camera.aspect)) : 30; camera.updateProjectionMatrix(); callouts.setAttribute('viewBox', `0 0 ${cw} ${ch}`); };
    size(); window.addEventListener('resize', size);
    new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(exEl);
    callouts.innerHTML = LAYERS.map((L, i) => `<g opacity="0"><circle r="3.5" fill="#E8743B"/><line stroke="#E8743B" stroke-width="1"/><text text-anchor="end">${label(i)}</text></g>`).join('');
    const cGroups = [...callouts.children], tmp = new THREE.Vector3();
    const ease = x => x < .5 ? 2 * x * x : 1 - Math.pow(-2 * x + 2, 2) / 2;
    let tAcc = 0, last = performance.now();
    (function frame(now) {
      const dt = Math.min(((now || last) - last) / 1000, .05); last = now || last; tAcc += dt;
      if (visible && !document.hidden) {
        const p = exState.p;
        group.rotation.y = -.75 + p * .9 + (REDUCED ? 0 : Math.sin(tAcc * .4) * .05);
        group.rotation.x = .18 + p * .16;
        const locals = meshes.map((m, i) => { const l = Math.min(1, Math.max(0, (p - i * .14) / .58)); m.position.y = OFF3[i] * ease(l); return l; });
        group.updateMatrixWorld(true);
        meshes.forEach((m, i) => {
          tmp.copy(m.userData.anchor); m.localToWorld(tmp); tmp.project(camera);
          const x = (tmp.x * .5 + .5) * cw, y = (-tmp.y * .5 + .5) * ch, lx = Math.min(cw - 4, x + Math.max(48, cw * .16));
          const cg = cGroups[i];
          cg.setAttribute('opacity', Math.max(0, Math.min(1, (locals[i] - .55) / .3)).toFixed(2));
          cg.children[0].setAttribute('cx', x); cg.children[0].setAttribute('cy', y);
          const ln = cg.children[1]; ln.setAttribute('x1', x); ln.setAttribute('y1', y); ln.setAttribute('x2', lx); ln.setAttribute('y2', y - 18);
          cg.children[2].setAttribute('x', lx); cg.children[2].setAttribute('y', y - 24);
        });
        renderer.render(scene, camera);
      }
      requestAnimationFrame(frame);
    })();
  } else if (exEl && D) {
    const fb = $('#exFallback'); fb.hidden = false; exCv.hidden = true;
    const OFF = [-205, -100, -48, 0], fills = ['url(#pgFront)', 'url(#pgFoam)', 'url(#pgCool)', 'url(#pgBase)'];
    fb.innerHTML = `<svg viewBox="0 -170 ${D.W + 150} 400">${D.DEFS}${LAYERS.map((L, i) => `<g transform="translate(0 ${OFF[i]})"><path d="${D.slab(L.f[0], L.f[1])}" fill="${fills[i]}" ${i === 0 ? 'fill-opacity=".2" stroke="#F5F3FF" stroke-dasharray="4 4"' : ''}/><text class="tag" x="${D.W + 10}" y="${D.pt(0, (L.f[0] + L.f[1]) / 2)[1] + 5}">${label(i)}</text></g>`).reverse().join('')}</svg>`;
  }

  /* ================= Spine diagrams ================= */
  function spineSVG(el, good) {
    if (!el) return [];
    // side view: the pillow is a block. Too low (bad) lets the head drop; the right height (good) keeps the neck level.
    const top = good ? 134 : 154, x = 22, w = 150, hh = 168 - top;
    const pill = good
      ? `<rect x="${x}" y="${168 - hh * 6 / 14}" width="${w}" height="${hh * 6 / 14}" rx="4" fill="url(#pgBase)"/>
         <rect x="${x}" y="${168 - hh * 8 / 14}" width="${w}" height="${hh * 2 / 14}" fill="url(#pgCool)"/>
         <rect x="${x}" y="${top}" width="${w}" height="${hh * 6 / 14}" rx="4" fill="url(#pgFoam)"/>`
      : `<rect x="${x}" y="${top}" width="${w}" height="${hh}" rx="5" fill="#8F84BF" opacity=".85"/>`;
    const headY = top - 34;
    el.innerHTML = `${D ? D.DEFS : ''}<line x1="0" y1="168" x2="360" y2="168" stroke="rgba(245,243,255,.18)"/>${pill}
      <rect x="150" y="112" width="230" height="56" rx="26" fill="#2B3560"/>
      <path d="M92 ${headY + 10} Q130 ${good ? 112 : 118} 160 118 L160 150 Q120 ${good ? 146 : 152} 92 ${headY + 34}Z" fill="#2B3560"/>
      <circle cx="74" cy="${headY}" r="34" fill="#3A4577"/>
      <g class="vert">${Array.from({ length: 9 }, (_, i) => `<circle cx="${104 + i * 28}" cy="${128 + Math.max(0, 4 - i) * 5.5}" r="5.5" fill="${good ? '#E8743B' : '#F08A8A'}"/>`).join('')}</g>
      <line x1="96" y1="128" x2="350" y2="128" stroke="${good ? '#8FD3B5' : '#F08A8A'}" stroke-dasharray="5 6" opacity=".7"/>`;
    return [...el.querySelectorAll('.vert circle')];
  }
  spineSVG($('#spineBad'), false);
  const goodVerts = spineSVG($('#spineGood'), true);

  /* ================= Build your height ================= */
  const bStage = $('#bStage');
  if (bStage) {
    const opts = [...document.querySelectorAll('.b-opt')], soft = bStage.querySelector('.soft'), fine = bStage.querySelector('.fine'), firm = bStage.querySelector('.firm');
    // which layers are in the stack for each height (index into product.heights)
    const USE = [[1, 0, 0], [1, 1, 0], [1, 0, 1], [1, 1, 1]];
    const pick = (i, focus) => {
      opts.forEach((o, k) => { o.setAttribute('aria-checked', k === i ? 'true' : 'false'); o.tabIndex = k === i ? 0 : -1; });
      const [a, b, c] = USE[i];
      soft.classList.toggle('off', !a); fine.classList.toggle('off', !b); firm.classList.toggle('off', !c);
      $('#bIn').textContent = opts[i].dataset.in; $('#bHow').textContent = opts[i].dataset.how;
      if (focus) opts[i].focus();
    };
    opts.forEach((o, i) => {
      o.addEventListener('click', () => pick(i));
      o.addEventListener('keydown', e => {
        const d = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[e.key];
        if (d) { e.preventDefault(); pick((i + d + opts.length) % opts.length, true); }
      });
    });
    pick(3);
  }

  /* ================= Night calendar ================= */
  const moonsEl = $('#moons');
  let setNights = () => {};
  if (moonsEl) {
    const total = parseInt(moonsEl.dataset.nights, 10) || 60;
    moonsEl.innerHTML = Array.from({ length: total }, () => '<i class="moon"></i>').join('');
    const moons = [...moonsEl.children];
    const sizeMoons = () => { const s = moons[0].getBoundingClientRect().width; moons.forEach((m, i) => { const ph = ((i + 1) % 30) / 30; m.style.setProperty('--off', `${(s * (1 - 2 * ph)).toFixed(1)}px`); }); };
    sizeMoons(); window.addEventListener('resize', sizeMoons);
    const num = $('#nightNum'), of = $('#nightOf');
    setNights = n => { moons.forEach((m, i) => { m.classList.toggle('lit', i < n); m.classList.toggle('now', i === n - 1 && n < total); }); num.textContent = String(n).padStart(2, '0'); of.textContent = n; };
    setNights(total);
    if (hasST && !REDUCED) {
      ScrollTrigger.create({ trigger: '#nights', start: 'top 70%', end: 'bottom 60%', scrub: .5, onUpdate: s => setNights(Math.round(s.progress * total)) });
      if (!ScrollTrigger.isInViewport('#nights')) setNights(0);
    }
  }

  if (!hasST || REDUCED) { setOn(3); exState.p = 1; goodVerts.forEach(c => c.setAttribute('cy', 128)); return; }

  /* ================= Motion ================= */
  gsap.from('.hero-display .w', { yPercent: 110, opacity: 0, rotate: 4, duration: 1.1, ease: 'power4.out', stagger: .08, delay: .2 });
  gsap.from('.hero-foot > *, .hero-coords', { y: 18, opacity: 0, duration: .8, ease: 'power2.out', stagger: .08, delay: 1.2 });
  if (heroAPI) { heroAPI.intro(); ScrollTrigger.create({ trigger: '#top', start: 'top top', end: 'bottom top', scrub: true, onUpdate: s => heroAPI.setScroll(s.progress) }); }
  gsap.to('.hero-display .l1', { xPercent: -28, opacity: .15, ease: 'none', scrollTrigger: { trigger: '#top', start: 'top top', end: 'bottom top', scrub: true } });
  gsap.to('.hero-display .l2', { xPercent: 28, opacity: .15, ease: 'none', scrollTrigger: { trigger: '#top', start: 'top top', end: 'bottom top', scrub: true } });

  setOn(0);
  gsap.matchMedia().add({ wide: '(min-width: 861px)', narrow: '(max-width: 860px)' }, c => {
    // desktop: pin the whole section. phone: pin just the 3D view under the header so every layer stays on screen while it opens
    const head = document.getElementById('nav');
    const top = c.conditions.narrow ? Math.round((head ? head.getBoundingClientRect().height : 64) + 8) : 0;
    gsap.fromTo(exState, { p: 0 }, { p: 1, ease: 'none', scrollTrigger: {
      trigger: c.conditions.wide ? '#insideStage' : '#exploded', start: c.conditions.wide ? 'center center' : `top ${top}px`,
      end: c.conditions.wide ? '+=1500' : '+=800', scrub: .8, pin: c.conditions.wide ? '#inside' : '#exploded',
      onUpdate: s => setOn(Math.min(3, Math.floor(s.progress * 4.2))),
    } });
  });
  gsap.to(goodVerts, { attr: { cy: 128 }, duration: 1.2, ease: 'elastic.out(1, .55)', stagger: .07, delay: .2, scrollTrigger: { trigger: '#spineGood', start: 'top 75%', once: true } });

  // in-page anchor links to #inside / #bundles still work with the pinned section
  window.addEventListener('load', () => ScrollTrigger.refresh());
})();
