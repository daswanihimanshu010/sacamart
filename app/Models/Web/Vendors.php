<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
  
    protected $table = 'vendors';
    protected $primaryKey = 'id';
    protected $fillable = [
        'package_id', 'package_expiery_date', 'name', 'email', 'phone', 'password', 'country_id', 'state_id', 'user_id'
    ];

   /* public function students()
    {
        return $this->belongsToMany('App\Student', 'enrollments', 'academic_id','student_id'); 
    }

    public function category()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Http\Models\Osp_terms','category_id','id');
    }
    
    function product(){
        return $this->belongsTo('App\Models\Web\Products','vendor_id','products_id');
    }*/
}
