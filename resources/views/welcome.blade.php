<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MLB Fantasy Draft 2022</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- Styles -->
        <link href="{{ asset('css/homeCustom.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Tablero de {{ Auth::user()->name }}</a>
                    @else
                        <a href="{{ route('login') }}">Iniciar Sesión</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <img src="{{ asset('storage/img/mlb.png') }}" width="50%" height="50%"/>
                <div ><h1>Bienvenidos</h1></div>
                <div class="title m-b-md"> MLB Fantasy Draft 2022</div>

                <div class="links">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}">Tablero de {{ Auth::user()->name }}</a>
                        @else
                            <a href="{{ route('login') }}">Iniciar Sesión</a>
                        @endauth
                @endif
                </div>
            </div>
        </div>
    </body>
</html>
