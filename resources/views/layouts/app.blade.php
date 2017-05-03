
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from www.exampleproject.getforge.io/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 25 Apr 2017 04:41:54 GMT -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Example Project</title>
    <!-- Your custom styles (optional) -->
    <script>
        window.Laravel = {!! json_encode([
                'csrf_token' => csrf_token(),
        ]) !!};
    </script>

    <script>
        (function(t,u,r,b,o,s){
            o=document.createElement(u);
            o.setAttribute('data-turbojs', b);
            o.async=1;o.src=r;
            s=t.getElementsByTagName(u)[0];
            s.parentNode.insertBefore(o, null);
        })(document, 'script'

        );
    </script>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>

    <script src="{{ asset('js/scrolling.js') }}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/gradient.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/main.css') }}" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


</head>

<body>
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">WebSiteName</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Page 1 <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#">Page 1-1</a></li>
                    <li><a href="#">Page 1-2</a></li>
                    <li><a href="#">Page 1-3</a></li>
                </ul>
            </li>
            <li><a href="#">Page 2</a></li>
        </ul>
        <form class="navbar-form navbar-left">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search">
                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
        </form>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
            <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Right Aligned Navbar</h3>
    <p>The .navbar-right class is used to right-align navigation bar buttons.</p>
</div>


@yield('content')


<footer>
    <h3>Copyright&copy; 2017 by Tứng</h3>
</footer>



<!-- JQuery -->
<meta name='forge-tag' value='forge-token:1479635692' /></body>y>


<!-- Mirrored from www.exampleproject.getforge.io/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 25 Apr 2017 04:41:56 GMT -->
</html>