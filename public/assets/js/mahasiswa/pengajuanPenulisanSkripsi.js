
$(function () {

  // =========================================
  // VALIDASI FORM PENGAJUAN
  // =========================================

  $("#formDataPersetujuanSkripsi").validate({
    ignore: ":hidden:not(#dropdownWajib input[type='checkbox']):not(#dropdownSedang input[type='checkbox'])",

    rules: {

      sks_lulus: {
        required: true,
        digits: true,
        min: 1,
        max: 140,
      },

      pembimbing_akademik: {
        required: true,
      },

      jenis_ta: {
        required: true,
      },

      "mk_sedang_diambil[]": {
        required: true,
      },

      "mk_akan_diambil[]": {
        required: function () {
          return !$("#sudahLulusSemua").is(":checked");
        },
      },

      inputKhs: {
        required: true,
        extension: "pdf",
        filesize: 10000000,
      },

      inputKrs: {
        required: true,
        extension: "pdf",
        filesize: 10000000,
      },

      inputPersetujuanSkripsi: {
        required: true,
        extension: "pdf",
        filesize: 10000000,
      },

    },

    messages: {

      jenis_ta: {
        required: "Kolom ini harus diisi.",
      },

      "mk_sedang_diambil[]": {
        required: "Kolom ini harus diisi.",
      },

      "mk_akan_diambil[]": {
        required: "Kolom ini harus diisi.",
      },

      inputKhs: {
        extension: "File harus berekstensi PDF",
        filesize: "File tidak boleh melebihi 10MB",
      },

      inputKrs: {
        extension: "File harus berekstensi PDF",
        filesize: "File tidak boleh melebihi 10MB",
      },

      inputPersetujuanSkripsi: {
        extension: "File harus berekstensi PDF",
        filesize: "File tidak boleh melebihi 10MB",
      },

    },

    errorClass: "text-danger",
    errorElement: "small",

    errorPlacement: function (error, element) {

    if (element.attr("type") == "file") {

        error.insertAfter(element.parent().parent());

    } else if (element.attr("name") == "jenis_ta") {

        error.insertAfter(element);

    } else if (element.attr("name") == "mk_akan_diambil[]") {

        error.insertAfter($("#dropdownWajib"));

    } else if (element.attr("name") == "mk_sedang_diambil[]") {

        error.insertAfter($("#dropdownSedang"));

    } else {

        error.insertAfter(element);

    }

},

    submitHandler: function (form) {

      // Ambil mata kuliah wajib yang dipilih
      const wajibDipilih = $("#dropdownWajib .dropdown-mk-option input:checked")
        .map(function () {
          return this.value;
        }).get();

      // Ambil mata kuliah yang sedang berjalan
      const sedangDipilih = $("#dropdownSedang .dropdown-mk-option input:checked")
        .map(function () {
          return this.value;
        }).get();

      // Hapus input tersembunyi sebelumnya
      $(form).find('input[data-generated="mata-kuliah"]').remove();

      // Data mata kuliah wajib
      $("<input>", {
        type: "hidden",
        name: "mk_akan_diambil",
        value: $("#sudahLulusSemua").is(":checked")
          ? "Sudah lulus semua"
          : wajibDipilih.join(", "),
        "data-generated": "mata-kuliah"
      }).appendTo(form);

      // Data mata kuliah sedang berjalan
      $("<input>", {
        type: "hidden",
        name: "mk_sedang_diambil",
        value: sedangDipilih.join(", "),
        "data-generated": "mata-kuliah"
      }).appendTo(form);

      form.submit();

    },

  });


  // =========================================
  // VALIDASI UKURAN FILE
  // =========================================

  jQuery.validator.addMethod(
    "filesize",
    function (value, element, param) {
      return this.optional(element) || element.files[0].size <= param;
    },
    "Ukuran file melebihi batas"
  );


  // =========================================
  // TINJAU FILE KHS
  // =========================================

  // BASE_URL didapatkan dari constant.js

  $("#tinjauKhs").on("click", function () {

    const file = $(this).parent().parent().children("input").val();
    const npm = $(this).data("npm");

    $("#filePreview").modal("show");
    $("#filePreviewLabel").text("Tinjau File KHS");

    $("#previewContainer iframe").attr(
      "src",
      `${BASE_URL}folderKHS/${file}`
    );

    $("#filePreview #formHapus").attr(
      "action",
      `${BASE_URL}mahasiswa/deleteKhs/${npm}`
    );

    $("#filePreview #formUnduh").attr(
      "action",
      `${BASE_URL}mahasiswa/downloadKhs/${npm}`
    );

  });


  // =========================================
  // TINJAU FILE KRS
  // =========================================

  $("#tinjauKrs").on("click", function () {

    const file = $(this).parent().parent().children("input").val();
    const npm = $(this).data("npm");

    $("#filePreview").modal("show");
    $("#filePreviewLabel").text("Tinjau File KRS");

    $("#previewContainer iframe").attr(
      "src",
      `${BASE_URL}folderKRS/${file}`
    );

    $("#filePreview #formHapus").attr(
      "action",
      `${BASE_URL}mahasiswa/deleteKrs/${npm}`
    );

    $("#filePreview #formUnduh").attr(
      "action",
      `${BASE_URL}mahasiswa/downloadKrs/${npm}`
    );

  });


  // =========================================
  // TINJAU FILE PERSETUJUAN
  // =========================================

  $("#tinjauPersetujuan").on("click", function () {

    const file = $(this).parent().parent().children("input").val();
    const npm = $(this).data("npm");

    $("#filePreview").modal("show");

    $("#filePreviewLabel").text(
      "Tinjau File Persetujuan Penulisan Skripsi"
    );

    $("#previewContainer iframe").attr(
      "src",
      `${BASE_URL}folderPersetujuanSkripsi/${file}`
    );

    $("#filePreview #formHapus").attr(
      "action",
      `${BASE_URL}mahasiswa/deletePersetujuanSkripsi/${npm}`
    );

    $("#filePreview #formUnduh").attr(
      "action",
      `${BASE_URL}mahasiswa/downloadPersetujuanSkripsi/${npm}`
    );

  });


  // =========================================
  // MENAMPILKAN NAMA FILE
  // =========================================

  $("input[type='file']").on("change", function (e) {

    if (e.target.files.length > 0) {
      $(`label[for="${e.target.id}"]`).text(e.target.files[0].name);
    }

  });


  // =========================================
  // SWEETALERT
  // =========================================

  if ($("#flashdata").data("open") == true) {

    Swal.fire(
      $("#flashdata #title").text(),
      $("#flashdata #text").text(),
      $("#flashdata #icon").text().trim()
    );

  }

});
