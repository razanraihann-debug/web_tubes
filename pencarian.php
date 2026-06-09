<?php
require 'config/database.php';
include 'includes/header.php';

$hasil_jam = [];
$hasil_harga = null;

// 1. Eksekusi Pencarian Jam Operasional
if (isset($_GET['cari_jam']) && !empty($_GET['jam_input'])) {
    $stmt = $pdo->prepare("CALL sp_umkm_buka_range_jam(?)");
    $stmt->execute([$_GET['jam_input']]);
    $hasil_jam = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}

// 2. Eksekusi Perhitungan Range Harga
if (isset($_GET['cari_harga'])) {
    $min = intval($_GET['harga_min'] ?? 0);
    $max = intval($_GET['harga_max'] ?? 999999);
    $stmt = $pdo->prepare("CALL sp_umkm_range_harga(?, ?)");
    $stmt->execute([$min, $max]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hasil_harga = $row['jumlah_umkm'];
    }
    $stmt->closeCursor();
}
?>

<div class="ios-navbar-top">
    <h1>Eksplorasi Pintar</h1>
</div>

<div class="ios-card">
    <div class="ios-card-header">
        <h3><i class="bi bi-clock" style="color: #007aff;"></i> Jam Operasional Kuliner</h3>
    </div>
    <form method="GET" action="" style="margin-top: 15px;">
        <div class="ios-input-group" style="display: flex; gap: 8px;">
            <input type="time" name="jam_input" class="ios-form-control" 
                   value="<?= htmlspecialchars($_GET['jam_input'] ?? '12:00') ?>" required 
                   style="flex: 1; height: 42px; background: rgba(255, 255, 255, 0.05); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 0 12px; transition: all 0.2s;"
                   onfocus="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='#007aff';"
                   onblur="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">
            <button type="submit" name="cari_jam" value="1" class="ios-btn" style="width: auto; padding: 0 16px; margin: 0; height: 42px;">Cari</button>
        </div>
    </form>

    <?php if (isset($_GET['cari_jam'])): ?>
        <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
            <span style="font-size: 13px; color: #8e8e93;">Buka pukul <b style="color:#fff;"><?= htmlspecialchars($_GET['jam_input']) ?></b>:</span>
            
            <div id="ios-jam-container" class="ios-vertical-layout">
                <?php if (count($hasil_jam) > 0): ?>
                    <?php foreach ($hasil_jam as $umkm): ?>
                        <a href="umkm_detail.php?id=<?= $umkm['id_UMKM'] ?>" class="ios-card" 
                           style="margin: 0; padding: 14px 16px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; box-sizing: border-box;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="bi bi-shop" style="color: #007aff; font-size: 16px;"></i> 
                                <div>
                                    <span style="font-size: 14px; font-weight: 600; color: #ffffff; display: block;"><?= htmlspecialchars($umkm['nama_UMKM']) ?></span>
                                    <span style="font-size: 11px; color: #8e8e93;"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($umkm['jalan']) ?></span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right" style="color: #8e8e93; font-size: 14px;"></i>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 12px; text-align: center; color: var(--ios-danger); font-size: 13px; width:100%;">
                        <i class="bi bi-exclamation-circle"></i> Tidak ada kuliner yang buka di jam ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="ios-card" style="margin-bottom: 80px;">
    <div class="ios-card-header">
        <h3><i class="bi bi-tags" style="color: #34c759;"></i> Cari Berdasarkan Range Harga</h3>
    </div>
    <form method="GET" action="" style="margin-top: 15px;">
        <div style="display: flex; gap: 8px; align-items: center;">
            <input type="number" name="harga_min" class="ios-form-control" placeholder="Min (Rp)" 
                   value="<?= htmlspecialchars($_GET['harga_min'] ?? '') ?>" required
                   style="flex: 1; height: 42px; background: rgba(255, 255, 255, 0.05); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 0 12px; transition: all 0.2s;"
                   onfocus="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='#34c759';"
                   onblur="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">
            
            <span style="color: #8e8e93;">-</span>
            
            <input type="number" name="harga_max" class="ios-form-control" placeholder="Max (Rp)" 
                   value="<?= htmlspecialchars($_GET['harga_max'] ?? '') ?>" required
                   style="flex: 1; height: 42px; background: rgba(255, 255, 255, 0.05); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 0 12px; transition: all 0.2s;"
                   onfocus="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='#34c759';"
                   onblur="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">
            
            <button type="submit" name="cari_harga" value="1" class="ios-btn" style="width: auto; padding: 0 16px; margin: 0; height: 42px; background: #34c759;">Cari</button>
        </div>
    </form>

    <?php if ($hasil_harga !== null): ?>
        <div style="margin-top: 15px; padding: 14px; background: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.2); border-radius: 12px; text-align: center;">
            <span style="font-size: 14px; color: #34c759; font-weight: 600;">Ditemukan <strong><?= $hasil_harga ?> UMKM</strong> yang menyajikan menu dalam rentang harga tersebut.</span>
        </div>
    <?php endif; ?>
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