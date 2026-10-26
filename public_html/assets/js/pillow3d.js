/* Mars Pillow — shared 3D + SVG pillow helpers (used by home.js and product.js) */
(() => {
  'use strict';
  const webgl = (() => {
    if (typeof THREE === 'undefined') return false;
    try { const c = document.createElement('canvas'); return !!(c.getContext('webgl') || c.getContext('experimental-webgl')); } catch (e) { return false; }
  })();

  // Pillow cross-section: raised neck roll (t≈0.2), head dip, lower rear roll (t≈0.8)
  const g = (x, m, s) => Math.exp(-((x - m) ** 2) / (2 * s * s));
  const h = t => (0.5 + 0.5 * g(t, 0.2, 0.13) + 0.28 * g(t, 0.8, 0.15)) * Math.pow(Math.max(0, Math.sin(Math.PI * t)), 0.32);

  // ---- 2D SVG versions (fallback without WebGL, cart thumbnails) ----
  const W = 420, B = 150, HMAX = 92, X0 = 20, X1 = 400, N2 = 70;
  const pt = (t, f) => [X0 + t * (X1 - X0), B - f * HMAX * h(t)];
  function slab(f1, f2) {
    let d = '';
    for (let i = 0; i <= N2; i++) { const [x, y] = pt(i / N2, f2); d += (i ? 'L' : 'M') + x.toFixed(1) + ',' + y.toFixed(1); }
    for (let i = N2; i >= 0; i--) { const [x, y] = pt(i / N2, f1); d += 'L' + x.toFixed(1) + ',' + y.toFixed(1); }
    return d + 'Z';
  }
  const DEFS = `<defs>
    <linearGradient id="pgFront" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F5F3FF"/><stop offset=".55" stop-color="#D9D2F2"/><stop offset="1" stop-color="#8F84BF"/></linearGradient>
    <linearGradient id="pgBack" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#B7AEE0"/><stop offset="1" stop-color="#5B4B8A"/></linearGradient>
    <linearGradient id="pgCool" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7FC8E8"/><stop offset="1" stop-color="#A9B8F5"/></linearGradient>
    <linearGradient id="pgFoam" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#EDE6FF"/><stop offset="1" stop-color="#B8ABE3"/></linearGradient>
    <linearGradient id="pgBase" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7C6CB4"/><stop offset="1" stop-color="#3E3366"/></linearGradient>
  </defs>`;
  const svgPillow = (label = 'Illustration of the contoured Mars Pillow') =>
    `<svg viewBox="0 0 ${W} 190" role="img" aria-label="${label}">${DEFS}<g transform="translate(16,-18)"><path d="${slab(0, 1)}" fill="url(#pgBack)"/></g><path d="${slab(0, 1)}" fill="url(#pgFront)"/></svg>`;

  if (!webgl) { window.MP3D = { webgl: false, h, W, slab, pt, DEFS, svgPillow }; return; }

  // ---- 3D ----
  function pillowGeometry({ f1 = 0, f2 = 1, N = 96, M = 60, Wd = 4, D = 6.2, H = 1.25, inflate = 0 } = {}) {
    const pos = [], uv = [], idx = [];
    const surf = (f, top) => {
      const base = pos.length / 3;
      for (let j = 0; j <= M; j++) {
        const v = j / M, tp = Math.pow(Math.max(0, Math.sin(Math.PI * v)), 0.22);
        for (let i = 0; i <= N; i++) {
          const t = i / N;
          pos.push((t - .5) * Wd * (0.9 + 0.1 * tp) * (1 + inflate), f * H * h(t) * tp * (1 + inflate) - (f === 0 ? inflate * .05 : 0), (v - .5) * D * (1 + inflate * .5));
          uv.push(t * 6, v * 9);
        }
      }
      for (let j = 0; j < M; j++) for (let i = 0; i < N; i++) {
        const a = base + j * (N + 1) + i, b = a + (N + 1), c = a + 1, d = b + 1;
        if (top) idx.push(a, b, c, c, b, d); else idx.push(a, c, b, c, d, b);
      }
    };
    surf(f2, true); surf(f1, false);
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
    geo.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
    geo.setIndex(idx);
    geo.computeVertexNormals();
    geo.userData = { topCount: (N + 1) * (M + 1), orig: Float32Array.from(pos), H };
    return geo;
  }
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
        const { orig, topCount, H } = geo.userData, P = geo.attributes.position.array, sig2 = 2 * .42 * .42;
        for (let v = 0; v < topCount; v++) {
          const o = v * 3, ox = orig[o], oy = orig[o + 1], oz = orig[o + 2];
          let d = 0;
          for (const im of imprints) { const dx = ox - im.x, dz = oz - im.z, q = dx * dx + dz * dz; if (q < 1.6) d += im.s * Math.exp(-q / sig2); }
          P[o + 1] = oy - Math.min(d, oy * .78) * Math.min(1, (oy / H) * 20);
        }
        geo.attributes.position.needsUpdate = true; geo.computeVertexNormals();
        dirty = imprints.length > 0;
      },
    };
  }

  window.MP3D = { webgl: true, h, W, slab, pt, DEFS, svgPillow, pillowGeometry, canvasTex, knitTex, glowTex, marsTex, stdLights, makeRenderer, pillowMaterial, worldAt, foam };
})();
