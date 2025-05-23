<?php

namespace App\Models;

use App\Scopes\ZoneScope;
use Illuminate\Support\Str;
use App\Scopes\RestaurantScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model
{
    use HasFactory;

    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'avg_rating' => 'float',
        'set_menu' => 'integer',
        'category_id' => 'integer',
        'restaurant_open' => 'integer',
        'restaurant_id' => 'integer',
        'reviews_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'veg' => 'integer',
        'min' => 'integer',
        'max' => 'integer',
        'maximum_cart_quantity' => 'integer',
        'recommended' => 'integer',
    ];


    public function logs()
    {
        return $this->hasMany(Log::class,'model_id')->where('model','Food');
    }

    public function scopeRecommended($query)
    {
        return $query->where('recommended',1);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    
    public function scopeRestaurantOpen($query)
    {
        $query->selectRaw('*, IF(((select count(*) from `restaurant_schedule` where `food`.`restaurant_id` = `restaurant_schedule`.`restaurant_id` and `restaurant_schedule`.`day` = '.now()->dayOfWeek.' and `restaurant_schedule`.`opening_time` < "'.now()->format('H:i:s').'" and `restaurant_schedule`.`closing_time` >"'.now()->format('H:i:s').'") > 0), true, false) as restaurant_open');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1)->whereHas('restaurant', function ($query) {
            return $query->where('status', 1);
        });
    }
    
    public function scopeDiscount($query)
    {
        return $query->where('discount','>',0);
    }

    public function scopeAvailable($query,$time)
    {
        $query->where(function($q)use($time){
            $q->where('available_time_starts','<=',$time)->where('available_time_ends','>=',$time);
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('order_count', 'desc');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, count(food_id) rating_count, food_id'))
            ->groupBy('food_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }


    // public function getCategoryAttribute()
    // {
    //     // $category = Category::with('translations')->find(json_decode($this->category_ids)[0]->id);
    //     $category = $this->category;
    //     return $category ? ($category->parent ? $category->parent->name : $category->name) : translate('messages.uncategorize');
    // }

    protected static function booted()
    {
        // dd( app()->getLocale());
        if (auth('vendor')->check() || auth('vendor_employee')->check()) {
            static::addGlobalScope(new RestaurantScope);
        }

        static::addGlobalScope(new ZoneScope);

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }


    public function scopeType($query, $type)
    {
        if ($type == 'veg') {
            return $query->where('veg', true);
        } else if ($type == 'non_veg') {
            return $query->where('veg', false);
        }

        return $query;
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($food) {
            $food->slug = $food->generateSlug($food->name);
            $food->save();
        });
    }
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like',"{$slug}%")->latest('id')->value('slug')) {

            if($max_slug == $slug) return "{$slug}-2";

            $max_slug = explode('-',$max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[]= ++$count;
                return implode('-', $max_slug);
            }
        }
        return $slug;
    }


    public function getNameAttribute($value){
        if (count($this->translations) > 0) {
            // info(count($this->translations));
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getDescriptionAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'description') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

}
