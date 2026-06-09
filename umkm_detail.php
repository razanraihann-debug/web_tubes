<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
require 'config/database.php';
$id_umkm = $_GET['id'] ?? null;
if (!$id_umkm) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_menu'])) {
    $nama_menu = $_POST['nama_menu']; $harga_menu = $_POST['harga_menu'];
    $kategori = $_POST['kategori']; $rasa = implode(',', $_POST['rasa'] ?? []);

    if ($_POST['action_menu'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO menu (id_UMKM, nama_menu, harga_menu, rasa, kategori) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_umkm, $nama_menu, $harga_menu, $rasa, $kategori]);
    } elseif ($_POST['action_menu'] == 'edit') {
        $id_menu = $_POST['id_menu'];
        $stmt = $pdo->prepare("UPDATE menu SET nama_menu = ?, harga_menu = ?, rasa = ?, kategori = ? WHERE id_menu = ?");
        $stmt->execute([$nama_menu, $harga_menu, $rasa, $kategori, $id_menu]);
    }
    header("Location: umkm_detail.php?id=" . $id_umkm); exit;
}

if (isset($_GET['delete_menu'])) {
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id_menu = ?");
    $stmt->execute([$_GET['delete_menu']]);
    header("Location: umkm_detail.php?id=" . $id_umkm); exit;
}

$stmt = $pdo->prepare("SELECT u.*, l.jalan FROM umkm u LEFT JOIN lokasi l ON u.id_UMKM = l.id_UMKM WHERE u.id_UMKM = ?");
$stmt->execute([$id_umkm]); $umkm = $stmt->fetch();

$sp_total = $pdo->prepare("CALL sp_total_menu(?)"); $sp_total->execute([$id_umkm]); $total_menu = $sp_total->fetch(); $sp_total->closeCursor();
$sp_mahal = $pdo->prepare("CALL sp_menu_termahal(?)"); $sp_mahal->execute([$id_umkm]); $menu_termahal = $sp_mahal->fetch(); $sp_mahal->closeCursor();

$sp_menu = $pdo->prepare("CALL get_menu_umkm(?)"); $sp_menu->execute([$id_umkm]); $menus = $sp_menu->fetchAll(); $sp_menu->closeCursor();

$stmt_log = $pdo->prepare("SELECT l.*, m.nama_menu FROM log_menu l JOIN menu m ON l.id_menu = m.id_menu WHERE m.id_UMKM = ? ORDER BY l.waktu DESC");
$stmt_log->execute([$id_umkm]); $logs = $stmt_log->fetchAll();

include 'includes/header.php';
?>

<div class="ios-navbar-top">
    <a href="index.php" class="ios-back-btn"><i class="bi bi-chevron-left"></i> Eksplor</a>
    <h1>Detail</h1>
</div>

<div class="ios-card ios-analytics-widget">
    <div class="ios-analytics-flex">
        <div>
            <div class="ios-analytics-label">Total Jenis Menu</div>
            <div class="ios-analytics-value"><?= $total_menu['total_menu'] ?? 0 ?></div>
        </div>
        <div class="ios-analytics-right">
            <div class="ios-analytics-label text-danger">Paling Mahal 🔥</div>
            <div class="ios-analytics-right-title"><?= isset($menu_termahal['nama_menu']) ? htmlspecialchars($menu_termahal['nama_menu']) : '-' ?></div>
            <div class="small-text opacity-80">Rp<?= isset($menu_termahal['harga_menu']) ? number_format($menu_termahal['harga_menu']) : '0' ?></div>
        </div>
    </div>
</div>

<div class="ios-card">
    <h3 class="ios-card-title small-text" id="form-title" style="margin-bottom:14px;">Tambah Item Baru</h3>
    <form method="POST" id="menu-form">
        <input type="hidden" name="action_menu" id="action_menu" value="add">
        <input type="hidden" name="id_menu" id="id_menu" value="">
        
        <div class="ios-form-group">
            <input type="text" name="nama_menu" id="form_nama" class="ios-form-control" placeholder="Nama Makanan/Minuman" required>
        </div>
        <div class="ios-form-group ios-form-group-row">
            <input type="number" name="harga_menu" id="form_harga" class="ios-form-control" placeholder="Harga (Rp)" required>
            <select name="kategori" id="form_kategori" class="ios-form-control" required>
                <option value="makanan_berat">Makanan</option>
                <option value="cemilan">Cemilan</option>
                <option value="minuman">Minuman</option>
            </select>
        </div>
        
        <div class="ios-form-group">
            <label>Profil Rasa</label>
            <div class="ios-checkbox-wrapper">
                <?php foreach(['manis', 'asin', 'pedas', 'masam'] as $r): ?>
                    <label class="ios-checkbox-label" for="rasa_<?= $r ?>">
                        <input type="checkbox" name="rasa[]" value="<?= $r ?>" id="rasa_<?= $r ?>" class="ios-checkbox-input"> <?= ucfirst($r) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="ios-btn" id="btn-submit-menu">Simpan Item</button>
        <button type="button" class="ios-btn ios-btn-secondary d-none" id="btn-cancel-edit" onclick="resetMenuForm()" style="margin-top:6px;">Batal</button>
    </form>
</div>

<h3 class="ios-section-title">Daftar Menu Tersedia</h3>
<div class="ios-list-view">
    <?php if (empty($menus)): ?>
        <p class="text-center text-muted" style="padding: 20px;">Belum ada item menu.</p>
    <?php else: ?>
        <?php foreach($menus as $m): ?>
            <div class="ios-list-item">
                <div class="ios-list-item-main">
                    <div class="ios-list-item-title"><?= htmlspecialchars($m['nama_menu']) ?></div>
                    <div class="ios-list-item-subtitle">
                        <?= str_replace('_', ' ', $m['kategori']) ?> 
                        <?php if(!empty($m['rasa'])): ?> &bull; <span style="color: var(--ios-warning);"><?= $m['rasa'] ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="ios-list-item-action">
                    <span class="ios-list-item-price">Rp<?= number_format($m['harga_menu']) ?></span>
                    <button type="button" onclick='editMenu(<?= json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="ios-icon-btn edit"><i class="bi bi-pencil-square"></i></button>
                    <a href="umkm_detail.php?id=<?= $id_umkm ?>&delete_menu=<?= $m['id_menu'] ?>" onclick="return confirm('Hapus menu?')" class="ios-icon-btn delete"><i class="bi bi-trash3-fill"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h3 class="ios-section-title">Histori Perubahan Harga</h3>
<div class="ios-card">
    <?php if (empty($logs)): ?>
        <p class="text-center text-muted small-text">Tidak ada log perubahan harga terbaru.</p>
    <?php else: ?>
        <?php foreach($logs as $log): ?>
            <div class="ios-timeline-item">
                <strong><?= htmlspecialchars($log['nama_menu']) ?></strong>: 
                <span class="text-muted" style="text-decoration: line-through;">Rp<?= number_format($log['harga_lama']) ?></span> &rarr; 
                <span style="color: var(--ios-success); font-weight:600;">Rp<?= number_format($log['harga_baru']) ?></span>
                <div class="text-muted" style="font-size: 10px;"><?= $log['waktu'] ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function editMenu(data) {
    document.getElementById('form-title').innerText = "Edit Item: " + data.nama_menu;
    document.getElementById('action_menu').value = "edit";
    document.getElementById('id_menu').value = data.id_menu;
    document.getElementById('form_nama').value = data.nama_menu;
    document.getElementById('form_harga').value = data.harga_menu;
    document.getElementById('form_kategori').value = data.kategori;
    
    document.querySelectorAll(".ios-checkbox-input").forEach(el => el.checked = false);
    if(data.rasa) {
        data.rasa.split(',').forEach(r => {
            let cb = document.getElementById('rasa_' + r.trim());
            if(cb) cb.checked = true;
        });
    }
    document.getElementById('btn-submit-menu').className = "ios-btn ios-btn-warning";
    document.getElementById('btn-cancel-edit').classList.remove('d-none');
    window.scrollTo({top: 180, behavior: 'smooth'});
}

function resetMenuForm() {
    document.getElementById('form-title').innerText = "Tambah Item Baru";
    document.getElementById('action_menu').value = "add";
    document.getElementById('menu-form').reset();
    document.getElementById('btn-submit-menu').className = "ios-btn";
    document.getElementById('btn-cancel-edit').classList.add('d-none');
}
</script>

<?php include 'includes/footer.php'; ?>