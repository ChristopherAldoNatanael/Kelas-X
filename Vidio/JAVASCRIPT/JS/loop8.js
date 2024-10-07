let mulai = 1;
let selesai = 10;

while (mulai <= selesai) {
  console.log(mulai);

  mulai++;
}
console.log("--------------------------------");

let start = 50;
let finish = 100;

do {
  console.log(start);
  start++;
} while (start <= finish);
console.log("--------------------------------");

let awal = 150;
let akhir = 200;

for (awal; awal <= akhir; awal++) {
  console.log(awal);
}

console.log("--------------------------------");
for (let i = 0; i < 10; i = i + 4) {
  console.log(i);
}
