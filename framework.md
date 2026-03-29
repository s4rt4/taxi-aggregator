**KERANGKA KERJA PROYEK**

**Taxi Aggregator Platform**

*(Minicabit-Style --- UK Market)*

**1. Ringkasan Eksekutif**

Dokumen ini adalah kerangka kerja komprehensif untuk membangun platform
agregator taksi berbasis web yang menargetkan pasar UK. Platform akan
menghubungkan penumpang dengan ratusan operator taksi independen,
memberikan perbandingan harga real-time, dan memfasilitasi pemesanan
online.

*Referensi kompetitor utama: Minicabit, Cabcompare, Kabbee --- semuanya
beroperasi di UK.*

  -----------------------------------------------------------------------
  **Parameter**               **Detail**
  --------------------------- -------------------------------------------
  Tech Stack                  PHP (Laravel) + MySQL + Bootstrap 5 +
                              jQuery / Alpine.js

  Real-time Notification      Laravel Reverb (WebSocket) + Email + SMS
                              (Vonage/Twilio)

  Payment Gateway             Stripe (wajib untuk UK market)

  Maps & Routing              Google Maps Platform API

  Target Market               United Kingdom

  Estimasi MVP                3 - 4 bulan development

  Compliance                  GDPR, PCI-DSS (via Stripe), UK Transport
                              Licensing
  -----------------------------------------------------------------------

**2. Arsitektur Sistem**

**2.1 Komponen Utama**

Platform terdiri dari empat layer utama:

-   Frontend Layer --- Bootstrap 5 + jQuery/Alpine.js (responsive,
    mobile-first)

-   Backend Layer --- Laravel 11 sebagai core framework

-   Database Layer --- MySQL sebagai primary DB, Redis untuk caching &
    queue

-   External Services --- Google Maps, Stripe, Vonage/Twilio, Reverb
    WebSocket

**2.2 Stack Teknologi Detail**

  ------------------------------------------------------------------------
  **Layer**         **Teknologi**           **Fungsi**
  ----------------- ----------------------- ------------------------------
  Backend           Laravel 11              Framework utama, routing, ORM,
                                            auth

  Frontend          Bootstrap 5 + Alpine.js UI responsive tanpa overhead
                                            React/Vue

  Database          MySQL 8                 Data utama: user, booking,
                                            operator

  Cache/Queue       Redis                   Session, job queue, real-time
                                            cache

  WebSocket         Laravel Reverb          Notifikasi real-time ke
                                            browser

  Email             Laravel Mail + Mailgun  Konfirmasi booking, invoice

  SMS               Vonage / Twilio         OTP, status booking ke
                                            penumpang

  Maps              Google Maps Platform    Autocomplete, distance matrix,
                                            routing

  Payment           Stripe                  Pembayaran kartu, refund,
                                            escrow

  Storage           Laravel Storage + S3    Dokumen operator, foto profil
                                            driver

  Server            Nginx + PHP-FPM         Web server production
  ------------------------------------------------------------------------

**3. User Roles & Permissions**

Platform memiliki empat jenis pengguna dengan hak akses berbeda:

  ------------------------------------------------------------------------
  **Role**          **Deskripsi**          **Akses Utama**
  ----------------- ---------------------- -------------------------------
  Passenger         Penumpang / customer   Search, book, pay, review,
                                           track

  Operator          Perusahaan / individu  Manage fleet, terima/tolak job,
                    taksi                  invoice

  Driver            Pengemudi (opsional    Lihat job, update status
                    MVP)                   perjalanan

  Admin             Tim platform           Full access, approval operator,
                                           monitoring
  ------------------------------------------------------------------------

**4. Fitur MVP (Phase 1)**

MVP difokuskan pada core booking flow yang fungsional dan dapat
dimonetisasi.

**4.1 Modul Passenger**

-   Search & Quote --- input pickup, destination, date/time, jumlah
    penumpang

```{=html}
<!-- -->
```
-   Autocomplete Google Maps untuk alamat

-   Kalkulasi estimasi jarak & durasi otomatis

```{=html}
<!-- -->
```
-   Comparison Page --- tampilkan quotes dari multiple operator

```{=html}
<!-- -->
```
-   Sort by price, rating, vehicle type

-   Filter: vehicle class, max passengers, amenities

```{=html}
<!-- -->
```
-   Booking Flow --- pilih operator, isi detail, konfirmasi

-   Payment --- Stripe Checkout (kartu kredit/debit UK)

-   Booking Management --- lihat, cancel, download invoice PDF

-   Review & Rating --- setelah perjalanan selesai

**4.2 Modul Operator**

-   Onboarding & Verification --- daftar, upload dokumen lisensi,
    approval admin

-   Fleet Management --- tambah/edit kendaraan, kapasitas, harga per
    mile/km

-   Job Management --- terima/tolak booking, update status perjalanan

-   Pricing Rules --- base fare, per mile rate, surge pricing sederhana

-   Dashboard --- statistik booking, revenue, rating

-   Payout --- withdrawal earnings via Stripe Connect

**4.3 Modul Admin**

-   Operator Approval --- verifikasi dokumen, approve/reject

-   Booking Monitoring --- overview semua transaksi

-   Dispute Management --- handle komplain passenger vs operator

-   Commission Settings --- persentase platform fee per booking

-   Analytics Dashboard --- revenue, booking volume, top operators

**5. Sistem Notifikasi Real-Time**

Ini adalah komponen kritis platform. Setiap perubahan status booking
harus dikomunikasikan secara instan ke semua pihak terkait.

**5.1 Arsitektur Notifikasi**

  -----------------------------------------------------------------------
  **Event**               **Channel**         **Penerima**
  ----------------------- ------------------- ---------------------------
  Booking baru masuk      WebSocket + Email   Operator

  Operator accept/decline WebSocket + SMS     Passenger

  Driver en route         WebSocket + SMS     Passenger

  Driver arrived          WebSocket + SMS     Passenger

  Perjalanan selesai      Email + SMS         Passenger + Operator

  Payment confirmed       Email               Passenger + Operator

  New review received     Email               Operator

  Payout processed        Email               Operator
  -----------------------------------------------------------------------

**5.2 Implementasi Laravel Reverb**

Laravel Reverb adalah WebSocket server resmi dari Laravel team ---
self-hosted, gratis, native integration dengan Laravel Broadcasting.

-   Install via composer, konfigurasi di .env

-   Buat Events (BookingStatusUpdated, NewJobReceived, dll)

-   Broadcast ke private/presence channel berdasarkan user role

-   Frontend subscribe via Laravel Echo + Pusher JS client

-   Fallback: polling setiap 30 detik jika WebSocket terputus

**6. Payment & Financial Flow**

**6.1 Stripe Integration**

Stripe adalah satu-satunya pilihan reasonable untuk UK market ---
coverage, compliance, dan trust yang sudah established.

-   Stripe Checkout --- untuk pembayaran passenger (kartu, Apple Pay,
    Google Pay)

-   Stripe Connect (Express) --- untuk payout ke operator

-   Webhook handling --- update status payment secara otomatis

-   Refund API --- untuk cancellation sesuai policy

**6.2 Revenue Model**

  -----------------------------------------------------------------------
  **Model**           **Keterangan**                  **Rekomendasi**
  ------------------- ------------------------------- -------------------
  Commission %        Platform ambil X% dari setiap   Utama (10-15%)
                      booking                         

  Listing Fee         Operator bayar bulanan untuk    Opsional tambahan
                      listing                         

  Featured Placement  Operator bayar untuk muncul di  Phase 2
                      atas                            

  Insurance Add-on    Upsell asuransi perjalanan      Phase 2
  -----------------------------------------------------------------------

**7. Compliance & Legal (UK Market)**

Ini adalah area yang tidak boleh diabaikan. Non-compliance bisa berujung
denda besar atau platform ditutup.

**7.1 GDPR Compliance**

-   Privacy Policy --- harus jelas dan accessible

-   Cookie Consent --- wajib sebelum set any non-essential cookies

-   Data Subject Rights --- user bisa request delete akun & data

-   Data Breach Protocol --- notifikasi ICO dalam 72 jam jika ada breach

-   Data Retention Policy --- berapa lama data disimpan

**7.2 Transport & Business Compliance**

-   Platform hanya sebagai aggregator/marketplace --- bukan operator
    transport

-   Setiap operator wajib punya Private Hire Operator Licence

-   Verifikasi lisensi operator sebelum approval (Hackney/PHV licence
    check)

-   Terms of Service harus jelas membatasi liability platform

**7.3 PCI-DSS**

-   Gunakan Stripe hosted fields / Stripe Checkout --- jangan store card
    data sendiri

-   HTTPS wajib di semua halaman

-   Stripe handles PCI compliance jika implementasi benar

**8. Timeline & Development Phases**

  -----------------------------------------------------------------------
  **Phase**               **Durasi**      **Deliverable**
  ----------------------- --------------- -------------------------------
  Phase 0: Setup &        1-2 minggu      Laravel setup, DB schema, auth,
  Architecture                            roles

  Phase 1: Core Booking   4-5 minggu      Search, quote, booking, basic
  Flow                                    payment

  Phase 2: Operator       3-4 minggu      Onboarding, fleet mgmt, job
  Dashboard                               acceptance

  Phase 3: Notifications  2-3 minggu      Reverb WebSocket, email, SMS
                                          integration

  Phase 4: Admin Panel    2-3 minggu      Approval, monitoring,
                                          commission

  Phase 5: Payment &      2-3 minggu      Stripe Connect, payout flow,
  Payout                                  invoice PDF

  Phase 6: Testing & QA   2 minggu        Bug fix, load test, security
                                          audit

  Phase 7: Launch Prep    1-2 minggu      GDPR checklist, staging,
                                          go-live
  -----------------------------------------------------------------------

*Total estimasi: 17 - 24 minggu (4 - 6 bulan) untuk full MVP. Dengan
AI-assisted development, timeline bisa dipercepat 20-30%.*

**9. Risiko & Mitigasi**

  ------------------------------------------------------------------------
  **Risiko**                **Level**   **Mitigasi**
  ------------------------- ----------- ----------------------------------
  Operator network kosong   TINGGI      Klien harus onboard min. 20
  saat launch                           operator sebelum launch

  GDPR violation            TINGGI      Hire consultant hukum UK atau
                                        gunakan template GDPR-ready

  Stripe account suspended  MEDIUM      Patuhi Stripe ToS, verifikasi KYC
                                        operator dengan benar

  Scalability saat traffic  MEDIUM      Queue dengan Redis, caching
  tinggi                                agresif, CDN untuk assets

  Operator tidak adopsi     MEDIUM      UX operator harus semudah mungkin,
  platform                              onboarding support

  Kompetitor react agresif  LOW         Focus niche (area tertentu dulu)
                                        sebelum expand
  ------------------------------------------------------------------------

**10. Pertanyaan Kritis untuk Klien**

Sebelum development dimulai, klien WAJIB menjawab pertanyaan berikut:

**Bisnis & Operasional**

-   Sudah ada berapa operator yang siap onboard saat launch?

-   Area coverage awal: seluruh UK atau mulai dari kota tertentu?

-   Model bisnis: commission only, atau ada subscription fee untuk
    operator?

-   Siapa yang akan handle customer support & dispute?

**Legal & Compliance**

-   Apakah sudah konsultasi dengan solicitor UK soal struktur bisnis?

-   Siapa yang bertanggung jawab verifikasi lisensi operator?

-   Apakah platform akan ikut bertanggung jawab jika terjadi insiden?

**Teknis & Timeline**

-   Ada preferensi hosting? (AWS, DigitalOcean, Hetzner, dll)

-   Apakah perlu mobile app di Phase 1, atau web-first dulu?

-   Budget untuk third-party services? (Google Maps, Twilio, Mailgun,
    Stripe fees)

-   Siapa yang akan maintain platform setelah launch?

**11. Estimasi Biaya Operasional Bulanan**

  ------------------------------------------------------------------------
  **Service**             **Estimasi/bulan**   **Keterangan**
  ----------------------- -------------------- ---------------------------
  Server (VPS/Cloud)      £30 - £80            DigitalOcean / Hetzner,
                                               tergantung traffic

  Google Maps Platform    £50 - £200           Tergantung volume search &
                                               booking

  Vonage / Twilio SMS     £20 - £100           Per SMS, tergantung volume
                                               notifikasi

  Mailgun / Email         £10 - £30            Transactional email

  Stripe Fees             1.5% + £0.20 /       Standard UK rate
                          transaksi            

  SSL Certificate         £0 (Let\'s Encrypt)  Free, auto-renew

  Total Estimasi          £110 - £410+         Belum termasuk Stripe
                                               per-transaksi
  ------------------------------------------------------------------------

**12. Catatan Akhir & Rekomendasi**

Dokumen ini adalah kerangka awal yang perlu dikonfirmasi dan disesuaikan
dengan kebutuhan spesifik klien. Beberapa rekomendasi penting:

-   Mulai dengan area coverage terbatas (1-2 kota) sebelum expand ke
    seluruh UK

-   Pastikan minimum 20-30 operator aktif sebelum soft launch --- tanpa
    ini platform tidak ada value

-   Gunakan Laravel Reverb untuk WebSocket, bukan Pusher --- lebih hemat
    biaya operasional jangka panjang

-   Web-first MVP dulu, mobile app setelah traction terbukti

-   Alokasikan budget khusus untuk legal consultation GDPR --- ini bukan
    opsional di UK

-   Testing dengan real operators sebelum launch adalah kunci --- bukan
    hanya QA teknis

*Dokumen ini siap di-paste ke Claude Opus untuk menghasilkan technical
specification lebih detail, database schema, API endpoint list, atau
breakdown task per sprint.*

*--- END OF DOCUMENT ---*
