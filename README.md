# FlipPlay REST API (`repoWeb`)

RESTful API untuk platform **FlipPlay** (katalog Anime/Manga & koleksi pribadi pengguna) yang dibangun menggunakan **Laravel** dan **MySQL** untuk **Demo Modul 4 Pemrograman Web**.

## Fitur Utama
- **CRUD Media Lengkap** (`/api/media`):
  - `GET /api/media` — Menampilkan daftar media dengan pagination & query filter pencarian (`?search=...`, `?type=...`)
  - `POST /api/media` — Menambahkan media baru dengan validasi `FormRequest` (`201 Created` / `422`)
  - `GET /api/media/{media}` — Detail media beserta daftar pengguna pengoleksinya (`whenLoaded`)
  - `PATCH /api/media/{media}` — Memperbarui sebagian/seluruh data media
  - `DELETE /api/media/{media}` — Menghapus data media
- **Relasi Many-to-Many (`belongsToMany`) & Custom Actions Koleksi Pengguna**:
  - `GET /api/users` & `GET /api/users/{user}` — Menampilkan daftar dan detail pengguna beserta koleksi media
  - `GET /api/users/{user}/collection` — Menampilkan daftar koleksi media milik pengguna
  - `POST /api/users/{user}/collection` — Menambahkan media ke koleksi (`attach`) dengan proteksi duplikat (`409 Conflict`)
  - `DELETE /api/users/{user}/collection/{media}` — Menghapus media dari koleksi (`detach`)

## Cara Menjalankan
1. Salin `.env.example` menjadi `.env` dan sesuaikan konfigurasi database MySQL (`DB_DATABASE=flipplay_api`).
2. Jalankan perintah berikut:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate:fresh --seed
   php artisan serve
   ```
3. Import file `flipplay.postman_collection.json` ke **Postman** untuk menguji skenario *Happy Path* dan *Failure Path*.
