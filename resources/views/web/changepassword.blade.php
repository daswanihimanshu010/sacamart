@extends('web.layout')
@section('content')

<div class="container-fuild">
  <nav aria-label="breadcrumb">
    <div class="container">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ URL::to('/')}}">@lang('website.Home')</a></li>
        <li class="breadcrumb-item"><a href="{{ URL::to('/profile')}}">@lang('website.Profile')</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ URL::to('/change-password')}}">@lang('website.Change Password')</a></li>
      </ol>
    </div>
  </nav>
</div> 

<!-- page Content -->
<section class="page-area pro-content">
  <div class="container"> 
    <h2>Profile Detail</h2><br/><br/>
    <div class="col-12 media-main" style="margin-bottom: 30px;">
        <div class="media" style="display: flex;
                  align-items: center;
                  background-color: white;
                  padding: 20px;">
            <img src="{{ URL::to('/')}}/public/web/images/miscellaneous/avatar.jpg" alt="avatar" style="width: 50px;
    height: 50px;
    border: 1px solid #ddd;
    border-radius: 200px;">
            <div class="media-body" style="margin-left: 15px;">
              <div class="row">
                <div class="col-12 col-sm-4 col-md-6">
                  <h4>{{auth()->guard('customer')->user()->first_name}} {{auth()->guard('customer')->user()->last_name}}<br>
                  <small>@lang('website.Phone'): {{ auth()->guard('customer')->user()->phone }} </small></h4>
                </div>
                <div class="col-12 col-sm-8 col-md-6 detail">                  
                  <p class="mb-0">@lang('website.E-mail'):<span style="display: block;
    font-size: 0.875rem;
    font-family: "Montserrat-Bold", sans-serif;">{{auth()->guard('customer')->user()->email}}</span></p>
                </div>
                </div>
            </div>
            
        </div>
      </div>
      <div class="row">

          <div class="col-12 col-lg-3">
           <div class="heading">
               <h2>
                   @lang('website.My Account')
               </h2>
               <hr >
             </div>

           <ul class="list-group">
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/profile')}}">
                       <i class="fas fa-user"></i>
                     @lang('website.Profile')
                   </a>
               </li>
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/wishlist')}}">
                       <i class="fas fa-heart"></i>
                    @lang('website.Wishlist')
                   </a>
               </li>
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/orders')}}">
                       <i class="fas fa-shopping-cart"></i>
                     @lang('website.Orders')
                   </a>
               </li>
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/shipping-address')}}">
                       <i class="fas fa-map-marker-alt"></i>
                    @lang('website.Shipping Address')
                   </a>
               </li>
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/logout')}}">
                       <i class="fas fa-power-off"></i>
                     @lang('website.Logout')
                   </a>
               </li>
               <li class="list-group-item">
                   <a class="nav-link" href="{{ URL::to('/change-password')}}">
                       <i class="fas fa-unlock-alt"></i>
                     @lang('website.Change Password')
                   </a>
               </li>
             </ul>
            </div>
            
          <div class="col-12 col-lg-9">
              
             <h5>@lang('website.Change Password')</h5>
             

             <hr style="margin-bottom: 0;">
                <div class="tab-content" id="registerTabContent">
                  @if(session()->has('success') )
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session()->get('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                  @endif

                  @if(session()->has('error') )
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session()->get('error') }}
                    </div>
                  @endif              

                
                  
                  <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                      <div class="registration-process">
                      <form name="updateMyPassword" id="updateMyPassword" enctype="multipart/form-data" action="{{ URL::to('/updateMyPassword')}}" method="post">
                        @csrf

                        <div class="from-group mb-3">
                          <div class="col-12"> <label for="current_password">@lang('website.Current Password')</label></div>
                          <div class="input-group col-12">                          
                            <input name="current_password" type="password" class="form-control" id="current_password" placeholder="@lang('website.Current Password')">
                            <span class="help-block error-content" hidden>@lang('website.Please enter current password')</span>
                          </div>
                        </div>
                        
                        <div class="from-group mb-3">
                          <div class="col-12"> <label for="password">@lang('website.New Password')</label></div>
                          <div class="input-group col-12">                             
                            <input name="new_password" type="password" class="form-control" id="new_password" placeholder="@lang('website.New Password')">
                            <span class="help-block error-content" hidden>@lang('website.Please enter your password and should be at least 6 characters long')</span>
                          </div>
                        </div>

                        <div class="from-group mb-3">
                          <div class="col-12"> <label for="confirm_password">@lang('website.Confirm Password')</label></div>
                          <div class="input-group col-12">                             
                            <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="@lang('website.New Password')">
                            <span class="help-block error-content" hidden>@lang('website.Please enter your password and should be at least 6 characters long')</span>
                          </div>
                        </div>

                        <div class="alert alert-danger fade show" hidden id="passowrd-error" role="alert">
                        </div>

                          <div class="col-12 col-sm-12">
                              <button type="submit" class="btn btn-secondary">@lang('website.Update')</button>                            
                          </div>
                      </form>
                      </div>
                      
                  </div>
                </div>
          </div>
        

      </div>
  </div>
</section>
 @endsection
