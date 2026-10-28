<?php
// ============================================
// HALAMAN KONTAK
// ============================================
require_once __DIR__ . '/includes/init.php';

$judulHalaman = "Kontak";
$halamanAktif = "kontak.php";

$isi   = ["nama" => "", "email" => "", "pesan" => ""];
$galat = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrfCek()) {
        $galat["umum"] = "Sesi tidak valid, silakan coba lagi.";
    }
    foreach ($isi as $kunci => $nilai) {
        $isi[$kunci] = trim($_POST[$kunci] ?? "");
    }
    if (mb_strlen($isi["nama"]) < 3) {
        $galat["nama"] = "Nama minimal 3 karakter.";
    }
    if (!filter_var($isi["email"], FILTER_VALIDATE_EMAIL)) {
        $galat["email"] = "Format email tidak valid.";
    }
    if (mb_strlen($isi["pesan"]) < 10) {
        $galat["pesan"] = "Pesan minimal 10 karakter.";
    }

    if (empty($galat)) {
        // Sementara hanya simulasi. Nanti pesan bisa disimpan ke database / dikirim lewat email.
        setFlash("sukses", "Terima kasih, " . $isi["nama"] . "! Pesanmu sudah kami terima.");
        header("Location: kontak.php");
        exit;
    }
}

$kelasInput = "w-full rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400";
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-1">Hubungi Kami</h1>
    <p class="text-slate-500 mb-6">Ada pertanyaan soal produk atau pesanan? Kami siap membantu.</p>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            <?php foreach ([["📍", "Alamat", $infoToko["alamat"]], ["📞", "Telepon / WA", $infoToko["telepon"]], ["✉️", "Email", $infoToko["email"]], ["🕘", "Jam buka", $infoToko["jam"]]] as [$ikon, $judul, $isiInfo]): ?>
                <div class="flex gap-3">
                    <div class="text-2xl"><?= $ikon ?></div>
                    <div><p class="text-sm text-slate-500"><?= $judul ?></p><p class="font-semibold"><?= e($isiInfo) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4" novalidate>
            <?= csrfField() ?>
            <?php if (isset($galat["umum"])): ?><p class="rounded-xl bg-red-100 text-red-800 px-4 py-3 text-sm"><?= e($galat["umum"]) ?></p><?php endif; ?>
            <div>
                <label class="block text-sm font-semibold mb-1">Nama</label>
                <input type="text" name="nama" value="<?= e($isi["nama"]) ?>" class="<?= $kelasInput ?>">
                <?php if (isset($galat["nama"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["nama"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="<?= e($isi["email"]) ?>" class="<?= $kelasInput ?>">
                <?php if (isset($galat["email"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["email"]) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Pesan</label>
                <textarea name="pesan" rows="4" class="<?= $kelasInput ?>"><?= e($isi["pesan"]) ?></textarea>
                <?php if (isset($galat["pesan"])): ?><p class="text-red-600 text-xs mt-1"><?= e($galat["pesan"]) ?></p><?php endif; ?>
            </div>
            <button class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl">Kirim pesan</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
