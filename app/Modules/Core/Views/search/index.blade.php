@extends('layouts.app')

@section('content-header')

<div class="page-title top-search-container">
  <form class="no-padding no-margin" action="/search" method="GET" onsubmit="Search.query(jQuery('#q').val()); return false;">
    <div class="input-group">
      <input type="text" class="form-control" name="q" id="q" value="{{ request()->input('q') }}" autocomplete="off" autofocus="true" />
      <button type="button" class="btn btn-default" onclick="Search.query(jQuery('#q').val());">{{___('Search')}}</button>
    </div>
  </form>
</div>

@stop

@section('content')

<div class="container" id="vSearch" v-cloak>

  <div class="row">

    <div class="col-md-2">
    


    </div>

    <div class="col-md-8">
  
      <div v-if="searching">
      
      </div>

      <div v-if="!searching">

        <div v-if="!q">

          <h3 class="text-lighter text-center"><i class="fa fa-arrow-up"></i> &nbsp; <i class="fa fa-arrow-up"></i><br />{{___('Type something and press ENTER or click/touch the search button.')}}</h3>

        </div>

        <div v-if="q">

          <h3 class="text-lighter">{{___('Results for')}} <strong>@{{ q }}</strong></h3>

          <div v-if="suggestions">
            <div v-html="suggestions" class="search-suggestions"></div>
          </div>
          
          <div v-if="results.length < 1">

            <h3 class="text-lighter">{{___('No results found.')}}</h3>

          </div>

          <div v-if="results.length">

            <b-row>
              <b-col>
                <b-pagination base-url="#" :total-rows="paging.total" :per-page="paging.limit" v-model="paging.page" :simple="false" @change="pageChange"></b-pagination>
              </b-col>
              <b-col class="p-10">
                <span class="push-right">@{{ paging.total }} <span v-show="paging.total == 1">{{ ___('result') }}</span><span v-show="paging.total != 1">{{ __('results') }}</span> | {{ ___('Showing') }} @{{ Math.min( paging.total, ( paging.page -1 ) * paging.limit + 1 ) }} - @{{ Math.min( paging.total, ( paging.page -1 ) * paging.limit + paging.limit ) }} </span></b-col>
            </b-row>

            <div class="search-results-container">

              <div v-for="result in results" class="search-result-item">

                <div class="result-heading">
                  <a :href="result.url">@{{result.heading}}</a>
                </div>

                <div class="result-description">
                  @{{result.description}}
                </div>

                <span class="badge badge-info">@{{result.type}}</span>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')

  <script type="text/javascript">
    include( [ 'Core/Search' ] );
    jQuery(document).ready( function() {
      @php if ( request()->input('q') ): @endphp
      Search.query("{{ request()->input('q') }}");
      @php endif @endphp
    });
  </script>

@endsection
