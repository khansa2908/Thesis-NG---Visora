document.addEventListener('DOMContentLoaded', function () {
    const judul = document.getElementById('judul');

    if (!judul) return;

    // Otomatis mengubah judul menjadi huruf kapital
    judul.addEventListener('input', function () {
        const posisiKursor = this.selectionStart;

        this.value = this.value.toUpperCase();

        this.setSelectionRange(posisiKursor, posisiKursor);
    });

    // Memblokir paste dan menampilkan pemberitahuan
    judul.addEventListener('paste', function (e) {
        e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Paste Tidak Diizinkan',
        text: 'Penulisan judul harus dilakukan secara langsung. Fitur salin dan tempel (paste) tidak diperbolehkan.',
        confirmButtonText: 'Mengerti'
    });
});
});