<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
  
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'role_id', 'username', 'first_name'
    ];

    
    public function category()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Http\Models\Osp_terms','category_id','id');
    }
}
