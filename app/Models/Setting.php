<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = ['key', 'value', 'user_id', 'description', 'type'];
    
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @param int|null $userId
     * @return mixed
     */
    public static function get($key, $default = null, $userId = null)
    {
        // Try to get from cache first
        $cacheKey = "setting.$key" . ($userId ? ".$userId" : "");
        
        $value = Cache::rememberForever($cacheKey, function() use ($key, $default, $userId) {
            $query = self::where('key', $key);
            
            if ($userId) {
                $setting = (clone $query)->where('user_id', $userId)->first();
                
                if ($setting) {
                    return $setting->value;
                }
            }
            
            $setting = $query->whereNull('user_id')->first();
            
            return $setting ? $setting->value : $default;
        });
        
        return $value;
    }
    
    /**
     * Update a setting
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @return bool
     */
    public static function set($key, $value, $userId = null)
    {
        $query = self::where('key', $key);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }
        
        $setting = $query->first();
        
        if ($setting) {
            $setting->value = $value;
            $result = $setting->save();
        } else {
            $result = self::create([
                'key' => $key, 
                'value' => $value,
                'user_id' => $userId
            ]);
        }
        
        // Update cache
        $cacheKey = "setting.$key" . ($userId ? ".$userId" : "");
        Cache::forever($cacheKey, $value);
        
        return $result;
    }
    
    /**
     * Get the user associated with this setting
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}