$(function () {

  if ($("#flashdata").data("open") == true) {

    Swal.fire(
      $("#flashdata #title").text(),

      $("#flashdata #text").text(),

      $("#flashdata #icon").text().trim()
    );

  }



  $("#rubrikNilai").DataTable({

    columnDefs: [

      {

        targets: [0, 2],

        className: "text-center",

      },

      {

        targets: "_all",

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
  $("#rubrikNilai").DataTable()
    .on("order.dt search.dt", function () {
      let i = 1;

      $("#rubrikNilai").DataTable()
        .cells(null, 0, { search: "applied", order: "applied" })
        .every(function (cell) {
          this.data(i++);
        });
    })
    .draw();

});

