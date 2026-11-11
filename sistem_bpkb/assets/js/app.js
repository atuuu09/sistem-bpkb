// =======================================
// BPKBSys — app.js
// =======================================

document.addEventListener('DOMContentLoaded', function () {

    // ── DataTables (auto-init semua tabel .dt-table) ──────────────
    if (typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
        $('.dt-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>t<"d-flex justify-content-between align-items-center"ip>',
        });
    }

    // ── Auto-hide alert setelah 4 detik ──────────────────────────
    document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // ── Konfirmasi hapus ──────────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || 'Yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // ── Tombol cetak ─────────────────────────────────────────────
    document.querySelectorAll('.btn-print').forEach(function (btn) {
        btn.addEventListener('click', () => window.print());
    });

    // ── Topbar: tanggal ──────────────────────────────────────────
    const el = document.getElementById('live-date');
    if (el) {
        const now = new Date();
        const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        el.textContent = now.toLocaleDateString('id-ID', opts);
    }

});

// Format angka ribuan saat mengetik
function formatRibuan(input) {
    let raw = input.value.replace(/\D/g, '');
    input.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
}
