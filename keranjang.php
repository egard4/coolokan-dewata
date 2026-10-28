<?php
// ============================================
// HALAMAN KERANJANG BELANJA
// ============================================
require_once __DIR__ . '/includes/init.php';

// ---- Proses aksi (POST), lalu redirect agar tidak terkirim ulang saat refresh ----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrfCek()) {
        setFlash("error", "Sesi tidak valid, silakan coba lagi.");
        header("Location: keranjang.php");
        exit;
    }

    $aksi = $_POST["aksi"] ?? "";
    $kode = $_POST["kode"] ?? "";

    switch ($aksi) {
        case "tambah":
            keranjangTambah($kode, (int) ($_POST["jumlah"] ?? 1), $produk);
            break;
        case "ubah":
            keranjangUbah($kode, (int) ($_POST["jumlah"] ?? 1), $produk);
            break;
        case "hapus":
            keranjangUbah($kode, 0, $produk);
            break;
        case "kosongkan":
            unset($_SESSION["keranjang"], $_SESSION["voucher"]);
            setFlash("sukses", "Keranjang dikosongkan.");
            break;
        case "voucher":
            $kodeVoucher = strtoupper(trim($_POST["voucher"] ?? ""));
            if ($kodeVoucher === "") {
                unset($_SESSION["voucher"]);
                setFlash("sukses", "Voucher dilepas.");
            } elseif (isset($voucher[$kodeVoucher])) {
                $_SESSION["voucher"] = $kodeVoucher;
                setFlash("sukses", "Voucher $kodeVoucher dipakai: " . $voucher[$kodeVoucher]["ket"] . ".");
            } else {
                setFlash("error", "Kode voucher tidak valid.");
            }
            break;
    }
    header("Location: keranjang.php");
    exit;
}

$judulHalaman = "Keranjang";
$halamanAktif = "keranjang.php";
$r = ringkasanKeranjang($produk, $voucher);

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-6">🛒 Keranjang Belanja</h1>

    <?php if (empty($r["baris"])): ?>
        <div class="text-center bg-white rounded-2xl border border-dashed border-slate-300 py-16">
            <div class="text-6xl mb-3">🛒</div>
            <p class="font-semibold mb-1">Keranjangmu masih kosong</p>
            <a href="produk.php" class="inline-block mt-3 bg-amber-400 hover:bg-amber-300 font-bold px-6 py-2 rounded-xl">Mulai belanja</a>
        </div>
    <?php else: ?>
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Daftar barang -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($r["baris"] as $b): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-wrap items-center gap-4">
                        <div class="text-5xl bg-slate-100 rounded-xl w-20 h-20 flex items-center justify-center"><?= $b["item"]["ikon"] ?></div>
                        <div class="flex-1 min-w-40">
                            <a href="detail.php?kode=<?= e($b['kode']) ?>" class="font-semibold hover:text-amber-600"><?= e($b["item"]["nama"]) ?></a>
                            <p class="text-sm text-slate-500"><?= formatRupiah($b["harga"]) ?> / unit</p>
                            <p class="font-bold"><?= formatRupiah($b["subtotal"]) ?></p>
                        </div>
                        <form method="post" class="flex items-center gap-2">
                            <?= csrfField() ?>
                            <input type="hidden" name="aksi" value="ubah">
                            <input type="hidden" name="kode" value="<?= e($b['kode']) ?>">
                            <input type="number" name="jumlah" value="<?= $b["jumlah"] ?>" min="1" max="<?= $b["item"]["stok"] ?>"
                                   class="w-20 rounded-lg border border-slate-300 px-2 py-2 text-center">
                            <button class="text-sm font-semibold px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800">Ubah</button>
                        </form>
                        <form method="post">
                            <?= csrfField() ?>
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="kode" value="<?= e($b['kode']) ?>">
                            <button class="text-sm text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <form method="post" onsubmit="return confirm('Kosongkan semua isi keranjang?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="aksi" value="kosongkan">
                    <button class="text-sm text-slate-500 hover:text-red-600 hover:underline">Kosongkan keranjang</button>
                </form>
            </div>

            <!-- Ringkasan -->
            <aside class="bg-white rounded-2xl border border-slate-200 p-6 h-fit lg:sticky lg:top-24">
                <h2 class="font-bold text-lg mb-4">Ringkasan</h2>

                <form method="post" class="flex gap-2 mb-4">
                    <?= csrfField() ?>
                    <input type="hidden" name="aksi" value="voucher">
                    <input type="text" name="voucher" value="<?= e($r['kodeVoucher']) ?>" placeholder="Kode voucher"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase">
                    <button class="text-sm font-semibold px-3 rounded-lg bg-amber-400 hover:bg-amber-300">Pakai</button>
                </form>

                <div class="space-y-1 text-sm">
                    <p class="flex justify-between"><span>Total barang</span><span><?= $r["totalBarang"] ?> item</span></p>
                    <p class="flex justify-between"><span>Subtotal</span><span><?= formatRupiah($r["subtotal"]) ?></span></p>
                    <?php if ($r["diskonOtomatis"] > 0): ?>
                        <p class="flex justify-between text-red-600"><span>Diskon belanja</span><span>- <?= formatRupiah($r["diskonOtomatis"]) ?></span></p>
                    <?php endif; ?>
                    <?php if ($r["diskonVoucher"] > 0): ?>
                        <p class="flex justify-between text-red-600"><span>Voucher <?= e($r["kodeVoucher"]) ?></span><span>- <?= formatRupiah($r["diskonVoucher"]) ?></span></p>
                    <?php endif; ?>
                    <p class="flex justify-between"><span>Ongkir</span><span><?= $r["ongkir"] === 0 ? "Gratis" : formatRupiah($r["ongkir"]) ?></span></p>
                    <p class="flex justify-between text-xl font-extrabold border-t pt-3 mt-2"><span>Total bayar</span><span><?= formatRupiah($r["total"]) ?></span></p>
                </div>

                <?php if ($r["diskonOtomatis"] === 0): ?>
                    <p class="mt-3 text-xs text-slate-500">💡 Belanja min. <?= formatRupiah(3000000) ?> dapat diskon 5%, min. <?= formatRupiah(10000000) ?> diskon 7%.</p>
                <?php endif; ?>

                <a href="checkout.php" class="block text-center mt-5 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl">Lanjut ke checkout →</a>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
