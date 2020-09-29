<!-- contact Content -->

<div class="container-fuild">
  <nav aria-label="breadcrumb">
      <div class="container">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ URL::to('/')}}">@lang('website.Home')</a></li>
            <li class="breadcrumb-item active" aria-current="page">@lang('website.Contact Us')</li>
          </ol>
      </div>
    </nav>
</div> 

<section class="pro-content">
        
  <div class="container">
    <div class="page-heading-title">
        <h2> @lang('website.Contact Us') 
        </h2>
     
        </div>
</div>

<section class="contact-content">
  <div class="container"> 
    <div class="row">
      <div class="col-12 col-sm-12">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="form-start">
                  
                  @if(session()->has('success') )
                     <div class="alert alert-success">
                         {{ session()->get('success') }}
                     </div>
                  @endif

                  <form enctype="multipart/form-data" action="{{ URL::to('/processContactUs')}}" method="post">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                      <label class="first-label" for="email">@lang('website.Full Name')</label>
                      <div class="input-group"> 
                        
                        <input type="text" class="form-control" id="name" name="name" placeholder="@lang('website.Please enter your name')" aria-describedby="inputGroupPrepend" required>
                        <div class="help-block error-content invalid-feedback" hidden>@lang('website.Please enter your name')</div>
                      
                      </div>
                      <label for="email">@lang('website.Email')</label>
                      <div class="input-group">                     
                          <input type="email"  name="email" class="form-control" id="validationCustomUsername" placeholder="@lang('website.Enter Email here').." aria-describedby="inputGroupPrepend" required>
                          <div class="help-block error-content invalid-feedback" hidden>@lang('website.Please enter your valid email address')</div>
                      </div> 
                      <label for="mobile">Phone Number</label>
                      <div class="input-group">                     
                          <input type="number"  name="mobile" class="form-control" id="validationCustomUsername" placeholder="Enter Mobile Number.." aria-describedby="inputGroupPrepend">
                          <div class="help-block error-content invalid-feedback" hidden>Please enter a valid Phone Number</div>
                      </div>  
                      <label for="email">@lang('website.Message')</label>
                      <textarea type="text" name="message"  placeholder="@lang('website.write your message here')..." rows="5" cols="56"></textarea>
                      <div class="help-block error-content invalid-feedback" hidden>@lang('website.Please enter your message')</div>

                      <button type="submit" class="btn btn-secondary swipe-to-top">@lang('website.Submit') <i class="fas fa-location-arrow"></i>                 
                     
                    </form>
                </div>
          </div>     
        
          <!-- <div class="col-12 col-lg-5">
                <div id="map" style="height:400px; margin-top: 5px;">
                  
                </div>
                <script>
                  var map;
                  function initMap() {
                    map = new google.maps.Map(document.getElementById('map'), {
                      center: {lat: -34.397, lng: 150.644},
                      zoom: 8
                    });
                  }
                </script>
                @if($result['commonContent']['setting'][62]->value)
                <script src="https://maps.googleapis.com/maps/api/js?key=".{{$result['commonContent']['setting'][62]->value}}."&callback=initMap"
                async defer></script>
                 @endif
                
          </div> --> 
          <div class="col-12 col-lg-5">

          <div class="info-boxes-content">
          <div class="col-12 col-md-12">
              <div class="info-box first">
                  <div class="panel">
                      <h3 class="fas fa-truck"></h3>
                      <div class="block">
                          <h4 class="title">@lang('website.bannerLabel1')</h4>
                          <p>@lang('website.bannerLabel1Text')</p>
                      </div>
                  </div>
              </div>
          </div>
          </div>
            
            <br/>

          <div class="info-boxes-content">
          <div class="col-12 col-md-12">
              <div class="info-box first">
                  <div class="panel">
                        <h3 class="fas fa-money-bill-alt"></h3>
                        <div class="block">
                            <h4 class="title">@lang('website.bannerLabel2')</h4>
                            <p>@lang('website.bannerLabel2Text')</p>
                        </div>
                  </div>
              </div>
          </div>
          </div>

          <br/>
          <div class="info-boxes-content">
            <div class="col-12 col-md-12">
              <div class="info-box">
                  <div class="panel">
                      <h3 class="fas fa-life-ring"></h3>
                      <div class="block">
                          <h4 class="title">@lang('website.bannerLabel3')</h4>
                          <p>@lang('website.hotline')&nbsp;:&nbsp;({{$result['commonContent']['setting'][11]->value}})</p>
                      </div>
                  </div>
              </div>
            </div>
          </div>

          <br/>
          <div class="info-boxes-content">
            <div class="col-12 col-md-12">
                <div class="info-box last">
                    <div class="panel">
                        <h3 class="fas fa-credit-card"></h3>
                        <div class="block">
                            <h4 class="title">@lang('website.bannerLabel4')</h4>
                            <p>@lang('website.bannerLabel4Text')</p>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          

          </div>
          <div class="col-12 col-lg-3">
             
              <div class="">
                  <ul class="contact-info pl-0 mb-0"  >
                      <li> <i class="fas fa-mobile-alt"></i><span><a href="#">{{$result['commonContent']['setting'][11]->value}}</a></span> </li>
                      
                      <li> <i class="fas fa-envelope"></i><span> <a href="mailto:{{$result['commonContent']['setting'][3]->value}}">{{$result['commonContent']['setting'][3]->value}}</a> </span> </li>
                      
                 
                    </ul>         
                </div>
        
          </div>
        
        </div>
      </div>
    </div>
    
  </div>      
</section>

</section>