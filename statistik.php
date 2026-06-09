<?php
require 'config/database.php';
include 'includes/header.php';

$status_halal_summary = ['halal' => 0, 'bersertifikat' => 0, 'non-halal' => 0];
$stmt = $pdo->query("SELECT status_halal, COUNT(*) as jumlah FROM umkm GROUP BY status_halal");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status_halal_summary[$row['status_halal']] = $row['jumlah'];
}
$stmt->closeCursor();

$stmt = $pdo->query("CALL sp_umkm_by_rasa()");
$kategori_rasa = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$drill_down_title = "";
$drill_down_data = [];

if (isset($_GET['view_halal'])) {
    $status = $_GET['view_halal'];
    $drill_down_title = "Sertifikasi: " . ucfirst($status);
    $stmt = $pdo->prepare("SELECT id_UMKM, nama_UMKM FROM umkm WHERE status_halal = ?");
    $stmt->execute([$status]);
    $drill_down_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}

if (isset($_GET['view_rasa'])) {
    $rasa = $_GET['view_rasa'];
    $drill_down_title = "Karakteristik Rasa: " . ucfirst($rasa);
    $stmt = $pdo->prepare("SELECT DISTINCT u.id_UMKM, u.nama_UMKM FROM umkm u JOIN menu m ON u.id_UMKM = m.id_UMKM WHERE FIND_IN_SET(?, m.rasa)");
    $stmt->execute([$rasa]);
    $drill_down_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}
?>

<div class="ios-navbar-top">
    <h1>Karakteristik Produk</h1>
</div>

<div class="ios-card">
    <div class="ios-card-header">
        <h3><i class="bi bi-shield-check" style="color: #34c759;"></i> Ringkasan Sertifikasi Halal</h3>
    </div>
    <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
        <a href="?view_halal=bersertifikat" class="ios-list-item" style="display: flex; justify-content: space-between; text-decoration: none; color: inherit; padding: 10px 4px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <span><i class="bi bi-patch-check-fill" style="color:#34c759;"></i> Bersertifikat Resmi</span>
            <strong style="color:#fff;"><?= $status_halal_summary['bersertifikat'] ?> UMKM <i class="bi bi-chevron-right" style="color:#8e8e93;"></i></strong>
        </a>
        <a href="?view_halal=halal" class="ios-list-item" style="display: flex; justify-content: space-between; text-decoration: none; color: inherit; padding: 10px 4px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <span><i class="bi bi-heart-fill" style="color:#ffcc00;"></i> Halal (Self Declare)</span>
            <strong style="color:#fff;"><?= $status_halal_summary['halal'] ?> UMKM <i class="bi bi-chevron-right" style="color:#8e8e93;"></i></strong>
        </a>
        <a href="?view_halal=non-halal" class="ios-list-item" style="display: flex; justify-content: space-between; text-decoration: none; color: inherit; padding: 10px 4px;">
            <span><i class="bi bi-exclamation-triangle-fill" style="color:var(--ios-danger);"></i> Non-Halal</span>
            <strong style="color:#fff;"><?= $status_halal_summary['non-halal'] ?> UMKM <i class="bi bi-chevron-right" style="color:#8e8e93;"></i></strong>
        </a>
    </div>
</div>

<div class="ios-card">
    <div class="ios-card-header">
        <h3><i class="bi bi-egg-fried" style="color: #ff2d55;"></i> Karakteristik Kategori Rasa</h3>
    </div>
    <div style="margin-top: 12px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
        <?php foreach ($kategori_rasa as $rasa): ?>
            <a href="?view_rasa=<?= urlencode($rasa['rasa']) ?>" class="ios-card" style="margin: 0; padding: 12px; background: rgba(255, 45, 85, 0.04); border-radius: 10px; border-left: 4px solid #ff2d55; text-decoration: none; color: inherit; display: block;">
                <div style="font-size: 11px; color: #8e8e93; text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($rasa['rasa']) ?></div>
                <div style="font-size: 16px; font-weight: bold; color: #ff2d55; margin-top: 2px; display: flex; justify-content: space-between; align-items: center;">
                    <?= $rasa['jumlah_umkm'] ?> <span style="font-size: 11px; font-weight: normal; color: #8e8e93;">UMKM <i class="bi bi-chevron-right"></i></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!empty($drill_down_title)): ?>
    <div class="ios-card" style="margin-bottom: 80px; border: 1px solid #007aff; background: rgba(0, 122, 255, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
            <h4 style="margin: 0; color: #007aff; font-size: 14px;"><i class="bi bi-funnel"></i> <?= htmlspecialchars($drill_down_title) ?></h4>
            <a href="statistik.php" style="text-decoration: none; font-size: 12px; color: #8e8e93;"><i class="bi bi-x-circle"></i> Tutup</a>
        </div>
        
        <div id="ios-drilldown-container" class="ios-vertical-layout">
            <?php if (count($drill_down_data) > 0): ?>
                <?php foreach ($drill_down_data as $item): ?>
                    <a href="umkm_detail.php?id=<?= $item['id_UMKM'] ?>" class="ios-card" 
                       style="margin: 0; padding: 12px 14px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: inherit; width: 100%; box-sizing: border-box;">
                        <span style="font-size: 14px; font-weight: 600; color: #fff;"><i class="bi bi-shop" style="color:#007aff;"></i> <?= htmlspecialchars($item['nama_UMKM']) ?></span>
                        <i class="bi bi-chevron-right" style="color: #8e8e93; font-size: 12px;"></i>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: 13px; color: #8e8e93; text-align: center; width:100%;">Tidak ditemukan data gerai kuliner.</p>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div style="margin-bottom: 80px;"></div>
<?php endif; ?>

<style>
    .ios-vertical-layout {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 12px;
        width: 100%;
    }
    @media (orientation: landscape) {
        .ios-vertical-layout {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .ios-vertical-layout .ios-card {
            width: 100% !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>