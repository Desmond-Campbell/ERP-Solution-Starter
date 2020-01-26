<!doctype html>
<html lang="{{ app()->getLocale() }}">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
                        
    <title>{{ config( 'company' )->name ?? config( 'app.name' ) }}</title>

    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    {!! favicon_link() !!}

    <script>
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>

    @yield('inject-css')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    {{--<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>--}}
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css"/>

    <!-- Styles -->
    <link href="{{ asset('assets/Core/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/Core/css/app.css') }}" rel="stylesheet">

    <style type="text/css">

      {{ branding_css() }}

    </style>

  </head>
  <body>

    <div>

      <div id="vProgress">

        <template>
          <vue-progress-bar></vue-progress-bar>
        </template>

      </div>

      <div id="app" v-cloak>

        <b-navbar toggleable="md" type="dark" variant="info" class="main-nav-container">

          <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

          <b-navbar-brand href="/companies">
            
            {!! branding_html() !!} <i class="fa fa-sign-in-alt"></i></b-navbar-brand>

          <b-collapse is-nav id="nav_collapse">

            @if (!Auth::guest())

            <b-navbar-nav>
            
            {!! menu( Config::get( 'menu' ), 'left_nav', '', [ 'auth' => 1 ] ) !!}

            </b-navbar-nav>

            @endif

            @if (!Auth::guest())

            <form class="hidden" id="logout" name="logout" method="post" action="/logout">
              {!! csrf_field() !!}
            </form>
            
            <b-navbar-nav class="ml-auto">

              <b-nav-item-dropdown right>
                
                <template slot="button-content">
                  {!! user_avatar() !!} &nbsp; {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                </template>
                
                {!! menu( Config::get( 'menu' ), 'right_nav', '', [ 'auth' => 1 ] ) !!}
                
              </b-nav-item-dropdown>
            

            </b-navbar-nav>

            @else

            <b-navbar-nav class="ml-auto">

              {!! menu( Config::get( 'menu' ), 'right_nav', '', [ 'guest' => 1 ] ) !!}
              
            </b-navbar-nav>

            @endif

          </b-collapse>

        </b-navbar>

      </div>

      <div class="content-header" id="header" v-cloak>

        @yield('content-header')

      </div>

      <div class="content-container">

        @yield('content')  

      </div>

    </div>

    <div id="overlay" style="display: none;">
      &nbsp;
    </div>

    <!-- Scripts -->
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"><\/script>')</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>

    <script
      src="https://code.jquery.com/jquery-3.2.1.js"
      integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
      crossorigin="anonymous"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <!--script src="https://unpkg.com/select2@4.0.3"></script>
    <script src="/js/core/pSelect2.js"></script-->
    <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    <script>
      include( [ 'Core/Progress' ] );
    </script>

    @yield('vue-modules')
    
    <script src="{{ asset('assets/js/dist/bundle.js') }}"></script>
    
    @yield('javascript-controllers')
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/holder/2.9.4/holder.min.js"></script>

  </body>
            
</html>
