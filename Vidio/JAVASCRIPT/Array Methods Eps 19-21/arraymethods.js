// array - bisa berupa String, number, objek, function, campuran

let nilai = [
  { nama: "aldo", ipa: 80, bahasa: 80, matematika: 70 },
  { nama: "haqi", ipa: 70, bahasa: 89, Matematika: 60 },
  { nama: "daniel", ipa: 80, bahasa: 76, Matematika: 50 },
  { nama: "akmal", ipa: 87, bahasa: 85, Matematika: 40 },
];

let nama = ["aldo", "haqi", "daniel", "akmal"];

// nama.push("tirta","tobias"); // .push  menambahkan nilai arrray di bagian akhir

// console.log(nama.shift()); // .shift di gunakan untuk memanggil nilai array pertama

// nama.unshift("natan","islam"); // .unshift menambahkan nilai arrray di bagian awal

// console.log(nama.slice(0,3)) // slice mengambil dari nilai array tetapi tidak nge hapus nilai array

let mapel = ["ipa", "bahasa", "matematika"];
// console.log(nama.concat(mapel));
// console.log(nama.concat(['ips','pkn','sejarah']))

// console.log(nama.splice(0,3))  // splice mengambil dari nilai array tetapi nge hapus nilai array

// console.log(nama.pop()); // .pop di gunakan untuk memanggil nilai array terakhir

// console.log(nilai);

// console.log(nama[0]); // memanggil satu nilai array

// console.log(nama);

// for (let index = 0; index < nama.length; index++) {
//     console.log(nama[index]);
// } //ini sama yang bawahnya sama lebih singkat yang bawah

// nama.forEach(function (a) {
//     console.log(a);
// })

// nama.forEach(a => console.log(a)) //Lebih Singkat lagi

// nilai.filter(function (a) {
//     if (a.ipa > 80) {
//         console.log(a.nama);
//     }
// });

// console.log(nilai);

// nilai.filter((a) =>
//     a.ipa > 79 && a.matematika > 60 ? console.log(a.nama) : null //Jika Ingin Memakai 1 line if
// );

// let siswa = nilai.map(function (b) {
//     return b.nama;
// });

// let siswa = nilai.map(a => [a.nama, a.ipa,a.bahasa]); // => adalah AeroFunction

// console.log(siswa);

// mapel.sort();

// console.log(mapel);

// let hasil = nilai.reduce(function (a, b) {
//   return (a = a + b.ipa);
// }, 0);

let hasil = nilai.reduce((a, b) => (a = a + b.ipa), 0);

console.log(hasil);
