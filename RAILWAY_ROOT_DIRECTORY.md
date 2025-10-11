# 🎯 RAILWAY ROOT DIRECTORY - HƯỚNG DẪN CHI TIẾT

## ❓ VẤN ĐỀ: SAU KHI PUSH LÊN GITHUB, RAILWAY TRỎ VÀO ĐÂU?

Khi bạn có monorepo như thế này:
```
ChuyenDeThucTap/                    # ← Repository root
├── BE-HR-main/                     # ← Backend code
│   └── Dockerfile
├── FE-HR-main/                     # ← Frontend code
│   └── Dockerfile
└── README.md
```

**Railway mặc định sẽ tìm Dockerfile ở ROOT** (`ChuyenDeThucTap/Dockerfile`) → **KHÔNG TÌM THẤY** → **FAIL!**

---

## ✅ GIẢI PHÁP: SET ROOT DIRECTORY

Railway cho phép **mỗi service chỉ định thư mục gốc riêng** (Root Directory).

### Kết quả:
```
Railway Project
├── Backend Service
│   ├── Root Directory: BE-HR-main/     ← TRỎ VÀO ĐÂY
│   └── Tìm Dockerfile tại: BE-HR-main/Dockerfile
│
└── Frontend Service
    ├── Root Directory: FE-HR-main/     ← TRỎ VÀO ĐÂY
    └── Tìm Dockerfile tại: FE-HR-main/Dockerfile
```

---

## 📋 BƯỚC 1: PUSH CODE LÊN GITHUB

### 1.1. Khởi tạo Git (nếu chưa có)

```bash
cd d:\PC\Downloads\ChuyenDeThucTap

# Kiểm tra git đã init chưa
git status

# Nếu chưa có, init
git init
```

### 1.2. Add và Commit

```bash
# Add tất cả files
git add .

# Commit
git commit -m "feat: add Railway deployment config for monorepo

- Backend (BE-HR-main) with Dockerfile
- Frontend (FE-HR-main) with Dockerfile
- Railway MySQL configuration
- Migration safety with entrypoint scripts
- Documentation
"
```

### 1.3. Tạo GitHub Repository

1. Vào https://github.com/new
2. Tên repo: `hr-management-system` (hoặc tên bạn thích)
3. **KHÔNG** chọn "Add README" (vì đã có local)
4. Click **"Create repository"**

### 1.4. Push lên GitHub

```bash
# Add remote (thay YOUR_USERNAME bằng username GitHub của bạn)
git remote add origin https://github.com/YOUR_USERNAME/hr-management-system.git

# Đổi branch thành main (nếu đang là master)
git branch -M main

# Push
git push -u origin main
```

✅ **Xong!** Code đã lên GitHub.

---

## 🚂 BƯỚC 2: TẠO PROJECT TRÊN RAILWAY

### 2.1. Login Railway

1. Vào https://railway.app
2. Click **"Login"** → Login bằng **GitHub**
3. Authorize Railway

### 2.2. Tạo Project mới

1. Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. **Select repository**: Chọn `hr-management-system`
4. Railway sẽ scan repo và phát hiện **2 Dockerfiles**

**LƯU Ý:** Railway có thể tự động tạo 2 services, hoặc hỏi bạn chọn. Nếu tự tạo, **XÓA ĐI** và làm theo bước dưới.

---

## 🗄️ BƯỚC 3: TẠO MYSQL DATABASE

**QUAN TRỌNG: Tạo database TRƯỚC khi tạo services!**

1. Trong project Railway, click **"+ New"**
2. Chọn **"Database"** → **"Add MySQL"**
3. Railway tự động:
   - Tạo database `railway`
   - Generate password
   - Cung cấp variables: `MYSQLHOST`, `MYSQLPORT`, etc.

✅ **MySQL service đã sẵn sàng!**

---

## 🎯 BƯỚC 4: TẠO BACKEND SERVICE (QUAN TRỌNG!)

### 4.1. Tạo Service

1. Click **"+ New"** trong project
2. Chọn **"GitHub Repo"**
3. Chọn repo: `hr-management-system`
4. Railway sẽ tạo service và **tự động deploy** (sẽ FAIL vì chưa set root directory)

### 4.2. SET ROOT DIRECTORY (⭐ BƯỚC QUAN TRỌNG NHẤT!)

1. Click vào **service vừa tạo** (tên mặc định: `hr-management-system`)
2. Click vào tab **"Settings"** (⚙️ icon)
3. Scroll xuống tìm **"Root Directory"**
4. Click **"Edit"** hoặc click vào ô input
5. Nhập: `BE-HR-main`
6. Nhấn **Enter** hoặc click ngoài để save

**Screenshot:**
```
┌─────────────────────────────────────────┐
│ Service Settings                        │
├─────────────────────────────────────────┤
│ Service Name: hr-management-system      │
│ ✏️  Edit                                │
├─────────────────────────────────────────┤
│ Root Directory                          │
│ ┌─────────────────────────────────────┐ │
│ │ BE-HR-main                        ✓ │ │ ← NHẬP VÀO ĐÂY!
│ └─────────────────────────────────────┘ │
│ The directory Railway should use as    │
│ the root when building your service.   │
└─────────────────────────────────────────┘
```

7. Railway sẽ **TỰ ĐỘNG REDEPLOY** sau khi save

### 4.3. Đổi tên Service (Optional nhưng nên làm)

1. Vẫn trong **Settings**
2. Tìm **"Service Name"**
3. Click **"Edit"**
4. Đổi tên thành: `Backend` hoặc `HR Backend API`
5. Save

### 4.4. Connect MySQL Database

1. Click tab **"Variables"**
2. Click **"+ New Variable"**
3. Chọn **"Add Reference"**
4. Select MySQL service
5. Railway tự động thêm tất cả MySQL variables:
   - `MYSQLDATABASE`
   - `MYSQLHOST`
   - `MYSQLPASSWORD`
   - `MYSQLPORT`
   - `MYSQLUSER`

### 4.5. Thêm Laravel Variables

Vẫn trong **Variables**, click **"+ New Variable"** và thêm:

```
APP_NAME=HR Backend API
APP_ENV=production
APP_DEBUG=false
APP_KEY=                     ← Để trống, sẽ generate sau
APP_URL=                     ← Sẽ update sau khi có domain
```

### 4.6. Generate APP_KEY

**Option 1: Dùng Railway Logs**

1. Đợi deployment hoàn thành
2. Click tab **"Deployments"**
3. Click vào deployment mới nhất
4. Click **"View Logs"**
5. Tìm dòng có error về APP_KEY
6. Railway CLI: `railway run php artisan key:generate --show`

**Option 2: Generate local rồi copy**

```bash
cd BE-HR-main
php artisan key:generate --show
# Copy output: base64:xxxxxxxxxxxxx
```

Paste vào **Variables** → **APP_KEY**

### 4.7. Update APP_URL

1. Click tab **"Settings"**
2. Tìm **"Domains"**
3. Copy domain Railway generate (VD: `backend-production-abc.up.railway.app`)
4. Quay lại **Variables**
5. Update **APP_URL**:
   ```
   APP_URL=https://backend-production-abc.up.railway.app
   ```

✅ **Backend đã sẵn sàng!**

---

## 🎨 BƯỚC 5: TẠO FRONTEND SERVICE (TƯƠNG TỰ BACKEND)

### 5.1. Tạo Service

1. Click **"+ New"** trong project
2. Chọn **"GitHub Repo"**
3. Chọn repo: `hr-management-system` (cùng repo)

### 5.2. SET ROOT DIRECTORY (⭐ QUAN TRỌNG!)

1. Click vào service vừa tạo
2. Tab **"Settings"** → **"Root Directory"**
3. Nhập: `FE-HR-main`
4. Save → Auto redeploy

### 5.3. Đổi tên Service

Settings → Service Name → `Frontend` hoặc `HR Frontend`

### 5.4. Connect MySQL Database

Variables → Add Reference → Select MySQL service

### 5.5. Thêm Laravel Variables + Backend URL

```
APP_NAME=HR Frontend
APP_ENV=production
APP_DEBUG=false
APP_KEY=                     ← Generate như Backend
APP_URL=                     ← Update sau khi có domain

VITE_API_URL=https://backend-production-abc.up.railway.app/api
                     ↑ Backend URL từ bước 4.7
```

### 5.6. Generate APP_KEY

Tương tự Backend

### 5.7. Update APP_URL

Copy domain của Frontend service và update vào APP_URL

✅ **Frontend đã sẵn sàng!**

---

## 🎯 CẤU TRÚC RAILWAY PROJECT SAU KHI SETUP

```
Railway Project: HR Management System
│
├── 📦 MySQL                         # Database service
│   ├── Database: railway
│   ├── User: root
│   └── Variables: MYSQLHOST, MYSQLPORT, MYSQLDATABASE, etc.
│
├── 🖥️  Backend (HR Backend API)     # Backend service
│   ├── Root Directory: BE-HR-main/   ← QUAN TRỌNG!
│   ├── Dockerfile: BE-HR-main/Dockerfile
│   ├── Domain: backend-production-abc.up.railway.app
│   ├── Health Check: /api/ping
│   └── Variables:
│       ├── APP_KEY, APP_URL, APP_ENV, etc.
│       └── MySQL vars (referenced từ MySQL service)
│
└── 🎨 Frontend (HR Frontend)        # Frontend service
    ├── Root Directory: FE-HR-main/   ← QUAN TRỌNG!
    ├── Dockerfile: FE-HR-main/Dockerfile
    ├── Domain: frontend-production-xyz.up.railway.app
    ├── Health Check: /
    └── Variables:
        ├── APP_KEY, APP_URL, APP_ENV, etc.
        ├── VITE_API_URL (Backend URL)
        └── MySQL vars (referenced từ MySQL service)
```

---

## 🔍 XÁC NHẬN ROOT DIRECTORY ĐÃ ĐÚNG

### Cách 1: Check trong Settings

1. Vào service → **Settings**
2. Xem **Root Directory** có hiển thị:
   - Backend: `BE-HR-main`
   - Frontend: `FE-HR-main`

### Cách 2: Check trong Build Logs

1. Vào **Deployments** → Click deployment mới nhất
2. **View Logs** → Tab **"Build"**
3. Xem log đầu tiên:

```bash
# Backend (ĐÚNG)
--> Building from BE-HR-main/
--> Detected Dockerfile

# Frontend (ĐÚNG)
--> Building from FE-HR-main/
--> Detected Dockerfile

# SAI (nếu không set root directory)
--> Building from /
--> No Dockerfile found ❌
```

---

## 🚨 TROUBLESHOOTING

### ❌ Lỗi: "No Dockerfile found"

**Nguyên nhân:** Chưa set Root Directory

**Fix:**
1. Settings → Root Directory
2. Nhập `BE-HR-main` hoặc `FE-HR-main`
3. Save

---

### ❌ Lỗi: Build thành công nhưng sai service

**Nguyên nhân:** Nhầm Root Directory (VD: Backend set thành `FE-HR-main`)

**Fix:**
1. Verify Root Directory
2. Backend phải là `BE-HR-main`
3. Frontend phải là `FE-HR-main`

---

### ❌ Lỗi: "COPY docker-entrypoint.sh: no such file"

**Nguyên nhân:** Root Directory sai, Docker không tìm thấy file

**Fix:**
1. Đảm bảo `docker-entrypoint.sh` có trong thư mục:
   - `BE-HR-main/docker-entrypoint.sh`
   - `FE-HR-main/docker-entrypoint.sh`
2. Commit và push lại
3. Redeploy

---

### ❌ Lỗi: "Both services using same root?"

**Nguyên nhân:** 2 services cùng trỏ vào 1 thư mục

**Fix:**
- Backend: Root Directory = `BE-HR-main`
- Frontend: Root Directory = `FE-HR-main`
- **KHÁC NHAU!**

---

## ✅ CHECKLIST DEPLOY

Trước khi test:

- [ ] Code đã push lên GitHub
- [ ] Railway project đã tạo
- [ ] MySQL database đã tạo
- [ ] Backend service đã tạo
- [ ] Backend **Root Directory** = `BE-HR-main` ✅
- [ ] Backend đã connect MySQL (Variables có MYSQL*)
- [ ] Backend APP_KEY đã generate
- [ ] Backend APP_URL đã update
- [ ] Backend deployment thành công (green checkmark)
- [ ] Frontend service đã tạo
- [ ] Frontend **Root Directory** = `FE-HR-main` ✅
- [ ] Frontend đã connect MySQL
- [ ] Frontend APP_KEY đã generate
- [ ] Frontend APP_URL đã update
- [ ] Frontend VITE_API_URL = Backend URL
- [ ] Frontend deployment thành công (green checkmark)

---

## 🎯 TEST DEPLOYMENT

### Backend:

```bash
# Test API
curl https://backend-production-abc.up.railway.app/api/ping

# Expected response:
{"pong":true}

# Test health check
curl https://backend-production-abc.up.railway.app/api/test

# Expected response:
API test works!
```

### Frontend:

```
# Mở browser
https://frontend-production-xyz.up.railway.app

# Kiểm tra:
- Trang load thành công
- Không có error trong Console (F12)
- Có thể call API backend
```

---

## 🎉 DONE!

Bây giờ bạn có:
- ✅ 2 services riêng biệt từ 1 repo
- ✅ Root directory được set đúng
- ✅ MySQL shared giữa 2 services
- ✅ Auto-deploy khi push code mới

---

## 🔄 UPDATE CODE SAU NÀY

```bash
# 1. Sửa code
cd BE-HR-main  # hoặc FE-HR-main
# ... edit files ...

# 2. Commit
git add .
git commit -m "fix: your changes"

# 3. Push
git push origin main

# 4. Railway tự động deploy cả 2 services (vì cùng repo)
#    - Backend service: chỉ build BE-HR-main/
#    - Frontend service: chỉ build FE-HR-main/
```

**Railway biết deploy đúng service vì có ROOT DIRECTORY!** 🎯
