<?php
require 'config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?status=hapus_gagal');
    exit;
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Trigger lama di database menghapus tabel jam_operasional yang tidak ada,
    // sehingga DELETE FROM umkm gagal. Hapus trigger rusak ini jika masih ada.
    $pdo->exec('DROP TRIGGER IF EXISTS bersihkan_data_umkm_total');

    $pdo->beginTransaction();

    $tables = [
        'lokasi',
        'menu',
        'metode_pembayaran',
        'mitra_online',
        'waktu_operasional',
    ];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id_UMKM = ?");
        $stmt->execute([$id]);
    }

    $stmt = $pdo->prepare('DELETE FROM umkm WHERE id_UMKM = ?');
    $stmt->execute([$id]);

    $pdo->commit();

    header('Location: index.php?status=hapus_berhasil');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Gagal menghapus UMKM: ' . $e->getMessage());
    header('Location: index.php?status=hapus_gagal');
    exit;
}
