<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiSetting extends Model
{
    protected $table = 'ai_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * الحصول على قيمة الإعداد
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    /**
     * تعيين قيمة الإعداد
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * الحصول على إعداد معين
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * تعيين إعداد معين
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null, $isEncrypted = false)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
                'is_encrypted' => $isEncrypted,
            ]
        );
    }

    /**
     * الحصول على جميع الإعدادات في مجموعة معينة
     */
    public static function getGroup($group)
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }
}
