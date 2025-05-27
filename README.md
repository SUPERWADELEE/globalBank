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