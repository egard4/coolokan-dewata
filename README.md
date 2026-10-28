# Colokan Dewata - Toko Elektronik (PHP native + Tailwind CDN)

## Struktur folder
```
colokan-dewata/
├── includes/
│   ├── config.php        # session, zona waktu, nama toko, info kontak, aturan ongkir
│   ├── init.php          # require config + data + fungsi (1 baris di tiap halaman)
│   ├── data.php          # array kategori, produk, voucher, metode bayar
│   ├── fungsi.php        # format rupiah, diskon, cari produk, keranjang, CSRF, flash
│   ├── header.php        # navbar, pencarian, badge keranjang, pesan flash
│   ├── footer.php        # footer + script
│   └── kartu-produk.php  # komponen kartu produk (dipakai ulang)
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── index.php             # beranda
├── produk.php            # katalog + cari/filter/urutkan
├── detail.php            # detail produk
├── keranjang.php         # keranjang + voucher
├── checkout.php          # form pengiriman + validasi
├── pesanan.php           # nota pesanan terakhir
└── kontak.php            # kontak + form
```

## Menjalankan
Letakkan folder di `htdocs` (XAMPP) lalu buka `http://localhost/colokan-dewata/`
atau jalankan `php -S localhost:8000` di dalam folder. Butuh PHP 8.0+ dan internet (Tailwind CDN).

## Voucher untuk dicoba
`DEWATA10` (10%, maks Rp 300.000) dan `HEMAT50K` (Rp 50.000).
