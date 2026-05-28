# Sistem-Klinik-Kecantikan-Book-Buy
# Sistem-Klinik-Kecantikan-Book-Buy

# Rangkuman Desain Sistem Website Klinik
> Dokumen ini merangkum percakapan desain arsitektur dan database untuk pengembangan website klinik. Gunakan sebagai konteks saat melanjutkan sesi baru.

---

## 1. Arsitektur Sistem

**Pola:** Hybrid Microservice Architecture

### Services & Database (1 service = 1 DB, tidak ada shared DB)

| Service | Tanggung Jawab | Database |
|---|---|---|
| Core Service | Auth, Booking, Medical/Consultation, Record | Core DB |
| Product Service | Manajemen produk, SKU, stok | Product DB |
| Order & Payment Service | Order, integrasi Midtrans, webhook handler | Order DB |
| Reporting Service | Agregasi data, dashboard analytics | Report DB |

### API Gateway
- Single entry point untuk semua request frontend
- Routing ke masing-masing service
- Validasi auth (tidak dilakukan di setiap service secara langsung)

### Aturan Penting Arsitektur
- **Tidak boleh** ada cross-service DB query langsung
- Komunikasi antar service hanya via **API call** atau **event/message**
- Data dari service lain disimpan sebagai **snapshot** (denormalized copy), bukan FK hidup
- Snapshot digunakan terutama di Order Service dan Reporting Service

---

## 2. Data Flow

| Flow | Jalur |
|---|---|
| Booking | Frontend → Gateway → Core Service → Core DB |
| Konsultasi | Core Service → Medical → simpan Medical Record |
| Order | Core Service → Order Service → Payment |
| Payment | Order Service → Midtrans → Webhook → Update Order status |
| Reporting | Semua service → kirim snapshot → Reporting Service (agregasi) |

---

## 3. Skema Database per Service

### Core Service DB

**Entitas & Relasi:**
- `USERS` — identitas login (email, password hash, role)
- `PATIENTS` — profil medis pasien (FK user_id, UNIQUE → relasi 1:1 dengan USERS)
- `DOCTORS` — profil profesional dokter (FK user_id, UNIQUE → relasi 1:1 dengan USERS; FK specialization_id)
- `SPECIALIZATIONS` — daftar spesialisasi dokter
- `SERVICE_CATEGORIES` — kategori layanan klinik
- `SERVICES` — daftar layanan (FK category_id)
- `BOOKINGS` — junction table: menghubungkan PATIENTS × DOCTORS × SERVICES (relasi M:N:N)
- `MEDICAL_RECORDS` — rekam medis hasil konsultasi (FK booking_id UNIQUE, patient_id, doctor_id → relasi 1:1 dengan BOOKINGS)

### Product Service DB

- `PRODUCT_CATEGORIES` — kategori produk/obat
- `PRODUCTS` — katalog produk (FK category_id, menyimpan stock_qty)
- `STOCK_LOGS` — riwayat perubahan stok (FK product_id)

### Order & Payment DB

> **Catatan penting:** Tidak ada FK ke Core DB atau Product DB. Data dari service lain disimpan sebagai kolom snapshot (string, bukan FK relasional).

- `ORDERS` — menyimpan `patient_id_snapshot`, `patient_name_snapshot`, `booking_id_snapshot` (bukan FK hidup)
- `ORDER_ITEMS` — junction table ORDERS × PRODUCTS; menyimpan `product_id_snapshot`, `product_name_snapshot`, `unit_price_snapshot`
- `PAYMENTS` — data transaksi Midtrans (FK order_id, relasi 1:1 dengan ORDERS)

### Reporting DB

> Semua tabel adalah agregat read-only, tidak ada FK ke DB manapun.

- `BOOKING_STATS` — statistik booking per periode
- `REVENUE_STATS` — statistik pendapatan
- `PRODUCT_STATS` — statistik penjualan produk
- `DOCTOR_PERFORMANCE` — statistik kinerja dokter

---

## 4. Jenis Relasi yang Digunakan

### One-to-One (1:1)
Implementasi: FK + constraint `UNIQUE`

| Relasi | Keterangan |
|---|---|
| USERS ↔ PATIENTS | Satu akun = satu profil pasien |
| USERS ↔ DOCTORS | Satu akun = satu profil dokter |
| BOOKINGS ↔ MEDICAL_RECORDS | Satu sesi konsultasi = satu rekam medis |
| ORDERS ↔ PAYMENTS | Satu order = satu transaksi bayar |

### One-to-Many (1:N)
Implementasi: FK biasa (tanpa UNIQUE)

| Relasi | Keterangan |
|---|---|
| PATIENTS → BOOKINGS | Satu pasien bisa booking berkali-kali |
| DOCTORS → BOOKINGS | Satu dokter menangani banyak booking |
| SERVICES → BOOKINGS | Satu layanan bisa dipilih di banyak booking |
| MEDICAL_RECORDS → PRESCRIPTIONS | Satu rekam medis bisa punya banyak item resep |
| ORDERS → ORDER_ITEMS | Satu order berisi banyak item |
| PRODUCT_CATEGORIES → PRODUCTS | Satu kategori punya banyak produk |
| PRODUCTS → STOCK_LOGS | Satu produk punya banyak riwayat stok |

### Many-to-Many (M:N)
Implementasi: junction table dengan dua FK

| Relasi | Junction Table | Atribut tambahan di junction |
|---|---|---|
| ORDERS ↔ PRODUCTS | ORDER_ITEMS | qty, unit_price_snapshot |
| MEDICAL_RECORDS ↔ PRODUCTS | PRESCRIPTIONS | qty, dosage_instructions |
| PATIENTS ↔ DOCTORS ↔ SERVICES | BOOKINGS (ternary) | booked_at, status, notes |

---

## 5. Keputusan Desain Penting

### Mengapa `doctor_id` dipisah dari `user_id`?

Pola yang dipakai: **User + Profile Pattern**

- `USERS` = tabel **identitas & autentikasi** (siapa yang login)
- `DOCTORS` = tabel **profil profesional** (domain bisnis klinik)
- `PATIENTS` = tabel **profil medis** (domain bisnis klinik)

**Alasan pemisahan:**
1. **Normalisasi** — menghindari sparse table (kolom yang selalu NULL untuk sebagian baris)
2. **Skalabilitas peran** — satu orang bisa jadi dokter sekaligus pasien di klinik yang sama (satu `user_id`, dua baris di tabel berbeda)
3. **Single Responsibility** — Auth Service hanya query ke `USERS`, tidak perlu join ke data medis
4. **Keamanan** — permission akses bisa diatur per tabel secara granular
5. **Performa** — query domain bisnis tidak perlu scan tabel `USERS` yang besar

### Mengapa Order Service menggunakan snapshot, bukan FK?

Karena Order Service dan Core/Product Service punya DB terpisah (prinsip microservice). Snapshot menjamin:
- Data order tidak berubah meski profil pasien atau harga produk di-update belakangan
- Konsisten dengan prinsip bisnis (struk/invoice harus mencatat harga saat transaksi, bukan harga hari ini)

---

## 6. Hal yang Belum Dibahas / Potensi Lanjutan

- [ ] Implementasi tabel `PRESCRIPTIONS` di Core DB (jika resep bagian dari rekam medis) vs di Order DB (jika resep langsung jadi order)
- [ ] Struktur `schedule_json` di tabel `DOCTORS` — apakah perlu tabel tersendiri (`DOCTOR_SCHEDULES`)?
- [ ] Notifikasi sistem (booking reminder, konfirmasi pembayaran) — apakah perlu Notification Service?
- [ ] Refresh token & session management di Auth
- [ ] Rate limiting & middleware di API Gateway
- [ ] Strategi migrasi data jika ada perubahan schema di salah satu service
- [ ] Tech stack konkret (Next.js? Laravel? Node.js? PostgreSQL? Redis?)
- [ ] Implementasi webhook Midtrans secara detail
- [ ] Strategi event-driven (apakah pakai message queue seperti RabbitMQ atau Kafka?)

---

*Dibuat dari sesi percakapan desain sistem klinik — siap dilanjutkan kapan saja.*
