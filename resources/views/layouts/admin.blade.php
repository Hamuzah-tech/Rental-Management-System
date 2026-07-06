<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Rental Management System')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f5f7fb;
        }

        .wrapper{
            display:flex;
            min-height:100vh;
        }

        .sidebar{

            width:260px;
            background:#1e293b;
            color:white;

        }

        .logo{

            padding:25px;
            text-align:center;
            font-size:20px;
            font-weight:bold;
            border-bottom:1px solid rgba(255,255,255,.1);

        }

        .menu a{

            display:block;
            padding:15px 25px;
            color:white;
            text-decoration:none;

        }

        .menu a:hover{

            background:#334155;

        }

        .content{

            flex:1;
            display:flex;
            flex-direction:column;

        }

        .topbar{

            background:white;
            padding:18px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 2px 8px rgba(0,0,0,.05);

        }

        .page{

            padding:30px;

        }

        .card{

            background:white;
            border-radius:10px;
            padding:25px;
            box-shadow:0 2px 10px rgba(0,0,0,.05);

        }

        footer{

            padding:15px;
            text-align:center;
            color:#777;

        }

    </style>

</head>

<body>

<div class="wrapper">

    <aside class="sidebar">

        <div class="logo">
            Rental MS
        </div>

        <div class="menu">

            <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>

            <a href="{{ route('admin.landlords.index') }}">👤 Landlords</a>

            <a href="#">🏢 Properties</a>

            <a href="#">🧑‍🤝‍🧑 Tenants</a>

            <a href="#">💰 Payments</a>

            <a href="#">📊 Reports</a>

            <a href="#">⚙ Settings</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button style="width:100%;padding:15px;border:none;background:none;color:white;text-align:left;padding-left:25px;cursor:pointer;">
                    🚪 Logout
                </button>

            </form>

        </div>

    </aside>

    <div class="content">

        <div class="topbar">

            <div>

                <strong>@yield('page-title')</strong>

            </div>

            <div>

                {{ auth()->user()->name }}

            </div>

        </div>

        <main class="page">

            @yield('content')

        </main>

        <footer>

            Rental Management System © {{ date('Y') }}

        </footer>

    </div>

</div>

</body>
</html>