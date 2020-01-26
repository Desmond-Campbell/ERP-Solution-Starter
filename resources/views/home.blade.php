@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="text-lighter">{{___('Welcome')}}</h1>
                </div>

                <div class="panel-body">
                    <p>{{ ___('Hi there,') }}</p>
                    <p>{{ ___("You've landed on this so-called 'homepage' and it is actually blank, so it's not an error!") }}</p>
                    <p>{{ ___("TopFloor at its core is an awesome boilerplate that engineers and developers can use to build great stuff. The real value of TopFloor is not what you see or don't see on this mere homepage, but rather all of what you realise you don't have to do when you start your app, because we've done it for you already!") }}</p>
                    <p>{{ ___("So...not to worry...you can always build upon this homepage. You can find it at the default view that comes with Laravel.") }}</p>
                    <p>{{ ___("Happy....coding!") }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
