/* Mars Pillow — shared 3D + SVG pillow helpers (used by home.js and product.js) */
(() => {
  'use strict';
  const webgl = (() => {
    if (typeof THREE === 'undefined') return false;
    try { const c = document.createElement('canvas'); return !!(c.getContext('webgl') || c.getContext('experimental-webgl')); } catch (e) { return false; }
  })();

  // The Mars Pillow is a rectangular block, 60 x 40 x 14 cm, made of three removable layers:
  // firm 6 cm (bottom), fine-tune 2 cm, soft 6 cm (top). f = height fraction from the bottom.
  const LAYER_F = { firm: [0, 6 / 14], fine: [6 / 14, 8 / 14], soft: [8 / 14, 1] };
  // squared-off profile with softly rounded edges (superellipse): ~1 in the middle, 0 at the edge
  const se = (t, p) => Math.pow(Math.max(0, 1 - Math.pow(Math.abs(2 * t - 1), p)), 1 / p);
  const h = t => se(t, 12);

  // ---- 2D SVG versions (fallback without WebGL, cart animation) ----
  const W = 420, B = 150, HMAX = 88, X0 = 20, X1 = 400;
  const pt = (t, f) => [X0 + t * (X1 - X0), B - f * HMAX];
  const rr = (x, y, w, hh, r) => `M${x + r},${y}H${x + w - r}Q${x + w},${y} ${x + w},${y + r}V${y + hh - r}Q${x + w},${y + hh} ${x + w - r},${y + hh}H${x + r}Q${x},${y + hh} ${x},${y + hh - r}V${y + r}Q${x},${y} ${x + r},${y}Z`;
  function slab(f1, f2) {
    const y1 = B - f2 * HMAX, hh = (f2 - f1) * HMAX;
    return rr(X0, y1, X1 - X0, hh, Math.min(12, hh / 2));
  }
  const DEFS = `<defs>
    <linearGradient id="pgFront" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F5F3FF"/><stop offset=".55" stop-color="#D9D2F2"/><stop offset="1" stop-color="#8F84BF"/></linearGradient>
    <linearGradient id="pgBack" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#B7AEE0"/><stop offset="1" stop-color="#5B4B8A"/></linearGradient>
    <linearGradient id="pgCool" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#F2A07A"/><stop offset="1" stop-color="#E8743B"/></linearGradient>
    <linearGradient id="pgFoam" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F5F1FF"/><stop offset="1" stop-color="#C9BEEA"/></linearGradient>
    <linearGradient id="pgBase" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7C6CB4"/><stop offset="1" stop-color="#3E3366"/></linearGradient>
  </defs>`;
  // a block seen slightly from above: top face, right side, and the three layers on the front
  const svgPillow = (label = 'Illustration of the Mars Pillow, a rectangular pillow with three layers') => {
    const dx = 30, dy = -26, top = B - HMAX, f = LAYER_F;
    const band = (k, fill) => `<path d="${slab(f[k][0], f[k][1])}" fill="${fill}"/>`;
    return `<svg viewBox="0 0 ${W + dx + 6} 190" role="img" aria-label="${label}">${DEFS}<g transform="translate(0,${-dy / 2 + 4})">
      <path d="M${X0 + 8},${top} L${X0 + 8 + dx},${top + dy} H${X1 + dx - 8} L${X1 - 8},${top}Z" fill="#EEE9FF"/>
      <path d="M${X1},${top + 8} L${X1 + dx},${top + dy + 8} V${B + dy - 8} L${X1},${B - 8}Z" fill="url(#pgBack)"/>
      ${band('firm', 'url(#pgBase)')}${band('fine', 'url(#pgCool)')}${band('soft', 'url(#pgFoam)')}
      <path d="${slab(0, 1)}" fill="none" stroke="rgba(245,243,255,.5)"/></g></svg>`;
  };

  if (!webgl) { window.MP3D = { webgl: false, h, W, slab, pt, DEFS, svgPillow, LAYER_F }; return; }

  // ---- 3D ----
  // Pillow block between height fractions f1..f2 (0 = bottom, 1 = top). x = short side, z = long side.
  const DIM = { Wd: 4.13, D: 6.2, H: 1.45 };
  function pillowGeometry({ f1 = 0, f2 = 1, N = 96, M = 60, Wd = DIM.Wd, D = DIM.D, H = DIM.H, inflate = 0, p = 7 } = {}) {
    const pos = [], uv = [], idx = [], w = [];
    const y0 = f1 * H, y1 = f2 * H, yc = (y0 + y1) / 2, ht = (y1 - y0) / 2 + inflate * .9;
    const cs = u => .5 - .5 * Math.cos(Math.PI * u); // more vertices near the edges, for crisp corners
    const surf = sign => {
      const base = pos.length / 3;
      for (let j = 0; j <= M; j++) {
        const v = cs(j / M), pz = se(v, p);
        for (let i = 0; i <= N; i++) {
          const t = cs(i / N), px = se(t, p), prof = px * pz;
          const X = 2 * t - 1, Z = 2 * v - 1, crown = sign > 0 ? (1 - X * X) * (1 - Z * Z) * .05 * (y1 - y0) : 0;
          pos.push(X * Wd / 2 * (1 + inflate), yc + sign * (ht * prof) + crown * prof, Z * D / 2 * (1 + inflate * .6));
          uv.push(t * 6, v * 9);
          if (sign > 0) w.push(prof);
        }
      }
      for (let j = 0; j < M; j++) for (let i = 0; i < N; i++) {
        const a = base + j * (N + 1) + i, b = a + (N + 1), c = a + 1, d = b + 1;
        if (sign > 0) idx.push(a, b, c, c, b, d); else idx.push(a, c, b, c, d, b);
      }
    };
    surf(1); surf(-1);
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
    geo.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
    geo.setIndex(idx);
    geo.computeVertexNormals();
    geo.userData = { topCount: (N + 1) * (M + 1), orig: Float32Array.from(pos), H, y0, w: Float32Array.from(w) };
    return geo;
  }
  // a point on the top surface of a block (for decorations like foam pores)
  const topY = (t, v, f1, f2, H = DIM.H, p = 7) => { const prof = se(t, p) * se(v, p); return (f1 + f2) / 2 * H + (f2 - f1) / 2 * H * prof; };
  function canvasTex(w, hgt, draw) { const c = document.createElement('canvas'); c.width = w; c.height = hgt; draw(c.getContext('2d'), w, hgt); return new THREE.CanvasTexture(c); }
  const knitTex = () => {
    const tx = canvasTex(128, 128, (c, w, hh) => {
      c.fillStyle = '#808080'; c.fillRect(0, 0, w, hh);
      c.strokeStyle = '#a0a0a0'; c.lineWidth = 3;
      for (let i = -hh; i < w; i += 12) { c.beginPath(); c.moveTo(i, 0); c.lineTo(i + hh, hh); c.stroke(); }
      c.strokeStyle = '#6a6a6a'; c.lineWidth = 2;
      for (let i = -hh; i < w; i += 12) { c.beginPath(); c.moveTo(i + 6, 0); c.lineTo(i + 6 + hh, hh); c.stroke(); }
    });
    tx.wrapS = tx.wrapT = THREE.RepeatWrapping; return tx;
  };
  const glowTex = rgb => canvasTex(256, 256, (c, w) => {
    const gr = c.createRadialGradient(w / 2, w / 2, 0, w / 2, w / 2, w / 2);
    gr.addColorStop(0, `rgba(${rgb},.85)`); gr.addColorStop(.35, `rgba(${rgb},.28)`); gr.addColorStop(1, `rgba(${rgb},0)`);
    c.fillStyle = gr; c.fillRect(0, 0, w, w);
  });
  const marsTex = () => canvasTex(1024, 512, (c, w, hh) => {
    c.fillStyle = '#A9492A'; c.fillRect(0, 0, w, hh);
    const rnd = (a, b) => a + Math.random() * (b - a);
    for (let k = 0; k < 1100; k++) {
      const x = rnd(0, w), y = rnd(hh * .06, hh * .94), r = rnd(4, 80), dark = Math.random() < .5;
      const gr = c.createRadialGradient(x, y, 0, x, y, r);
      gr.addColorStop(0, dark ? 'rgba(70,26,40,.22)' : 'rgba(232,140,90,.16)'); gr.addColorStop(1, 'rgba(0,0,0,0)');
      c.fillStyle = gr; c.fillRect(x - r, y - r, r * 2, r * 2);
    }
    for (let k = 0; k < 60; k++) { const x = rnd(0, w), y = rnd(hh * .15, hh * .85), r = rnd(3, 16); c.strokeStyle = 'rgba(50,18,28,.35)'; c.lineWidth = 2; c.beginPath(); c.arc(x, y, r, 0, 6.29); c.stroke(); c.fillStyle = 'rgba(60,22,34,.25)'; c.fill(); }
    c.fillStyle = 'rgba(60,20,30,.32)'; c.beginPath(); c.ellipse(w * .55, hh * .5, w * .22, hh * .045, .08, 0, 6.29); c.fill();
    const cap = (y0, y1) => { const gr = c.createLinearGradient(0, y0, 0, y1); gr.addColorStop(0, 'rgba(245,235,230,.8)'); gr.addColorStop(1, 'rgba(245,235,230,0)'); c.fillStyle = gr; c.fillRect(0, Math.min(y0, y1), w, Math.abs(y1 - y0)); };
    cap(0, hh * .08); cap(hh, hh * .93);
  });
  function stdLights(scene) {
    scene.add(new THREE.HemisphereLight(0xB8AEF0, 0x0B132B, 0.45));
    const key = new THREE.DirectionalLight(0xFFF4EE, 0.75); key.position.set(-5, 7, 4); scene.add(key);
    const rim = new THREE.DirectionalLight(0xE8743B, 1.5); rim.position.set(6, 3, -6); scene.add(rim);
    const fill = new THREE.DirectionalLight(0x6F5FB0, .45); fill.position.set(4, -2, 6); scene.add(fill);
  }
  function makeRenderer(canvas) {
    const r = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    r.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
    r.outputEncoding = THREE.sRGBEncoding; r.toneMapping = THREE.ACESFilmicToneMapping; r.toneMappingExposure = 1.05;
    return r;
  }
  const pillowMaterial = () => new THREE.MeshStandardMaterial({ color: 0xD9D1F0, roughness: .9, metalness: 0, bumpMap: knitTex(), bumpScale: .012 });
  const worldAt = (camera, nx, ny, zPlane) => {
    const v = new THREE.Vector3(nx, ny, .5).unproject(camera);
    const dir = v.sub(camera.position).normalize();
    return camera.position.clone().add(dir.multiplyScalar((zPlane - camera.position.z) / dir.z));
  };

  /**
   * Memory-foam behaviour: pointer presses leave dents that sink fast and recover slowly.
   * Returns { update(dt), press(e), move(e), release() }.
   */
  function foam(mesh, camera, canvas, { fine = true } = {}) {
    const geo = mesh.geometry, imprints = [], ray = new THREE.Raycaster(), ndc = new THREE.Vector2();
    let pressing = false, active = null, hover = null, dirty = false;
    const hit = e => {
      const r = canvas.getBoundingClientRect();
      ndc.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
      ray.setFromCamera(ndc, camera);
      const x = ray.intersectObject(mesh)[0];
      return x ? mesh.worldToLocal(x.point.clone()) : null;
    };
    return {
      get pressing() { return pressing; },
      hit,
      press(e) { const p = hit(e); if (!p) return false; pressing = true; active = { x: p.x, z: p.z, s: 0, target: .6, hold: true }; imprints.push(active); return true; },
      move(e) {
        const p = hit(e);
        if (pressing && active && p) {
          if (Math.hypot(p.x - active.x, p.z - active.z) > .28) { active.hold = false; active = { x: p.x, z: p.z, s: active.s, target: .6, hold: true }; imprints.push(active); }
        } else if (fine && p && !pressing) {
          if (!hover || Math.hypot(p.x - hover.x, p.z - hover.z) > .35) { if (hover) hover.hold = false; hover = { x: p.x, z: p.z, s: 0, target: .13, hold: true }; imprints.push(hover); }
        } else if (hover) { hover.hold = false; hover = null; }
        return p;
      },
      release() { pressing = false; if (active) active.hold = false; active = null; },
      leave() { if (hover) { hover.hold = false; hover = null; } },
      update(dt) {
        if (!imprints.length && !dirty) return;
        for (let k = imprints.length - 1; k >= 0; k--) {
          const im = imprints[k];
          if (im.hold) im.s += (im.target - im.s) * Math.min(1, dt * 7);
          else { im.s *= Math.exp(-dt / 1.4); if (im.s < .004) imprints.splice(k, 1); }
        }
        while (imprints.length > 40) imprints.shift();
        const { orig, topCount, y0, w } = geo.userData, P = geo.attributes.position.array, sig2 = 2 * .42 * .42;
        for (let v = 0; v < topCount; v++) {
          const o = v * 3, ox = orig[o], oy = orig[o + 1], oz = orig[o + 2];
          let d = 0;
          for (const im of imprints) { const dx = ox - im.x, dz = oz - im.z, q = dx * dx + dz * dz; if (q < 1.6) d += im.s * Math.exp(-q / sig2); }
          P[o + 1] = oy - Math.min(d, (oy - y0) * .6) * Math.min(1, w[v] * 3);
        }
        geo.attributes.position.needsUpdate = true; geo.computeVertexNormals();
        dirty = imprints.length > 0;
      },
    };
  }

  window.MP3D = { webgl: true, h, W, slab, pt, DEFS, svgPillow, LAYER_F, DIM, topY, pillowGeometry, canvasTex, knitTex, glowTex, marsTex, stdLights, makeRenderer, pillowMaterial, worldAt, foam };
})();
