@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="secondary-text">{{ ___( "Settings Portal" ) }}</span>
</h1>

@stop

@section('content')
<div class="container" id="vSettings" v-cloak>

  <div class="row">

    <div class="col-md-12">

      <div class="row push-down">

        <div class="col-md-3">

          <div class="side-menu">
            <nav class="nav flex-column">
              
              {!! admin_links_side() !!}   

            </nav>   
          </div>

        </div>

        <div class="col-md-9">

          <div class="tab-content">
            
            {!! admin_links() !!}

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')

  <script type="text/javascript">
    include( [ 'Core/Settings' ] );
  </script>

@endsection
