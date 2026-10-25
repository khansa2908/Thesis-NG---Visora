$(function () {
  $("#formTambahSkripsi").validate({
    rules: {
      judul: {
        required: true,

        minlength: 20,

        maxlength: 300,
      },

      jenis_skripsi: {
        required: true,
      },
      klien: {
        required: true,
        maxlength: 100,
      },
      sifat: {
        required: true,
      },

      sumber: {
        required: true,
      },

      bidang: {
        required: true,
      },
    },

    errorClass: "text-danger",

    errorElement: "small",

    submitHandler: function (form) {
      form.submit();
    },
  });

  $("#jenis_skripsi").on("change", function () {
    const jenis = $(this).val();

    switch (jenis.toLowerCase()) {
      case "proyek tim":
      case "proyek mandiri":
        $("#klien").parent().parent().show();
        break;
      case "skripsi reguler":
        $("#klien").parent().parent().hide();
        break;
    }
  });

  //untuk nampilin nama file

  $("input[type='file']").on("change", function (e) {
    $(`label[for="${e.target.id}"]`).text(e.target.files[0].name);
  });

  //untuk sweetalert

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }

  $("#tableSkripsi").DataTable({
    columnDefs: [
      {
        targets: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10],

        className: "text-center",
      },

      {
        targets: 10,

        searchable: false,

        orderable: false,
      },

      {
        targets: "_all",

        className: "align-middle",
      },
    ],
  });
});
