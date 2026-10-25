$(function () {
  $("#formTambahSemester").validate({
    rules: {
      tahunAkademik: {
        required: true,
        digits: true,
        min: 2000,
        max: 2100,
      },
      semester: {
        required: true,
      },
    },
    errorClass: "text-danger",
    errorElement: "small",
    submitHandler: function (form) {
      form.submit();
    },
  });

  $("#tableSemester #formHapusSemester").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Apakah anda ingin menghapus semester ini?",

      text: "Semester yang sudah dihapus tidak dapat dikembalikan",

      icon: "warning",

      showCancelButton: true,

      confirmButtonColor: "#3085d6",

      cancelButtonColor: "#d33",

      confirmButtonText: "Ya, Hapus!",

      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        e.target.submit();
      }
    });
  });

  $("#tableSemester #formAktivasiSemester").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Apakah anda ingin mengaktifkan semester ini?",

      icon: "warning",

      showCancelButton: true,

      confirmButtonColor: "#3085d6",

      cancelButtonColor: "#d33",

      confirmButtonText: "Ya, Aktfikan!",

      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        e.target.submit();
      }
    });
  });

  $("#tableSemester #formNonaktivasiSemester").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Apakah anda ingin menonaktifkan semester ini?",

      icon: "warning",

      showCancelButton: true,

      confirmButtonColor: "#3085d6",

      cancelButtonColor: "#d33",

      confirmButtonText: "Ya, Nonaktifkan!",

      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        e.target.submit();
      }
    });
  });

  //untuk sweetalert

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }

  let dtable = $("#tableSemester").DataTable({
    columnDefs: [
      {
        targets: [0, 2, 3],
        className: "text-center",
      },

      {
        targets: 3,
        searchable: false,
        orderable: false,
      },

      {
        targets: "_all",
        className: "align-middle",
      },
    ],
  });

  //auto generate number
  dtable
    .on("order.dt search.dt", function () {
      let i = 1;

      dtable
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();
});
