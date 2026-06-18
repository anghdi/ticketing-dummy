# Product Requirement Document (PRD) - Dummy Ticketing System (Laravel + MySQL + Midtrans Sandbox CC)

## 1. Pendahuluan & Informasi Dokumen
* **Nama Produk:** MiniTick (Website Tiketing Sederhana)
* **Versi:** 1.6 (Midtrans Credit Card Sandbox Edition)
* **Status:** Ready for Development
* **Tujuan:** Panduan teknis pembuatan prototype website ticketing menggunakan **Laravel**, **MySQL**, dan **Midtrans Snap API (Sandbox Mode)** khusus untuk mensimulasikan alur pembayaran Kartu Kredit internasional (Visa/MasterCard).

---

## 2. Sasaran Produk & Ruang Lingkup (Scope)
* **Simulasi Pembayaran Global:** Menggunakan fitur Kartu Kredit pada Midtrans Snap untuk menerima pembayaran simulasi dengan logo Visa/MasterCard.
* **Otomatisasi Status:** Memanfaatkan *HTTP Notification (Webhook)* dari Midtrans Sandbox ke Laravel untuk mengubah status pesanan secara otomatis di database MySQL setelah otentikasi kartu berhasil.
* **Pembatasan MVP:** Sistem bersifat *Guest Checkout* (tanpa login akun) dan *Free Seating* (tidak ada pemilihan nomor kursi).

---

## 3. Alur Pengujian Pembayaran Kartu di Sandbox
1. **User** mengisi form data diri (Nama, Email, No HP, Jumlah Tiket) di web lokal Laravel Anda.
2. **Laravel Backend** membuat data pesanan berstatus `PENDING` di MySQL, lalu meminta `snap_token` ke API Midtrans Sandbox.
3. **Frontend** memunculkan pop-up Midtrans Snap. User memilih opsi pembayaran **"Credit Card / Kartu Kredit"**.
4. **User** memasukkan **Nomor Kartu Tes Midtrans** (misal kartu sukses: `4811 1111 1111 1111`) beserta CVV dan Expiry Date dummy.
5. Pop-up Midtrans akan menampilkan simulasi halaman **3D Secure (OTP)**. User cukup klik tombol **"OK / Authorize"**.
6. Server Midtrans Sandbox mengirim notifikasi webhook ke Laravel (memerlukan bantuan Ngrok).
7. **Laravel** memproses notifikasi tersebut, jika sukses maka status di database diubah menjadi `SUCCESS` dan kuota event dikurangi.

---

## 4. Spesifikasi Database (MySQL Schema)

### 4.1. Tabel `events` (Migration: `create_events_table`)
| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInteger | PK, Auto Increment | ID unik acara |
| `title` | String | - | Judul/Nama acara |
| `price` | UnsignedInteger | - | Harga tiket per lembar (dalam IDR) |
| `quota` | UnsignedInteger | - | Sisa kuota tiket yang tersedia |
| `timestamps` | Timestamp | - | `created_at` & `updated_at` |

### 4.2. Tabel `bookings` (Migration: `create_bookings_table`)
| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInteger | PK, Auto Increment | ID unik internal |
| `order_id` | String | Unique | ID Pesanan unik (misal: `ORDER-YmdHis`) |
| `snap_token` | String | Nullable | Token dari Midtrans untuk memicu Pop-up |
| `event_id` | BigInteger | FK (References `id` on `events`) | Relasi ke tabel `events` |
| `customer_name` | String | - | Nama lengkap pembeli |
| `customer_email`| String | - | Alamat email pembeli |
| `customer_phone`| String | - | Nomor WhatsApp pembeli |
| `ticket_qty` | UnsignedInteger | - | Jumlah tiket yang dibeli |
| `total_price` | UnsignedInteger | - | Hasil perkalian `price` $\times$ `ticket_qty` |
| `status` | Enum | `['PENDING', 'SUCCESS', 'FAILED', 'EXPIRED']` | Status Pembayaran |
| `timestamps` | Timestamp | - | `created_at` & `updated_at` |

---

## 5. Konfigurasi Lingkungan Kerja (.env)

Buka dashboard Midtrans Anda, pastikan posisinya di **Sandbox Mode** (bukan Production), lalu ambil *Access Keys* di menu *Settings*. Masukkan ke `.env` Laravel:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_dummy
DB_USERNAME=root
DB_PASSWORD=

# Kredensial Midtrans Sandbox Anda
MIDTRANS_SERVER_KEY=SB-Mid-server-ZlhGx2xjOIwJei3kbeZYnCbN
MIDTRANS_CLIENT_KEY=SB-Mid-client-Wp-yeWnfa13pzTiB
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
