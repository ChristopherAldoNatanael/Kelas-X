let coba = function () {
  return "coba function";
};

let buah = [
    "apel", 
    "mangga", 
    "jeruk", 
    10, 
    coba(), 
    (tes = () => "hasil return arrow function"),
    function nama() {
        return "SMKN 2 Buduran"
    }
];

console.log(buah);

console.log(buah[0]);
console.log(buah[1]);

for (let i in buah) {
  console.log(buah[i]);
}

console.log(buah[5]());

console.log(buah[6]()); // memanggil satu dari array harus pake [] angka array berawal dari 0
