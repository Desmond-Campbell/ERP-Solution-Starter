@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="secondary-text">{{ ___( "Companies" ) }}</span>
  <span class="push-right"><a href="/companies/new" class="btn btn-warning"><i class="fa fa-plus"></i> {{ ___('Create a Company') }}</a> &nbsp; &nbsp; </span>
</h1>

@stop

@section('content')
<div class="container" id="vCompanies">

  <div class="row">

    <div class="col-md-2">
    </div>

    <div class="col-md-8">

      @if( config( 'company' )->name ?? null )
      <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> {{ ___('You are currenty logged into')}} <strong>{{ config( 'company' )->name }}</strong>
      </div>
      @endif

      <div class="row">
  
        @foreach ( $companies as $c )

        <div class="col-md-6 push-down">
          <b-card title="{{ $c->name }}"
                  >
            <p class="card-text">
              {{ ___( "Created on" ) }} {{ date( 'F d, Y', strtotime( $c->created_at ) ) }}
            </p>
            <b-button href="/companies/{{ $c->id }}/switch??" variant="success">{{ ___( "Enter" ) }}</b-button>
            <b-button href="/companies/{{ $c->id }}/close" variant="secondary" v-b-popover.hover="'{{ ___( "You will get to confirm this." ) }}'">{{ ___( "Close" ) }}</b-button>
          </b-card>
        </div>

        @endforeach

        <div class="col-md-12 push-down">
          <b-card title=""
                  style="border: none;"
                  >
            <p class="card-text text-center">

              <a href="/companies/new" style="font-size: 1.1em"><i class="fa fa-plus-circle" style="font-size: 4.5em"></i><br />{{___('Add New')}}</a>
              
            </p>

          </b-card>
        </div>
      
      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')
  <script type="text/javascript">
    include( [ 'Core/Companies' ] );
  </script>
@stop
