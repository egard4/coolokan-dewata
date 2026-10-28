<?php
// ============================================
// HALAMAN KATALOG: cari, filter, urutkan
// ============================================
require_once __DIR__ . '/includes/init.php';

$judulHalaman = "Produk";
$halamanAktif = "produk.php";

// Ambil parameter dari URL (GET) dan bersihkan
$kata = trim($_GET["q"] ?? "");
$kat  = $_GET["kategori"] ?? "";
$urut = $_GET["urut"] ?? "";
if (!isset($kategori[$kat])) {
    $kat = "";
}

$hasil = cariProduk($produk, $kata, $kat, $urut);

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-1">Katalog Produk</h1>
    <p class="text-slate-500 mb-6">Menampilkan <b><?= count($hasil) ?></b> dari <?= count($produk) ?> produk.</p>

    <!-- Filter -->
    <form method="get" class="bg-white rounded-2xl border border-slate-200 p-4 grid md:grid-cols-4 gap-3 mb-6">
        <input type="search" name="q" value="<?= e($kata) ?>" placeholder="Cari nama / merek..."
               class="md:col-span-2 rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
        <select name="kategori" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            <?php foreach ($kategori as $kunci => $k): ?>
                <option value="<?= $kunci ?>" <?= $kunci === $kat ? "selected" : "" ?>><?= $k["ikon"] ?> <?= e($k["nama"]) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="urut" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <option value="">Urutkan</option>
            <option value="murah"  <?= $urut === "murah"  ? "selected" : "" ?>>Harga termurah</option>
            <option value="mahal"  <?= $urut === "mahal"  ? "selected" : "" ?>>Harga termahal</option>
            <option value="diskon" <?= $urut === "diskon" ? "selected" : "" ?>>Diskon terbesar</option>
            <option value="rating" <?= $urut === "rating" ? "selected" : "" ?>>Rating tertinggi</option>
        </select>
        <div class="md:col-span-4 flex gap-2">
            <button class="bg-slate-900 text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-slate-800">Terapkan</button>
            <a href="produk.php" class="text-sm font-semibold px-5 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">Reset</a>
        </div>
    </form>

    <?php if (empty($hasil)): ?>
        <div class="text-center bg-white rounded-2xl border border-dashed border-slate-300 py-16">
            <div class="text-6xl mb-3">🔍</div>
            <p class="font-semibold">Produk tidak ditemukan</p>
            <p class="text-sm text-slate-500">Coba kata kunci atau kategori lain.</p>
        </div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($hasil as $kode => $item): ?>
                <?php require __DIR__ . '/includes/kartu-produk.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
