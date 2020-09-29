<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'states';
    protected $primaryKey = 'id';
    protected $fillable = [
        'country_id', 'name', 'deleted_at'
    ];

    
    public function category()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Http\Models\Osp_terms','category_id','id');
    }
}
