/* Mars Pillow — pillow finder: one question at a time (without JavaScript all questions show at once) */
(() => {
  'use strict';
  const form = document.getElementById('quiz'); if (!form) return;
  const steps = [...form.querySelectorAll('.quiz-step')], submit = form.querySelector('button[type=submit]');
  const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches, hasGsap = typeof gsap !== 'undefined';
  const bar = document.createElement('div'); bar.className = 'quiz-bar'; bar.innerHTML = '<i></i>'; form.prepend(bar);
  let cur = 0;
  const show = (i, focus = true) => {
    cur = i;
    steps.forEach((s, k) => { s.hidden = k !== i; });
    submit.hidden = true;
    bar.firstChild.style.width = `${(i / steps.length) * 100}%`;
    const first = steps[i].querySelector('input:checked') || steps[i].querySelector('input');
    if (hasGsap && !REDUCED) gsap.from(steps[i], { x: 30, opacity: 0, duration: .4, ease: 'power2.out' });
    if (focus) first.focus({ preventScroll: true });
  };
  steps.forEach((s, i) => s.addEventListener('change', () => {
    if (i < steps.length - 1) setTimeout(() => show(i + 1), 180);
    else { bar.firstChild.style.width = '100%'; form.requestSubmit ? form.requestSubmit() : form.submit(); }
  }));
  const back = document.createElement('button'); back.type = 'button'; back.className = 'link-btn fine quiz-back'; back.textContent = '← Back';
  back.addEventListener('click', () => { if (cur > 0) show(cur - 1); });
  form.append(back);
  show(0, false);
})();
