# Before Live Checklist

Panduan lengkap sebelum deploy ke production.

---

## 1. Environment (.env)

### WAJIB GANTI

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=your_production_db_host
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### WAJIB ISI (API Keys)

| Key | Cara Dapat | Status |
|-----|-----------|--------|
| `GOOGLE_MAPS_API_KEY` | Google Cloud Console → APIs & Services → Credentials | ✅ Sudah ada (test) |
| `STRIPE_KEY` | Stripe Dashboard → Developers → API keys → Publishable key (`pk_live_...`) | ⚠️ Ganti ke live key |
| `STRIPE_SECRET` | Stripe Dashboard → Developers → API keys → Secret key (`sk_live_...`) | ⚠️ Ganti ke live key |
| `STRIPE_WEBHOOK_SECRET` | Stripe Dashboard → Developers → Webhooks → Add endpoint → `https://yourdomain.com/webhooks/stripe` | ⚠️ Buat baru untuk production URL |
| `TWILIO_SID` | Twilio Console → Account SID | ❌ Belum diisi |
| `TWILIO_AUTH_TOKEN` | Twilio Console → Auth Token | ❌ Belum diisi |
| `TWILIO_FROM_NUMBER` | Twilio Console → Phone Numbers → Buy a UK number (+44...) | ❌ Belum diisi |

### WAJIB ISI (Email/SMTP)

Pilih salah satu provider:

**Opsi A: Mailgun (Recommended untuk transactional)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.com
MAIL_PASSWORD=mailgun_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Opsi B: Gmail SMTP (Untuk testing/low volume)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```
> Gmail: Buat App Password di https://myaccount.google.com/apppasswords

**Opsi C: Mailtrap (Untuk staging/testing)**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=mailtrap_username
MAIL_PASSWORD=mailtrap_password
```

### OPSIONAL

```env
# Brand (sesuaikan nama perusahaan)
APP_NAME=YourCompanyName
APP_BRAND_PREFIX=your
APP_BRAND_HIGHLIGHT=company
APP_BRAND_SUFFIX=name
APP_BRAND_LOGO=images/logo.png    # taruh file di public/images/

# Reverb WebSocket (untuk real-time notifications)
REVERB_APP_ID=random-string
REVERB_APP_KEY=random-string
REVERB_APP_SECRET=random-string
REVERB_HOST=yourdomain.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

---

## 2. Stripe Setup

### Test → Live Migration
1. Login ke [Stripe Dashboard](https://dashboard.stripe.com)
2. Toggle dari "Test mode" ke "Live mode" (tombol di kanan atas)
3. Complete account verification (KYC):
   - Business details
   - Bank account (UK)
   - Identity verification
4. Copy live API keys ke `.env`
5. Buat webhook endpoint:
   - URL: `https://yourdomain.com/webhooks/stripe`
   - Events: `checkout.session.completed`, `payment_intent.payment_failed`
   - Copy webhook signing secret ke `STRIPE_WEBHOOK_SECRET`

### Penting
- Jangan pernah commit live keys ke git
- Test payment flow end-to-end sebelum announce ke publik
- Pastikan refund flow berjalan (test cancel booking)

---

## 3. Google Maps Setup

### Pastikan API berikut ENABLED di Google Cloud Console:
- ✅ Places API
- ✅ Distance Matrix API
- ✅ Maps JavaScript API

### Restrict API Key (Production)
1. Google Cloud Console → APIs & Services → Credentials
2. Edit API key
3. Application restrictions: HTTP referrers
4. Add: `https://yourdomain.com/*`
5. API restrictions: Restrict to Places API + Distance Matrix API + Maps JS API

### Billing
- Pastikan billing account aktif
- Set budget alert (misalnya £50/bulan)
- Google kasih $200 free credit per bulan

---

## 4. Server Setup

### Requirements
- PHP 8.2+
- MySQL 8
- Nginx
- Composer
- Node.js 18+ (untuk build)
- Redis (opsional, untuk cache/queue)
- Supervisor (untuk queue worker)

### Deploy Steps
```bash
# Clone repo
git clone https://github.com/s4rt4/taxi-aggregator.git /var/www/taxi
cd /var/www/taxi

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Setup environment
cp .env.example .env
nano .env  # isi semua production values

# Generate key & run migrations
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data /var/www/taxi
chmod -R 755 /var/www/taxi/storage
chmod -R 755 /var/www/taxi/bootstrap/cache
```

### Nginx Config (contoh)
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    root /var/www/taxi/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### SSL (Let's Encrypt)
```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com
```

### Queue Worker (Supervisor)
```ini
# /etc/supervisor/conf.d/taxi-worker.conf
[program:taxi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/taxi/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/taxi/storage/logs/worker.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start taxi-worker:*
```

### Cron (Scheduler)
```bash
# crontab -e
* * * * * cd /var/www/taxi && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Security Checklist

- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production`
- [ ] Semua API keys pakai live/production keys
- [ ] `.env` TIDAK ada di git (cek `.gitignore`)
- [ ] HTTPS aktif (SSL certificate)
- [ ] Firewall: hanya port 80, 443, 22 terbuka
- [ ] MySQL: nonaktifkan remote access atau whitelist IP
- [ ] Database password kuat (min 16 karakter)
- [ ] Rate limiting aktif (sudah di-config: search 10/min, booking 5/min)
- [ ] Security headers aktif (sudah di-config)
- [ ] CSRF protection aktif (default Laravel)
- [ ] File upload di-validasi (sudah di AccountController)
- [ ] Backup database harian (setup cron + mysqldump)

---

## 6. Testing Sebelum Live

### Manual Flow Test
- [ ] Register sebagai passenger → login → search → booking → payment → review
- [ ] Register sebagai operator → onboarding 5 step → admin approve → set pricing
- [ ] Admin: approve operator → view bookings → revenue dashboard
- [ ] Cancel booking → cek refund policy ditampilkan
- [ ] Forgot password → cek email terkirim
- [ ] Invoice page bisa diakses dan di-print

### Automated Tests
```bash
php artisan test
# Harus: 201 passed, 411 assertions
```

### Payment Test
1. Gunakan Stripe test card: `4242 4242 4242 4242`
2. Expiry: any future date
3. CVC: any 3 digits
4. Cek payment berhasil di Stripe Dashboard
5. Test cancel → cek refund muncul di Stripe

---

## 7. Data Setup

### Seeder (sudah otomatis)
```bash
php artisan db:seed
# Creates: 8 fleet types, 123 postcode areas, 59 M&G locations, 3 test users
```

### Operator Dummy (untuk testing search)
Buat minimal 2-3 operator approved dengan pricing lengkap:
1. Register operator → complete onboarding
2. Admin approve operator
3. Operator set PMP rates untuk semua fleet types
4. Operator set trip range, vehicle availability
5. Test search dari passenger → harusnya muncul quotes

### Hapus Test Users Sebelum Live
```bash
php artisan tinker
> App\Models\User::whereIn('email', ['admin@test.com','operator@test.com','passenger@test.com'])->forceDelete();
```
Lalu buat admin user baru:
```bash
php artisan tinker
> App\Models\User::create(['name'=>'Your Name','email'=>'admin@yourdomain.com','role'=>'admin','password'=>bcrypt('your_strong_password'),'email_verified_at'=>now()]);
```

---

## 8. Monitoring (Opsional tapi Recommended)

| Tool | Fungsi | Free? |
|------|--------|-------|
| [Laravel Telescope](https://laravel.com/docs/telescope) | Debug requests, queries, jobs | Yes |
| [Sentry](https://sentry.io) | Error tracking | Free tier |
| [UptimeRobot](https://uptimerobot.com) | Uptime monitoring | Free 50 monitors |
| [Oh Dear](https://ohdear.app) | Full site monitoring | Paid |

---

## 9. Backup Strategy

```bash
# Backup database harian (tambahkan ke crontab)
0 2 * * * mysqldump -u root taxi_aggregator | gzip > /backups/taxi_$(date +\%Y\%m\%d).sql.gz

# Hapus backup lebih dari 30 hari
0 3 * * * find /backups -name "taxi_*.sql.gz" -mtime +30 -delete
```

---

## 10. Post-Launch

- [ ] Monitor error logs: `tail -f storage/logs/laravel.log`
- [ ] Cek Stripe webhook events: Stripe Dashboard → Developers → Webhooks
- [ ] Monitor Google Maps API usage: Google Cloud Console → APIs → Dashboard
- [ ] Respond to first operator registrations (approve/reject)
- [ ] Set up Google Analytics (opsional)
- [ ] Submit sitemap ke Google Search Console: `https://yourdomain.com/sitemap.xml`

---

*Last updated: {{ date }}*
*Platform version: Tier 0-8 complete + UK features*
