<?php
// app/Services/AdminLogService.php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\AdminLog;

class AdminLogService
{
    public static function log(string $eventType, string $modelName, $record): void
    {
        $original = $record->getOriginal();
        $current = $record->getAttributes();

        $changes = [];
        $contentLines = [];

        $changes = $record->getChanges(); // 只會包含有改過的欄位
        // dd($record);
        foreach ($changes as $key => $newValue) {
            $oldValue = $record->getOriginal($key);
            $contentLines[] = "將「{$key}」從「{$oldValue}」改為「{$newValue}」";
        
            $diff[$key] = [
                'from' => $oldValue,
                'to' => $newValue,
            ];
        }
        $action = "{$eventType} {$modelName}";
        $content = $contentLines
            ? implode('；', $contentLines)
            : '無欄位變更';

        AdminLog::create([
            'admin_user_id' => Auth::id(),
            'action' => $action,
            'content' => $content,
            'ip' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }
}
