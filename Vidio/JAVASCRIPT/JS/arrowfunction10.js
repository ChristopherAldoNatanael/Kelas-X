let fungsi = function (nama) {
  console.log("Belajar Function" + nama);
};

fungsi("Aldoooo");

let contoh = (nama) => {
  console.log("Belajar Arrow Function" + nama);
};

contoh("Haqi");

let tambah = function (a, b) {
  return a + b;
};

console.log(tambah(2, 3));

let plus = (a, b) => a + b;

console.log(plus(2, 3));

let hasil = (a) => a * 2;

console.log(hasil(5));

let lagi = () => console.log("Coba Lagi");

lagi();

let belajar = () => {
  console.log("Baris Satu");
  console.log("Baris Dua");
  console.log("Baris Tiga");
  console.log("Baris Selanjutnya");
};

belajar();

let nilai = 8;

let uji = nilai < 7 ? () => (Predikat = "Gagal") : () => (Predikat = "LULUS");
console.log(uji());
