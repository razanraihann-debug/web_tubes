<?php
require 'config/database.php';
include 'includes/header.php';

$hasil_jam = [];
$hasil_harga = null;
$daftar_umkm_harga = []; 

// 1. Eksekusi Pencarian Jam Operasional
if (isset($_GET['cari_jam']) && !empty($_GET['jam_input'])) {
    $stmt = $pdo->prepare("CALL sp_umkm_buka_range_jam(?)");
    $stmt->execute([$_GET['jam_input']]);
    $hasil_jam = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}

// 2. Eksekusi Stored Procedure Ringkas Range Harga
if (isset($_GET['cari_harga'])) {
    $min = intval($_GET['harga_min'] ?? 0);
    $max = intval($_GET['harga_max'] ?? 999999);
    
    // Memanggil procedure yang sudah diperbaiki (hanya data UMKM)
    $stmt = $pdo->prepare("CALL sp_umkm_range_harga(?, ?)");
    $stmt->execute([$min, $max]);
    
    // Ambil baris data UMKM yang unik
    $daftar_umkm_harga = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Menghitung jumlah UMKM yang ditemukan
    $hasil_harga = count($daftar_umkm_harga);
    
    // PENGAMAN UTAMA BLANK HITAM: Membersihkan sisa buffer koneksi MySQL
    $stmt->closeCursor();
    while ($stmt->nextRowset()) {
        // Mengosongkan rowset tambahan dari procedure
    }
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
            
            <div class="ios-vertical-layout">
                <?php if (count($hasil_jam) > 0): ?>
                    <?php foreach ($hasil_jam as $umkm): ?>
                        <a href="umkm_detail.php?id=<?= $umkm['id_UMKM'] ?>" class="ios-item-link" 
                           style="padding: 14px 16px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; box-sizing: border-box; transition: transform 0.2s, background 0.2s;"
                           onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.transform='translateY(0)';">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="background: rgba(0, 122, 255, 0.15); padding: 8px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shop" style="color: #007aff; font-size: 18px;"></i> 
                                </div>
                                <div>
                                    <span style="font-size: 14px; font-weight: 600; color: #ffffff; display: block;"><?= htmlspecialchars($umkm['nama_UMKM']) ?></span>
                                    <span style="font-size: 11px; color: #8e8e93;"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($umkm['jalan'] ?? 'Lokasi belum diatur') ?></span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right" style="color: #8e8e93; font-size: 14px;"></i>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 12px; text-align: center; color: #ff3b30; font-size: 13px; width:100%;">
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
        <div style="margin-top: 15px; padding: 14px; background: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.2); border-radius: 12px; text-align: center; margin-bottom: 16px;">
            <span style="font-size: 14px; color: #34c759; font-weight: 600;">Ditemukan <strong><?= $hasil_harga ?> Tempat UMKM</strong> dengan menu di rentang harga tersebut.</span>
        </div>

        <div class="ios-grid-container">
            <?php if ($hasil_harga > 0): ?>
                <?php foreach ($daftar_umkm_harga as $u_harga): ?>
                    <a href="umkm_detail.php?id=<?= urlencode($u_harga['id_UMKM'] ?? '') ?>" class="ios-card-item" 
                       onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.transform='translateY(0)';">
                        
                        <div class="ios-card-content">
                            <div class="ios-icon-box">
                                <i class="bi bi-shop"></i> 
                            </div>
                            <div class="ios-text-box">
                                <span class="ios-title-umkm"><?= htmlspecialchars($u_harga['nama_UMKM'] ?? '') ?></span>
                                <span class="ios-sub-alamat">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars(($u_harga['jalan'] ?? 'Lokasi belum diatur')) ?>
                                </span>
                            </div>
                        </div>
                        
                        <i class="bi bi-chevron-right ios-arrow-icon"></i>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; padding: 24px; text-align: center; color: #8e8e93; font-size: 13px;">
                    <i class="bi bi-exclamation-circle"></i> Tidak ada data UMKM untuk rentang harga ini.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Mengamankan Container Utama agar Item tidak Meluber */
    .ios-card {
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important; /* Mencegah elemen keluar kotak abu-abu */
        padding: 16px !important;
    }

    /* Grid Layout Aman Menggunakan auto-fit */
    .ios-grid-container {
        display: grid !important;
        /* auto-fit akan otomatis menyesuaikan jumlah kolom berdasarkan ruang yang tersedia di kotak abu-abu */
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
        gap: 12px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Styling Komponen Kartu UMKM Individu */
    .ios-card-item {
        padding: 14px 16px; 
        background: rgba(255, 255, 255, 0.06); 
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        text-decoration: none; 
        color: inherit; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        width: 100% !important; 
        box-sizing: border-box !important; 
        transition: transform 0.2s, background 0.2s;
        overflow: hidden;
    }

    .ios-card-content {
        display: flex; 
        align-items: center; 
        gap: 12px; 
        width: 88%;
        overflow: hidden;
    }

    .ios-icon-box {
        background: rgba(52, 199, 89, 0.15); 
        padding: 10px; 
        border-radius: 10px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        flex-shrink: 0;
    }

    .ios-icon-box i {
        color: #34c759; 
        font-size: 18px;
    }

    .ios-text-box {
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .ios-title-umkm {
        font-size: 14px; 
        font-weight: 600; 
        color: #ffffff; 
        display: block; 
        white-space: nowrap; 
        text-overflow: ellipsis; 
        overflow: hidden;
    }

    .ios-sub-alamat {
        font-size: 11px; 
        color: #8e8e93; 
        display: block; 
        margin-top: 4px; 
        white-space: nowrap; 
        text-overflow: ellipsis; 
        overflow: hidden;
    }

    .ios-arrow-icon {
        color: #8e8e93; 
        font-size: 12px;
        flex-shrink: 0;
    }
</style>

<?php include 'includes/footer.php'; ?>