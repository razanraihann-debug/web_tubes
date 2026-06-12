<?php
require 'config/database.php';

// 1. Ambil data lama berdasarkan ID untuk ditampilkan di form
$id_umkm = $_GET['id'] ?? null;
if (!$id_umkm) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT u.*, l.jalan, w.jam_buka, w.jam_tutup 
    FROM umkm u 
    LEFT JOIN lokasi l ON u.id_UMKM = l.id_UMKM
    LEFT JOIN waktu_operasional w ON u.id_UMKM = w.id_UMKM
    WHERE u.id_UMKM = ?
");
$stmt->execute([$id_umkm]);
$umkm = $stmt->fetch();

if (!$umkm) {
    header("Location: index.php");
    exit;
}

// 2. Proses Update data ketika tombol Simpan ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_UMKM']; 
    $halal = $_POST['status_halal']; 
    $kontak = $_POST['nomor_kontak'];
    $jalan = $_POST['jalan']; 
    $jam_buka = $_POST['jam_buka']; 
    $jam_tutup = $_POST['jam_tutup'];

    try {
        $pdo->beginTransaction();
        
        // Update tabel utama umkm
        $pdo->prepare("UPDATE umkm SET nama_UMKM = ?, status_halal = ?, nomor_kontak = ? WHERE id_UMKM = ?")
            ->execute([$nama, $halal, $kontak, $id_umkm]);

        // Update tabel lokasi
        $pdo->prepare("UPDATE lokasi SET jalan = ? WHERE id_UMKM = ?")
            ->execute([$jalan, $id_umkm]);

        // Update tabel waktu operasional
        $pdo->prepare("UPDATE waktu_operasional SET jam_buka = ?, jam_tutup = ? WHERE id_UMKM = ?")
            ->execute([$jam_buka, $jam_tutup, $id_umkm]);

        $pdo->commit(); 
        header("Location: index.php"); 
        exit;
    } catch (Exception $e) {
        $pdo->rollBack(); 
        $error = $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="ios-navbar-top" style="display: flex; align-items: center; gap: 15px;">
    <a href="index.php" style="color: var(--ios-primary, #007aff); text-decoration: none; font-size: 1.2rem; display: flex; align-items: center;">
        <i class="bi bi-chevron-left"></i> Kembali
    </a>
    <h1 style="margin: 0; font-size: 1.8rem;">Edit UMKM</h1>
</div>

<?php if (isset($error)): ?>
    <div style="color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 8px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="ios-card">
    <form method="POST">
        <div class="ios-form-group">
            <label>Nama Tempat Kuliner</label>
            <input type="text" name="nama_UMKM" class="ios-form-control" value="<?= htmlspecialchars($umkm['nama_UMKM']) ?>" required>
        </div>
        
        <div class="ios-form-group">
            <label>Sertifikasi Halal</label>
            <select name="status_halal" class="ios-form-control">
                <option value="halal" <?= $umkm['status_halal'] == 'halal' ? 'selected' : '' ?>>Halal (Self Declare)</option>
                <option value="bersertifikat" <?= $umkm['status_halal'] == 'bersertifikat' ? 'selected' : '' ?>>Bersertifikat Resmi</option>
                <option value="non-halal" <?= $umkm['status_halal'] == 'non-halal' ? 'selected' : '' ?>>Non-Halal</option>
            </select>
        </div>

        <div class="ios-form-group">
            <label>Nomor Kontak/WhatsApp</label>
            <input type="text" name="nomor_kontak" class="ios-form-control" value="<?= htmlspecialchars($umkm['nomor_kontak']) ?>">
        </div>

        <div class="ios-form-group">
            <label>Alamat Spesifik di Komplek KPAD</label>
            <input type="text" name="jalan" class="ios-form-control" value="<?= htmlspecialchars($umkm['jalan'] ?? '') ?>" required>
        </div>

        <div class="ios-form-group ios-form-group-row">
            <div>
                <label>Jam Buka</label>
                <input type="time" name="jam_buka" class="ios-form-control" value="<?= substr($umkm['jam_buka'], 0, 5) ?>" required>
            </div>
            <div>
                <label>Jam Tutup</label>
                <input type="time" name="jam_tutup" class="ios-form-control" value="<?= substr($umkm['jam_tutup'], 0, 5) ?>" required>
            </div>
        </div>

        <button type="submit" class="ios-btn" style="margin-top: 10px;">Simpan Perubahan</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>