<?php
// ============================================
// HALAMAN BERANDA
// ============================================
require_once __DIR__ . '/includes/init.php';

$judulHalaman = "Beranda";
$halamanAktif = "index.php";

// Ringkasan toko
$jumlahProduk = count($produk);
$jumlahDiskon = hitungProdukDiskon($produk);
$nilaiStok    = hitungNilaiStok($produk);

// Produk unggulan & produk diskon terbesar
$unggulan   = array_filter($produk, fn($p) => $p["unggulan"]);
$unggulan   = array_slice($unggulan, 0, 4, true);
$diskonBesar = cariProduk($produk, "", "", "diskon");
$diskonBesar = array_slice($diskonBesar, 0, 3, true);

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero-bg text-white">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <span class="inline-block bg-amber-400/20 text-amber-300 text-xs font-semibold px-3 py-1 rounded-full mb-4">Toko elektronik terpercaya di Bali</span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">Gadget impian, <span class="text-amber-400">harga bersahabat.</span></h1>
            <p class="text-slate-300 mb-8"><?= e($slogan) ?></p>
            <div class="flex flex-wrap gap-3">
                <a href="produk.php" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold px-6 py-3 rounded-xl">Belanja sekarang</a>
                <a href="produk.php?urut=diskon" class="border border-white/30 hover:bg-white/10 font-semibold px-6 py-3 rounded-xl">Lihat promo 🔥</a>
            </div>
        </div>
        <div class="text-center text-[8rem] md:text-[10rem] melayang select-none" aria-hidden="true">🎧</div>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 py-10 space-y-14">

    <!-- Ringkasan -->
    <section class="grid sm:grid-cols-3 gap-4 -mt-16 relative">
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <p class="text-sm text-slate-500">Jumlah produk</p>
            <p class="text-2xl font-extrabold"><?= $jumlahProduk ?></p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <p class="text-sm text-slate-500">Sedang diskon</p>
            <p class="text-2xl font-extrabold"><?= $jumlahDiskon ?> produk</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <p class="text-sm text-slate-500">Total nilai stok</p>
            <p class="text-2xl font-extrabold"><?= formatRupiah($nilaiStok) ?></p>
        </div>
    </section>

    <!-- Kategori -->
    <section>
        <h2 class="text-2xl font-bold mb-4">Belanja per kategori</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php foreach ($kategori as $kunci => $k): ?>
                <a href="produk.php?kategori=<?= $kunci ?>" class="bg-gradient-to-br <?= $k['warna'] ?> text-white rounded-2xl p-5 text-center shadow hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="text-4xl mb-2"><?= $k['ikon'] ?></div>
                    <p class="text-sm font-semibold"><?= e($k['nama']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Produk unggulan -->
    <section>
        <div class="flex items-end justify-between mb-4">
            <h2 class="text-2xl font-bold">⭐ Produk unggulan</h2>
            <a href="produk.php" class="text-sm font-semibold text-amber-600 hover:underline">Lihat semua →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($unggulan as $kode => $item): ?>
                <?php require __DIR__ . '/includes/kartu-produk.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Banner voucher -->
    <section class="rounded-3xl bg-gradient-to-r from-amber-400 to-orange-500 p-8 md:p-10 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-slate-900/70">Voucher spesial</p>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Pakai kode <span class="bg-white/80 px-2 rounded">DEWATA10</span> diskon 10%</h2>
            <p class="text-slate-900/70 text-sm mt-1">Atau <b>HEMAT50K</b> untuk potongan Rp 50.000. Masukkan di halaman keranjang.</p>
        </div>
        <a href="produk.php" class="bg-slate-900 text-white font-bold px-6 py-3 rounded-xl hover:bg-slate-800">Mulai belanja</a>
    </section>

    <!-- Promo terbesar -->
    <section>
        <h2 class="text-2xl font-bold mb-4">🔥 Diskon terbesar</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($diskonBesar as $kode => $item): ?>
                <?php require __DIR__ . '/includes/kartu-produk.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Keunggulan -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php
        $keunggulan = [
            ["🛡️", "Garansi resmi", "Semua produk bergaransi toko."],
            ["🚚", "Kirim cepat", "Pengiriman se-Bali 1-2 hari."],
            ["💳", "Bayar fleksibel", "Transfer, QRIS, atau COD."],
            ["🎧", "Layanan ramah", "Siap membantu setiap hari."],
        ];
        foreach ($keunggulan as [$ikon, $judul, $isi]): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-3xl mb-2"><?= $ikon ?></div>
                <p class="font-semibold"><?= $judul ?></p>
                <p class="text-sm text-slate-500"><?= $isi ?></p>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
