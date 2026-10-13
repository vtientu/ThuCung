'use strict';
const searchToggle = document.querySelector('#search-toggle');
const searchBar = document.querySelector('#search-bar');
searchToggle?.addEventListener('click', () => {
    searchBar.hidden = !searchBar.hidden;
    searchToggle.setAttribute('aria-expanded', String(!searchBar.hidden));
    if (!searchBar.hidden) searchBar.querySelector('input[type="search"]').focus();
});
const menuToggle = document.querySelector('#menu-toggle');
menuToggle?.addEventListener('click', () => {
    const open = document.querySelector('#navigation').classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(open));
});
document.querySelector('#sort-select')?.addEventListener('change', function () { this.form.requestSubmit(); });
let favorites = [];
try { favorites = JSON.parse(localStorage.getItem('mochi_favorites') || '[]'); if (!Array.isArray(favorites)) favorites = []; } catch (_) { favorites = []; }
const liveMessage = document.querySelector('#live-message');
let messageTimer;
function notify(message) {
    liveMessage.textContent = message;
    liveMessage.classList.add('visible');
    clearTimeout(messageTimer);
    messageTimer = setTimeout(() => liveMessage.classList.remove('visible'), 2500);
}
document.querySelectorAll('[data-favorite]').forEach(button => {
    const id = Number(button.dataset.favorite);
    button.setAttribute('aria-pressed', String(favorites.includes(id)));
    button.addEventListener('click', () => {
        const alreadySaved = favorites.includes(id);
        favorites = alreadySaved ? favorites.filter(item => item !== id) : [...favorites, id];
        button.setAttribute('aria-pressed', String(!alreadySaved));
        try { localStorage.setItem('mochi_favorites', JSON.stringify(favorites)); } catch (_) { /* Yêu thích vẫn dùng được trong phiên hiện tại. */ }
        notify(alreadySaved ? 'Đã bỏ khỏi danh sách yêu thích.' : 'Đã lưu món xinh vào danh sách yêu thích.');
    });
});
const policyDialog = document.querySelector('#policy-dialog');
document.querySelectorAll('[data-policy]').forEach(button => button.addEventListener('click', () => policyDialog.showModal()));
policyDialog?.querySelectorAll('.dialog-close').forEach(button => button.addEventListener('click', () => policyDialog.close()));
policyDialog?.addEventListener('click', event => { if (event.target === policyDialog) { const rect = policyDialog.getBoundingClientRect(); if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) policyDialog.close(); } });
setTimeout(() => { const toast = document.querySelector('.toast'); if (toast) { toast.classList.add('fade'); setTimeout(() => toast.remove(), 600); } }, 4000);
