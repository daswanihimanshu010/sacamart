<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'country_id', 'state_id', 'city_id', 'location', 'status', 'deleted_at'
    ];

    
    public function category()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Http\Models\Osp_terms','category_id','id');
    }
}
