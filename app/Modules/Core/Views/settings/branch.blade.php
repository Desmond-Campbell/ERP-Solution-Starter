@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "Branches" ) }}</span>
  <span class="push-right"><a href="javascript:void(0);" class="btn btn-warning" onclick="Branches.editBranch(-1)"><i class="fa fa-plus"></i> {{ ___('Create a Branch') }}</a> &nbsp; &nbsp; </span>
</h1>

@stop

@section('content')
<div class="container" id="vBranches" v-cloak>

  @if ( !get_branch_id() )

  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 alert alert-danger text-center">
      {{___('You are not connected to a branch. Please enter a branch to continue.')}}
    </div>
  </div>

  <hr />

  @endif

  <div class="row">
  
    <div :class="{ 'col-md-8' : edit_mode, 'col-md-12' : !edit_mode}">

      @php

      paginate( [
                  'item_singular' => 'branch',
                  'item_plural' => 'branches',
                  'search_method' => 'fetchBranches',
                ] ) 

      @endphp

      <div v-if="branches.length">
          
        @php

        table( [
                  'item_singular' => 'branch',
                  'item_plural' => 'branches',
                  'menu_options' => [ 
                    [ 'action' => 'editBranch(branch.id)', 'text' => ___('Edit') ],
                    [ 'action' => 'enterBranch(branch.id)', 'text' => ___('Enter this Branch') ],
                    [ 'action' => 'closeBranch(branch.id)', 'text' => ___('Close Branch') ],
                  ]
                ] )

        @endphp

      </div>

    </div>

    <div v-show="edit_mode" class="p-l-15" :class="{ 'col-md-0' : !edit_mode, 'col-md-4' : edit_mode}">

      <div class="form-container"> 

        <h3 class="form-heading" v-show="edit_id < 1">{{ ___( "New Branch" ) }}</h3>
        <h3 class="form-heading" v-show="edit_id > 0">{{ ___( "Edit Branch" ) }}</h3>

        @php

        elements( Config::get( 'forms.branches.branch' ) )

        @endphp

        <div class="form-group">
          <button class="btn btn-success" @click="updateBranch()"><i class="fa fa-check"></i> {{ ___( 'OK' ) }}</button>
          &nbsp;
          <button class="btn btn-danger" @click="cancelEditBranch()"><i class="fa fa-undo"></i> {{ ___( 'Cancel' ) }}</button>
        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')
  <script type="text/javascript">
    include( [ 'Core/Branches' ] );

    @php if ( $id ?? 0 ): @endphp
    jQuery(document).ready( function() {
    
      Branches.editBranch( {{ $id }} );
    
    });
    @php endif @endphp
  </script>
@endsection