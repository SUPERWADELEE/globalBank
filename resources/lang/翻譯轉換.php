<?php

$sourceLang = 'zh_TW';
$targetLang = 'en';

$sourceDir = "Users/liweide/USDT_exchange/USDT_exchange/resources/lang/zh_TW";
$targetDir = "Users/liweide/USDT_exchange/USDT_exchange/resources/lang/en";

// 確保 target 資料夾存在
if (!is_dir($targetDir)) {
    echo "✅ 建立目錄：$targetDir\n";
    mkdir($targetDir, 0777, true);
}

$files = glob("$sourceDir/*.php");

foreach ($files as $filePath) {
    $fileName = basename($filePath);
    $translations = include $filePath;

    if (!is_array($translations)) {
        echo "跳過無效檔案：$fileName\n";
        continue;
    }

    // 處理 key => '英文' 內容
    $translatedArray = array_map(function ($value, $key) {
        // 這裡你可以改用真正的翻譯 API，例如 Google Translate
        return "[EN] " . (is_array($value) ? json_encode($value) : $value);
    }, $translations, array_keys($translations));

    // 將翻譯後的內容寫入目標檔案
    $output = "<?php\n\nreturn [\n";
    foreach ($translatedArray as $key => $value) {
        $escapedValue = addslashes($value);
        $output .= "    '{$key}' => '{$escapedValue}',\n";
    }
    $output .= "];\n";

    file_put_contents("$targetDir/$fileName", $output);
    echo "✅ 已建立：$fileName\n";
}