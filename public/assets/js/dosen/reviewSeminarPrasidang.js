$(function () {
  $("#formReviewSempra").validate({
    rules: {
      komentar: {
        required: true,
      },
      status: {
        required: true,
      },
      rekomendasi_nilai: {
        required: true,
      },
    },
    errorClass: "text-danger",
    errorElement: "small",
    errorPlacement: function (error, element) {
      if (
        element.attr("name") == "status" ||
        element.attr("name") == "rekomendasi_nilai"
      ) {
        error.insertAfter(element.parent().parent().children(".last"));
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler: function (form) {
      form.submit();
    },
  });

  switch ($("#jenis").val()) {
    case "PROYEK MANDIRI":
    case "PROYEK TIM":
      $("#klien").parent().parent().show();
      break;
    case "SKRIPSI REGULER":
      $("#klien").parent().parent().hide();
      break;
  }

  //untuk sweetalert
  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),
      $("#flashdata #text").text(),
      $("#flashdata #icon").text().trim()
    );
  }
});
