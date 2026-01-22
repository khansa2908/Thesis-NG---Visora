$(function () {
  //untuk sweetalert
  if ($("#flashdata").data("open") == true) {
    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );
  }

  const dtable = $("#jadwalSeminarPrasidang").DataTable({
    columnDefs: [
      {
        targets: [0, 1, 3, 5, 6, 7, 8],

        className: "text-center",
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
