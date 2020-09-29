@extends('web.layout')
@section('content')
<!-- login Content -->
<div class="container-fuild">
	<nav aria-label="breadcrumb">
		<div class="container">
			<ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="{{ URL::to('/')}}">@lang('website.Home')</a></li>
			  <li class="breadcrumb-item active" aria-current="page">@lang('website.Login')</li>

			</ol>
		</div>
	  </nav>
  </div> 

<section class="page-area pro-content">
	<div class="container">

		@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif
			<div class="row">
				
				<div class="col-12 col-sm-12 col-md-6">
						<div class="col-12"><h4 class="heading login-heading">NEW VENDOR</h4></div>
						<div class="registration-process">
							@if( count($errors) > 0)
								@foreach($errors->all() as $error)
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
										<span class="sr-only">@lang('website.Error'):</span>
										{{ $error }}
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								 @endforeach
							@endif

							@if(Session::has('error'))
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
									<span class="sr-only">@lang('website.Error'):</span>
									{!! session('error') !!}

									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							@endif

							<form name="signup" enctype="multipart/form-data"  action="{{ URL::to('/storevendor')}}" method="post">
								{{csrf_field()}}
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Full Name')</label></div>
									<div class="input-group col-12">
										<input  name="fullname" type="text" class="form-control field-validate" id="fullname" placeholder="@lang('Please Full name')" value="{{ old('fullname') }}" required>
										<span class="help-block" hidden>@lang('Please Full name')</span>
									</div>
								</div>
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Email Address')</label></div>
									<div class="input-group col-12">
										<input  name="email" type="email" class="form-control field-validate" id="email" placeholder="@lang('Please Email Address')" value="{{ old('email') }}" required>
										<span class="help-block" hidden>@lang('Please Email Address')</span>
									</div>
								</div>
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Phone Number')</label></div>
									<div class="input-group col-12">
										<input  name="phonenumber" type="number" class="form-control field-validate" id="phonenumber" placeholder="@lang('Please Enter Phone Number')" value="{{ old('phonenumber') }}" required>
										<span class="help-block" hidden>@lang('Please Enter Phone Number')</span>
									</div>
								</div>
                                
                                 <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>{{ trans('Password') }}</label></div>
									<div class="input-group col-12">
										<input  name="password" type="password" class="form-control field-validate" id="password" placeholder="@lang('Please Enter Password')" value="{{ old('password') }}" required>
										<span class="help-block" hidden>@lang('Please Enter Password')</span>
									</div>
								</div>
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>{{ trans('Country Name') }}</label></div>
									<div class="input-group col-12">
										 <select name="country_id" class="form-control" required>
                                             <option value="">--SELECT Country--</option>
                                            @foreach ($countrylist as $key=>$countrylist)
                                             <option value="{{ $countrylist->id }}">{{ $countrylist->name }}</option>
                                            @endforeach
                                         </select>
										<span class="help-block" hidden>@lang('Please select country')</span>
									</div>
								</div>
                                
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>{{ trans('Select State') }}</label></div>
									<div class="input-group col-12">
										 <select name="state_id" class="form-control state" required>
                                            <option value="">--SELECT State--</option>
                                            @foreach ($statelist as $key=>$statelist)
                                             <option value="{{ $statelist->id }}">{{ $statelist->name }}</option>
                                            @endforeach
                                         </select>
										<span class="help-block" hidden>@lang('Please select sate')</span>
									</div>
								</div>
                                <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>{{ trans('Select City') }}</label></div>
									<div class="input-group col-12">
										<select name="city_id" class="form-control city" required>
                                                     <option value="">--Select City--</option>
                                                     <option value="3378">Jaipur</option>
                                                     </select>
										<span class="help-block" hidden>@lang('Please select city')</span>
									</div>
								</div>
								<div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>Pincode</label></div>
									<div class="input-group col-12">
										<input  name="pincode" type="number" class="form-control field-validate" id="pincode" placeholder="Please enter pincode" required>
										<span class="help-block" hidden>Please enter pincode</span>
									</div>
								</div>
                            </div>
                            </div>
                     <div class="col-12 col-sm-12 col-md-6">
						<div class="col-12"><h4 class="heading login-heading">Business Details</h4></div>
						<div class="registration-process">
                               
                                 <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Name of Business')</label></div>
									<div class="input-group col-12">
										<input  name="nameofbusiness" type="text" class="form-control field-validate" id="nameofbusiness" placeholder="@lang('Please Enter Name of Business')" value="{{ old('nameofbusiness') }}" required>
										<span class="help-block" hidden>@lang('Please Enter Name of Business')</span>
									</div>
								</div>
                                
                                 <div class="from-group mb-3">
									<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Detailed Address of Business')</label></div>
									<div class="input-group col-12">
                                        <textarea name="businessaddress" type="text" class="form-control field-validate" id="businessaddress" placeholder="@lang('Please Enter Business Address')" required>{{ old('businessaddress') }}</textarea>
										<span class="help-block" hidden>@lang('Please Enter Name of Business')</span>
									</div>
								</div>
                                
                                <div class="from-group mb-3">
                                        <div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>Servable Areas where he can deliver</label></div>
                                         <input type="hidden" name="selectedareas" value="" id="selectedareas">
                                        <div class="displaycity displayarea">
                                            
                                        </div>
                                         <span class="help-block" hidden>@lang('Please Select city')</span>
                                    </div>
                     
                                 
								
								
									<div class="from-group mb-3">
										<div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>@lang('Category')</label></div>
										<div class="input-group col-12">
                                            <?php print_r($result['categories']);?> 
<!--
                                            @foreach($category as $singlecategory)
                                          <div class="col-md-4">
											<input  name="category[]" type="checkbox" id="inlineFormInputGroup" value="{{ $singlecategory->categories_id }}">
                                                {{ $singlecategory->categories_name }}
                                            </div>
                                            @endforeach
-->
											<span class="help-block" hidden>@lang('Please enter your valid email address')</span>
										</div>
									</div>
									
                                    <div class="from-group mb-3">
                                        <div class="col-12"> <label for="inlineFormInputGroup"><strong style="color: red;">*</strong>Verification Documents</label></div>
                                        <div class="input-group col-12">										
                                           <input type="file" name="doucmument[]" multiple required>
                                            <small>Verification Documents (Upto 5 Images/PDFs can be uploaded)</small>
                                            <span class="help-block" hidden>@lang('Please enter your phone number')</span>
                                        </div>
                                    </div>
									
										
										
											<div class="from-group mb-3">
													<div class="input-group col-12">
														<input required style="margin:4px;"class="form-controlt checkbox-validate" type="checkbox">
														@lang('Creating an account means you are okay with our')  @if(!empty($result['commonContent']['pages'][3]->slug))&nbsp;<a href="{{ URL::to('/page?name='.$result['commonContent']['pages'][3]->slug)}}">@endif @lang('Terms and Services')@if(!empty($result['commonContent']['pages'][3]->slug))</a>@endif, @if(!empty($result['commonContent']['pages'][1]->slug))<a href="{{ URL::to('/page?name='.$result['commonContent']['pages'][1]->slug)}}">@endif @lang('Privacy Policy')@if(!empty($result['commonContent']['pages'][1]->slug))</a> @endif &nbsp; and &nbsp; @if(!empty($result['commonContent']['pages'][2]->slug))<a href="{{ URL::to('/page?name='.$result['commonContent']['pages'][2]->slug)}}">@endif @lang('Refund Policy') @if(!empty($result['commonContent']['pages'][3]->slug))</a>@endif.
														<span class="help-block" hidden>@lang('Please accept our terms and conditions')</span>
													</div>
												</div>
										<div class="col-12 col-sm-12">
												<button type="submit" class="btn btn-light swipe-to-top">@lang('Create an Account')</button>

										</div>
							</form>
						</div>
				</div>
				<div class="col-12 col-sm-12 my-5">
						<div class="registration-socials">
					<div class="row align-items-center justify-content-between">
									<div class="col-12 col-sm-6">
										@lang('Access Your Account Through Your Social Networks')
									</div>
									<div class="col-12 col-sm-6 right">

											@if($result['commonContent']['setting'][61]->value==1)
												<a href="login/google" type="button" class="btn btn-google"><i class="fab fa-google-plus-g"></i>&nbsp; @lang('Google') </a>
											@endif
											@if($result['commonContent']['setting'][2]->value==1)
												<a  href="login/facebook" type="button" class="btn btn-facebook"><i class="fab fa-facebook-f"></i>&nbsp;@lang('Facebook')</a>
											@endif
									</div>
							</div>
					</div>
				</div>
			</div>

	</div>
</section>
<script>
function propchecked(parents_id){
	//alert(parents_id);
	$('#categories_'+parents_id).prop('checked', true);
	var parent_id = $('#categories_'+parents_id).attr('parents_id');
	if(parents_id !== undefined){
		//call nested function
		propchecked(parent_id);
	}
}

function propunchecked(parents_id){
	$('.sub_categories_'+parents_id).prop('checked', false);
	$('.sub_categories_'+parents_id).each(function() {
		var subparents_id = $(this).attr('id');
		var subparents_id = subparents_id.replace("categories_", "");
		propunchecked(subparents_id);
	});
}

// check sub categories
$(document).on('click', '.sub_categories', function(){

	if($(this).is(':checked')){
		var parents_id = $(this).attr('parents_id');
		if(parents_id !== undefined){
			propchecked(parents_id);
		}
	}else{
		var parents_id = $(this).attr('id');
		if(parents_id !== undefined){
			var parents_id = parents_id.replace("categories_", "");
			propunchecked(parents_id);
		}
	}

});

</script>

 @endsection
