/*
 * Dashboard admin Leo Swimming Course.
 * Semua perilaku dipasang lewat atribut data-* di Blade, jadi tidak perlu mengedit file ini
 * untuk menambah tombol atau halaman baru.
 *
 *   data-nav-toggle            buka/tutup menu samping (HP)
 *   data-modal-open="id"       buka jendela pop-up   (data-select="nama=nilai" memilih satu pilihan)
 *   data-modal-close           tutup jendela pop-up
 *   data-auto-open             jendela terbuka sendiri (dipasang server saat validasi gagal)
 *   data-confirm="pesan"       form minta konfirmasi sebelum dikirim
 *   data-autosubmit            select / saklar langsung mengirim form-nya
 *   data-summary-form          form dengan kotak "Ringkasan" yang berubah sendiri
 *   data-stepper               kotak angka dengan tombol − dan +
 *   data-savebar-form          form dengan bilah "Ada perubahan yang belum disimpan"
 *   data-remove-row            hapus satu baris harga
 *   data-toggle-password="id"  tampilkan / sembunyikan kata sandi
 *   data-copy="teks"           salin ke papan klip
 *   data-insert + data-target  sisipkan teks ke kolom isian
 *   data-upload                pratinjau foto sebelum diunggah
 */
(function () {
    'use strict';

    var $ = function (selector, root) { return (root || document).querySelector(selector); };
    var $$ = function (selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); };

    /* ---------- Menu samping (HP dan tablet) ---------- */

    function setNav(open) {
        document.body.classList.toggle('nav-open', open);
    }

    $$('[data-nav-toggle]').forEach(function (el) {
        el.addEventListener('click', function () {
            setNav(!document.body.classList.contains('nav-open'));
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 960) setNav(false);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setNav(false);
    });


    /* ---------- Jendela pop-up ---------- */

    function openModal(dialog) {
        if (!dialog || dialog.open) return;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', '');
        $$('form[data-summary-form]', dialog).forEach(updateSummary);
    }

    $$('[data-modal-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            var dialog = document.getElementById(button.getAttribute('data-modal-open'));
            var preset = button.getAttribute('data-select');

            if (dialog && preset) {
                var pair = preset.split('=');
                var radio = $('input[name="' + pair[0] + '"][value="' + pair[1] + '"]', dialog);
                if (radio) radio.checked = true;
            }

            openModal(dialog);
        });
    });

    $$('dialog.modal').forEach(function (dialog) {
        dialog.addEventListener('click', function (e) {
            // Klik di area gelap (bukan di kotak putih) menutup jendela.
            if (e.target === dialog) dialog.close();
        });

        if (dialog.hasAttribute('data-auto-open')) {
            openModal(dialog);
            var errors = $('.modal-errors', dialog);
            if (errors && errors.scrollIntoView) errors.scrollIntoView({ block: 'nearest' });
        }
    });

    $$('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            var dialog = button.closest('dialog');
            if (dialog) dialog.close();
        });
    });


    /* ---------- Konfirmasi, kirim otomatis, pesan sukses ---------- */

    $$('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    $$('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () {
            if (el.form) el.form.submit();
        });
    });

    $$('[data-flash]').forEach(function (flash) {
        var timer = setTimeout(function () { flash.remove(); }, 6000);
        var close = $('[data-flash-close]', flash);

        if (close) {
            close.addEventListener('click', function () {
                clearTimeout(timer);
                flash.remove();
            });
        }
    });


    /* ---------- Kotak "Ringkasan" ---------- */

    /** Nilai satu kolom form sebagai teks: pilihan chip memakai tulisannya, bukan nilainya. */
    function fieldText(form, name) {
        var parts = [];

        $$('[name="' + name + '"]', form).forEach(function (el) {
            if (el.type === 'hidden') return;

            if (el.type === 'radio' || el.type === 'checkbox') {
                if (el.checked) parts.push(el.getAttribute('data-label') || el.value);
            } else if (el.tagName === 'SELECT') {
                if (el.value) parts.push(el.options[el.selectedIndex].text);
            } else if (el.value.trim() !== '') {
                parts.push(el.value.trim());
            }
        });

        if (name === 'price' && parts.length) {
            parts[0] = Number(parts[0]).toLocaleString('id-ID');
        }

        return parts.join(', ');
    }

    function updateSummary(form) {
        var box = $('[data-summary]', form);
        var out = box && $('[data-summary-text]', box);
        if (!out) return;

        var template = box.getAttribute('data-summary').replace(/^Ringkasan:\s*/, '');

        out.textContent = template.replace(/\{([a-z_\[\]]+)\}/g, function (_, name) {
            return fieldText(form, name) || '…';
        });
    }

    $$('form[data-summary-form]').forEach(function (form) {
        ['input', 'change'].forEach(function (type) {
            form.addEventListener(type, function () { updateSummary(form); });
        });
        updateSummary(form);
    });


    /* ---------- Kotak angka (− dan +) ---------- */

    $$('[data-stepper]').forEach(function (box) {
        var input = $('input', box);

        $$('[data-step]', box).forEach(function (button) {
            button.addEventListener('click', function () {
                var min = input.min !== '' ? Number(input.min) : -Infinity;
                var max = input.max !== '' ? Number(input.max) : Infinity;
                var next = (Number(input.value) || 0) + Number(button.getAttribute('data-step'));

                input.value = Math.min(max, Math.max(min, next));
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    });


    /* ---------- Kata sandi, salin, sisipkan teks ---------- */

    $$('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-toggle-password'));
            if (!input) return;

            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Sembunyikan' : 'Tampilkan';
        });
    });

    $$('[data-copy]').forEach(function (button) {
        button.addEventListener('click', function () {
            var text = button.getAttribute('data-copy');
            var label = $('[data-copy-label]', button);
            var original = label ? label.textContent : '';

            var done = function () {
                if (!label) return;
                label.textContent = button.getAttribute('data-copied') || 'Tersalin';
                setTimeout(function () { label.textContent = original; }, 2000);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done, function () { window.prompt('Salin link ini:', text); });
            } else {
                window.prompt('Salin link ini:', text);
            }
        });
    });

    $$('[data-insert]').forEach(function (button) {
        button.addEventListener('click', function () {
            var field = document.getElementById(button.getAttribute('data-target'));
            if (!field) return;

            var token = button.getAttribute('data-insert');
            var start = field.selectionStart == null ? field.value.length : field.selectionStart;
            var end = field.selectionEnd == null ? start : field.selectionEnd;

            field.value = field.value.slice(0, start) + token + field.value.slice(end);
            field.focus();
            field.setSelectionRange(start + token.length, start + token.length);
            field.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });


    /* ---------- Pratinjau foto sebelum diunggah ---------- */

    $$('[data-upload]').forEach(function (box) {
        var input = $('[data-upload-input]', box);
        var preview = $('[data-upload-preview]', box);
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


    /* ---------- Ukuran tulisan: langsung terlihat sebelum disimpan ---------- */

    $$('[data-fontsize] input').forEach(function (input) {
        input.addEventListener('change', function () {
            var root = document.documentElement;
            root.className = root.className.replace(/\bfs-\w+\b/g, '').trim() + ' fs-' + input.value;
        });
    });


    /* ---------- Bilah simpan: "Ada perubahan yang belum disimpan" ---------- */

    $$('form[data-savebar-form]').forEach(function (form) {
        var bar = $('[data-savebar]', form);
        if (!bar) return;

        var clean = $('[data-savebar-clean]', bar);
        var dirty = $('[data-savebar-dirty]', bar);
        var reset = $('[data-savebar-reset]', bar);
        var submitting = false;

        var snapshot = function () {
            return new URLSearchParams(new FormData(form)).toString();
        };

        var initial = snapshot();

        var refresh = function () {
            var changed = snapshot() !== initial;
            clean.hidden = changed;
            dirty.hidden = !changed;
            if (reset) reset.hidden = !changed;
            return changed;
        };

        ['input', 'change'].forEach(function (type) {
            form.addEventListener(type, refresh);
        });

        form.addEventListener('submit', function () { submitting = true; });
        form.addEventListener('dirty', refresh);

        if (reset) {
            reset.addEventListener('click', function () {
                submitting = true;
                window.location.reload();
            });
        }

        window.addEventListener('beforeunload', function (e) {
            if (!submitting && refresh()) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });


    /* ---------- Paket dan harga: hapus baris + contoh tampilan ---------- */

    function rupiah(value) {
        return 'Rp' + (Number(value) || 0).toLocaleString('id-ID');
    }

    function renderPreview(card) {
        var panel = $('[data-preview-card]');
        if (!panel || !card) return;

        panel.setAttribute('data-preview-card', card.getAttribute('data-package'));
        $('[data-preview-title]', panel).textContent = $('[data-preview-name]', card).value;

        var rows = $$('[data-row]', card).map(function (row) {
            var visible = $('input[type="checkbox"][name$="[visible]"]', row);

            return {
                meetings: Number($('[data-preview-meet]', row).value) || 0,
                price: $('[data-preview-price]', row).value,
                bonus: Number($('[data-preview-bonus]', row).value) || 0,
                visible: !visible || visible.checked
            };
        }).filter(function (row) { return row.visible; })
          .sort(function (a, b) { return a.meetings - b.meetings; });

        var list = $('[data-preview-list]', panel);
        list.innerHTML = '';

        rows.forEach(function (row) {
            var item = document.createElement('li');
            var left = document.createElement('span');
            var price = document.createElement('b');

            left.textContent = row.meetings + '× pertemuan';

            if (row.bonus > 0) {
                var bonus = document.createElement('em');
                bonus.textContent = '+' + row.bonus + '× gratis';
                left.appendChild(bonus);
            }

            price.textContent = rupiah(row.price);
            item.appendChild(left);
            item.appendChild(price);
            list.appendChild(item);
        });
    }

    $$('[data-package]').forEach(function (card) {
        ['input', 'change', 'focusin'].forEach(function (type) {
            card.addEventListener(type, function () { renderPreview(card); });
        });

        card.addEventListener('click', function (e) {
            var button = e.target.closest('[data-remove-row]');
            if (!button) return;

            if ($$('[data-row]', card).length <= 1) {
                window.alert('Setiap kelas minimal punya satu pilihan paket. Ubah angkanya saja, atau matikan "Tampil di website".');
                return;
            }

            button.closest('[data-row]').remove();
            renderPreview(card);

            var form = card.closest('form');
            if (form) form.dispatchEvent(new Event('dirty'));
        });
    });
})();
