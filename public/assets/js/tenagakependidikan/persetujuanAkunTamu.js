$(function () {
  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }

  $("#listAkunTamu").DataTable({
    columnDefs: [
      {
        targets: [0, 4, 5],

        className: "text-center",
      },

      {
        targets: 5,

        searchable: false,

        orderable: false,
      },

      {
        targets: "_all",

        className: "align-middle",
      },
    ],
  });

  $("#listAkunTamu")
    .DataTable()
    .on("search.dt order.dt", function (e) {
      let i = 1;

      $("#listAkunTamu")
        .DataTable()
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();

  $("#setujuiAkunDosPem button.btn-success").on("click", function () {
    Swal.fire({
      title: "Apakah anda yakin menjadikan akun ini sebagai Dosen Pembimbing?",
      text: "Akun yang telah disetujui tidak dapat dinonaktifkan",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, jadikan Dosen Pembimbing!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $("#setujuiAkunDosPem").submit();
      }
    });
  });

  $("#setujuiAkunKlien button.btn-success").on("click", function () {
    Swal.fire({
      title: "Apakah anda yakin menjadikan akun ini sebagai Klien?",
      text: "Akun yang telah disetujui tidak dapat dinonaktifkan",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, jadikan Klien!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $("#setujuiAkunKlien").submit();
      }
    });
  });

  $("#tolakAkun button.btn-danger").on("click", function () {
    Swal.fire({
      title: "Apakah anda yakin?",
      text: "Akun yang telah ditolak tidak dapat dikembalikan",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, tolak!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $("#tolakAkun").submit();
      }
    });
  });
});
