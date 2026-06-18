# Product Requirement Document (PRD) - Dummy Ticketing System (Laravel + MySQL + DOKU)

## 1. Pendahuluan & Informasi Dokumen
* **Nama Produk:** MiniTick (Website Ticketing Sederhana)
* **Versi:** 1.2 (Laravel & MySQL Edition)
* **Status:** Ready for Development
* **Tujuan:** Panduan teknis pembuatan MVP website ticketing menggunakan **Laravel** sebagai backend/frontend framework, **MySQL** sebagai database, dan **DOKU API (Sandbox Mode)** sebagai gerbang pembayaran otomatis.

---

## 2. Sasaran Produk & Ruang Lingkup
* **Otomatisasi Pembayaran:** Mengeliminasi validasi manual dengan memanfaatkan fitur Webhook dari DOKU yang langsung memproses status transaksi di server Laravel.
* **Efisiensi Database:** Menggunakan MySQL dengan relasi data terstruktur demi menjaga konsistensi kuota tiket saat transaksi berhasil.
* **Sederhana:** Tidak memakai sistem registrasi user (pembelian langsung/guest checkout) untuk mempercepat proses *development*.

---

## 3. Alur Pengguna & Sistem (User & System Flow)
1. **User** memilih event di halaman utama (Blade View).
2. **User** mengisi form data diri dan klik "Bayar Sekarang".
3. **Laravel Controller** menangkap request, lalu:
   * Menggenerasi *Signature* keamanan sesuai standar DOKU.
   * Melakukan HTTP Request (`Http::withHeaders()->post()`) ke API DOKU Checkout.
   * Menyimpan data pemesanan awal ke database MySQL dengan status `PENDING`.
4. **User** dialihkan (*redirect*) ke halaman DOKU Checkout untuk membayar.
5. Setelah bayar, **DOKU** mengirimkan HTTP POST (*Notification/Webhook*) ke endpoint Laravel `/api/doku/webhook`.
6. **Laravel Webhook Controller** memvalidasi *signature* kiriman DOKU. Jika valid dan sukses, sistem mengubah status booking menjadi `SUCCESS` dan mengurangi `quota` pada tabel `events`.

---

## 4. Spesifikasi Database (MySQL Schema - Laravel Migration)

Aplikasi ini hanya membutuhkan dua tabel utama yang saling berelasi:

### 4.1. Tabel `events` (Migration: `create_events_table`)
| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInteger | PK, Auto Increment | ID unik acara |
| `title` | String | - | Judul/Nama acara |
| `image_url` | String | Nullable | Path/URL poster acara |
| `date_time` | DateTime | - | Waktu pelaksanaan acara |
| `location` | String | - | Lokasi/Venue |
| `price` | UnsignedInteger | - | Harga tiket per lembar |
| `quota` | UnsignedInteger | - | Sisa kuota tiket yang tersedia |
| `timestamps` | Timestamp | - | `created_at` & `updated_at` |

### 4.2. Tabel `bookings` (Migration: `create_bookings_table`)
| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInteger | PK, Auto Increment | ID unik internal aplikasi |
| `invoice_number`| String | Unique | Nomor invoice (contoh: `INV-YmdHis`) |
| `doku_trans_id` | String | Nullable | ID transaksi resmi dari pihak DOKU |
| `event_id` | BigInteger | FK (References `id` on `events`) | Relasi ke acara yang dibeli |
| `customer_name` | String | - | Nama lengkap pembeli |
| `customer_email`| String | - | Alamat email pembeli |
| `customer_phone`| String | - | Nomor WhatsApp pembeli |
| `ticket_qty` | UnsignedInteger | - | Jumlah tiket yang dibeli |
| `total_price` | UnsignedInteger | - | Hasil perkalian `price` $\times$ `ticket_qty` |
| `status` | Enum | `['PENDING', 'SUCCESS', 'FAILED']` | Status pembayaran (Default: `PENDING`) |
| `timestamps` | Timestamp | - | `created_at` & `updated_at` |

---

## 5. Implementasi Teknis & Routing (Laravel)

### 5.1. Route Configuration (`routes/web.php` & `routes/api.php`)
* `GET /` : Menampilkan katalog event (mengambil data dari model `Event`).
* `POST /checkout` : Memproses form pembelian, hit API DOKU, dan redirect ke DOKU.
* `GET /checkout/success` : Halaman *landing* setelah user selesai membayar di portal DOKU.
* `POST /api/doku/webhook` : Endpoint khusus (API) untuk menerima notifikasi asinkronus dari DOKU *(Catatan: Route ini wajib dikecualikan dari proteksi CSRF di `bootstrap/app.php` atau `VerifyCsrfToken` middleware)*.

### 5.2. Kebutuhan Konfigurasi `.env`
Masukkan kredensial Sandbox dari dashboard DOKU ke file `.env` Laravel Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_dummy
DB_USERNAME=root
DB_PASSWORD=

DOKU_CLIENT_ID=Ganti_Dengan_Client_Id_Sandbox_Anda
DOKU_SECRET_KEY=Ganti_Dengan_Secret_Key_Sandbox_Anda
DOKU_API_URL=[https://api-sandbox.doku.com](https://api-sandbox.doku.com)
