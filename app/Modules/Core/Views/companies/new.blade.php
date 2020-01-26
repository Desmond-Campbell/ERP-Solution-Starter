@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="secondary-text">{{ ___( "New Company" ) }} <a href="/companies" class="btn btn-warning push-right">{{ ___('View Companies') }} <i class="fa fa-arrow-alt-circle-right"></i></a></span>
</h1>

@stop

@section('content')
<div class="container" id="vNewCompany" v-cloak>

  <div class="row">

    <div class="col-md-3">
    </div>

    <div class="col-md-6">

      <div class="form-container bg-white">
  
        <h3 class="form-heading">{{ ___( "What's the name of your new company?" ) }}</h3>

        <div>

          <form role="form" @submit.prevent="createCompany()">
            
            <div class="form-group push-down">
              <input type="text" class="form-control input-lg" id="company_name" v-model="name" @enter="createCompany()" />
            </div>

            <button type="button" class="btn btn-success btn-lg" @click="createCompany()">{{ ___( "Create" ) }}</button>

          </form>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')
<script type="text/javascript">
  include( [ 'Core/NewCompany' ] );
</script>
@endsection
