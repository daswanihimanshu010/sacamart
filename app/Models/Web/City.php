<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = [
         'state_id','name','deleted_at'
    ];

    
    public function states()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Models\Web\States','state_id','id');
    }
  
}
