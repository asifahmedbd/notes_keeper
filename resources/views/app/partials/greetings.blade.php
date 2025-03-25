@if(isset($app_settings['app_greetings']) && $app_settings['app_greetings'] != NULL)
  <div class="row">
    <div class="col-md-12">
      <div class="card text-white bg-danger text-xs-center">
        <div class="card-body">
          <blockquote class="card-bodyquote text-center">
            <h3 class="text-white">{{ $app_settings['app_greetings'] }}</h3>
          </blockquote>
        </div>
      </div>
    </div>
  </div>
@endif
