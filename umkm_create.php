<?php
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_UMKM']; $halal = $_POST['status_halal']; $kontak = $_POST['nomor_kontak'];
    $layanan = implode(',', $_POST['layanan_makan'] ?? ['takeaway']);
    $jalan = $_POST['jalan']; $jam_buka = $_POST['jam_buka']; $jam_tutup = $_POST['jam_tutup'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO umkm (nama_UMKM, status_halal, nomor_kontak, layanan_makan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $halal, $kontak, $layanan]);
        $id_umkm = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO lokasi (id_UMKM, kota, kecamatan, jalan, link_gmap) VALUES (?, 'Kota Bandung', 'Sukasari', ?, '')")->execute([$id_umkm, $jalan]);
        $pdo->prepare("INSERT INTO waktu_operasional (id_UMKM, hari_buka, jam_buka, jam_tutup) VALUES (?, 'Setiap_hari', ?, ?)")->execute([$id_umkm, $jam_buka, $jam_tutup]);

        $pdo->commit(); header("Location: index.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); $error = $e->getMessage();
    }
}
include 'includes/header.php';
?>

<div class="ios-navbar-top">
    <h1>Daftarkan UMKM</h1>
</div>

<div class="ios-card">
    <form method="POST">
        <div class="ios-form-group">
            <label>Nama Tempat Kuliner</label>
            <input type="text" name="nama_UMKM" class="ios-form-control" placeholder="Contoh: Warmindo KPAD" required>
        </div>
        
        <div class="ios-form-group">
            <label>Sertifikasi Halal</label>
            <select name="status_halal" class="ios-form-control">
                <option value="halal">Halal (Self Declare)</option>
                <option value="bersertifikat">Bersertifikat Resmi</option>
                <option value="non-halal">Non-Halal</option>
            </select>
        </div>

        <div class="ios-form-group">
            <label>Nomor Kontak/WhatsApp</label>
            <input type="text" name="nomor_kontak" class="ios-form-control" value="-">
        </div>

        <div class="ios-form-group">
            <label>Alamat Spesifik di Komplek KPAD</label>
            <input type="text" name="jalan" class="ios-form-control" placeholder="Contoh: Jl. Gg. Lapangan No. 12" required>
        </div>

        <div class="ios-form-group ios-form-group-row">
            <div>
                <label>Jam Buka</label>
                <input type="time" name="jam_buka" class="ios-form-control" value="09:00" required>
            </div>
            <div>
                <label>Jam Tutup</label>
                <input type="time" name="jam_tutup" class="ios-form-control" value="21:00" required>
            </div>
        </div>

        <button type="submit" class="ios-btn" style="margin-top: 10px;">Simpan ke Direktori</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>