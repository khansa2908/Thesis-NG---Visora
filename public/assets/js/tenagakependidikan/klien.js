$(function () {
  $("#formUbahKlien").validate({
    rules: {
      nama: {
        required: true,
        maxlength: 150,
      },
      inisial: {
        required: true,
        alphabet: true,
        rangelength: [2, 3],
      },
      email: {
        required: true,
        email: true,
        maxlength: 120,
      },
    },
    messages: {
      inisial: {
        rangelength: "Inisial harus terdiri dari 2 atau 3 huruf",
      },
    },

    errorClass: "text-danger",

    errorElement: "small",

    errorPlacement: function (error, element) {
      if (element.attr("type") != "checkbox") {
        error.insertAfter(element.parent());
      }
    },

    highlight: function (element, errorClass) {
      $(element).addClass(errorClass);

      $(element.form)
        .find("label[for=" + element.id + "]")

        .addClass(errorClass);
    },

    unhighlight: function (element, errorClass) {
      $(element).removeClass(errorClass);

      $(element.form)
        .find("label[for=" + element.id + "]")

        .removeClass(errorClass);
    },

    submitHandler: function (form) {
      form.submit();
    },
  });

  jQuery.validator.addMethod(
    "alphabet",
    function (value, element, param) {
      return this.optional(element) || /^.[A-Z]+$/.test(value);
    },
    "Kolom harus berisikan huruf saja"
  );

  $("#tableKlien .btn-edit-klien").on("click", function (e) {
    const parent = $(this).parent().parent();
    const idKlien = parent.children(".id-klien").text();
    const inisial = parent.children(".inisial").text();
    const nama = parent.children(".nama").text();
    const email = parent.children(".email").text();
    const instansi = parent.children(".instansi").text();

    $("#ubahKlien").modal("show");

    $("#formUbahKlien #idKlien").val(idKlien);
    $("#formUbahKlien #nama").val(nama);
    $("#formUbahKlien #instansi").val(instansi);
    $("#formUbahKlien #email").val(email);
    $("#formUbahKlien #inisial").val(inisial);
  });

  $("#formUbahKlien #inisial").on("input", function (e) {
    const inisial = $("#formUbahKlien #inisial").val();

    $("#formUbahKlien #inisial").val(inisial.toUpperCase());
  });

  $("#tableKlien").DataTable({
    dom:
      "<'row mt-3'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [
      {
        extend: "excelHtml5",
        className: "btn-dark",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "pdfHtml5",
        className: "btn-dark",
        orientation: "landscape",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "colvis",
        className: "btn-dark",
      },
    ],
    columnDefs: [
      {
        targets: [0, 2, 4, 6],
        className: "text-center",
      },
      {
        targets: 6,
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
  $("#tableKlien")
    .DataTable()
    .on("order.dt search.dt", function () {
      let i = 1;

      $("#tableKlien")
        .DataTable()
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();

  $("#tableKlien #formInactiveKlien").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Apakah anda ingin menonaktifkan Klien ini?",

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

  $("#tableKlien #formReactiveKlien").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Apakah anda ingin mengaktifkan Klien ini?",

      icon: "warning",

      showCancelButton: true,

      confirmButtonColor: "#3085d6",

      cancelButtonColor: "#d33",

      confirmButtonText: "Ya, Aktifkan!",

      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        e.target.submit();
      }
    });
  });

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),
      $("#flashdata #text").text(),
      $("#flashdata #icon").text().trim()
    );
  }
});
