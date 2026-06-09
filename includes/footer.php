</div> <nav class="ios-bottom-nav">
    <a href="index.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'umkm_detail.php' ? 'active' : '' ?>">
        <i class="bi bi-compass-fill"></i>
        <span>Eksplor</span>
    </a>
    <a href="umkm_create.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'umkm_create.php' ? 'active' : '' ?>">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Tambah</span>
    </a>
</nav>

</body>
</html>