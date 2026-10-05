# Product Manager — Tugas Akhir Pemrograman Web Pertemuan 3

Aplikasi web PHP–MySQL untuk mengelola produk dengan konsep CRUD, validasi server-side, Post–Redirect–Get (PRG), PDO prepared statement, escaping output untuk mencegah XSS, CSRF pada delete, serta UI responsif menggunakan Box Model dan Flexbox.

## 1. Struktur proyek

```text
Product-Manager/
├── config/
│   ├── db.php
│   └── helpers.php
├── database/
│   └── store_db.sql
├── public/
│   ├── assets/
│   │   └── style.css
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── delete.php
└── README.md
```

## 2. Kebutuhan

- PHP 8.x atau lebih baru
- MySQL/MariaDB
- XAMPP/Laragon atau web server PHP lain
- Browser modern

## 3. Instalasi database

### Cara A — phpMyAdmin

1. Jalankan Apache dan MySQL dari XAMPP/Laragon.
2. Buka phpMyAdmin.
3. Pilih menu **Import**.
4. Pilih file `database/store_db.sql`.
5. Jalankan import sampai database `store_db` dan tabel `products` terbentuk.

### Cara B — MySQL CLI

Jalankan:

```bash
mysql -u root -p < database/store_db.sql
```

Jika password root kosong, tekan Enter ketika diminta password.

## 4. Konfigurasi koneksi

Secara default aplikasi menggunakan:

```text
Host     : localhost
Database : store_db
Username : root
Password : kosong
```

Pengaturan berada di `config/db.php`. Sesuaikan jika konfigurasi MySQL di komputer berbeda.

## 5. Menjalankan dengan XAMPP

Letakkan folder `Product-Manager` ke:

```text
C:\xampp\htdocs\Product-Manager
```

Jalankan Apache dan MySQL, lalu buka:

```text
http://localhost/Product-Manager/public/
```

## 6. Menjalankan dengan PHP built-in server

Dari folder proyek:

```bash
php -S localhost:8000 -t public
```

Kemudian buka:

```text
http://localhost:8000
```

Pastikan MySQL tetap aktif.

## 7. Fitur yang diterapkan

- **Create**: tambah nama, kategori, harga, dan stok.
- **Read**: daftar produk dalam card responsif.
- **Update**: edit produk berdasarkan ID.
- **Delete**: hapus produk memakai POST dan token CSRF.
- **Validasi server-side**: nama minimal 3 karakter, harga > 0, stok >= 0.
- **Nama unik**: database menggunakan `UNIQUE` pada nama produk.
- **PDO prepared statement**: semua operasi SQL yang menerima input user memakai parameter.
- **XSS protection**: output HTML di-escape menggunakan `htmlspecialchars`.
- **PRG**: create, update, dan delete melakukan redirect setelah berhasil agar refresh tidak mengulang submit.
- **Search bonus**: pencarian nama/kategori menggunakan GET dan prepared statement.
- **UI responsif**: Box Model dan Flexbox digunakan untuk layout card dan form.

## 8. Pengujian sebelum demo

### Create
1. Tambahkan produk valid.
2. Pastikan produk muncul di daftar.
3. Refresh halaman.
4. Pastikan produk tidak masuk dua kali.

### Validasi
1. Nama 1–2 karakter → ditolak.
2. Harga 0 atau negatif → ditolak.
3. Stok negatif → ditolak.
4. Nama sama dengan produk yang sudah ada → ditolak.

### XSS
Masukkan nama:

```html
<b>Promo</b>
```

Output harus tampil sebagai teks `<b>Promo</b>`, bukan menjadi tulisan tebal.

### Update
1. Klik **Edit**.
2. Ubah data.
3. Simpan.
4. Pastikan perubahan tampil di daftar.

### Delete + CSRF
1. Klik **Hapus**.
2. Konfirmasi.
3. Produk harus hilang.
4. Request tanpa token CSRF harus mendapat HTTP 403.

### Responsive UI
Persempit jendela browser. Card harus membungkus ke baris berikutnya dan form harus tetap nyaman digunakan.

## 9. Pemetaan terhadap tugas

| Kriteria | Implementasi |
|---|---|
| CRUD | `create.php`, `index.php`, `edit.php`, `delete.php` |
| Keamanan | PDO, escaping XSS, CSRF |
| Validasi + PRG | Validasi PHP + redirect setelah proses |
| UI responsif | `assets/style.css`, Flexbox, Box Model |
| Struktur + README | Folder terpisah untuk config, public, database |

## 10. Catatan pengumpulan

Nama arsip sesuai instruksi tugas dapat dibuat seperti:

```text
Praktikum3_NIM_Nama.zip
```

Ganti `NIM` dan `Nama` sesuai identitas mahasiswa sebelum dikumpulkan.
