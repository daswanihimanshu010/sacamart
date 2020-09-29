<section class="new-products-content pro-content" >
  <div class="container">
    <div class="products-area">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> Top Sellers of the Week

            </h2>
            <p>
              View Our top sellers</p>
          </div>
        </div>
      </div>
      <div class="row ">  
        
          @if($result['weeklySoldProducts']['success']==1)
            @foreach($result['weeklySoldProducts']['product_data'] as $key=>$products)
            
            @if($key<=6)

          <div class="col-12 col-sm-6 col-lg-3">
            @include('web.common.vendor')
          </div>  
          @endif
          
          @endforeach
          @endif
   
      </div>
    </div>
  </div>  
</section>

