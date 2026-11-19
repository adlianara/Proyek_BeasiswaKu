# BeasiswaKu

Portal informasi beasiswa untuk mahasiswa dengan pencarian publik, halaman detail program, dan panel pengelola terlindungi. Dibangun dengan Laravel 12, PHP 8.2+, Blade, dan MySQL/MariaDB. Tampilan publik dan admin mengikuti acuan Figma yang tersedia di `Unduhan/Gambar/RPL`.

## Tim pengembang

| Nama | NIM |
| --- | --- |
| Neisya Fadilla Khairunnisa | H1101251007 |
| Syarifah Putri | H1101251013 |
| Shinta Ayuwidati | H1101251019 |
| Adli Anara Sofian | H1101251051 |

## Menjalankan dengan XAMPP

1. Pasang PHP 8.2 atau lebih baru, Composer 2, dan XAMPP. Nyalakan **MySQL** dari XAMPP.
2. Buat database bernama `beasiswaku` di phpMyAdmin (`http://localhost/phpmyadmin`).
3. Dari folder proyek, jalankan `composer install`, lalu salin `.env.example` menjadi `.env`.
4. Atur `DB_CONNECTION=mysql`, `DB_DATABASE=beasiswaku`, `DB_USERNAME=root`, dan `DB_PASSWORD=` sesuai konfigurasi MySQL XAMPP.
5. Jalankan:

   ```sh
   php artisan key:generate
   php artisan migrate --seed
   php artisan beasiswaku:admin admin@beasiswaku.test --name="Admin BeasiswaKu"
   php artisan serve
   ```

   Masukkan kata sandi admin (minimal 12 karakter) saat diminta. Jangan gunakan kata sandi contoh untuk data sungguhan.

6. Buka `http://127.0.0.1:8000`. Akses pengelola hanya melalui `http://127.0.0.1:8000/pengelola/masuk`; alamat ini sengaja tidak ditautkan di halaman pengguna. Akses panel tetap memerlukan autentikasi dan peran administrator.

Jika memakai XAMPP bawaan macOS, gunakan `php` dan `composer` yang sesuai instalasi XAMPP atau tambahkan folder PHP XAMPP ke PATH. PHP harus memiliki ekstensi PDO MySQL.

## Fitur yang bisa diuji

- Beranda, pencarian nama/penyelenggara, filter jenjang dan tenggat, urutan hasil, serta detail beasiswa.
- Admin terpisah, pembatasan berdasarkan peran, pembatasan percobaan login, logout, dan CRUD beasiswa dengan validasi.
- Seeder menambahkan enam data contoh untuk simulasi. Ganti dengan informasi yang sudah diverifikasi sebelum digunakan sungguhan.
- Tes otomatis untuk halaman publik, filter, beasiswa nonaktif, penjagaan halaman admin, login, dan tambah data:

  ```sh
  php artisan test
  ```

## Basis data

Skema dibentuk dari migration Laravel (`database/migrations`). Tabel utama: `users` (termasuk penanda administrator) dan `scholarships` (nama, penyelenggara, jenjang, tenggat, ringkasan, deskripsi, persyaratan, manfaat, tautan, dan status). Jalankan `php artisan migrate` saat skema berubah. `php artisan migrate:fresh --seed` akan menghapus seluruh data lebih dulu; gunakan hanya pada database pengujian atau lokal yang memang boleh direset.

## Deploy ke Vercel (persiapan)

Proyek menyertakan `Dockerfile.vercel`, `Caddyfile`, dan `vercel.json` untuk menjalankan Laravel melalui FrankenPHP di Vercel. Siapkan repository Git, import ke Vercel, lalu isi environment variables `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, dan `SESSION_SECURE_COOKIE=true`. Gunakan database MySQL terkelola yang dapat diakses dari Vercel, bukan database MySQL XAMPP di laptop.

Jalankan migration sebagai langkah rilis satu kali (`php artisan migrate --force`), bukan setiap container mulai. File sistem container Vercel bersifat sementara; aplikasi ini belum mengunggah berkas pengguna, dan data penting disimpan di MySQL. Untuk deploy nyata masih perlu akun Vercel, database terkelola, APP_KEY produksi, pengaturan domain/HTTPS, dan menjalankan pemeriksaan build container di lingkungan dengan Docker.

## Catatan keamanan

- Jangan commit `.env`, kata sandi, kunci aplikasi, atau dump database berisi data pribadi.
- Gunakan HTTPS dan kata sandi admin yang unik serta kuat.
- Data contoh hanya untuk uji coba, bukan informasi beasiswa resmi.
- Akses admin yang tidak ditautkan bukan pengganti kontrol keamanan; setiap route panel tetap dilindungi middleware autentikasi dan peran admin.
