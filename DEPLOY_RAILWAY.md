# 🚂 HƯỚNG DẪN DEPLOY LÊN RAILWAY

## 📋 TÓM TẮT

Deploy **monorepo** (BE-HR-main + FE-HR-main) lên Railway với:
- ✅ 2 services riêng biệt từ 1 repo
- ✅ MySQL của Railway (shared hoặc riêng)
- ✅ Root directory rõ ràng
- ✅ Dockerfile + railway.toml đã config sẵn

---

## 📁 CẤU TRÚC MONOREPO

```
ChuyenDeThucTap/                    # 1 Git Repository
├── BE-HR-main/                     # Backend Service
│   ├── Dockerfile                  # ✅ Đã tạo
│   ├── .dockerignore               # ✅ Đã tạo
│   ├── railway.toml                # ✅ Đã tạo
│   └── .env.example                # ✅ Đã cập nhật cho Railway
│
├── FE-HR-main/                     # Frontend Service
│   ├── Dockerfile                  # ✅ Đã tạo
│   ├── .dockerignore               # ✅ Đã tạo
│   ├── railway.toml                # ✅ Đã tạo
│   └── .env.example                # ✅ Đã cập nhật cho Railway
│
└── DEPLOY_RAILWAY.md               # File này
```

---

## 🚀 BƯỚC 1: CHUẨN BỊ CODE

### 1.1. Khởi tạo Git (nếu chưa có)

```bash
cd d:\PC\Downloads\ChuyenDeThucTap

# Khởi tạo git
git init

# Add tất cả files
git add .

# Commit
git commit -m "Initial commit: BE-HR-main + FE-HR-main monorepo"
```

### 1.2. Push lên GitHub

```bash
# Tạo repo trên GitHub: https://github.com/new
# Đặt tên: hr-management-system (hoặc tên bạn thích)

# Add remote
git remote add origin https://github.com/YOUR_USERNAME/hr-management-system.git

# Push
git branch -M main
git push -u origin main
```

---

## 🚂 BƯỚC 2: SETUP RAILWAY

### 2.1. Đăng ký/Đăng nhập Railway

1. Vào: https://railway.app
2. Đăng nhập bằng GitHub
3. Authorize Railway để truy cập repos

### 2.2. Tạo Project mới

1. Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Chọn repo `hr-management-system`
4. Railway sẽ phát hiện monorepo

### 2.3. Tạo MySQL Database

1. Trong project, click **"+ New"**
2. Chọn **"Database"** → **"Add MySQL"**
3. Railway sẽ tạo database và cung cấp credentials tự động

**LƯU Ý:** Railway sẽ tự động inject các biến:
- `MYSQL_HOST`
- `MYSQL_PORT`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`
- `MYSQL_URL` (connection string)

---

## 🎯 BƯỚC 3: DEPLOY BACKEND

### 3.1. Tạo Service cho Backend

1. Trong project, click **"+ New"**
2. Chọn **"GitHub Repo"** → chọn repo của bạn
3. Railway sẽ tự động phát hiện `BE-HR-main/Dockerfile`

### 3.2. Config Root Directory

**QUAN TRỌNG:** Phải chỉ định root directory!

1. Vào service Backend vừa tạo
2. Click tab **"Settings"**
3. Tìm **"Root Directory"**
4. Nhập: `BE-HR-main`
5. Click **"Save"**

### 3.3. Config Environment Variables

Vào tab **"Variables"**, thêm các biến sau:

```env
# Laravel
APP_NAME=HR Backend API
APP_ENV=production
APP_KEY=                          # Sẽ generate ở bước sau
APP_DEBUG=false
APP_URL=https://YOUR-BACKEND-URL.railway.app

# Database (Railway tự inject, nhưng cần map lại)
# Click "Connect" to MySQL service để Railway tự động add
```

**Cách kết nối với MySQL:**
1. Trong Backend service, click **"Variables"**
2. Click **"+ New Variable"** → **"Add Reference"**
3. Chọn MySQL database service
4. Railway sẽ tự động thêm tất cả MYSQL_* variables

### 3.4. Generate APP_KEY

Sau khi deploy lần đầu:

```bash
# Vào tab "Deployments" → Click deployment mới nhất → "View Logs"
# Hoặc dùng Railway CLI:
railway run php artisan key:generate --show
```

Copy key và paste vào biến `APP_KEY` trong Variables.

### 3.5. Chạy Migrations

```bash
# Option 1: Dùng Railway CLI
railway run php artisan migrate --seed --force

# Option 2: Thêm vào Dockerfile (đã có sẵn)
# Migrations sẽ tự động chạy khi deploy
```

### 3.6. Cấu hình Domain (Optional)

1. Tab **"Settings"** → **"Domains"**
2. Railway tự generate domain: `*.railway.app`
3. Hoặc add custom domain

---

## 🎨 BƯỚC 4: DEPLOY FRONTEND

### 4.1. Tạo Service cho Frontend

1. Trong project, click **"+ New"**
2. Chọn **"GitHub Repo"** → chọn repo của bạn
3. Railway sẽ tự động phát hiện `FE-HR-main/Dockerfile`

### 4.2. Config Root Directory

**QUAN TRỌNG:** Phải chỉ định root directory!

1. Vào service Frontend vừa tạo
2. Click tab **"Settings"**
3. Tìm **"Root Directory"**
4. Nhập: `FE-HR-main`
5. Click **"Save"**

### 4.3. Config Environment Variables

Vào tab **"Variables"**, thêm:

```env
# Laravel
APP_NAME=HR Frontend
APP_ENV=production
APP_KEY=                          # Generate như backend
APP_DEBUG=false
APP_URL=https://YOUR-FRONTEND-URL.railway.app

# Backend API URL (QUAN TRỌNG!)
VITE_API_URL=https://YOUR-BACKEND-URL.railway.app/api

# Database (connect to MySQL like backend)
# Click "Add Reference" → Select MySQL service
```

### 4.4. Kết nối MySQL

Giống như Backend, add reference tới MySQL service.

### 4.5. Generate APP_KEY & Run Migrations

```bash
railway run php artisan key:generate --show
railway run php artisan migrate --force
```

---

## ✅ BƯỚC 5: KIỂM TRA

### Backend Health Check

```
https://YOUR-BACKEND-URL.railway.app/api/ping
```

Response:
```json
{"pong": true}
```

### Frontend

```
https://YOUR-FRONTEND-URL.railway.app
```

### Test API từ Frontend

Kiểm tra trong browser console (F12):
```javascript
fetch('https://YOUR-BACKEND-URL.railway.app/api/test')
  .then(r => r.text())
  .then(console.log)
```

---

## 🔄 BƯỚC 6: AUTO DEPLOY (CI/CD)

Railway đã tự động setup CI/CD:

1. **Mỗi lần push code lên GitHub**
2. Railway sẽ tự động:
   - Pull code mới
   - Build Docker image
   - Deploy service
   - Run health checks

### Trigger Manual Deploy

1. Vào service
2. Tab **"Deployments"**
3. Click **"Deploy"** → **"Redeploy"**

---

## 📊 CẤU TRÚC RAILWAY PROJECT

```
Railway Project: HR Management System
│
├── MySQL Database                 # Shared database
│   ├── MYSQL_HOST
│   ├── MYSQL_PORT
│   ├── MYSQL_DATABASE
│   ├── MYSQL_USER
│   └── MYSQL_PASSWORD
│
├── Backend Service (BE-HR-main)
│   ├── Root Directory: BE-HR-main/
│   ├── Dockerfile: BE-HR-main/Dockerfile
│   ├── Port: 8000 (internal)
│   ├── Domain: https://backend-xyz.railway.app
│   └── Connected to: MySQL Database
│
└── Frontend Service (FE-HR-main)
    ├── Root Directory: FE-HR-main/
    ├── Dockerfile: FE-HR-main/Dockerfile
    ├── Port: 9000 (internal)
    ├── Domain: https://frontend-xyz.railway.app
    └── Connected to: MySQL Database + Backend
```

---

## 🛠️ RAILWAY CLI (OPTIONAL)

### Cài đặt

```bash
npm install -g @railway/cli

# Hoặc
curl -fsSL https://railway.app/install.sh | sh
```

### Login

```bash
railway login
```

### Link Project

```bash
cd d:\PC\Downloads\ChuyenDeThucTap\BE-HR-main
railway link
# Chọn project và service Backend

cd ../FE-HR-main
railway link
# Chọn project và service Frontend
```

### Useful Commands

```bash
# View logs
railway logs

# Run artisan commands
railway run php artisan migrate

# Open service in browser
railway open

# View variables
railway variables
```

---

## 🔧 TROUBLESHOOTING

### 1. Build Failed

**Lỗi:** `No Dockerfile found`

**Fix:** Kiểm tra Root Directory đã set đúng chưa:
- Backend: `BE-HR-main`
- Frontend: `FE-HR-main`

---

### 2. Database Connection Failed

**Lỗi:** `SQLSTATE[HY000] [2002] Connection refused`

**Fix:**
1. Kiểm tra MySQL service đã start chưa
2. Kiểm tra đã "Add Reference" MySQL trong Variables
3. Verify variables: `MYSQL_HOST`, `MYSQL_PORT`, etc.

---

### 3. APP_KEY Missing

**Lỗi:** `No application encryption key has been specified`

**Fix:**
```bash
railway run php artisan key:generate --show
# Copy output và paste vào Variables → APP_KEY
```

---

### 4. CORS Error (Frontend không gọi được Backend)

**Fix:** Thêm CORS middleware trong Backend

File: `BE-HR-main/bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'api/*',
    ]);

    // Add CORS
    $middleware->append(\App\Http\Middleware\Cors::class);
})
```

Tạo file: `BE-HR-main/app/Http/Middleware/Cors.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
```

---

### 5. Storage Link Error

**Lỗi:** `The "public/storage" directory does not exist`

**Fix:** Đã được handle trong Dockerfile:
```dockerfile
RUN php artisan storage:link || true
```

Hoặc chạy manual:
```bash
railway run php artisan storage:link
```

---

## 💰 PRICING

Railway Free Tier:
- $5 credit/tháng
- Đủ cho hobby projects
- Auto-sleep sau 1 giờ không hoạt động

Upgrade nếu cần:
- $20/tháng cho unlimited usage

---

## 📞 SUPPORT

### Railway Docs
- https://docs.railway.app/

### Discord
- https://discord.gg/railway

### GitHub Issues
- https://github.com/railwayapp/railway/issues

---

## 🎯 CHECKLIST DEPLOY

- [ ] Code pushed to GitHub
- [ ] Railway project created
- [ ] MySQL database added
- [ ] Backend service created with root dir `BE-HR-main`
- [ ] Backend variables configured (APP_KEY, MySQL connection)
- [ ] Backend deployed successfully
- [ ] Backend health check passed (`/api/ping`)
- [ ] Frontend service created with root dir `FE-HR-main`
- [ ] Frontend variables configured (APP_KEY, VITE_API_URL, MySQL)
- [ ] Frontend deployed successfully
- [ ] Frontend can access backend API
- [ ] Migrations ran successfully
- [ ] Test login/features work

---

## 🔄 UPDATE CODE WORKFLOW

```bash
# 1. Make changes locally
cd d:\PC\Downloads\ChuyenDeThucTap

# 2. Test locally
cd BE-HR-main
php artisan serve

# 3. Commit and push
git add .
git commit -m "Update: description of changes"
git push origin main

# 4. Railway auto-deploys (wait ~2-5 minutes)

# 5. Check deployment in Railway dashboard
```

---

**Chúc bạn deploy thành công! 🚀**
