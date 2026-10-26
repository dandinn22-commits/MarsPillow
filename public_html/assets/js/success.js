/* Mars Pillow — order confirmation: send the purchase event once, or wait for the payment to confirm */
(() => {
  'use strict';
  const data = document.getElementById('purchase-data');
  if (data && window.MPUI) { try { window.MPUI.track.purchase(JSON.parse(data.textContent)); } catch (e) {} }
  const pending = document.getElementById('pending-msg');
  if (pending) {
    const n = parseInt(sessionStorage.getItem('mp_wait') || '0', 10);
    if (n < 12) { try { sessionStorage.setItem('mp_wait', String(n + 1)); } catch (e) {} setTimeout(() => location.reload(), 4000); }
  } else { try { sessionStorage.removeItem('mp_wait'); } catch (e) {} }
})();
