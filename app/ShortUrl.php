<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ShortUrl extends Model
{
    use SoftDeletes;

    protected $fillable = [  
        'code', 'link'  
    ];  
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
    public function users(){
		return $this->belongsTo('App\User','user_id');
	}
}
