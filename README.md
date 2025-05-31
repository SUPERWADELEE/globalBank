## 🔧 後台專案啟動流程

```bash
# 1. 複製專案
git clone <你的 repo 連結>
cd <專案資料夾>

# 2. 切換到開發分支
git checkout dev

# 3. 安裝相依套件
composer install

# 4. 建立環境設定檔
cp .env.example .env
```

➡️ `.env DB可以這樣設定：

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

```bash
# 5. 啟動容器
./vendor/bin/sail up -d

# 6. 建立資料表
./vendor/bin/sail artisan migrate

# 7. 產生 Shield 權限與政策
./vendor/bin/sail artisan shield:generate --all --ignore-existing-policies

# 8. 匯入初始資料
./vendor/bin/sail artisan db:seed

# 9. 產生應用程式金鑰
./vendor/bin/sail artisan key:generate
```

### 🧪 預設管理員帳號（具所有權限）

- 帳號：`admin`  
- 密碼：`aaaa1234`

---

## 🔧 前台專案啟動流程

```bash
# 安裝前端依賴（包含 Tailwind）
npm install

# 啟動熱更新開發伺服器
npm run dev
```

---

### 📌 前台路由與畫面對應表

| 對外網址                   | Route 名稱                | 用途說明       |
|----------------------------|----------------------------|----------------|
| `/login`                   | `login`                    | 使用者登入頁   |
| `/member/wallet`           | `member.wallet`            | 錢包資訊頁     |
| `/member/withdraw`         | `member.withdraw`          | 提領頁面       |
| `/member/deposit`          | `member.deposit`           | 入金頁面       |
| `/member/exchange`         | `member.exchange`          | 兌換頁面       |
| `/member/exchange-rate`    | `member.exchange_rate`     | 匯率查詢頁     |
| `/member/records`          | `member.records.index`     | 交易紀錄列表   |
| `/member/records/{record}` | `member.records.show`      | 單筆紀錄詳情   |
| `/member/info`             | `member.info`              | 個人資訊頁     |

---

### 📁 Blade 檔案結構對應位置

```
resources/views/
└── livewire/
    └── front/
        ├── auth/
        │   └── login.blade.php
        └── member/
            ├── wallet.blade.php
            ├── withdraw.blade.php
            ├── deposit.blade.php
            ├── exchange.blade.php
            ├── exchange_rate.blade.php
            ├── info.blade.php
            └── records/
                ├── index.blade.php
                └── show.blade.php
```

> 💡 所有頁面皆為 Livewire 組件所對應的 Blade 檔案，位於 `resources/views/livewire/front/` 路徑中。