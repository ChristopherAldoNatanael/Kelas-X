function coba() {
    a = document.querySelector(".isi");
    a.innerHTML = "Belajar EventListener";
    console.log("Coba EventListener");
}

//BISA MEMAKAI 3 CARA DI BAWAH INI UNTUK RUN FUNCTION

// judul.addEventListener("click", coba);

// judul.onmouseover = coba; //fungsi onmouseover yaitu jika mouse ngelewati langsung memanggil function coba

judul.onmouseover = function () {
    console.log("Coba Function Anonymous");
}