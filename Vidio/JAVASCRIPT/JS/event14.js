function tampil(b) {
    a = document.querySelector("p").innerText= "Belajar Event js " + b; //Bisa menggunakan Ini 
    // a.innerText = "Belajar Event js"; //Bisa Menggunakan ini
    console.log("Belajar Event js");
}

judul.onclick = function () {
    // console.log("Belajar Event js menggunakan id");
    document.querySelector(".isi").innerHTML = "Belajar Event js menggunakan id";
}

