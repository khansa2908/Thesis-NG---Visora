$(function () {
  const urlSearchParams = new URLSearchParams(window.location.search);
  const kodeSemester = urlSearchParams.get("semester");
  if (kodeSemester != null) $(`#semesterOption`).val(kodeSemester);

  $("#semesterOption").on("change", function () {
    $("#semesterForm").submit();
  });

  $("#nav-sinkronus-tab").on("click", function () {
    $("#nav-sinkronus-tab").tab("show");
  });

  $("#nav-asinkronus-tab").on("click", function () {
    $("#nav-asinkronus-tab").tab("show");
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

  $("#tableSemproSinkronus").DataTable({
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
        targets: [0, 1, 3, 5, 6, 8, 9, 10, 12],

        className: "text-center",
      },
      {
        targets: "_all",

        className: "align-middle",
      },
    ],
  });

  $("#tableSemproSinkronus")
    .DataTable()
    .on("search.dt order.dt", function (e) {
      let i = 1;

      $("#tableSemproSinkronus")
        .DataTable()
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();

  $("#tableSemproAsinkronus").DataTable({
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
        targets: [0, 1, 3, 5, 6, 7, 8, 10],

        className: "text-center",
      },
      {
        targets: "_all",

        className: "align-middle",
      },
    ],
  });

  $("#tableSemproAsinkronus")
    .DataTable()
    .on("search.dt order.dt", function (e) {
      let i = 1;

      $("#tableSemproAsinkronus")
        .DataTable()
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();
});
