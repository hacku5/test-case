# API dökümantasyonu

## 🚀 Kurulum

1. **Docker'ı Başlatın:**
   ```bash
   chmod +x dock
   ./dock up -d
   ```

2. **Bağımlılıkları Yükleyin:**
   ```bash
   ./dock composer install
   ./dock npm install
   ```

3. **Yapılandırmayı Tamamlayın:**
   ```bash
   ./dock artisan key:generate
   ./dock artisan migrate --seed
   ./dock npm run build
   ```

### Yöntem 2: Manuel (Docker Compose) - Alternatif

Eğer `./dock` scripti çalışmazsa (örneğin Windows'ta veya izin sorunlarında):

1. **Başlatın:**
   ```bash
   docker compose up -d
   ```

2. **Komutları Çalıştırın:**
   ```bash
   docker compose exec app composer install
   docker compose exec app npm install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   docker compose exec app npm run build
   ```

## 🖥 Kullanım

- **Yönetim Paneli:** [http://localhost](http://localhost)
- **API Dokümantasyonu:** [http://localhost/docs/api](http://localhost/docs/api)
- **Log Görüntüleyici:** [http://localhost/telescope](http://localhost/telescope)

## 🧪 Testler

Testler **Docker** içerisindeki PostgreSQL üzerinde (`testing` veritabanı) ve izole şekilde çalışır.

- **Arayüzden:** Dashboard > **Test Merkezi** sekmesinden görsel olarak çalıştırın.
- **Terminalden:**
  ```bash
  ./dock test
  ```

## 🛠 Teknoloji Yığını

- **Backend:** Laravel 12, PHP 8.4, PostgreSQL, Redis
- **Frontend:** Blade, TailwindCSS 4, modern JavaScript (No Framework)
- **Test:** PEST, System Tests (Process Based)
