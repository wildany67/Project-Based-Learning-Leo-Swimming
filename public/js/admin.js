/* Dashboard admin: menu geser di HP, konfirmasi hapus, ubah status otomatis, pratinjau foto. */
(function () {
    'use strict';

    /* ---------- Menu samping (HP & tablet) ---------- */
    var body = document.body;

    function setNav(open) {
        body.classList.toggle('nav-open', open);
    }

    document.querySelectorAll('[data-nav-toggle]').forEach(function (el) {
        el.addEventListener('click', function () {
            setNav(!body.classList.contains('nav-open'));
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setNav(false);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 960) setNav(false);
    });

    /* ---------- Konfirmasi sebelum hapus ---------- */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    /* ---------- Ubah status dari tabel: kirim begitu dipilih ---------- */
    document.querySelectorAll('select[data-autosubmit]').forEach(function (select) {
        select.addEventListener('change', function () {
            select.form.submit();
        });
    });

    /* ---------- Pesan sukses hilang sendiri ---------- */
    document.querySelectorAll('[data-flash]').forEach(function (flash) {
        var hide = function () { flash.remove(); };
        var timer = setTimeout(hide, 6000);

        var close = flash.querySelector('[data-flash-close]');
        if (close) {
            close.addEventListener('click', function () {
                clearTimeout(timer);
                hide();
            });
        }
    });

    /* ---------- Pratinjau foto sebelum diunggah ---------- */
    document.querySelectorAll('[data-upload]').forEach(function (box) {
        var input = box.querySelector('[data-upload-input]');
        var preview = box.querySelector('[data-upload-preview]');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file || file.type.indexOf('image/') !== 0) return;

            var img = document.createElement('img');
            img.alt = 'Pratinjau foto';
            img.src = URL.createObjectURL(file);
            img.onload = function () { URL.revokeObjectURL(img.src); };

            preview.innerHTML = '';
            preview.appendChild(img);
        });
    });
})();
