$(function () {
  $("#pendaftaranTamu").validate({
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
      instansi: {
        required: true,
        maxlength: 100,
      },
      email: {
        required: true,
        email: true,
        maxlength: 120,
      },
      username: {
        required: true,
        rangelength: [6, 30],
      },
      password: {
        required: true,
        rangelength: [6, 12],
      },
      repassword: {
        required: true,
        samePassword: true,
      },
      persetujuan: {
        required: true,
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

  jQuery.validator.addMethod(
    "samePassword",

    function (value, element, param) {
      return this.optional(element) || value == $("#password").val();
    },

    "Konfirmasi password tidak sesuai"
  );

  $("#inisial").on("input", function () {
    $("#inisial").val($(this).val().toUpperCase());
  });

  $("#persetujuan").on("change", function () {
    if ($("#persetujuan").prop("checked") == true) {
      $("label[for='persetujuan']").removeClass("text-danger");
    }

    if ($("#persetujuan").prop("checked") == false) {
      $("label[for='persetujuan']").addClass("text-danger");
    }
  });

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }
});
