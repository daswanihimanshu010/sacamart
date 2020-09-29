<!-- //header style Ten -->
@include('web.headers.fixedHeader') 
<header id="headerTen" class="header-area header-ten  header-desktop d-none d-lg-block d-xl-block">
  <div class="header-mini bg-top-bar" style="background-color: #f69916;">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12">
          
          <nav id="navbar_0_6" class="navbar navbar-expand-md navbar-dark navbar-0">
            <div class="navbar-lang">
              @if(count($languages) > 1)

              <div class="country-flag">
                <h4>
                  <span>
                    <ul>
                      @foreach($languages as $language)
                      <li><a onclick="myFunction1({{$language->languages_id}})" href="#"><img class="img-fluid" src="{{asset('').$language->image_path}}"></a></li>
                      @endforeach 
                      
                    </ul>
                  </span>
                </h4>  
              </div> 

              @include('web.common.scripts.changeLanguage')
              @endif
              @if(count($currencies) > 1)
                <div class="dropdown">
                  <button class="btn dropdown-toggle" type="button" >
                    {{session('currency_code')}}
                  </button>
                  <div class="dropdown-menu">
                    @foreach($currencies as $currency)
                    <a onclick="myFunction2({{$currency->id}})" class="dropdown-item" href="#">
                      <span>{{$currency->code}}</span>   
                    </a>
                    @endforeach
                  </div>
                </div>
                @include('web.common.scripts.changeCurrency')
              @endif
            </div>                   
            
            <div class="navbar-collapse">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <div class="nav-avatar nav-link">
                    <div class="avatar">
                      <?php
                      if(auth()->guard('customer')->check()){
                       if(auth()->guard('customer')->user()->avatar == null){ ?>
                        <img class="img-fluid" src="{{asset('web/images/miscellaneous/avatar.jpg')}}">
                      <?php }else{ ?>
                        <img class="img-fluid" src="{{auth()->guard('customer')->user()->avatar}}">
                      <?php
                            }
                         }
                      ?>
                      </div>
                      <span><?php if(auth()->guard('customer')->check()){ ?>@lang('website.Welcome') {{auth()->guard('customer')->user()->first_name}}&nbsp;! <?php }?> </span>
                  </div>
                </li>
                <?php if(auth()->guard('customer')->check()){ ?>
                  <li class="nav-item"> <a href="{{url('profile')}}" class="nav-link">@lang('website.Profile')</a> </li>
                  <li class="nav-item"> <a href="{{url('wishlist')}}" class="nav-link">@lang('website.Wishlist')<span class="total_wishlist"> ({{$result['commonContent']['total_wishlist']}})</span></a> </li>
                  <li class="nav-item"> <a href="{{url('compare')}}" class="nav-link">@lang('website.Compare')&nbsp;(<span id="compare">{{$count}}</span>)</a> </li>
                  <li class="nav-item"> <a href="{{url('orders')}}" class="nav-link">@lang('website.Orders')</a> </li>
                  <li class="nav-item"> <a href="{{url('shipping-address')}}" class="nav-link">@lang('website.Shipping Address')</a> </li>
                  <li class="nav-item"> <a href="{{url('logout')}}" class="nav-link">@lang('website.Logout')</a> </li>
                  <?php }else{ ?>
                    <li class="nav-item"><div style="color: white;"class="nav-link">@lang('website.Welcome')!</div></li>
                    <li class="nav-item"> <a style="color: white;" href="{{ URL::to('/login')}}" class="nav-link -before"><i class="fa fa-lock" aria-hidden="true"></i>&nbsp;@lang('website.Login/Register')</a> </li>    
                    <!-- <li class="nav-item"> <a href="{{ URL::to('/')}}/vendor-registration" class="nav-link -before"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Vendor Registration</a> </li>       -->                
                  <?php } ?>
              </ul> 
            </div>   
          </nav>
        </div>
      </div>
    </div> 
  </div>
  <div class="header-maxi bg-header-bar">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12 col-md-12 col-lg-3">
          <a href="{{ URL::to('/')}}" class="logo">
            @if($result['commonContent']['settings']['sitename_logo']=='name')
            <?=stripslashes($result['commonContent']['setting'][78]->value)?>
            @endif
  
            @if($result['commonContent']['settings']['sitename_logo']=='logo')
            <img src="{{asset('').$result['commonContent']['setting'][15]->value}}" alt="<?=stripslashes($result['commonContent']['setting'][79]->value)?>">
            @endif
          </a>
        </div>
        
            <div class="col-12 col-sm-6">
            
              <form class="form-inline" action="{{ URL::to('/shop')}}" method="get">   
                <div class="search-field-module">   
                    <input type="hidden" name="category" class="category-value" value="">
                    @include('web.common.HeaderCategories')
                  <button style="display: none;" class="btn btn-secondary swipe-to-top dropdown-toggle header-selection" type="button" id="headerOneCartButton"  
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                    data-toggle="tooltip" data-placement="bottom" title="@lang("website.Choose Any Category")"> 
                    @lang("website.Choose Any Category")
                  </button> 
                  <div  style="display: none;" class="dropdown-menu dropdown-menu-right" aria-labelledby="headerOneCartButton">   
                      @php    productCategories(); @endphp                                                                 
                  </div>
                  <div style="float: left;" class="search-field-wrap">
                      <input  type="search" name="search" placeholder="@lang('website.Search entire store here')..." data-toggle="tooltip" data-placement="bottom" title="@lang('website.Search Products')" alue="{{ app('request')->input('search') }}">
                      <button class="btn btn-secondary swipe-to-top" data-toggle="tooltip" 
                      data-placement="bottom" title="@lang('website.Search Products')">
                      <i class="fa fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>
          <div class="col-6 col-sm-6 col-md-4 col-lg-3">
           <ul class="pro-header-right-options">
            <li>
              <a href="{{ URL::to('/wishlist')}}" class="btn" data-toggle="tooltip" data-placement="bottom" title="@lang('website.Wishlist')">
                <i class="fas fa-heart"></i>
                <span class="badge badge-secondary total_wishlist">{{$result['commonContent']['total_wishlist']}}</span>
              </a>
            </li>
            <li class="dropdown head-cart-content">
              @include('web.headers.cartButtons.cartButton6')
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div> 
      <div class="header-navbar bg-menu-bar">
          <div class="container">
            <nav id="navbar_header_9" class="navbar navbar-expand-lg  bg-nav-bar">
        
              <div class="navbar-collapse" >
                <ul class="navbar-nav"><?php session_start(); ?>
                  {!! $result['commonContent']["menusRecursive"] !!}
                   <li class="nav-item " style="padding: 13px;">
                          <div id="result">
                                <!--Position information will be inserted here-->
                            </div>
                            @inject('shipping', 'App\Http\Controllers\Web\CartController')
                            <!-- <?php if(@$_SESSION["your_area"]!=''){ ?>
                            <button type="button" class="areaselect"> 
                            <?php $shipping->current_state(@$_SESSION["your_state"]);  ?>
                            </button>
                            <button type="button" class="areaselect"> 
                            <?php $shipping->current_city(@$_SESSION["your_city"]);  ?>
                            </button>
                            <button type="button" class="areaselect"><i class="fa fa-location-arrow"></i> 
                            <?php $shipping->current_location(@$_SESSION["your_area"]);  ?>
                            </button>
                            <a title="Change Location" alt="Change Location" style="color: white;"href="{{ URL::to('/reset_location') }}"> X </a>
                            <?php }else{ ?>
                            <button type="button" data-original-title="Current Location" class="areaselect" onclick="showPositions();"><i class="fa fa-location-arrow"></i> Locate Me</button>
                            <?php } ?> -->
                            
                    </li> 
                  <!--<li class="nav-item ">
                    <a class="nav-link">
                        <span style="color: #fff;">@lang('website.Call Us Now')</span>
                        <phone dir="ltr">{{$result['commonContent']['setting'][11]->value}}</phone>
                    </a>
                  </li> -->    
                </ul>
              </div>
            </nav>
          </div>
      </div>
</header>