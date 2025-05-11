<nav>
    <ul>
        <li><a href="?menu=kontak">Kontak</a></li>
        <li><a href="?menu=sejarah">Sejarah</a></li>
        <li><a href="?menu=jurusan">Jurusan</a></li>
    </ul>
</nav>

<?php 
    if (isset($_GET['menu'])) {
        $menu = $_GET['menu'];

        require_once $menu.'.php';
    }

?>