$(function () {
  $("#formUbahDosen").validate({
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

  $("#tableDosen .btn-edit-dosen").on("click", function (e) {
    const parent = $(this).parent().parent();
    const idDosen = parent.children(".id-dosen").text();
    const inisial = parent.children(".inisial").text();
    const nama = parent.children(".nama").text();
    const idProdi = parent.children(".prodi").data("id");
    const email = parent.children(".email").text();

    $("#ubahDosen").modal("show");

    $("#formUbahDosen #idDosen").val(idDosen);
    $("#formUbahDosen #prodi").val(idProdi);
    $("#formUbahDosen #nama").val(nama);
    $("#formUbahDosen #email").val(email);
    $("#formUbahDosen #inisial").val(inisial);
  });

  $("#formUbahDosen #inisial").on("input", function (e) {
    const inisial = $("#formUbahDosen #inisial").val();

    $("#formUbahDosen #inisial").val(inisial.toUpperCase());
  });

  $("#tableDosen").DataTable({
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

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),
      $("#flashdata #text").text(),
      $("#flashdata #icon").text().trim()
    );
  }
});
