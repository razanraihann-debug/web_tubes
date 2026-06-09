</div> <nav class="ios-bottom-nav">
    <a href="index.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-compass-fill"></i>
        <span>Eksplor</span>
    </a>
    <a href="pencarian.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'pencarian.php' ? 'active' : '' ?>">
        <i class="bi bi-search-heart-fill"></i>
        <span>Cari & Jam</span>
    </a>
    <a href="mitra.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'mitra.php' ? 'active' : '' ?>">
        <i class="bi bi-bicycle"></i>
        <span>Mitra & Bayar</span>
    </a>
    <a href="statistik.php" class="ios-nav-item <?= basename($_SERVER['PHP_SELF']) == 'statistik.php' ? 'active' : '' ?>">
        <i class="bi bi-pie-chart-fill"></i>
        <span>Karakteristik</span>
    </a>
</nav>

</body>
</html>