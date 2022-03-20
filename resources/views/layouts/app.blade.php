<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MLB Fantasy Draft 2022</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/playerDetail.css') }}" rel="stylesheet">
    <link href="{{ asset('css/blinker.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/homeCustom.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                @if (Route::has('login'))
                    <a class="navbar-brand" href="{{ url('/home') }}">subasta</a>
                    <a class="navbar-brand" href="{{ url('/settings') }}">configuracion</a>
                    <a class="navbar-brand" href="{{ url('/events') }}">eventos</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                @endif
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @guest
                        <div></div>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
{{--                                    <a href="{{ route('logout') }}">Logout</a>--}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
          <!-- Main Footer -->
        <footer class="main-footer">
            <div class="row">
                <div class="col-md-6">
                    <strong>Copyright &copy; 2022 <a href="mailto:carlosamaya1@gmail.com">Carlos Amaya</a>.</strong>
                </div>
            </div>
        </footer>
    </div>
    <!-- Scripts -->
    <script type="application/javascript" src="{{ asset('js/app.js') }}" defer></script>
    <script type="application/javascript" src="{{ asset('js/jquery-3.3.0.min.js') }}" ></script>
    <script type="application/javascript" src="{{ asset('js/bootstrap.min.js') }}" ></script>
    <script type="application/javascript" src="{{ asset('js/pusher.min.js') }}" ></script>
{{--    <script type="application/javascript" src="https://js.pusher.com/3.1/pusher.min.js"></script>--}}
    <script>
        // global app configuration object
        var config = {
            routes: {
                search: "{{ route('home.searchplayer') }}",
                changepassword: "{{ route('home.generatepwd') }}",
                updateprice: "{{ route('home.updatePlayerPrice') }}",
                loadauction: "{{ route('home.loadauction') }}",
                cancelauction: "{{ route('home.cancelauction') }}",
                addfavs: "{{ route('settings.addplayertofavs') }}",
                initmanualauction: "{{ route('home.initmanualauction') }}",
                deletefavs: "{{ route('settings.deletefavs') }}",
                deleteauction: "{{ route('home.deleteauction') }}",
                searchevents: "{{ route('events.searchevents') }}"
            }
        };
    </script>
    <script type="application/javascript" src="{{ asset('js/businessLogic.js') }}" ></script>
    <script type="application/javascript" src="{{ asset('js/eventsLogic.js') }}" ></script>
</body>
</html>
