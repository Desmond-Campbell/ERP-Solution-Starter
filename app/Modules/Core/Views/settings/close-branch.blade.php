@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="secondary-text text-danger"><strong>{{ ___( "Close Branch" ) }}</strong> &nbsp; &nbsp; <a href="{{ url('/settings/branches') }}" class="btn btn-warning"><i class="fa fa-arrow-alt-circle-left"></i> {{ ___('Go Back') }}</a></span>
</h1>

@stop

@section('content')

<div class="container" id="vCloseBranch" v-cloak>

  <div class="row">

    <div class="col-md-3">

    </div>

    <div class="col-md-6 text-center">

      <h1 class="page-title text-center text-lighter">{{ $branch_name }}</h1>

      <br />

      <div class="alert alert-danger">
        {{ ___( "Closing a branch entails temporarily deleting the associated employees and all data created and generated within that branch. The data will be held for 60 days and then permanently deleted. If you've deleted a branch, please contact Support to reactivate it." ) }}
      </div>

      <p>
      </p>

      <a href="{{ url('/settings/branches') }}" class="btn btn-info"><i class="fa fa-arrow-left"></i> {{___('Go Back')}}</a>

      &nbsp;

      <button type="button" class="btn btn-danger" @click="closeBranch({{ $branch_id }})"><i class="fa fa-times"></i> {{ ___( "Close" ) }} {{ $branch_name }}</button>
      
    </div>

  </div>

</div>

@endsection

@section('vue-modules')
  <script type="text/javascript">
    include( [ 'Core/CloseBranch' ] );
  </script>
@endsection
