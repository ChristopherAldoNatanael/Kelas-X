<?php
/*
* Dasar Pemrograman PHP
* File ini berisi materi dasar pemrograman PHP
*/

// Pengenalan PHP
echo "<h1>Dasar Pemrograman PHP</h1>";
echo "<p>PHP (PHP: Hypertext Preprocessor) adalah bahasa pemrograman server-side yang dirancang khusus untuk pengembangan web. PHP dapat disisipkan ke dalam HTML dan berjalan di server, menghasilkan HTML yang kemudian dikirim ke klien.</p>";

echo "<h2>Kelebihan PHP</h2>";
echo "<ul>";
echo "<li>Gratis dan Open Source - PHP tersedia secara gratis dan didukung oleh komunitas besar.</li>";
echo "<li>Mudah dipelajari - Sintaks PHP mirip dengan C dan Java, sehingga mudah dipelajari.</li>";
echo "<li>Platform Independent - PHP berjalan di berbagai sistem operasi (Windows, Linux, Mac).</li>";
echo "<li>Dukungan Database - PHP mendukung banyak database seperti MySQL, PostgreSQL, Oracle, dll.</li>";
echo "<li>Dukungan Server - PHP bekerja dengan baik dengan server web seperti Apache dan IIS.</li>";
echo "</ul>";

echo "<h2>Struktur Dasar PHP</h2>";
echo "<p>Kode PHP ditulis di antara tag <code>&lt;?php</code> dan <code>?&gt;</code>. File PHP biasanya disimpan dengan ekstensi <code>.php</code> dan dapat berisi campuran HTML dan kode PHP.</p>";
echo "<pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Contoh Halaman PHP&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Selamat Datang&lt;/h1&gt;
    &lt;?php
        // Ini adalah komentar satu baris
        /*
           Ini adalah
           komentar multi-baris
        */
        echo \"Hello dari PHP!\"; // Menampilkan output ke browser
    ?&gt;
    &lt;p&gt;Ini adalah teks HTML biasa.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>";

echo "<h2>Variabel dan Tipe Data</h2>";
echo "<p>Variabel dalam PHP dimulai dengan tanda dolar (<code>$</code>) diikuti dengan nama variabel. PHP adalah bahasa yang *loosely typed*, artinya Anda tidak perlu mendeklarasikan tipe data secara eksplisit.</p>";
echo "<ul>";
echo "<li><strong>String:</strong> Kumpulan karakter. Contoh: <code>\$nama = \"Alice\";</code></li>";
echo "<li><strong>Integer:</strong> Bilangan bulat. Contoh: <code>\$umur = 30;</code></li>";
echo "<li><strong>Float (atau Double):</strong> Bilangan desimal. Contoh: <code>\$harga = 19.99;</code></li>";
echo "<li><strong>Boolean:</strong> Nilai kebenaran (true atau false). Contoh: <code>\$is_aktif = true;</code></li>";
echo "<li><strong>Array:</strong> Kumpulan nilai. Bisa berupa array terindeks atau asosiatif (key-value). Contoh: <code>\$warna = [\"merah\", \"hijau\", \"biru\"];</code> atau <code>\$pengguna = [\"nama\" => \"Bob\", \"umur\" => 25];</code></li>";
echo "<li><strong>NULL:</strong> Merepresentasikan variabel tanpa nilai. Contoh: <code>\$data = NULL;</code></li>";
echo "</ul>";
echo "<p>Anda dapat menggunakan fungsi <code>var_dump()</code> untuk melihat tipe data dan nilai sebuah variabel.</p>";

echo "<h2>Operator</h2>";
echo "<p>PHP mendukung berbagai jenis operator:</p>";
echo "<ul>";
echo "<li><strong>Aritmatika:</strong> <code>+</code>, <code>-</code>, <code>*</code>, <code>/</code>, <code>%</code> (modulus), <code>**</code> (pangkat).</li>";
echo "<li><strong>Penugasan:</strong> <code>=</code>, <code>+=</code>, <code>-=</code>, <code>*=</code>, <code>/=</code>, <code>%=</code>.</li>";
echo "<li><strong>Perbandingan:</strong> <code>==</code> (sama dengan), <code>===</code> (identik), <code>!=</code> (tidak sama dengan), <code>!==</code> (tidak identik), <code>&lt;</code>, <code>&gt;</code>, <code>&lt;=</code>, <code>&gt;=</code>.</li>";
echo "<li><strong>Logika:</strong> <code>&&</code> (and), <code>||</code> (or), <code>!</code> (not).</li>";
echo "<li><strong>Increment/Decrement:</strong> <code>++\$a</code> (pre-increment), <code>\$a++</code> (post-increment), <code>--\$a</code> (pre-decrement), <code>\$a--</code> (post-decrement).</li>";
echo "<li><strong>String Concatenation:</strong> <code>.</code> (titik). Contoh: <code>\$kalimat = \"Halo\" . \" \" . \"Dunia\";</code></li>";
echo "</ul>";

echo "<h2>Struktur Kontrol</h2>";
echo "<p>Struktur kontrol digunakan untuk mengatur alur eksekusi program.</p>";
echo "<h3>Percabangan (Conditional Statements)</h3>";
echo "<ul>";
echo "<li><strong>if:</strong> Menjalankan blok kode jika kondisi benar.</li>";
echo "<li><strong>if...else:</strong> Menjalankan satu blok kode jika kondisi benar, dan blok lain jika salah.</li>";
echo "<li><strong>if...elseif...else:</strong> Memeriksa beberapa kondisi secara berurutan.</li>";
echo "<li><strong>switch:</strong> Memilih salah satu dari banyak blok kode untuk dieksekusi berdasarkan nilai tertentu.</li>";
echo "</ul>";
echo "<h3>Perulangan (Loops)</h3>";
echo "<ul>";
echo "<li><strong>for:</strong> Mengulang blok kode sejumlah tertentu.</li>";
echo "<li><strong>while:</strong> Mengulang blok kode selama kondisi tertentu benar.</li>";
echo "<li><strong>do...while:</strong> Mengulang blok kode sekali, lalu mengulang selama kondisi tertentu benar.</li>";
echo "<li><strong>foreach:</strong> Cara mudah untuk mengulang array.</li>";
echo "</ul>";

echo "<h2>Fungsi</h2>";
echo "<p>Fungsi adalah blok kode yang dapat dipanggil berulang kali. PHP memiliki banyak fungsi bawaan, dan Anda juga dapat membuat fungsi sendiri (user-defined functions).</p>";
echo "<pre><code>function sapa(\$nama) {
    return \"Halo, \" . \$nama . \"!\";
}

\$pesan = sapa(\"Charlie\"); // Memanggil fungsi
echo \$pesan; // Output: Halo, Charlie!</code></pre>";
echo "<p>Fungsi dapat menerima argumen (input) dan mengembalikan nilai (output) menggunakan kata kunci <code>return</code>.</p>";

echo "<h2>Include dan Require</h2>";
echo "<p>Anda dapat menyertakan kode dari file PHP lain ke dalam file saat ini menggunakan <code>include</code> atau <code>require</code>.</p>";
echo "<ul>";
echo "<li><strong>include:</strong> Jika file tidak ditemukan, PHP akan menghasilkan peringatan (warning) tetapi skrip akan terus berjalan.</li>";
echo "<li><strong>require:</strong> Jika file tidak ditemukan, PHP akan menghasilkan kesalahan fatal (fatal error) dan skrip akan berhenti.</li>";
echo "<li><strong>include_once</strong> dan <strong>require_once:</strong> Sama seperti include/require, tetapi memastikan file hanya disertakan satu kali, bahkan jika dipanggil berkali-kali.</li>";
echo "</ul>";
echo "<p>Ini sangat berguna untuk mengorganisir kode, misalnya memisahkan konfigurasi database atau header/footer halaman.</p>";
echo "<pre><code>// Contoh: config.php
&lt;?php
\$dbHost = \"localhost\";
\$dbUser = \"root\";
?&gt;

// Contoh: index.php
&lt;?php
require_once 'config.php'; // Menyertakan file konfigurasi
echo \"Database host: \" . \$dbHost;
?&gt;</code></pre>";

echo "<h2>Array</h2>";
echo "<p>Array adalah struktur data yang dapat menyimpan banyak nilai dalam satu variabel. PHP mendukung array terindeks dan array asosiatif.</p>";

echo "<h3>Array Terindeks</h3>";
echo "<p>Array terindeks menggunakan angka sebagai indeks, dimulai dari 0.</p>";
echo "<pre><code>\$buah = array(\"Apel\", \"Jeruk\", \"Mangga\");
// atau dengan sintaks pendek
\$buah = [\"Apel\", \"Jeruk\", \"Mangga\"];

// Mengakses elemen array
echo \$buah[0]; // Output: Apel

// Menambah elemen baru
\$buah[] = \"Pisang\"; // Menambahkan \"Pisang\" ke akhir array

// Menghitung jumlah elemen
\$jumlah = count(\$buah); // \$jumlah = 4</code></pre>";

echo "<h3>Array Asosiatif</h3>";
echo "<p>Array asosiatif menggunakan string sebagai kunci (key).</p>";
echo "<pre><code>\$mahasiswa = array(
    \"nama\" => \"Budi\",
    \"nim\" => \"12345\",
    \"jurusan\" => \"Teknik Informatika\"
);

// Mengakses elemen
echo \$mahasiswa[\"nama\"]; // Output: Budi

// Menambah atau mengubah elemen
\$mahasiswa[\"semester\"] = 3;</code></pre>";

echo "<h3>Array Multidimensi</h3>";
echo "<p>Array multidimensi adalah array yang berisi array lain.</p>";
echo "<pre><code>\$siswa = array(
    array(\"Andi\", \"L\", 85),
    array(\"Budi\", \"L\", 78),
    array(\"Citra\", \"P\", 90)
);

// Mengakses elemen
echo \$siswa[0][0]; // Output: Andi
echo \$siswa[2][2]; // Output: 90

// Array asosiatif multidimensi
\$kelas = array(
    \"X\" => array(
        \"wali_kelas\" => \"Pak Dedi\",
        \"jumlah_siswa\" => 30
    ),
    \"XI\" => array(
        \"wali_kelas\" => \"Bu Siti\",
        \"jumlah_siswa\" => 28
    )
);

echo \$kelas[\"X\"][\"wali_kelas\"]; // Output: Pak Dedi</code></pre>";

echo "<h3>Fungsi Array</h3>";
echo "<p>PHP memiliki banyak fungsi bawaan untuk bekerja dengan array:</p>";
echo "<ul>";
echo "<li><code>count()</code> - Menghitung jumlah elemen dalam array</li>";
echo "<li><code>array_push()</code> - Menambahkan satu atau lebih elemen ke akhir array</li>";
echo "<li><code>array_pop()</code> - Menghapus dan mengembalikan elemen terakhir dari array</li>";
echo "<li><code>array_shift()</code> - Menghapus dan mengembalikan elemen pertama dari array</li>";
echo "<li><code>array_unshift()</code> - Menambahkan satu atau lebih elemen ke awal array</li>";
echo "<li><code>array_merge()</code> - Menggabungkan dua atau lebih array</li>";
echo "<li><code>array_slice()</code> - Mengambil sebagian dari array</li>";
echo "<li><code>array_splice()</code> - Menghapus dan mengganti sebagian dari array</li>";
echo "<li><code>sort()</code> - Mengurutkan array</li>";
echo "<li><code>rsort()</code> - Mengurutkan array dalam urutan terbalik</li>";
echo "<li><code>asort()</code> - Mengurutkan array asosiatif berdasarkan nilai</li>";
echo "<li><code>ksort()</code> - Mengurutkan array asosiatif berdasarkan kunci</li>";
echo "</ul>";

echo "<h2>Tanggal dan Waktu</h2>";
echo "<p>PHP menyediakan berbagai fungsi untuk bekerja dengan tanggal dan waktu.</p>";

echo "<h3>Fungsi date()</h3>";
echo "<p>Fungsi <code>date()</code> digunakan untuk memformat tanggal dan waktu.</p>";
echo "<pre><code>// Format tanggal
echo date(\"d-m-Y\"); // Output: 29-04-2025 (format: hari-bulan-tahun)
echo date(\"Y/m/d\"); // Output: 2025/04/29 (format: tahun/bulan/hari)

// Format waktu
echo date(\"H:i:s\"); // Output: 14:30:45 (format: jam:menit:detik dalam format 24 jam)
echo date(\"h:i:s A\"); // Output: 02:30:45 PM (format: jam:menit:detik AM/PM)

// Format tanggal dan waktu
echo date(\"d-m-Y H:i:s\"); // Output: 29-04-2025 14:30:45</code></pre>";

echo "<p>Parameter format untuk fungsi <code>date()</code>:</p>";
echo "<ul>";
echo "<li><code>d</code> - Hari dalam bulan (01-31)</li>";
echo "<li><code>m</code> - Bulan dalam angka (01-12)</li>";
echo "<li><code>Y</code> - Tahun dalam 4 digit (2025)</li>";
echo "<li><code>y</code> - Tahun dalam 2 digit (25)</li>";
echo "<li><code>H</code> - Jam dalam format 24 jam (00-23)</li>";
echo "<li><code>h</code> - Jam dalam format 12 jam (01-12)</li>";
echo "<li><code>i</code> - Menit (00-59)</li>";
echo "<li><code>s</code> - Detik (00-59)</li>";
echo "<li><code>A</code> - AM atau PM</li>";
echo "<li><code>l</code> - Nama hari (Sunday, Monday, ...)</li>";
echo "<li><code>F</code> - Nama bulan (January, February, ...)</li>";
echo "</ul>";

echo "<h3>Fungsi time()</h3>";
echo "<p>Fungsi <code>time()</code> mengembalikan timestamp Unix saat ini (jumlah detik sejak 1 Januari 1970).</p>";
echo "<pre><code>\$timestamp = time();
echo \$timestamp; // Output: 1714485045 (contoh)

// Menggunakan timestamp dengan date()
echo date(\"d-m-Y\", \$timestamp);</code></pre>";

echo "<h3>Fungsi mktime()</h3>";
echo "<p>Fungsi <code>mktime()</code> mengembalikan timestamp Unix untuk tanggal dan waktu tertentu.</p>";
echo "<pre><code>// mktime(jam, menit, detik, bulan, hari, tahun)
\$timestamp = mktime(0, 0, 0, 1, 1, 2025); // 1 Januari 2025 00:00:00
echo date(\"d-m-Y\", \$timestamp); // Output: 01-01-2025</code></pre>";

echo "<h3>Fungsi strtotime()</h3>";
echo "<p>Fungsi <code>strtotime()</code> mengubah string tanggal menjadi timestamp Unix.</p>";
echo "<pre><code>\$timestamp = strtotime(\"2025-04-29\");
echo date(\"d-m-Y\", \$timestamp); // Output: 29-04-2025

// Relatif terhadap waktu saat ini
echo date(\"d-m-Y\", strtotime(\"+1 week\")); // Tanggal 1 minggu dari sekarang
echo date(\"d-m-Y\", strtotime(\"+1 month\")); // Tanggal 1 bulan dari sekarang
echo date(\"d-m-Y\", strtotime(\"next Sunday\")); // Tanggal hari Minggu berikutnya</code></pre>";

echo "<h3>Kelas DateTime</h3>";
echo "<p>PHP juga menyediakan kelas <code>DateTime</code> untuk manipulasi tanggal dan waktu yang lebih canggih.</p>";
echo "<pre><code>\$date = new DateTime(); // Tanggal dan waktu saat ini
echo \$date->format(\"d-m-Y H:i:s\"); // Output: 29-04-2025 14:30:45

// Membuat tanggal tertentu
\$date = new DateTime(\"2025-04-29\");
echo \$date->format(\"d-m-Y\"); // Output: 29-04-2025

// Menambah interval
\$date->add(new DateInterval(\"P10D\")); // Menambah 10 hari
echo \$date->format(\"d-m-Y\"); // Output: 09-05-2025

// Mengurangi interval
\$date->sub(new DateInterval(\"P5D\")); // Mengurangi 5 hari
echo \$date->format(\"d-m-Y\"); // Output: 04-05-2025

// Perbedaan antara dua tanggal
\$date1 = new DateTime(\"2025-01-01\");
\$date2 = new DateTime(\"2025-12-31\");
\$interval = \$date1->diff(\$date2);
echo \$interval->format(\"%a hari\"); // Output: 364 hari</code></pre>";

echo "<h2>Penanganan Form</h2>";
echo "<p>PHP sering digunakan untuk memproses data yang dikirim melalui form HTML.</p>";

echo "<h3>Metode GET dan POST</h3>";
echo "<p>Form HTML dapat menggunakan metode GET atau POST untuk mengirim data:</p>";
echo "<ul>";
echo "<li><strong>GET:</strong> Data dikirim melalui URL. Tidak aman untuk data sensitif dan terbatas pada 2048 karakter.</li>";
echo "<li><strong>POST:</strong> Data dikirim melalui body request HTTP. Lebih aman dan tidak ada batasan ukuran.</li>";
echo "</ul>";

echo "<h3>Mengakses Data Form</h3>";
echo "<p>PHP menyediakan superglobal <code>\$_GET</code>, <code>\$_POST</code>, dan <code>\$_REQUEST</code> untuk mengakses data form.</p>";
echo "<pre><code>// Form dengan metode GET
&lt;form method=\"get\" action=\"proses.php\"&gt;
    &lt;input type=\"text\" name=\"nama\"&gt;
    &lt;input type=\"submit\" value=\"Kirim\"&gt;
&lt;/form&gt;

// proses.php (untuk metode GET)
\$nama = \$_GET[\"nama\"];
echo \"Halo, \" . \$nama;

// Form dengan metode POST
&lt;form method=\"post\" action=\"proses.php\"&gt;
    &lt;input type=\"text\" name=\"nama\"&gt;
    &lt;input type=\"submit\" value=\"Kirim\"&gt;
&lt;/form&gt;

// proses.php (untuk metode POST)
\$nama = \$_POST[\"nama\"];
echo \"Halo, \" . \$nama;

// \$_REQUEST dapat digunakan untuk kedua metode
\$nama = \$_REQUEST[\"nama\"];</code></pre>";

echo "<h3>Validasi Form</h3>";
echo "<p>Validasi data form sangat penting untuk keamanan aplikasi web.</p>";
echo "<pre><code>// Contoh validasi sederhana
if (\$_SERVER[\"REQUEST_METHOD\"] == \"POST\") {
    // Memeriksa apakah field kosong
    if (empty(\$_POST[\"nama\"])) {
        \$error = \"Nama harus diisi\";
    } else {
        \$nama = test_input(\$_POST[\"nama\"]);
    }
    
    // Memeriksa apakah email valid
    if (empty(\$_POST[\"email\"])) {
        \$error = \"Email harus diisi\";
    } else {
        \$email = test_input(\$_POST[\"email\"]);
        if (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {
            \$error = \"Format email tidak valid\";
        }
    }
}

// Fungsi untuk membersihkan input
function test_input(\$data) {
    \$data = trim(\$data);
    \$data = stripslashes(\$data);
    \$data = htmlspecialchars(\$data);
    return \$data;
}</code></pre>";

echo "<h3>Upload File</h3>";
echo "<p>PHP juga mendukung upload file melalui form.</p>";
echo "<pre><code>// Form untuk upload file
&lt;form method=\"post\" action=\"upload.php\" enctype=\"multipart/form-data\"&gt;
    &lt;input type=\"file\" name=\"berkas\"&gt;
    &lt;input type=\"submit\" value=\"Upload\"&gt;
&lt;/form&gt;

// upload.php
if (\$_SERVER[\"REQUEST_METHOD\"] == \"POST\") {
    // Memeriksa apakah file berhasil diupload
    if (isset(\$_FILES[\"berkas\"]) && \$_FILES[\"berkas\"][\"error\"] == 0) {
        \$target_dir = \"uploads/\";
        \$target_file = \$target_dir . basename(\$_FILES[\"berkas\"][\"name\"]);
        \$file_type = strtolower(pathinfo(\$target_file, PATHINFO_EXTENSION));
        
        // Memeriksa ukuran file (maksimal 5MB)
        if (\$_FILES[\"berkas\"][\"size\"] > 5000000) {
            echo \"File terlalu besar\";
        }
        // Memeriksa tipe file (hanya izinkan JPG, JPEG, PNG, dan GIF)
        elseif (!\in_array(\$file_type, [\"jpg\", \"jpeg\", \"png\", \"gif\"])) {
            echo \"Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan\";
        }
        // Jika semua validasi berhasil, upload file
        else {
            if (move_uploaded_file(\$_FILES[\"berkas\"][\"tmp_name\"], \$target_file)) {
                echo \"File \" . basename(\$_FILES[\"berkas\"][\"name\"]) . \" berhasil diupload\";
            } else {
                echo \"Terjadi kesalahan saat mengupload file\";
            }
        }
    } else {
        echo \"Terjadi kesalahan: \" . \$_FILES[\"berkas\"][\"error\"];
    }
}</code></pre>";

echo "<h3>Cookies dan Session</h3>";
echo "<p>PHP menyediakan mekanisme untuk menyimpan data antara request HTTP.</p>";

echo "<h4>Cookies</h4>";
echo "<p>Cookies disimpan di browser pengguna dan dapat diakses oleh server.</p>";
echo "<pre><code>// Membuat cookie (berlaku selama 30 hari)
setcookie(\"user\", \"John\", time() + (86400 * 30), \"/\");

// Mengakses cookie
if (isset(\$_COOKIE[\"user\"])) {
    echo \"Selamat datang kembali, \" . \$_COOKIE[\"user\"];
}

// Menghapus cookie
setcookie(\"user\", \"\", time() - 3600, \"/\");</code></pre>";

echo "<h4>Session</h4>";
echo "<p>Session disimpan di server dan lebih aman daripada cookies.</p>";
echo "<pre><code>// Memulai session
session_start();

// Menyimpan data session
\$_SESSION[\"username\"] = \"admin\";
\$_SESSION[\"login_time\"] = time();

// Mengakses data session
if (isset(\$_SESSION[\"username\"])) {
    echo \"Selamat datang, \" . \$_SESSION[\"username\"];
}

// Menghapus data session tertentu
unset(\$_SESSION[\"username\"]);

// Menghapus semua data session
session_destroy();</code></pre>";

echo "<h2>Koneksi Database MySQL</h2>";
echo "<p>PHP sering digunakan dengan MySQL untuk membuat aplikasi web dinamis.</p>";

echo "<h3>Membuat Koneksi</h3>";
echo "<pre><code>\$host = \"localhost\";
\$username = \"root\";
\$password = \"\";
\$database = \"nama_database\";

// Membuat koneksi
\$koneksi = mysqli_connect(\$host, \$username, \$password, \$database);

// Memeriksa koneksi
if (!\$koneksi) {
    die(\"Koneksi gagal: \" . mysqli_connect_error());
}
echo \"Koneksi berhasil\";</code></pre>";

echo "<h3>Operasi CRUD</h3>";
echo "<p>CRUD (Create, Read, Update, Delete) adalah operasi dasar database.</p>";

echo "<h4>Create (INSERT)</h4>";
echo "<pre><code>\$sql = \"INSERT INTO users (username, email, created_at)
        VALUES ('john_doe', 'john@example.com', NOW())\";

if (mysqli_query(\$koneksi, \$sql)) {
    echo \"Data berhasil ditambahkan\";
} else {
    echo \"Error: \" . \$sql . \"<br>\" . mysqli_error(\$koneksi);
}</code></pre>";

echo "<h4>Read (SELECT)</h4>";
echo "<pre><code>\$sql = \"SELECT id, username, email FROM users\";
\$result = mysqli_query(\$koneksi, \$sql);

if (mysqli_num_rows(\$result) > 0) {
    // Output data dari setiap baris
    while(\$row = mysqli_fetch_assoc(\$result)) {
        echo \"ID: \" . \$row[\"id\"] . \" - Username: \" . \$row[\"username\"] . \" - Email: \" . \$row[\"email\"] . \"<br>\";
    }
} else {
    echo \"0 hasil\";
}</code></pre>";

echo "<h4>Update (UPDATE)</h4>";
echo "<pre><code>\$sql = \"UPDATE users SET email='john.doe@example.com' WHERE username='john_doe'\";

if (mysqli_query(\$koneksi, \$sql)) {
    echo \"Data berhasil diperbarui\";
} else {
    echo \"Error: \" . mysqli_error(\$koneksi);
}</code></pre>";

echo "<h4>Delete (DELETE)</h4>";
echo "<pre><code>\$sql = \"DELETE FROM users WHERE id=3\";

if (mysqli_query(\$koneksi, \$sql)) {
    echo \"Data berhasil dihapus\";
} else {
    echo \"Error: \" . mysqli_error(\$koneksi);
}</code></pre>";

echo "<h3>Prepared Statements</h3>";
echo "<p>Prepared statements membantu mencegah SQL injection.</p>";
echo "<pre><code>// Prepared statement dengan mysqli
\$stmt = \$koneksi->prepare(\"INSERT INTO users (username, email) VALUES (?, ?)\");
\$stmt->bind_param(\"ss\", \$username, \$email);

// Set parameter dan eksekusi
\$username = \"jane_doe\";
\$email = \"jane@example.com\";
\$stmt->execute();

echo \"Data baru berhasil ditambahkan\";

\$stmt->close();</code></pre>";

echo "<h3>PDO (PHP Data Objects)</h3>";
echo "<p>PDO adalah antarmuka untuk mengakses database yang mendukung berbagai jenis database.</p>";
echo "<pre><code>try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$database\", \$username, \$password);
    // Set mode error ke exception
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepared statement dengan PDO
    \$stmt = \$pdo->prepare(\"INSERT INTO users (username, email) VALUES (:username, :email)\");
    \$stmt->bindParam(':username', \$username);
    \$stmt->bindParam(':email', \$email);
    
    // Set parameter dan eksekusi
    \$username = \"robert\";
    \$email = \"robert@example.com\";
    \$stmt->execute();
    
    echo \"Data baru berhasil ditambahkan\";
} catch(PDOException \$e) {
    echo \"Error: \" . \$e->getMessage();
}</code></pre>";

echo "<h2>Kesimpulan</h2>";
echo "<p>PHP adalah bahasa pemrograman yang kuat untuk pengembangan web. Dengan memahami konsep-konsep dasar seperti variabel, struktur kontrol, fungsi, array, tanggal dan waktu, penanganan form, dan koneksi database, Anda dapat membuat aplikasi web dinamis yang kompleks.</p>";
echo "<p>Untuk memperdalam pengetahuan Anda, cobalah untuk:</p>";
echo "<ul>";
echo "<li>Mempelajari framework PHP seperti Laravel, CodeIgniter, atau Symfony</li>";
echo "<li>Mempelajari lebih lanjut tentang keamanan PHP (validasi input, prepared statements)</li>";
echo "<li>Mempelajari tentang API dan integrasi dengan layanan web</li>";
echo "<li>Mempelajari tentang pengujian dan debugging kode PHP</li>";
echo "</ul>";
?>