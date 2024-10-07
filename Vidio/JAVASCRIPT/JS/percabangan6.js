if (true) {
  console.log("Dijalankan Jika benar");
} else {
  console.log("Dijalankan Jika Salah");
}

let nilai = 100;
let standard = 76;
let berhasil = "LULUS";
let gagal = "TIDAK LULUS";
let batasatas = 100;
let batasbawah = 0;
let peringatan = "Nilai Salah";

if (nilai <= batasatas && nilai >= batasbawah) {
  if (nilai >= standard) {
    console.log(berhasil);
  } else {
    console.log(gagal);
  }
} else {
  console.log(peringatan);
}
