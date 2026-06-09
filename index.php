<?php
require 'config/database.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$filter_rasa = $_GET['rasa'] ?? '';

$query = "SELECT u.*, l.jalan, w.jam_buka, w.jam_tutup 
          FROM umkm u 
          LEFT JOIN lokasi l ON u.id_UMKM = l.id_UMKM
          LEFT JOIN waktu_operasional w ON u.id_UMKM = w.id_UMKM";

$params = [];
if ($search || $filter_rasa) {
    $query .= " WHERE 1=1";
    if ($search) { $query .= " AND u.nama_UMKM LIKE ?"; $params[] = "%$search%"; }
    if ($filter_rasa) { $query .= " AND u.id_UMKM IN (SELECT DISTINCT id_UMKM FROM menu WHERE FIND_IN_SET(?, rasa))"; $params[] = $filter_rasa; }
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$umkms = $stmt->fetchAll();
?>

<div class="ios-navbar-top">
    <div>
        <span class="subtitle">Kuliner KPAD</span>
        <h1>Eksplorasi</h1>
    </div>
</div>

<form method="GET">
    <div class="ios-input-group">
        <i class="bi bi-search"></i>
        <input type="text" name="search" class="ios-input" placeholder="Cari kuliner..." value="<?= htmlspecialchars($search) ?>">
        <select name="rasa" class="ios-select" onchange="this.form.submit()">
            <option value="">Rasa</option>
            <option value="pedas" <?= $filter_rasa == 'pedas' ? 'selected' : '' ?>>🌶️ Pedas</option>
            <option value="manis" <?= $filter_rasa == 'manis' ? 'selected' : '' ?>>🍩 Manis</option>
            <option value="asin" <?= $filter_rasa == 'asin' ? 'selected' : '' ?>>🧂 Asin</option>
            <option value="masam" <?= $filter_rasa == 'masam' ? 'selected' : '' ?>>🍋 Masam</option>
        </select>
    </div>
</form>

<div>
    <?php foreach ($umkms as $umkm): 
        $sp_stmt = $pdo->prepare("CALL sp_status_umkm(?)");
        $sp_stmt->execute([$umkm['id_UMKM']]);
        $status_data = $sp_stmt->fetch();
        $sp_stmt->closeCursor();
        
        $status = $status_data['status'] ?? 'TUTUP';
        $badge_class = ($status == 'BUKA') ? 'ios-badge-success' : 'ios-badge-danger';
    ?>
    <div class="ios-card">
        <div class="ios-card-header">
            <h3 class="ios-card-title"><?= htmlspecialchars($umkm['nama_UMKM']) ?></h3>
            <span class="ios-badge <?= $badge_class ?>"><?= $status ?></span>
        </div>
        
        <p class="ios-card-meta">
            <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($umkm['jalan'] ?? 'Lokasi belum diatur') ?>
        </p>

        <div class="ios-badge-container">
            <span class="ios-badge ios-badge-primary">🛡️ Halal: <?= ucfirst($umkm['status_halal']) ?></span>
            <span class="ios-badge ios-badge-secondary"><i class="bi bi-clock"></i> <?= substr($umkm['jam_buka'], 0, 5) ?> - <?= substr($umkm['jam_tutup'], 0, 5) ?></span>
        </div>

        <div class="ios-btn-group">
            <a href="umkm_detail.php?id=<?= $umkm['id_UMKM'] ?>" class="ios-btn">Lihat Menu</a>
            <a href="umkm_edit.php?id=<?= $umkm['id_UMKM'] ?>" class="ios-btn ios-btn-secondary ios-btn-icon-only"><i class="bi bi-pencil"></i></a>
            <a href="umkm_delete.php?id=<?= $umkm['id_UMKM'] ?>" class="ios-btn ios-btn-danger ios-btn-icon-only" onclick="return confirm('Hapus seluruh data?')"><i class="bi bi-trash"></i></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>