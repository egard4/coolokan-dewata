<?php
// ============================================
// HALAMAN CHECKOUT (form data pengiriman + validasi)
// ============================================
require_once __DIR__ . '/includes/init.php';

$r = ringkasanKeranjang($produk, $voucher);
if (empty($r["baris"])) {
    setFlash("error", "Keranjang masih kosong.");
    header("Location: produk.php");
    exit;
}

$isi   = ["nama" => "", "telepon" => "", "alamat" => "", "kota" => "Denpasar", "pembayaran" => "transfer", "catatan" => ""];
$galat = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrfCek()) {
        $galat["umum"] = "Sesi tidak valid, silakan coba lagi.";
    }
    foreach ($isi as $kunci => $nilai) {
        $isi[$kunci] = trim($_POST[$kunci] ?? $nilai);
    }

    // Validasi
    if (mb_strlen($isi["nama"]) < 3) {
        $galat["nama"] = "Nama minimal 3 karakter.";
    }
    $telepon = preg_replace('/[\s\-]/', '', $isi["telepon"]);
    if (!preg_match('/^(\+62|62|0)8\d{8,12}$/', $telepon)) {
        $galat["telepon"] = "Nomor HP tidak valid (contoh: 081234567890).";
    }
    if (mb_strlen($isi["alamat"]) < 10) {
        $galat["alamat"] = "Alamat minimal 10 karakter.";
    }
    if (!isset($metodeBayar[$isi["pembayaran"]])) {
        $galat["pembayaran"] = "Pilih metode pembayaran.";
    }

    // Jika lolos validasi: simpan pesanan ke session, kosongkan keranjang
    if (empty($galat)) {
        $_SESSION["pesanan_terakhir"] = [
            "nomor"     => "CD-" . date("ymd") . "-" . strtoupper(bin2hex(random_bytes(3))),
            "waktu"     => date("d/m/Y H:i") . " WITA",
            "pembeli"   => $isi,
            "ringkasan" => $r,
        ];
        unset($_SESSION["keranjang"], $_SESSION["voucher"]);
        setFlash("sukses", "Pesanan berhasil dibuat. Terima kasih sudah berbelanja!");
        header("Location: pesanan.php");
        exit;
    }
}

$judulHalaman = "Checkout";
$halamanAktif = "keranjang.php";
$kelasInput = "w-full rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400";

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-6">Checkout</h1>

    <?php if (isset($galat["umum"])): ?>
        <p class="mb-4 rounded-xl bg-red-100 text-red-800 px-4 py-3 text-sm"><?= e($galat["umum"]) ?></p>
    <?php endif; ?>

    <div class="grid lg:grid-cols-3 gap-6">
        <form method="post" class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 space-y-4" novalidate>
            <?= csrfField() ?>

            <div>
                <label class="block text-sm font-semibold mb-1">Nama lengkap</label>
                <input type="text" name="nama" value="<?= e($isi["nama"]) ?>" class="<?= $kelasInput ?>">
                <?php if (isset($galat["nama"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["nama"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Nomor HP / WhatsApp</label>
                <input type="text" name="telepon" value="<?= e($isi["telepon"]) ?>" class="<?= $kelasInput ?>">
                <?php if (isset($galat["telepon"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["telepon"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Alamat lengkap</label>
                <textarea name="alamat" rows="3" class="<?= $kelasInput ?>"><?= e($isi["alamat"]) ?></textarea>
                <?php if (isset($galat["alamat"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["alamat"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Kota / Kabupaten</label>
                <select name="kota" class="<?= $kelasInput ?>">
                    <?php foreach (["Denpasar", "Badung", "Gianyar", "Tabanan", "Buleleng", "Klungkung", "Bangli", "Karangasem", "Jembrana"] as $k): ?>
                        <option <?= $k === $isi["kota"] ? "selected" : "" ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Metode pembayaran</label>
                <div class="grid sm:grid-cols-3 gap-2">
                    <?php foreach ($metodeBayar as $kunci => $nama): ?>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="pembayaran" value="<?= $kunci ?>" <?= $kunci === $isi["pembayaran"] ? "checked" : "" ?>>
                            <?= e($nama) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($galat["pembayaran"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["pembayaran"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" class="<?= $kelasInput ?>"><?= e($isi["catatan"]) ?></textarea>
            </div>

            <button class="w-full bg-amber-400 hover:bg-amber-300 font-bold py-3 rounded-xl">Buat pesanan</button>
        </form>

        <aside class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
            <h2 class="font-bold text-lg mb-3">Pesananmu</h2>
            <ul class="text-sm space-y-2 mb-4">
                <?php foreach ($r["baris"] as $b): ?>
                    <li class="flex justify-between gap-2"><span><?= $b["item"]["ikon"] ?> <?= e($b["item"]["nama"]) ?> &times; <?= $b["jumlah"] ?></span><span><?= formatRupiah($b["subtotal"]) ?></span></li>
                <?php endforeach; ?>
            </ul>
            <p class="flex justify-between text-sm"><span>Ongkir</span><span><?= $r["ongkir"] === 0 ? "Gratis" : formatRupiah($r["ongkir"]) ?></span></p>
            <p class="flex justify-between text-xl font-extrabold border-t pt-3 mt-3"><span>Total</span><span><?= formatRupiah($r["total"]) ?></span></p>
        </aside>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
