$(function () {

  $("#formTambahProposal").validate({

    rules: {

      judul: {

        required: true,

        minlength: 20,

        maxlength: 300,

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

      dosen_usulan1: {

        required: true,

      },

      dosen_usulan2: {

        required: true,

      },

      file_proposal: {

        required: true,

        extension: "pdf",

        filesize: 10000000,

      },

    },

    messages: {

      file_proposal: {

        extension: "File harus berekstensi PDF",

        filesize: "File tidak boleh melebihi 10MB",

      },

    },

    errorClass: "text-danger",

    errorElement: "small",

    errorPlacement: function (error, element) {

      if (element.attr("type") == "file") {

        error.insertAfter(element.parent().parent());

      } else {

        error.insertAfter(element);

      }

    },

    submitHandler: function (form) {

      form.submit();

    },

  });



  jQuery.validator.addMethod(

    "filesize",

    function (value, element, param) {

      return this.optional(element) || element.files[0].size <= param;

    },

    "Ukuran file melebihi batas"

  );



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



  $("#proposalMahasiswa").DataTable({

    columnDefs: [

      {

        targets: [0, 2, 3, 4, 5, 6, 7, 9],

        className: "text-center",

      },

      {

        targets: 9,

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

