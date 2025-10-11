# 🚀 HƯỚNG DẪN SETUP VÀ CHẠY DỰ ÁN VỚI XAMPP

## 📋 YÊU CẦU HỆ THỐNG

- XAMPP (PHP 8.2+, MySQL, Apache)
- Composer
- Node.js (v18+) và npm
- Git

---

## 1️⃣ SETUP XAMPP.

### Bước 1: Cài đặt XAMPP
- Download XAMPP từ: https://www.apachefriends.org/
- Cài đặt và đảm bảo có PHP 8.2 trở lên

### Bước 2: Khởi động XAMPP
1. Mở XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Bước 3: Tạo Database
1. Mở trình duyệt và truy cập: http://localhost/phpmyadmin
2. Tạo 2 database mới:
   - `hr_backend` (cho BE-HR-main)
   - `hr_frontend` (cho FE-HR-main)

**Cách tạo:**
```sql
CREATE DATABASE hr_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE hr_frontend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 2️⃣ SETUP BACKEND (BE-HR-main)

### Bước 1: Mở Terminal tại thư mục BE-HR-main
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\BE-HR-main
```

### Bước 2: Cài đặt Composer Dependencies
```bash
composer install
```

### Bước 3: Generate Application Key (nếu chưa có)
```bash
php artisan key:generate
```

### Bước 4: Chạy Migration và Seed Database
```bash
php artisan migrate --seed
```

### Bước 5: Tạo Storage Link
```bash
php artisan storage:link
```

### Bước 6: Build Frontend Assets
```bash
npm install
npm run build
```

### Bước 7: Chạy Server
```bash
php artisan serve --port=8000
```

✅ Backend sẽ chạy tại: **http://localhost:8000**

API Test: http://localhost:8000/api/ping

---

## 3️⃣ SETUP FRONTEND (FE-HR-main)

### Bước 1: Mở Terminal MỚI tại thư mục FE-HR-main
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\FE-HR-main
```

### Bước 2: Cài đặt Composer Dependencies
```bash
composer install
```

### Bước 3: Generate Application Key
```bash
php artisan key:generate
```

### Bước 4: Chạy Migration
```bash
php artisan migrate
```

### Bước 5: Tạo Storage Link
```bash
php artisan storage:link
```

### Bước 6: Build Frontend Assets
```bash
npm install
npm run build
```

### Bước 7: Chạy Server
```bash
php artisan serve --port=8001
```

✅ Frontend sẽ chạy tại: **http://localhost:8001**

---

## 4️⃣ CHẠY CHẾ ĐỘ DEVELOPMENT (với Hot Reload)

### Terminal 1 - Backend:
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\BE-HR-main
php artisan serve --port=8000
```

### Terminal 2 - Backend Vite (hot reload):
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\BE-HR-main
npm run dev
```

### Terminal 3 - Frontend:
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\FE-HR-main
php artisan serve --port=8001
```

### Terminal 4 - Frontend Vite (hot reload):
```bash
cd d:\PC\Downloads\ChuyenDeThucTap\FE-HR-main
npm run dev
```

---

## 5️⃣ HOẶC SỬ DỤNG SCRIPT TỰ ĐỘNG

Tôi đã tạo các file batch scripts để chạy tự động:

### Chạy Backend:
```bash
BE-HR-main\run-backend.bat
```

### Chạy Frontend:
```bash
FE-HR-main\run-frontend.bat
```

### Chạy cả hai cùng lúc:
```bash
run-all.bat
```

---

## 6️⃣ KIỂM TRA

### Backend API:
- Test endpoint: http://localhost:8000/api/test
- Ping endpoint: http://localhost:8000/api/ping
- Admin login: http://localhost:8000/api/admin/login

### Frontend:
- Home page: http://localhost:8001

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **PHP Version**: Đảm bảo XAMPP có PHP 8.2+
   ```bash
   php -v
   ```

2. **Composer**: Kiểm tra Composer đã cài đặt
   ```bash
   composer -V
   ```

3. **Node.js**: Kiểm tra Node.js
   ```bash
   node -v
   npm -v
   ```

4. **MySQL Running**: Đảm bảo MySQL trong XAMPP đang chạy

5. **Port Conflicts**: Nếu port 8000 hoặc 8001 đã bị sử dụng, thay đổi port khác:
   ```bash
   php artisan serve --port=9000
   ```

---

## 🔧 TROUBLESHOOTING

### Lỗi: "Class 'xxx' not found"
```bash
composer dump-autoload
php artisan clear-compiled
php artisan cache:clear
php artisan config:clear
```

### Lỗi: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Lỗi: Database connection
- Kiểm tra MySQL trong XAMPP đã start
- Kiểm tra file `.env` có đúng thông tin DB
- Kiểm tra database đã được tạo trong phpMyAdmin

### Lỗi: Permission denied (storage/logs)
```bash
# Windows
icacls "storage" /grant Everyone:F /t
icacls "bootstrap/cache" /grant Everyone:F /t
```

---

## 📦 CẤU TRÚC THƯ MỤC

```
ChuyenDeThucTap/
├── BE-HR-main/           # Backend API
│   ├── .env              # ✅ Đã config cho XAMPP
│   ├── run-backend.bat   # Script chạy backend
│   └── ...
│
├── FE-HR-main/           # Frontend
│   ├── .env              # ✅ Đã config cho XAMPP
│   ├── run-frontend.bat  # Script chạy frontend
│   └── ...
│
├── run-all.bat           # Chạy cả 2 cùng lúc
└── SETUP_XAMPP.md        # File này
```

---

## 🎯 DEFAULT CREDENTIALS (sau khi seed)

Kiểm tra file seeders để biết tài khoản mặc định:
- `BE-HR-main/database/seeders/`

---

## 📞 HỖ TRỢ

Nếu gặp lỗi, kiểm tra:
1. Log files: `storage/logs/laravel.log`
2. PHP errors trong Terminal
3. Browser Console (F12) cho frontend errors
