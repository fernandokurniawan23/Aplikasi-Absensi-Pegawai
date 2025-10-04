<?php
include 'helpers.php';

$user_id = $_SESSION['user_id'];
$message = '';
$data_absen_hari_ini = null;
$tanggal_hari_ini = date('Y-m-d');

// data absen hari ini
$stmt_cek = mysqli_prepare($koneksi, "SELECT * FROM absensi WHERE user_id = ? AND tanggal_absen = ?");
mysqli_stmt_bind_param($stmt_cek, "is", $user_id, $tanggal_hari_ini);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);
if ($data = mysqli_fetch_assoc($result_cek)) {
    $data_absen_hari_ini = $data;
}
mysqli_stmt_close($stmt_cek);

// Logic button absen masuk
if (isset($_POST['submit_masuk'])) {
    $jam_sekarang = date('H:i:s');
    $status = ($jam_sekarang > JAM_MASUK_STANDAR) ? 'Terlambat' : 'Hadir';

    $foto_name = $_FILES['foto_masuk']['name'];
    $foto_tmp = $_FILES['foto_masuk']['tmp_name'];
    $foto_error = $_FILES['foto_masuk']['error'];

    if ($foto_error === 0) {
        $file_extension = pathinfo($foto_name, PATHINFO_EXTENSION);
        $nama_file_unik = uniqid('masuk_') . '_' . time() . '.' . $file_extension;
        $tujuan_upload = 'uploads/' . $nama_file_unik;

        if (move_uploaded_file($foto_tmp, $tujuan_upload)) {
            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO absensi (user_id, tanggal_absen, jam_masuk, foto_masuk, status) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "issss", $user_id, $tanggal_hari_ini, $jam_sekarang, $nama_file_unik, $status);
            if (mysqli_stmt_execute($stmt_insert)) {
                // Alert terlambat
                if ($status === 'Terlambat') {
                    $_SESSION['feedback_message'] = "<p style='color:orange;'><b>PERINGATAN:</b> Anda tercatat TERLAMBAT. Absen masuk berhasil pada pukul $jam_sekarang.</p>";
                } else {
                    $_SESSION['feedback_message'] = "<p style='color:green;'>Absen masuk berhasil dicatat pada pukul $jam_sekarang.</p>";
                }
                header("Location: index.php?page=presensi");
                exit();
            }
        }
    } else {
        $message = "<p style='color:red;'>Harap pilih foto untuk diupload.</p>";
    }
}

// logic button absen pulang
if (isset($_POST['submit_pulang'])) {
    $jam_sekarang = date('H:i:s');
    $status_lembur = ($jam_sekarang > JAM_LEMBUR_MINIMAL) ? 'Ya' : 'Tidak';
    $id_absensi = $data_absen_hari_ini['id_absensi'];

    // Logika upload foto pulang
    $foto_name = $_FILES['foto_pulang']['name'];
    $foto_tmp = $_FILES['foto_pulang']['tmp_name'];
    $foto_error = $_FILES['foto_pulang']['error'];

    if ($foto_error === 0) {
        $file_extension = pathinfo($foto_name, PATHINFO_EXTENSION);
        $nama_file_unik = uniqid('pulang_') . '_' . time() . '.' . $file_extension;
        $tujuan_upload = 'uploads/' . $nama_file_unik;

        if (move_uploaded_file($foto_tmp, $tujuan_upload)) {
            $stmt_update = mysqli_prepare($koneksi, "UPDATE absensi SET jam_pulang = ?, foto_pulang = ?, status_lembur = ? WHERE id_absensi = ?");
            mysqli_stmt_bind_param($stmt_update, "sssi", $jam_sekarang, $nama_file_unik, $status_lembur, $id_absensi);
            if (mysqli_stmt_execute($stmt_update)) {
                if ($status_lembur === 'Ya') {
                    $_SESSION['feedback_message'] = "<p style='color:blue;'>Absen pulang berhasil. Anda tercatat LEMBUR.</p>";
                } else {
                    $_SESSION['feedback_message'] = "<p style='color:green;'>Absen pulang berhasil dicatat pada pukul $jam_sekarang.</p>";
                }
                header("Location: index.php?page=presensi");
                exit();
            }
        }
    } else {
        $message = "<p style='color:red;'>Harap pilih foto untuk diupload saat absen pulang.</p>";
    }
}

// Ambil pesan feedback dari session
if (isset($_SESSION['feedback_message'])) {
    $message = $_SESSION['feedback_message'];
    unset($_SESSION['feedback_message']);
}
?>

<h1>Formulir Presensi Harian</h1>
<p>Tanggal: <?= date('d F Y') ?></p>
<?php if ($message) echo $message; ?>

<div class="form-container" style="width:400px; border: 1px solid #ccc; padding: 1rem; border-radius: 8px;">
    <?php if ($data_absen_hari_ini === null): // KASUS 1: Belum absen sama sekali ?>
        <form method="POST" action="index.php?page=presensi" enctype="multipart/form-data">
            <p>Silakan lakukan absen masuk Anda.</p>
            <div>
                <label for="foto_masuk"><b>Upload Foto Selfie Masuk:</b></label><br>
                <input type="file" id="foto_masuk" name="foto_masuk" accept="image/*" required>
            </div>
            <br>
            <button type="submit" name="submit_masuk">Absen Masuk</button>
        </form>

    <?php elseif ($data_absen_hari_ini['jam_pulang'] === null): // KASUS 2: Sudah absen masuk, belum absen pulang ?>
        <p style="color:green;">Anda sudah absen masuk hari ini pada pukul <b><?= date('H:i', strtotime($data_absen_hari_ini['jam_masuk'])) ?></b>.</p>
        
        <?php
            $waktu_sekarang = date('H:i:s');
            if ($waktu_sekarang >= JAM_PULANG_MINIMAL):
        ?>
            <form method="POST" action="index.php?page=presensi" enctype="multipart/form-data">
                 <div>
                    <label for="foto_pulang"><b>Upload Foto Selfie Pulang:</b></label><br>
                    <input type="file" id="foto_pulang" name="foto_pulang" accept="image/*" required>
                </div>
                <br>
                <button type="submit" name="submit_pulang">Absen Pulang</button>
            </form>
        <?php else: ?>
            <p style="color:orange;">Anda baru bisa melakukan absen pulang setelah pukul <b><?= date('H:i', strtotime(JAM_PULANG_MINIMAL)) ?></b>.</p>
            <button type="submit" name="submit_pulang" disabled>Absen Pulang</button>
        <?php endif; ?>

    <!--absen masuk dan pulang -->
    <?php else: ?>
        <p style="color:blue;">Anda sudah menyelesaikan absensi hari ini.</p>
        <p>Jam Masuk: <b><?= date('H:i', strtotime($data_absen_hari_ini['jam_masuk'])) ?></b></p>
        <p>Jam Pulang: <b><?= date('H:i', strtotime($data_absen_hari_ini['jam_pulang'])) ?></b></p>
    <?php endif; ?>
</div>