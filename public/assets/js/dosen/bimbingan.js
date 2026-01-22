$(function () {
  const urlSearchParams = new URLSearchParams(window.location.search);
  const kodeSemester = urlSearchParams.get("semester");
  if (kodeSemester != null) $(`#semesterOption`).val(kodeSemester);

  $("#semesterOption").on("change", function () {
    $("#semesterForm").submit();
  });

  //untuk sweetalert

  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }

  $("#mahasiswaBimbingan").DataTable({
    columnDefs: [
      {
        targets: [0, 1, 5, 7],

        className: "text-center",
      },

      {
        targets: 7,

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
