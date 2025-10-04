<?php 
// includes/template_sidebar.php

// Mengambil data dari SESSION
$nama_user = $_SESSION['user_name'] ?? 'Pengguna';
$id_user = $_SESSION['user_id'] ?? 'NIM/ID';

// Tentukan halaman aktif berdasarkan parameter 'page' di URL
$current_page = $_GET['page'] ?? 'beranda';
?>
        <div class="sidebar">
            <div class="logo">Universitas ABC</div>
            
            <div class="user-info">
                <div class="user-icon"></div>
                <p class="user-name"><?= $nama_user ?></p>
                <p class="user-id"><?= $id_user ?></p>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li class="<?= $current_page == 'beranda' ? 'active' : '' ?>">
                        <a href="index.php?page=beranda">Beranda</a>
                    </li>
                    <li class="<?= $current_page == 'presensi' ? 'active' : '' ?>">
                        <a href="index.php?page=presensi">Presensi</a>
                    </li>                    
                    <li style="margin-top: 30px;"> 
                        <a href="logout.php" style="color: #d32f2f;">Logout</a>
                    </li>
                </ul>
            </nav>
        </div> <div class="content-area">