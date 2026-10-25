$(function () {
  $("#formDataDiri").validate({
    rules: {
      nama: {
        required: true,
        maxlength: 150,
      },
      instansi: {
        maxlength: 100,
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

  $("#inisial").on("input", function () {
    let value = $("#inisial").val();

    $("#inisial").val(value.toUpperCase());
  });

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }
});
