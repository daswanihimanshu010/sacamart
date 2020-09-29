@extends('web.layout')
@section('content')
<!-- wishlist Content -->
<section class="wishlist-content my-4">
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
			<div class="col-12 col-lg-9 ">
					<div class="heading">
							<h2>
									@lang('website.Wishlist')
							</h2>
							<hr >
						</div>

					<div class="col-12 media-main">
						@foreach($result['products']['product_data'] as $key=>$products)
						<div class="product">
							<article>

								<div class="media">
									<img class="img-fluid" src="{{asset('').$products->image_path}}" alt="{{$products->products_name}}">
									<div class="media-body">
									  <div class="row">
										<div class="col-12 col-md-8  texting">
										  <h3 class="title"><a href="{{ URL::to('/product-detail/'.$products->products_slug)}}">{{$products->products_name}}</a></h3>
										  <?php												

										  if(!empty($products->discount_price)){
											  $discount_price = $products->discount_price * session('currency_value');
										  }
										  $orignal_price = $products->products_price * session('currency_value');
											  if(!empty($products->discount_price)){

												  if(($orignal_price+0)>0){
											  $discounted_price = $orignal_price-$discount_price;
											  $discount_percentage = $discounted_price/$orignal_price*100;
											  }else{
												  $discount_percentage = 0;
												  $discounted_price = 0;
										  }
										}
									   ?>

  
										  <div class="price"> @lang('website.Total Price'): 
											@if(!empty($products->discount_price))
												<span>{{Session::get('symbol_left')}}&nbsp;{{$discount_price+0}}&nbsp;{{Session::get('symbol_right')}}</span>
												<del> {{Session::get('symbol_left')}}{{$orignal_price+0}}{{Session::get('symbol_right')}}</del>
												@else
												<span>{{Session::get('symbol_left')}}&nbsp;{{$orignal_price+0}}&nbsp;{{Session::get('symbol_right')}}</span>
												@endif 
										   </div>
										  <div class="wishlist-discription">
											<?=stripslashes($products->products_description)?>
										  </div>
										 
										  <div class="buttons">
											@if($products->products_type==0)
											@if(!in_array($products->products_id,$result['cartArray']))
												@if($products->defaultStock==0)

													<button type="button" class="btn  btn-danger swipe-to-top" products_id="{{$products->products_id}}">@lang('website.Out of Stock')</button>
												@elseif($products->products_min_order>1)
												<a class="btn  btn-secondary swipe-to-top" href="{{ URL::to('/product-detail/'.$products->products_slug)}}">@lang('website.View Detail')</a>
												@else
													<button type="button" class="btn  btn-secondary cart swipe-to-top" products_id="{{$products->products_id}}">@lang('website.Add to Cart')</button>
												@endif
												@else
													<button type="button" class="btn btn-secondary active swipe-to-top">@lang('website.Added')</button>
												@endif
											@elseif($products->products_type==1)
												<a class="btn  btn-secondary swipe-to-top" href="{{ URL::to('/product-detail/'.$products->products_slug)}}">@lang('website.View Detail')</a>
											@elseif($products->products_type==2)
												<a href="{{$products->products_url}}" target="_blank" class="btn  btn-secondary swipe-to-top">@lang('website.External Link')</a>
											@endif
										  </div>
										</div>
										<div class="col-12 col-md-4 detail">
										  <div class="share"><a href="{{ URL::to("/UnlikeMyProduct")}}/{{$products->products_id}}">@lang('website.Remove') &nbsp;<i class="fas fa-trash-alt"></i></a> </div>
										</div>
										</div>
									</div>									
								</div>								
							</article>
						</div>
						@endforeach
					</div>
					<hr class="border-line">

				<!-- ............the end..... -->
			</div>
		</div>
	</div>
</section>
@endsection
