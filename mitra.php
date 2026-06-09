<?php
require 'config/database.php';
include 'includes/header.php';

$mitra_dominan = '-';
$total_dominan = 0;
$pilihan_mitra = $_GET['jenis_mitra'] ?? '';
$umkm_by_mitra = [];

$stmt = $pdo->query("CALL sp_mitra_terbanyak()");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mitra_dominan = $row['nama_mitra'];
    $total_dominan = $row['total'];
}
$stmt->closeCursor();

$stmt = $pdo->query("CALL sp_metode_non_cash()");
$pembayaran_non_cash = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (!empty($pilihan_mitra)) {
    $stmt = $pdo->prepare("CALL sp_umkm_by_golongan_mitra(?)");
    $stmt->execute([$pilihan_mitra]);
    $umkm_by_mitra = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}
?>

<div class="ios-navbar-top">
    <h1>Mitra & Pembayaran</h1>
</div>

<div class="ios-card">
    <div class="ios-card-header">
        <h3><i class="bi bi-bicycle" style="color: #ff9500;"></i> Manajemen Mitra Online</h3>
    </div>
    
    <div style="margin-top: 12px; padding: 12px; background: rgba(255, 149, 0, 0.08); border-radius: 10px;">
        <div style="font-size: 11px; color: #8e8e93; text-transform: uppercase; font-weight: bold;">Mitra Paling Populer</div>
        <div style="font-size: 16px; font-weight: bold; color: #e65100; margin-top: 2px;">
            <?= htmlspecialchars($mitra_dominan) ?> <span style="font-size: 13px; font-weight: normal; color:#8e8e93;">(<?= $total_dominan ?> UMKM)</span>
        </div>
    </div>

    <form method="GET" action="" style="margin-top: 15px;">
        <label style="font-size: 13px; color: #8e8e93; font-weight: 600;">Lihat UMKM Terdaftar di Mitra Tertentu:</label>
        <div style="display: flex; gap: 8px; margin-top: 6px;">
            <select name="jenis_mitra" class="ios-form-control" style="flex: 1; height: 42px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 0 10px;">
                <option value="" style="background: #1c1c1e; color:#fff;">-- Pilih Layanan Mitra --</option>
                <option value="GoFood" <?= $pilihan_mitra == 'GoFood' ? 'selected' : '' ?> style="background: #1c1c1e; color:#fff;">GoFood</option>
                <option value="GrabFood" <?= $pilihan_mitra == 'GrabFood' ? 'selected' : '' ?> style="background: #1c1c1e; color:#fff;">GrabFood</option>
                <option value="ShopeeFood" <?= $pilihan_mitra == 'ShopeeFood' ? 'selected' : '' ?> style="background: #1c1c1e; color:#fff;">ShopeeFood</option>
            </select>
            <button type="submit" class="ios-btn" style="width: auto; padding: 0 16px; margin: 0; background:#ff9500;">Tampilkan</button>
        </div>
    </form>

    <?php if (!empty($pilihan_mitra)): ?>
        <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
            <span style="font-size: 13px; color: #8e8e93;">Hasil untuk mitra <b style="color: #fff;"><?= htmlspecialchars($pilihan_mitra) ?></b>:</span>
            
            <div id="ios-mitra-container" class="ios-vertical-layout">
                <?php if (count($umkm_by_mitra) > 0): ?>
                    <?php foreach ($umkm_by_mitra as $u): ?>
                        <a href="umkm_detail.php?id=<?= $u['id_UMKM'] ?>" class="ios-card" 
                           style="margin: 0; padding: 14px 16px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; box-sizing: border-box;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="bi bi-shop" style="color: #ff9500; font-size: 16px;"></i> 
                                <span style="font-size: 14px; font-weight: 600; color: #ffffff;"><?= htmlspecialchars($u['nama_UMKM']) ?></span>
                            </div>
                            <i class="bi bi-chevron-right" style="color: #8e8e93; font-size: 14px;"></i>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 12px; text-align: center; color: var(--ios-danger); font-size: 13px; width: 100%;">
                        <i class="bi bi-exclamation-circle"></i> Belum ada data UMKM untuk mitra ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="ios-card" style="margin-bottom: 80px;">
    <div class="ios-card-header">
        <h3><i class="bi bi-credit-card-2-front" style="color: #5856d6;"></i> Opsi Pembayaran Non-Cash</h3>
    </div>
    <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
        <?php foreach ($pembayaran_non_cash as $bayar): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(88, 86, 214, 0.05); border: 1px solid rgba(88, 86, 214, 0.1); border-radius: 8px;">
                <span style="font-size: 14px; font-weight: 600; color: #fff;"><i class="bi bi-qr-code-scan"></i> <?= htmlspecialchars($bayar['nama_metode']) ?></span>
                <span class="ios-badge" style="background: #5856d6; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;"><?= $bayar['jumlah_umkm'] ?> UMKM</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

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