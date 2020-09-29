<!DOCTYPE html>
<html class="no-js" lang="en">
  <head>
  @include('web.common.meta')
  <style type="text/css">
     .card {
     position: relative;
     display: -webkit-box;
     display: -ms-flexbox;
     display: flex;
     -webkit-box-orient: vertical;
     -webkit-box-direction: normal;
     -ms-flex-direction: column;
     flex-direction: column;
     min-width: 0;
     word-wrap: break-word;
     background-color: #fff;
     background-clip: border-box;
     border: 1px solid rgba(0, 0, 0, 0.1);
     border-radius: 0.10rem
    }

     .track {
     position: relative;
     background-color: #ddd;
     height: 7px;
     display: -webkit-box;
     display: -ms-flexbox;
     display: flex;
     margin-bottom: 60px;
     margin-top: 50px
     }

     .track .step {
         -webkit-box-flex: 1;
         -ms-flex-positive: 1;
         flex-grow: 1;
         width: 25%;
         margin-top: -18px;
         text-align: center;
         position: relative
     }

     .track .step.active:before {
         background: #12497c;
     }

     .track .step::before {
         height: 7px;
         position: absolute;
         content: "";
         width: 100%;
         left: 0;
         top: 18px
     }

     .track .step.active .icon {
         background: #12497c;
         color: #fff
     }

     .track .icon {
         display: inline-block;
         width: 40px;
         height: 40px;
         line-height: 40px;
         position: relative;
         border-radius: 100%;
         background: #ddd
     }

     .track .step.active .text {
         font-weight: 400;
         color: #000
     }

     .track .text {
         display: block;
         margin-top: 7px
     }

     .itemside {
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         width: 100%
     }

     .itemside .aside {
         position: relative;
         -ms-flex-negative: 0;
         flex-shrink: 0
     }

     .img-sm {
         width: 80px;
         height: 80px;
         padding: 7px
     }
  </style>
  </head>
    <!-- dir="rtl" -->
    <body class="animation-s<?php  echo $final_theme['transitions']; if(!empty(session('direction')) and session('direction')=='rtl') print ' bodyrtl';?> ">
      
      <div class="se-pre-con" id="loader" style="display: block">
        <div class="pre-loader">
          <div class="la-line-scale">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
          <p>@lang('website.Loading')..</p>
        </div>
     
      </div>

      @if (count($errors) > 0)
          @if($errors->any())
           <script>swal("Congrates!", "Thanks For Shopping!", "success");</script>
          @endif
      @endif
      
      <!-- Header Sections -->
      
        <!-- Top Offer -->
        <!-- <div class="header-area">
          <?php  echo $final_theme['top_offer']; ?>
        </div> -->

        

        
        <!-- End Top Offer -->
        
        <!-- Header Content -->
        <?php  echo $final_theme['header']; ?>        
        
        <!-- End Header Content -->       
        <?php  echo $final_theme['mobile_header']; ?>
      <!-- End of Header Sections -->
      
       <!-- NOTIFICATION CONTENT -->
         @include('web.common.notifications')
      <!-- END NOTIFICATION CONTENT -->
         @yield('content')



      <!-- Footer content -->
      <div class="notifications" id="notificationWishlist"></div>
      <?php  echo $final_theme['footer']; ?>

      <!-- End Footer content -->
      <?php  echo $final_theme['mobile_footer']; ?>
      @if(!empty($result['commonContent']['setting'][119]) and $result['commonContent']['setting'][119]->value==1)
      
        @if(empty(Cookie::get('cookies_data')))        

        <div class="alert alert-warning alert-dismissible alert-cookie fade show" role="alert">
          <div class="container">
              <div class="row align-items-center">
                  <div class="col-12 col-md-8 col-lg-9">
                      <div class="pro-description">
                          @lang('website.This site uses cookies. By continuing to browse the site you are agreeing to our use of cookies. Review our')
                          <a target="_blank" href="{{ URL::to('/page?name=cookies')}}" class="btn-link">@lang('website.cookies information')</a> 
                          
                          @lang('website.for more details').
                      </div>
                  </div>
                  <div class="col-12 col-md-4 col-lg-3">
                      <button type="button" class="btn btn-secondary swipe-to-top" id="allow-cookies">
                        @lang('website.OK, I agree')
                          </button>
                  </div>
              </div>
          </div>
        </div>
        @endif
      @endif

      <!-- Button trigger modal -->
      {{-- and empty(session('newsletter') --}}
      @if(!empty($result['commonContent']['setting'][118]) and $result['commonContent']['setting'][118]->value==1 and Request::path() == '/' ) 
      
    
       <!-- Newsletter Modal -->
       <div class="modal fade show" id="newsletterModal" tabindex="-1" role="dialog" aria-hidden="false">
       
       <div class="modal-dialog modal-dialog-centered modal-lg newsletter" role="document">
         <div class="modal-content">
             <div class="modal-body">

                 <div class="container">
                     <div class="row align-items-center">                   
                  
                     <div class="col-12 col-md-6" >
                        <div class="pro-image">
                          @if($result['commonContent']['setting'][124]->value)
                          <img class="img-fluid" src="{{asset('').$result['commonContent']['setting'][124]->value }}" alt="blogImage">  
                          @endif                        
                        </div>
                     </div>
                     <div class="col-12 col-md-6" style="padding-left: 0;">
                      <div class="promo-box">
                          <h2 class="text-01">                            
                            @lang('website.Sign Up for Our Newsletter')
                          </h2>
                          <p class="text-03">                            
                            @lang('website.Be the first to learn about our latest trends and get exclusive offers')
                          </p>
                            <form class=" mailchimp-form" action="{{url('subscribeMail')}}" >
                            <div class="form-group">
                              <input type="email" value="" name="email" class="required email form-control" placeholder="@lang('website.Enter Your Email Address')...">
                            </div>
                            <button type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn btn-secondary swipe-to-top newsletter">@lang('website.Subscribe')</button>
                          </form>
                      </div>
                   </div>
                   </div>
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">×</span>
                     </button>
                 </div>
               </div>
         </div>
       </div>
       </div>
       @endif


      <div class="mobile-overlay"></div>
      <!-- Product Modal -->


      <a href="web/#" id="back-to-top" class="btn-secondary swipe-to-top" title="@lang('website.back_to_top')">&uarr;</a>


      <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
      
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-body">
                  
                  <div class="container" id="products-detail">
                    
                  </div>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
          </div>
        </div>
    </div>

      <!-- Include js plugin -->
       @include('web.common.scripts')

<!-- State Modal -->
<div class="modal fade" id="stateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="/getProductsOfCity" accept-charset="UTF-8" id="jobForm" class="multiStepForm">
           {{ csrf_field() }}
      <div class="modal-body">
         <div class="row justify-content-center form-cotainer-top">
            <div class="col-md-12 col-md-offset-2 form-container-cu">
                <div class="form-group">   
                    <!--<label for="Country" class="form-label">County</label>       
                    <div class="search_inputs">
                        <select class="form-control" id="select_country" name="country"><option value="0">Select County</option><option value="101">India</option></select>
                    </div>-->
                    <!--<label for="State" class="form-label">State</label>-->
                    <span class="ajax_loader btn-btn-sub" style="position: absolute; left: 117px; display: none;">
                            <img src="https://wishwizz.com/public/uploads/loading_tra.gif" height="60" width="60">
                        </span>
                    <div class="search_inputs">
                        <select class="form-control" id="select_state" name="state"><option value="0" selected="selected">Select State..</option></select>
                    </div>
                    <!--<label for="City" class="form-label">City</label> -->      
                    <div class="search_inputs">
                        <select class="form-control" id="select_city" name="city"><option value="0" selected="selected">Select City..</option></select>
                    </div> 
                   <!-- <label for="Area" class="form-label">Area</label>   -->    
                    <div class="search_inputs">
                        <select class="form-control" id="select_area" name="area"><option value="0" selected="selected">Select Area / PIN..</option></select>
                    </div> 
                    <input name="rediret_url" type="hidden" value="getProductsOfCity">
                    <input id="longitude" type="hidden">
                    <input id="latitude" type="hidden">
                </div>
                <!--<input class="btn btn-success pull-right" type="submit" value="submit">-->
                
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success pull-right">Search Products</button>
      </div>
      <!-- </form>

      <form method="POST" action=" {{ url('/autoDetect') }}" accept-charset="UTF-8" id="jobForm1" class="multiStepForm">
      	{{ csrf_field() }}

      	<input name="rediret_url" type="hidden" value="autoDetect">

      	<div class="modal-footer">
        
        <button style="width: 100%" type="submit" class="btn btn-secondary">Auto Detect Location</button>
      </div>

      </form> -->
    </div>
  </div>
</div>
<input type="hidden" name="latitude" value="" class="latitude">
<input type="hidden" name="longitude" value="" class="longitude">
<script>

/*if('<?php echo @$_SESSION["your_area"]; ?>'==''){
  $("#stateModal").modal("show");
}*/
    var latitude = 0;
    var longitude = 0;
    $( document ).ready(function() {
    //$('#select_country').change(function () {
        if(navigator.geolocation)
            {
                	navigator.geolocation.getCurrentPosition(function(position){
                    latitude = position.coords.latitude;
                    longitude = position.coords.longitude;
                    showPosition(latitude,longitude);

                });

            }
    });
    
    $(window).scroll(function() {
      if($(window).scrollTop() + $(window).height() >= $(document).height()){
         //Your code here
      }
    });



    function showPosition(latitude,longitude) {
	    var country_id = 101;//$(this).val();
	        
	        var thisis = $(this); 

	        thisis.parent().parent().find('.ajax_loader').show();
	        jQuery.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
	        	}
	   		});
	        $.ajax({
	            url: "/getState",
	            dataType: 'json',
	            method: 'post',
	            data: {'country_id': country_id, 'latitude':latitude, 'longitude':longitude},
	            success: function (data) {
	            	
	                if (data.status == true) {
	                    
	                    $('#select_state').children().remove('option');
	                    $('#select_state').remove('option');
	                    //if(data.main.length > 0){    
	                    $('#select_state').append('<option value="">Please Select State</option>');
	                    $.each(data.data, function (i, item) {

	                    	/*if (selected_state_id) {
	                    		$('#select_state').append('<option value="' + item.id + '" label="' + item.name + '"></option>');
	                    		$('#select_state option[value="'+selected_state_id+'"]').attr("selected", "selected");
	                    		showCity(selected_state_id);
	                    	} else {
	                    		$('#select_state').append('<option value="' + item.id + '" label="' + item.name + '"></option>');
	                    	}*/

	                    	$('#select_state').append('<option value="' + item.id + '" label="' + item.name + '"></option>');
							
	                    });
	                } else {
	                    $('#select_state').append('<option selected>No State Are Avaliable Here</option>');
	                }
	            },
	            complete: function () {
	                thisis.parent().parent().find('.ajax_loader').hide();
	            }
	        });
      } 

      function showPositions(){
        navigator.geolocation.getCurrentPosition(function(position){
                    latitude = position.coords.latitude;
                    longitude = position.coords.longitude;
                    showPosition(latitude,longitude);

                });
         $("#stateModal").modal("show");
       // alert(longitude);
        
      }

     /*****Get State*******/
     	
     
        /*****Get City*******/
        $('#select_state').on('change', function() {
            var state_id = $(this).val();
            
            var thisis = $(this); 
            thisis.parent().parent().find('.ajax_loader').show();
            jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
            $.ajax({
                url: "/getCity",
                dataType: 'json',
                method: 'post',
                data: {'state_id': state_id},
                success: function (data) {
                	
                    if (data.status == true) {
                        $('#select_city').children().remove('option');
                        $('#select_city').remove('option');
                        //if(data.main.length > 0){    
                        $('#select_city').append('<option value="">Please Select City</option>');
                        $.each(data.data, function (i, item) {

                            $('#select_city').append('<option value="' + item.id + '" label="' + item.name + '"></option>');        
                            /*if (selected_city_id) {
	                    		$('#select_city').append('<option value="' + item.id + '" label="' + item.name + '"></option>');
	                    		$('#select_city option[value="'+selected_city_id+'"]').attr("selected", "selected");
	                    	} else {
	                    		$('#select_city').append('<option value="' + item.id + '" label="' + item.name + '"></option>');
	                    	}*/
                        });
                    } else {
                        $('#select_city').append('<option selected>No City Are Avaliable Here</option>');
                    }
                },
                complete: function () {
                    thisis.parent().parent().find('.ajax_loader').hide();
                }
            });
        });
        /*****Get City*******/
        $('#select_city').on('change', function() {
            var city_id = $(this).val();
            var thisis = $(this); 
            thisis.parent().parent().find('.ajax_loader').show();
            jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
            $.ajax({
                url: "/getArea",
                dataType: 'json',
                method: 'post',
                data: {'city_id': city_id},
                success: function (data) {
                    if (data.status == true) {
                        $('#select_area').children().remove('option');
                        $('#select_area').remove('option');
                        //if(data.main.length > 0){    
                        $('#select_area').append('<option value="">Please Select Area / PIN</option>');
                        $.each(data.data, function (i, item) {
                                    $('#select_area').append('<option value="' + item.id + '" label="' + item.location + '"></option>');
                        });
                    } else {
                        $('#select_area').append('<option selected>No City Are Avaliable Here</option>');
                    }
                },
                complete: function () {
                    thisis.parent().parent().find('.ajax_loader').hide();
                }
            });
        });
</script>
    </body>
</html>
