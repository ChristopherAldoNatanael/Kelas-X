let tblmenu = [
  { idmenu: 1, idkategori: 1, menu: "Basreng", gambar: "basreng.png", harga: 8000 },
  { idmenu: 2, idkategori: 1, menu: "Makroni", gambar: "makroni.png", harga: 8000 },
  { idmenu: 3, idkategori: 2, menu: "Bakmie Goreng Biasa", gambar: "bakmie.png", harga: 12000 },
  { idmenu: 4, idkategori: 2, menu: "Bakmie Goreng Seafood", gambar: "seafood.png", harga: 15000 },
  { idmenu: 5, idkategori: 3, menu: "Es Mojito", gambar: "mojito.png", harga: 10000 },
  { idmenu: 6, idkategori: 3, menu: "Es Dawet", gambar: "dawet.png", harga: 5000 },
  { idmenu: 7, idkategori: 3, menu: "Es Cincau", gambar: "cincau.png", harga: 5000 },
  { idmenu: 8, idkategori: 3, menu: "Es Cao", gambar: "cao.png", harga: 5000 },
  { idmenu: 9, idkategori: 4, menu: "Jus Jambu", gambar: "jusjambu.png", harga: 7000 },
];

let tampil = tblmenu
  .map(function (kolom) {
    return ` <div class="product-content">
          <div class="image">
            <img src="images/${kolom.gambar}" alt="" />
          </div>
          <div class="title">
            <h2>${kolom.menu}</h2>
          </div>
          <div class="harga">
            <h2>Rp.${kolom.harga}</h2>
          </div>

          <div class="btn-beli">
            <button data-idmenu=${kolom.idmenu}>Beli</button>
          </div>
        </div>`;
  })
  .join("");

let isi = document.querySelector(".product");
isi.innerHTML = tampil;

let btnbeli = document.querySelectorAll(".btn-beli > button");

let cart = [];

for (let index = 0; index < btnbeli.length; index++) {
  btnbeli[index].onclick = function () {
    // console.log(btnbeli[index].dataset["idmenu"]);
    // cart.push(btnbeli[index].dataset["idmenu"]);

    tblmenu.filter(function (a) {
      if (a.idmenu == btnbeli[index].dataset["idmenu"]) {
        cart.push(a);
        console.log(cart);
      }
    });
  };
}

// console.log(cart);
