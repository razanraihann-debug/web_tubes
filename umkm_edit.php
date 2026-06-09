<?php
require 'config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_UMKM'];
    $halal = $_POST['status_halal'];
    $kontak = $_POST['nomor_kontak'];
    $layanan = implode(',', $_POST['layanan_makan'] ?? ['takeaway']);
    $jalan = $_POST['jalan'];

    // Update data UMKM & Lokasi secara transaksional
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE umkm SET nama_UMKM = ?, status_halal = ?, nomor_kontak = ?, layanan_makan = ? WHERE id_UMKM = ?");
    $stmt->execute([$nama, $halal, $kontak, $layanan, $id]);

    $stmt_lokasi = $pdo->prepare("UPDATE lokasi SET jalan = ? WHERE id_UMKM = ?");
    $stmt_lokasi->execute([$jalan, $id]);
    $pdo->commit();

    header("Location: index.php"); exit;
}

$stmt = $pdo->prepare("SELECT u.*, l.jalan FROM umkm u LEFT JOIN lokasi l ON u.id_UMKM = l.id_UMKM WHERE u.id_UMKM = ?");
$stmt->execute([$id]);
$umkm = $stmt->fetch();
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0 p-4">
            <h3 class="fw-bold mb-4">Edit Profil UMKM</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama UMKM</label>
                    <input type="text" name="nama_UMKM" class="form-control" value="<?= htmlspecialchars($umkm['nama_UMKM']) ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label fw-semibold">Status Sertifikasi</label>
                        <select name="status_halal" class="form-select">
                            <option value="halal" <?= $umkm['status_halal']=='halal'?'selected':'' ?>>Halal (Self Declare)</option>
                            <option value="bersertifikat" <?= $umkm['status_halal']=='bersertifikat'?'selected':'' ?>>Bersertifikat Resmi</option>
                            <option value="non-halal" <?= $umkm['status_halal']=='non-halal'?'selected':'' ?>>Non-Halal</option>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label fw-semibold">Nomor Kontak</label>
                        <input type="text" name="nomor_kontak" class="form-control" value="<?= htmlspecialchars($umkm['nomor_kontak']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Jalan</label>
                    <input type="text" name="jalan" class="form-control" value="<?= htmlspecialchars($umkm['jalan']) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Layanan Makan</label><br>
                    <?php $lay_arr = explode(',', $umkm['layanan_makan']); ?>
                    <input type="checkbox" name="layanan_makan[]" value="takeaway" <?= in_array('takeaway', $lay_arr)?'checked':'' ?>> Takeaway
                    <input type="checkbox" name="layanan_makan[]" value="dine_in" <?= in_array('dine_in', $lay_arr)?'checked':'' ?> class="ms-3"> Dine In
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold">Update UMKM</button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>