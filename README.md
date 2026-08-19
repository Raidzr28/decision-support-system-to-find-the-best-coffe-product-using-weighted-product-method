# SPK Weighted Product — Rekomendasi Menu Kopi Favorit
### Cafe Marako Space · PHP Native + MySQL + AJAX

Sistem Pendukung Keputusan rekomendasi menu kopi menggunakan metode **Weighted Product (WP)**, diimplementasikan persis sesuai algoritma BAB III.

---

## 📦 Cara Instalasi (Laragon / XAMPP)

1. **Salin folder** `spk_kopi` ke direktori web server:
   - Laragon: `C:\laragon\www\spk_kopi`
   - XAMPP: `C:\xampp\htdocs\spk_kopi`

2. **Buat database**: buka phpMyAdmin (`http://localhost/phpmyadmin`) → tab **Import** → pilih file `database.sql` → **Go**.
   Database `spk_kopi_wp` akan dibuat otomatis lengkap dengan **100 data sample**.

3. **Cek konfigurasi** koneksi di `config/database.php` (default Laragon/XAMPP: user `root`, password kosong).

4. **Buka di browser**:
   - Laragon: `http://spk_kopi.test` atau `http://localhost/spk_kopi`
   - XAMPP: `http://localhost/spk_kopi`

5. **Login**:
   - Username: **admin**
   - Password: **admin123**

---

## 🗂️ Halaman Sistem

| Halaman | File | Fungsi |
|---|---|---|
| Autentikasi | `login.php` | Login/logout |
| Beranda | `beranda.php` | Dashboard statistik & rekomendasi terakhir |
| Input Kriteria | `kriteria.php` | Kelola bobot & atribut (AJAX) + referensi skala |
| Data Alternatif | `alternatif.php` | Input pembelian: menu, **nama pembeli**, **waktu pembelian**, 8 nilai kriteria (AJAX) |
| Hitung WP | `hitung.php` | Proses perhitungan bertahap + hasil ranking |
| Riwayat | `riwayat.php` | Histori perhitungan + detail |

Tombol **🖨️ Cetak** tersedia di setiap halaman.

---

## 🧮 Algoritma Weighted Product (sesuai BAB III)

**8 Kriteria:**
- C1 Cita Rasa *(Benefit)* · C2 Aroma *(Benefit)* · C3 Harga *(Cost)* · C4 Popularitas *(Benefit)*
- C5 Ketersediaan Bahan Baku *(Benefit)* · C6 Waktu Penyajian *(Cost)* · C7 Nilai Gizi *(Benefit)* · C8 Kepuasan Pelanggan *(Benefit)*

**Tahapan perhitungan:**
1. Normalisasi bobot: `Wj = wj / Σwj`
2. Vektor S: `Si = Π (Xij ^ Wj)` — kriteria **Cost** (C3, C6) memakai **pangkat negatif**, **Benefit** pangkat positif
3. Vektor V: `Vi = Si / ΣSi`
4. Perangkingan: nilai V tertinggi = menu kopi favorit

Logika ada di `includes/wp_function.php`.

---

## 🔧 Teknologi
PHP Native · MySQL (MySQLi) · JavaScript (vanilla) · AJAX (Fetch API) · HTML · CSS

Tema UI: **Caffeine & Cove** (beige / mocha / taupe).
