<?php
// ============================================
// HALAMAN DETAIL PRODUK
// ============================================
require_once __DIR__ . '/includes/init.php';

$kodeProduk = $_GET["kode"] ?? "";

if (!isset($produk[$kodeProduk])) {
    http_response_code(404);
    $judulHalaman = "Produk tidak ditemukan";
    $halamanAktif = "produk.php";
    require __DIR__ . '/includes/header.php';
    echo '<div class="max-w-6xl mx-auto px-4 py-20 text-center"><div class="text-7xl mb-4">😵</div>
          <h1 class="text-2xl font-bold mb-2">Produk tidak ditemukan</h1>
          <a href="produk.php" class="text-amber-600 font-semibold hover:underline">← Kembali ke katalog</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$item         = $produk[$kodeProduk];
$hargaAkhir   = hargaSetelahDiskon($item["harga"], $item["diskon"]);
$judulHalaman = $item["nama"];
$halamanAktif = "produk.php";

// Produk terkait: kategori sama, selain produk ini
$terkait = array_filter($produk, fn($p, $k) => $p["kategori"] === $item["kategori"] && $k !== $kodeProduk, ARRAY_FILTER_USE_BOTH);
$terkait = array_slice($terkait, 0, 3, true);

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <p class="text-sm text-slate-500 mb-4">
        <a href="index.php" class="hover:underline">Beranda</a> /
        <a href="produk.php?kategori=<?= $item['kategori'] ?>" class="hover:underline"><?= e($kategori[$item['kategori']]['nama']) ?></a> /
        <span class="text-slate-800"><?= e($item["nama"]) ?></span>
    </p>

    <div class="grid md:grid-cols-2 gap-8 bg-white rounded-3xl border border-slate-200 p-6 md:p-8">
        <div class="bg-gradient-to-br from-slate-50 to-slate-200 rounded-2xl flex items-center justify-center text-[9rem] py-12 relative">
            <?= $item["ikon"] ?>
            <?php if ($item["diskon"] > 0): ?>
                <span class="absolute top-4 left-4 bg-red-600 text-white font-bold px-3 py-1 rounded-full">-<?= $item["diskon"] ?>%</span>
            <?php endif; ?>
        </div>

        <div class="flex flex-col">
            <p class="text-sm text-slate-500"><?= $kodeProduk ?> &middot; <?= e($item["merek"]) ?></p>
            <h1 class="text-3xl font-extrabold mb-2"><?= e($item["nama"]) ?></h1>
            <p class="text-amber-500 mb-4"><?= bintang($item["rating"]) ?> <span class="text-slate-400 text-sm"><?= number_format($item["rating"], 1) ?> / 5</span></p>

            <?php if ($item["diskon"] > 0): ?>
                <p class="text-slate-400 line-through"><?= formatRupiah($item["harga"]) ?></p>
            <?php endif; ?>
            <p class="text-3xl font-extrabold mb-4"><?= formatRupiah($hargaAkhir) ?></p>

            <p class="text-slate-600 mb-4"><?= e($item["deskripsi"]) ?></p>
            <span class="self-start text-xs font-semibold px-2 py-1 rounded-full mb-6 <?= warnaStok($item["stok"]) ?>"><?= labelStok($item["stok"]) ?> (<?= $item["stok"] ?> unit)</span>

            <form method="post" action="keranjang.php" class="mt-auto flex gap-3">
                <?= csrfField() ?>
                <input type="hidden" name="aksi" value="tambah">
                <input type="hidden" name="kode" value="<?= e($kodeProduk) ?>">
                <input type="number" name="jumlah" value="1" min="1" max="<?= max(1, $item['stok']) ?>"
                       class="w-24 rounded-xl border border-slate-300 px-3 py-3 text-center" <?= $item["stok"] === 0 ? "disabled" : "" ?>>
                <button type="submit" <?= $item["stok"] === 0 ? "disabled" : "" ?>
                    class="flex-1 rounded-xl py-3 font-bold bg-amber-400 hover:bg-amber-300 text-slate-900 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed">
                    <?= $item["stok"] === 0 ? "Stok habis" : "🛒 Tambah ke keranjang" ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Spesifikasi -->
    <section class="bg-white rounded-3xl border border-slate-200 p-6 md:p-8 mt-6">
        <h2 class="text-xl font-bold mb-4">Spesifikasi</h2>
        <table class="w-full text-sm">
            <?php foreach ($item["spesifikasi"] as $label => $nilai): ?>
                <tr class="border-b last:border-0 border-slate-100">
                    <td class="py-2 w-1/3 text-slate-500"><?= e($label) ?></td>
                    <td class="py-2 font-medium"><?= e($nilai) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <?php if (!empty($terkait)): ?>
        <section class="mt-10">
            <h2 class="text-xl font-bold mb-4">Produk terkait</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($terkait as $kode => $item): ?>
                    <?php require __DIR__ . '/includes/kartu-produk.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
