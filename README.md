# 🏢 HR Management System - Monorepo

Hệ thống quản lý nhân sự với Laravel Backend API + Laravel Frontend.

## 📁 Cấu trúc dự án

```
ChuyenDeThucTap/
├── BE-HR-main/              # Backend API (Laravel 12)
│   ├── Dockerfile           # Docker config cho Railway
│   ├── docker-entrypoint.sh # Startup script (migrations, etc)
│   ├── railway.toml         # Railway deployment config
│   └── .env.example         # Environment variables template
│
├── FE-HR-main/              # Frontend (Laravel 12)
│   ├── Dockerfile           # Docker config cho Railway
│   ├── docker-entrypoint.sh # Startup script (migrations, etc)
│   ├── railway.toml         # Railway deployment config
│   └── .env.example         # Environment variables template
│
└── docs/                    # Documentation
    ├── SETUP.md             # Setup local với XAMPP
    ├── DEPLOY_RAILWAY.md    # Deploy lên Railway
    ├── RAILWAY_ROOT_DIRECTORY.md  # ⭐ Set Root Directory
    ├── RAILWAY_MYSQL_SETUP.md     # Config MySQL
    └── MIGRATION_STRATEGY.md      # Migration safety
```

## 🚀 Quick Start

### Local Development (XAMPP)

```bash
# 1. Setup Backend
cd BE-HR-main
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve --port=8000

# 2. Setup Frontend (terminal mới)
cd FE-HR-main
composer install
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve --port=9000
```

👉 **Chi tiết:** [SETUP.md](SETUP.md)

---

### Production (Railway)

#### Bước 1: Push lên GitHub
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/hr-management-system.git
git push -u origin main
```

#### Bước 2: Deploy trên Railway

1. **Tạo MySQL database** (+ New → Database → MySQL)

2. **Tạo Backend service:**
   - Deploy from GitHub repo
   - **⭐ Settings → Root Directory = `BE-HR-main`**
   - Variables → Add Reference → MySQL
   - Add: APP_KEY, APP_URL

3. **Tạo Frontend service:**
   - Deploy from GitHub repo (cùng repo)
   - **⭐ Settings → Root Directory = `FE-HR-main`**
   - Variables → Add Reference → MySQL
   - Add: APP_KEY, APP_URL, VITE_API_URL

👉 **Chi tiết:** [DEPLOY_RAILWAY.md](DEPLOY_RAILWAY.md)
👉 **Root Directory:** [RAILWAY_ROOT_DIRECTORY.md](RAILWAY_ROOT_DIRECTORY.md)

---

## 📚 Documentation

| File | Mô tả |
|------|-------|
| [SETUP.md](SETUP.md) | Setup local với XAMPP |
| [DEPLOY_RAILWAY.md](DEPLOY_RAILWAY.md) | Deploy lên Railway (tổng quan) |
| [RAILWAY_ROOT_DIRECTORY.md](RAILWAY_ROOT_DIRECTORY.md) | ⭐ **Cách set Root Directory (QUAN TRỌNG!)** |
| [RAILWAY_MYSQL_SETUP.md](RAILWAY_MYSQL_SETUP.md) | Config MySQL trên Railway |
| [MIGRATION_STRATEGY.md](MIGRATION_STRATEGY.md) | Migration an toàn, không crash khi redeploy |

---

## 🔑 Các điểm quan trọng

### 1. Railway Monorepo với Root Directory

Railway hỗ trợ deploy nhiều services từ 1 repo bằng cách set **Root Directory**:

```
Backend Service:
  Root Directory: BE-HR-main/     ← Trỏ vào backend
  Dockerfile: BE-HR-main/Dockerfile

Frontend Service:
  Root Directory: FE-HR-main/     ← Trỏ vào frontend
  Dockerfile: FE-HR-main/Dockerfile
```

**Không set Root Directory = Deploy fail!** ❌

👉 Xem chi tiết: [RAILWAY_ROOT_DIRECTORY.md](RAILWAY_ROOT_DIRECTORY.md)

---

### 2. Railway MySQL Variables

Railway inject biến khác với Laravel:

```env
# Railway provides:
MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD

# Laravel needs:
DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Solution (đã config sẵn trong .env.example):
DB_HOST=${MYSQLHOST}
DB_PORT=${MYSQLPORT}
# ... etc
```

**QUAN TRỌNG:** Dùng **private domain** (miễn phí), không dùng public (tốn egress fees)!

👉 Xem chi tiết: [RAILWAY_MYSQL_SETUP.md](RAILWAY_MYSQL_SETUP.md)

---

### 3. Migration Safety

Migration chạy trong **docker-entrypoint.sh** (runtime), không phải Dockerfile (build time):

```bash
# Mỗi khi container start:
1. Wait for database
2. Run migrations (safe, không crash nếu đã migrate)
3. Create storage link
4. Cache config
5. Start Nginx + PHP-FPM
```

**Lợi ích:** Redeploy bao nhiêu lần cũng không bị lỗi migration! ✅

👉 Xem chi tiết: [MIGRATION_STRATEGY.md](MIGRATION_STRATEGY.md)

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2, MySQL, Sanctum Auth
- **Frontend:** Laravel 12, PHP 8.2, Vite, TailwindCSS 4
- **Deployment:** Railway, Docker, Nginx, Supervisor
- **Database:** MySQL (Railway managed)

---

## 🌐 API Endpoints (Backend)

### Public
- `GET /api/test` - Test endpoint
- `GET /api/ping` - Health check
- `POST /api/admin/login` - Admin login
- `POST /api/employee/login` - Employee login

### Protected (Admin)
- `GET /api/admin/employees` - List employees
- `POST /api/admin/employees` - Create employee
- `GET /api/admin/departments` - List departments
- ... (xem routes/api.php)

### Protected (Employee)
- `GET /api/employee/me` - Current user info
- `GET /api/employee/my-work-schedules` - My schedules
- ... (xem routes/api.php)

---

## 🔐 Environment Variables

### Backend (.env)

```env
APP_NAME="HR Backend API"
APP_ENV=production
APP_KEY=                              # php artisan key:generate
APP_URL=https://your-backend.railway.app

DB_HOST=${MYSQLHOST}                  # Railway injects
DB_PORT=${MYSQLPORT}
DB_DATABASE=${MYSQLDATABASE}
DB_USERNAME=${MYSQLUSER}
DB_PASSWORD=${MYSQLPASSWORD}
```

### Frontend (.env)

```env
APP_NAME="HR Frontend"
APP_ENV=production
APP_KEY=                              # php artisan key:generate
APP_URL=https://your-frontend.railway.app

VITE_API_URL=https://your-backend.railway.app/api  # Backend URL

DB_HOST=${MYSQLHOST}                  # Railway injects
# ... same as backend
```

---

## 📞 Support

- **Railway Docs:** https://docs.railway.app/
- **Laravel Docs:** https://laravel.com/docs/12.x
- **Issues:** GitHub Issues

---

## 📄 License

MIT License

---

## 👥 Contributors

- Your Name - Initial work

---

## 🎯 Deployment Checklist

### Local Development
- [ ] XAMPP installed (PHP 8.2+, MySQL)
- [ ] Composer installed
- [ ] Node.js installed
- [ ] Database `hr_backend` và `hr_frontend` đã tạo
- [ ] Backend chạy thành công tại http://localhost:8000
- [ ] Frontend chạy thành công tại http://localhost:9000

### Railway Production
- [ ] Code đã push lên GitHub
- [ ] Railway project đã tạo
- [ ] MySQL database đã tạo
- [ ] Backend service: Root Directory = `BE-HR-main` ✅
- [ ] Frontend service: Root Directory = `FE-HR-main` ✅
- [ ] Backend: APP_KEY đã generate
- [ ] Frontend: APP_KEY đã generate
- [ ] Frontend: VITE_API_URL = Backend URL
- [ ] Health check: Backend /api/ping returns {"pong":true}
- [ ] Frontend trang chủ load thành công

---

**🚀 Happy Coding!**
