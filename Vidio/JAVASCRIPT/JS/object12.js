const objek = {
    nama : "SMKN 2 BUDURAN",
    telp : "12345677",
    buah : ["apel","jeruk","mangga"],

    coba : function () {
        return "Coba Function Dalam Object";
    },

    boleh : true,
    "Tulis aja" : 767122943,
};

console.log(objek.buah[2]); // memanggil satu dari array harus pake [] angka array berawal dari 0
console.log(objek.nama); 
console.log(objek.coba());  // memanggil function di dalam object harus nama function lalu tanda kurung ()
console.log(objek.boleh); 