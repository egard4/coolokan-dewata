<?php
// ============================================
// HALAMAN NOTA PESANAN (pesanan terakhir)
// ============================================
require_once __DIR__ . '/includes/init.php';

$judulHalaman = "Pesanan";
$halamanAktif = "pesanan.php";

$nota = $_SESSION["pesanan_terakhir"] ?? null;

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-6">Nota Pesanan</h1>

    <?php if (!$nota): ?>
        <div class="text-center bg-white rounded-2xl border border-dashed border-slate-300 py-16">
            <div class="text-6xl mb-3">🧾</div>
            <p class="font-semibold mb-1">Belum ada pesanan</p>
            <a href="produk.php" class="inline-block mt-3 bg-amber-400 hover:bg-amber-300 font-bold px-6 py-2 rounded-xl">Belanja dulu yuk</a>
        </div>
    <?php else: ?>
        <?php $r = $nota["ringkasan"]; $p = $nota["pembeli"]; ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 max-w-3xl">
            <div class="flex flex-wrap justify-between gap-2 mb-4">
                <div>
                    <p class="text-sm text-slate-500">Nomor pesanan</p>
                    <p class="font-extrabold text-lg"><?= e($nota["nomor"]) ?></p>
                </div>
                <div class="text-sm text-slate-500 sm:text-right">
                    <p><?= e($nota["waktu"]) ?></p>
                    <p>Pembayaran: <b><?= e($metodeBayar[$p["pembayaran"]] ?? "-") ?></b></p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 text-sm mb-4">
                <p class="font-semibold"><?= e($p["nama"]) ?> &middot; <?= e($p["telepon"]) ?></p>
                <p class="text-slate-600"><?= e($p["alamat"]) ?>, <?= e($p["kota"]) ?></p>
                <?php if ($p["catatan"] !== ""): ?><p class="text-slate-500 mt-1">Catatan: <?= e($p["catatan"]) ?></p><?php endif; ?>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b text-slate-500">
                            <th class="py-2">No</th><th>Produk</th>
                            <th class="text-right">Harga</th><th class="text-center">Jumlah</th><th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($r["baris"] as $b): ?>
                            <tr class="border-b">
                                <td class="py-2"><?= $no++ ?></td>
                                <td><?= $b["item"]["ikon"] ?> <?= e($b["item"]["nama"]) ?></td>
                                <td class="text-right"><?= formatRupiah($b["harga"]) ?></td>
                                <td class="text-center"><?= $b["jumlah"] ?></td>
                                <td class="text-right"><?= formatRupiah($b["subtotal"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 space-y-1 text-sm">
                <p class="flex justify-between"><span>Total barang</span><span><?= $r["totalBarang"] ?> item</span></p>
                <p class="flex justify-between"><span>Subtotal</span><span><?= formatRupiah($r["subtotal"]) ?></span></p>
                <p class="flex justify-between text-red-600"><span>Diskon belanja</span><span>- <?= formatRupiah($r["diskonOtomatis"]) ?></span></p>
                <?php if ($r["diskonVoucher"] > 0): ?>
                    <p class="flex justify-between text-red-600"><span>Voucher <?= e($r["kodeVoucher"]) ?></span><span>- <?= formatRupiah($r["diskonVoucher"]) ?></span></p>
                <?php endif; ?>
                <p class="flex justify-between"><span>Ongkir</span><span><?= $r["ongkir"] === 0 ? "Gratis" : formatRupiah($r["ongkir"]) ?></span></p>
                <p class="flex justify-between text-xl font-extrabold border-t pt-2"><span>Total bayar</span><span><?= formatRupiah($r["total"]) ?></span></p>
            </div>

            <button onclick="window.print()" class="mt-6 text-sm font-semibold px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">🖨️ Cetak nota</button>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
