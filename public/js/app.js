(function () {
    'use strict';

    /* ---------- Menu hamburger (HP) ---------- */
    var toggle = document.querySelector('.nav-toggle');
    var menu = document.getElementById('nav-menu');

    function setMenu(open) {
        if (!toggle || !menu) return;
        menu.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            setMenu(toggle.getAttribute('aria-expanded') !== 'true');
        });
        menu.addEventListener('click', function (e) {
            if (e.target.closest('a')) setMenu(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setMenu(false);
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) setMenu(false);
        });
    }

    /* ---------- Garis bawah menu aktif saat scroll ---------- */
    var links = document.querySelectorAll('[data-spy]');
    var targets = ['beranda', 'program', 'lokasi', 'kontak']
        .map(function (id) { return document.getElementById(id); })
        .filter(Boolean);

    if (!links.length || !targets.length) return; // halaman /daftar: tidak ada section

    function setActive(id) {
        links.forEach(function (a) {
            a.classList.toggle('is-active', a.dataset.spy === id);
        });
    }

    function update() {
        var header = document.querySelector('.site-header');
        var offset = (header ? header.offsetHeight : 80) + 40;
        var current = targets[0].id;

        targets.forEach(function (el) {
            if (el.getBoundingClientRect().top <= offset) current = el.id;
        });

        // mentok di bawah halaman (footer) = Kontak
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 4) {
            current = 'kontak';
        }
        setActive(current);
    }

    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('load', update);
    update();
})();
