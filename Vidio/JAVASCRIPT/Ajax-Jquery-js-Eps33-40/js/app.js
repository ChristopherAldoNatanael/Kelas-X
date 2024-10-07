$(document).ready(function () {
  let id = "";
  let pelanggan = "";
  let alamat = "";
  let telp = "";

  $("#submit").click(function (e) {
    e.preventDefault();
    id = $("#id").val();
    pelanggan = $("#pelanggan").val();
    alamat = $("#alamat").val();
    telp = $("#telp").val();

    if (id === "") {
      insertData();
    } else {
      updateData();
    }

    $("#id").val("");
    $("#pelanggan").val("");
    $("#alamat").val("");
    $("#telp").val("");
  });

  $("#btn-tambah").click(function (e) {
    e.preventDefault();
    $("#title").html("<p>Tambah Data</p>");

    $("#id").val("");
    $("#pelanggan").val("");
    $("#alamat").val("");
    $("#telp").val("");
  });

  $("tbody").on("click", ".btn-del", function () {
    let id = $(this).attr("data-id");
    if (confirm("yakin akan Menghapus ? ")) {
      deleteData(id);
    }
  });

  $("tbody").on("click", ".btn-ubah", function () {
    id = $(this).attr("data-id");
    $("#title").html("<p>Ubah Data</p>");
    selectUpdate(id);
  });

  function selectUpdate(id) {
    let idpelanggan = {
      idpelanggan: id,
    };

    $.ajax({
      type: "post",
      url: "php/selectupdate.php",
      cache: false,
      data: JSON.stringify(idpelanggan),
      success: function (response) {
        let data = JSON.parse(response);

        $("#id").val(data.idpelanggan);
        $("#pelanggan").val(data.pelanggan);
        $("#alamat").val(data.alamat);
        $("#telp").val(data.telp);
      },
    });
  }

  function selectData() {
    $.ajax({
      type: "post",
      url: "php/select.php",
      cache: false,
      dataType: "json",
      success: function (response) {
        let out = "";
        let no = 1;
        $.each(response, function (key, val) {
          out += `<tr>
                    <td>${no++}</td>
                    <td>${val.pelanggan}</td>
                    <td>${val.alamat}</td>
                    <td>${val.telp}</td>
                    <td><button type="button" class="btn btn-danger btn-del" data-id="${val.idpelanggan}">DELETE</button></td>
                    <td><button type="button" class="btn btn-warning btn-ubah" data-id="${val.idpelanggan}">UBAH</button></td>
                    </tr>`;
        });
        $("#isidata").html(out);
      },
    });
  }

  function insertData() {
    let dataPelanggan = {
      pelanggan: pelanggan,
      alamat: alamat,
      telp: telp,
    };

    $.ajax({
      type: "post",
      url: "php/insert.php",
      cache: false,
      data: JSON.stringify(dataPelanggan),
      success: function (response) {
        let out = `<p>${response}</p>`;
        $("#msg").html(out);
        selectData(); // Refresh data after insertion
      },
    });
  }

  function deleteData(id) {
    let idpelanggan = {
      idpelanggan: id,
    };

    $.ajax({
      type: "post",
      url: "php/delete.php",
      cache: false,
      data: JSON.stringify(idpelanggan),
      success: function (response) {
        let out = `<p>${response}</p>`;
        $("#msg").html(out);
        selectData(); // Refresh data after deletion
      },
    });
  }

  function updateData() {
    let dataPelanggan = {
      idpelanggan: id,
      pelanggan: pelanggan,
      alamat: alamat,
      telp: telp,
    };

    $.ajax({
      type: "post",
      url: "php/update.php",
      cache: false,
      data: JSON.stringify(dataPelanggan),
      success: function (response) {
        let out = `<p>${response}</p>`;
        $("#msg").html(out);
        selectData(); // Refresh data after update
      },
    });
  }

  selectData();
});
