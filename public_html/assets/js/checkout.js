/* Mars Pillow — PayPal buttons. The order is created and captured on our server, never trusted from the browser. */
(() => {
  'use strict';
  const box = document.getElementById('paypal-buttons'), msg = document.getElementById('paypal-msg');
  if (!box) return;
  const post = async (url, body) => {
    const res = await fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': (window.MP || {}).csrf || '' }, body: JSON.stringify(body || {}) });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || 'Something went wrong. Please try again.');
    return data;
  };
  const start = () => {
    if (!window.paypal) { msg.textContent = 'PayPal could not load. Please refresh the page or pay by card.'; return; }
    paypal.Buttons({
      style: { layout: 'vertical', color: 'gold', shape: 'pill', label: 'paypal', height: 48 },
      createOrder: () => post('/api/paypal/create-order').then(d => d.id),
      onApprove: data => post('/api/paypal/capture-order', { orderID: data.orderID }).then(d => { window.location.href = d.redirect; }),
      onCancel: () => { msg.textContent = 'PayPal payment cancelled. You have not been charged.'; },
      onError: err => { msg.textContent = (err && err.message) || 'PayPal ran into a problem. You have not been charged. Please try again or pay by card.'; },
    }).render('#paypal-buttons');
  };
  if (window.paypal) start(); else window.addEventListener('load', start);
})();
