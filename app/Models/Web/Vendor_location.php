<?php

namespace App;
namespace App\Models\Web;
use Illuminate\Database\Eloquent\Model;

class Vendor_location extends Model
{
  
    protected $table = 'vendors_location';
    protected $primaryKey = 'id';
    protected $fillable = [
        'vendor_id', 'location_name', 'shipping_fees', 'shipping_note', 'country_id', 'state_id', 'city_id', 'status', 'deleted_at'
    ];

   /* public function students()
    {
        return $this->belongsToMany('App\Student', 'enrollments', 'academic_id','student_id'); 
    }

    public function category()
    {
        //return $this->belongsTo('App\Job','job_category_id');
        return $this->belongsTo('App\Http\Models\Osp_terms','category_id','id');
    }*/
    
    function vendor(){
        return $this->belongsTo('App\Models\Web\Vendors','vendor_id','id');
    }
}
