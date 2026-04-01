# iCabbi Integration Setup Guide

Panduan lengkap untuk mengatur integrasi iCabbi dispatch dengan platform TaxiAggregator.

---

## 1. Prerequisites

- Akun iCabbi aktif dengan API access
- API credentials (App Key + Secret Key) dari iCabbi Support
- Platform TaxiAggregator running (branch `feature/icabbi-integration`)

---

## 2. iCabbi Dashboard Settings

### 2.1 API Credentials

Kamu sudah punya credentials ini dari iCabbi:
- **API URL**: `https://api.icabbidispatch.com/icd/`
- **App Key**: dari iCabbi Support
- **Secret Key**: dari iCabbi Support

Jika belum punya, email `support@icabbi.com` dan minta API Key + Secret Key.

### 2.2 Webhook Setup (di iCabbi)

iCabbi perlu mengirim status update balik ke platform kamu. Setup webhook:

1. Login ke iCabbi admin dashboard
2. Buka **Settings** > **API / Integrations** > **Hooks**
3. Buat webhook baru:
   - **Name**: TaxiAggregator Status Updates
   - **URL**: `https://yourdomain.com/webhooks/icabbi`
   - **Method**: POST
   - **Events**: Job Status Changed, Driver Assigned, Job Completed, Job Cancelled
4. Save

Webhook akan mengirim data seperti:
```json
{
    "booking_reference": "TX-20260401-A1B2",
    "status": "driver_assigned",
    "driver_name": "John Smith",
    "driver_phone": "07123456789",
    "vehicle_reg": "AB12 CDE"
}
```

### 2.3 Vehicle Types Mapping

Pastikan vehicle types di iCabbi match:

| Platform | iCabbi |
|----------|--------|
| 1-4 Passengers | Saloon |
| 1-4 Passengers (Estate) | Estate |
| 5-6 Passengers | MPV |
| 7 Passengers | 7 Seater |
| 8 Passengers | 8 Seater |
| 9 Passengers | Minibus |
| 10-14 Passengers | Minibus |
| 15-16 Passengers | Minibus |

Jika nama vehicle types di iCabbi kamu berbeda, edit mapping di:
`app/Services/Dispatch/IcabbiDispatch.php` method `mapFleetType()`

---

## 3. Platform Settings (Operator Account)

### 3.1 Enable iCabbi per Operator

1. Login sebagai operator di platform
2. Buka **My Account** > tab **iCabbi Integration**
3. Isi fields:
   - **Enable iCabbi Dispatch**: Toggle ON
   - **API URL**: `https://api.icabbidispatch.com/icd/`
   - **App Key**: masukkan App Key dari iCabbi
   - **Secret Key**: masukkan Secret Key dari iCabbi
   - **Integration Name**: nama integrasi (bebas, misal "ETGL Production")
4. Klik **Test Connection** - pastikan muncul "Connection successful!"
5. Klik **SAVE**

### 3.2 Verifikasi di Admin Panel

1. Login sebagai admin
2. Buka **Operators** > klik operator > tab **iCabbi**
3. Pastikan status menunjukkan "Active & Configured"

---

## 4. Booking Flow dengan iCabbi

Setelah iCabbi aktif, flow booking berubah:

```
Passenger search → Quote results → Book → Payment
                                        ↓
                              Booking created (status: pending)
                                        ↓
                              Auto-dispatch ke iCabbi API
                              (POST /Add Booking)
                                        ↓
                              iCabbi receives job
                                        ↓
                              iCabbi assigns driver
                                        ↓
                              Webhook callback ke platform
                              (status: driver_assigned)
                                        ↓
                              Passenger gets SMS + notification
                                        ↓
                              Driver completes trip
                                        ↓
                              Webhook: status completed
```

### Apa yang dikirim ke iCabbi:
- Pickup/destination address + coordinates
- Pickup date & time
- Passenger name, phone, email
- Number of passengers
- Vehicle type
- Fare amount
- Payment method (account/cash)
- Special requirements, flight number
- Meet & greet flag
- External reference (booking reference)

### Apa yang diterima dari iCabbi (webhook):
- Job status updates (dispatched, driver_assigned, arrived, completed, cancelled)
- Driver name & phone
- Vehicle registration

---

## 5. Status Mapping

| iCabbi Status | Platform Status |
|---------------|----------------|
| dispatched | accepted |
| accepted | accepted |
| driver_assigned | driver_assigned |
| driver_on_way | en_route |
| arrived | arrived |
| passenger_on_board | in_progress |
| completed | completed |
| cancelled | cancelled |
| no_show | no_show |

---

## 6. Troubleshooting

### Connection test gagal
- Cek App Key dan Secret Key benar
- Cek API URL benar (include trailing slash atau tidak)
- Cek iCabbi account aktif

### Booking tidak terkirim ke iCabbi
- Cek `storage/logs/laravel.log` untuk error
- Cek operator punya iCabbi enabled + keys terisi
- Cek booking status di operator notes (ada prefix `[iCabbi]`)

### Webhook tidak diterima
- Pastikan URL webhook benar di iCabbi dashboard
- Pastikan domain sudah HTTPS (iCabbi mungkin require HTTPS)
- Cek `storage/logs/laravel.log` untuk incoming webhook logs
- Test manual: `curl -X POST https://yourdomain.com/webhooks/icabbi -H "Content-Type: application/json" -d '{"booking_reference":"TX-TEST","status":"driver_assigned"}'`

### Driver info tidak muncul
- Webhook perlu kirim `driver_name` dan `driver_phone` fields
- Info disimpan di `operator_notes` pada booking

---

## 7. Files Terkait

| File | Fungsi |
|------|--------|
| `app/Services/Dispatch/IcabbiDispatch.php` | API client utama |
| `app/Services/Dispatch/DispatchInterface.php` | Contract interface |
| `app/Services/Dispatch/DispatchManager.php` | Factory (pilih Manual vs iCabbi) |
| `app/Services/Dispatch/ManualDispatch.php` | No-op untuk operator tanpa iCabbi |
| `app/Http/Controllers/IcabbiWebhookController.php` | Handle callback dari iCabbi |
| `app/Models/Operator.php` | Field `icabbi_enabled`, `icabbi_app_key`, dll |
| `database/migrations/...000130...` | Migration tambah icabbi fields |
| `tests/Unit/DispatchManagerTest.php` | Unit tests |

---

## 8. Multiple Operators

Setiap operator bisa punya iCabbi account berbeda:
- Operator A: iCabbi enabled, API key AAA
- Operator B: iCabbi enabled, API key BBB
- Operator C: Manual dispatch (no iCabbi)

Platform otomatis pilih dispatch method berdasarkan config masing-masing operator.

---

## 9. Disable iCabbi

Untuk disable tanpa hapus credentials:
1. Operator: My Account > iCabbi > Toggle OFF > Save
2. Booking baru akan dispatch manual (tidak ke iCabbi)
3. Booking yang sudah di-dispatch ke iCabbi tetap berjalan

---

*Branch: `feature/icabbi-integration`*
*Tidak termasuk di branch `main`*
