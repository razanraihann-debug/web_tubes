<?php
require 'config/database.php';
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();
        // Berhubung di database SQL relasi waktu_operasional menggunakan CASCADE, kita hapus sisa tabel manual.
        $pdo->prepare("DELETE FROM lokasi WHERE id_UMKM = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM menu WHERE id_UMKM = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM metode_pembayaran WHERE id_UMKM = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM mitra_online WHERE id_UMKM = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM waktu_operasional WHERE id_UMKM = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM umkm WHERE id_UMKM = ?")->execute([$id]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}
header("Location: index.php");
exit;
?>