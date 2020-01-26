@extends('layouts.app')

@section('content')
<div class="container">

    @if ( Config::get('settings.disable_password_reset') )

    <h3 class="text-lighter text-center">
        {{___('Sorry, the feature you requested is not available.')}}
    </h3>

    @else

    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <h2 class="text-center text-lighter">{{___('Password Reset')}}</h2>
                <div class="form-container push-down bg-white">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">{{___('Email Address')}}</label>

                            <div class="help-block">
                                {{___('Please enter your email address below and we will send you a ink to reset your password.')}}
                            </div>

                            <br />

                            <div class="">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="field-error help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <button type="submit" class="btn btn-primary">
                                    {{___('Continue')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endif
    
</div>
@endsection
