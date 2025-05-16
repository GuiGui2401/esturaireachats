<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ env('APP_URL')}}">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
  	<title>{{ config('app.name', 'eCommerce') }}</title>

    <!-- google font -->
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" integrity="sha512-3kAToXGLroNvC/4WUnxTIPnfsiaMlCn0blp0pl6bmR9X6ibIiBZAi9wXmvpmg1cTyd2CMrxnMxqj7D12Gn5rlw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- aiz core css -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}" onload="this.onload=null;this.removeAttribute('media');">
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000,9999) }}" onload="this.onload=null;this.removeAttribute('media');">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .install-card{
            width: 640px;
            height: 640px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #e6e6e6;
            box-shadow: 0px 16px 45px rgba(0, 0, 0, 0.08);
        }
        .install-card .install-card-body{
            padding: 3rem 4rem !important;
        }
        .btn-install{
            width: 280px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 20px;
            background: linear-gradient(to right, #e90608 0%, #f59e39 100%);
            box-shadow: 0px 8px 16px rgba(255, 88, 0, 0.16);
            font-weight: bold;
            font-size: 14px;
            line-height: 18px;
            text-align: center;
            color: #fff !important;
            transition: all 0.5s;
        }
        .btn-install:hover{
            box-shadow: 0px 8px 40px rgb(255 88 0 / 30%);
            letter-spacing: 0.3px;
        }
        .back-btn-svg svg * {
            transition: fill .4s ease;
        }
        .back-btn-svg:hover svg .inner{
            fill: #cccccc !important;
        }
        .back-btn-svg:hover svg .arrow{
            fill: #fff !important;
        }
        .right-links{
            position: relative;
            display: inline-block;
            cursor: pointer;
            outline: none;
            border: 0;
            padding: 0;
            vertical-align: middle;
            background: transparent;
            font-size: inherit;
            font-family: 'Roboto', sans-serif;
            width: 11rem;
            height: auto;
        }
        .right-links .circle {
            transition: all 0.8s cubic-bezier(0.65,0,.076,1);
            position: relative;
            display: flex;
            align-items: center;
            margin: 0;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 1.625rem;
            padding-left: 12px;
        }
        .right-links.site .circle {
            background: #007cff;
        }
        .right-links.video .circle {
            background: #ea4335;
        }
        .right-links.document .circle {
            background: #34a853;
        }
        .right-links.site:hover .circle {
            width: 100%;
        }
        .right-links.video:hover .circle {
            width: 8.5rem;
        }
        .right-links.document:hover .circle {
            width: 10.5rem;
        }
        .right-links .button-text {
            transition: all 0.5s cubic-bezier(0.65,0,.076,1);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 0.65rem 0;
            margin: 0 0 0 2.75rem;
            color: #f2f3f8;
            font-weight: 500;
            font-size: 12px;
            line-height: 18px;
            opacity: 0;
        }
        .right-links:hover .button-text {
            color: var(--white);
            opacity: 1;
        }
    </style>

    <script>
        var AIZ = AIZ || {};
    </script>
</head>
<body>
    <div class="aiz-main-wrapper d-flex">

        <div class="flex-grow-1">
            @yield('content')
        </div>

    </div><!-- .aiz-main-wrapper -->
    <script src="{{ static_asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ static_asset('assets/js/jquery-3.6.0.min.js') }}" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{ static_asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js" integrity="sha512-o36qZrjup13zLM13tqxvZTaXMXs+5i4TL5UWaDCsmbp5qUcijtdCFuW9a/3qnHGfWzFHBAln8ODjf7AnUNebVg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.min.js" integrity="sha512-aVkYzM2YOmzQjeGEWEU35q7PkozW0vYwEXYi0Ko06oVC4NdNzALflDEyqMB5/wB4wH50DmizI1nLDxBE6swF3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js" integrity="sha512-mh+AjlD3nxImTUGisMpHXW03gE6F4WdQyvuFRkjecwuWLwD2yCijw4tKA3NsEFpA1C3neiKhGXPSIGSfCYPMlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js" integrity="sha512-efUTj3HdSPwWJ9gjfGR71X9cvsrthIA78/Fvd/IN+fttQVy7XWkOAXb295j8B3cmm/kFKVxjiNYzKw9IQJHIuQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js" integrity="sha512-6rE6Bx6fCBpRXG/FWpQmvguMWDLWMQjPycXMr35Zx/HRD9nwySZswkkLksgyQcvrpYMx0FELLJVBvWFtubZhDQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.33.2/tagify.min.js" integrity="sha512-a6ZSFxj4WMZoVm8nWpD03fldTcKmjTdQVVsczBrWl+CUVkPkTdb+r4h/YVobA1ojJaNTBtEVoIYq8Yss4X0M7w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js?v=') }}{{ rand(1000,9999) }}"></script>

    @yield('script')

    <script type="text/javascript">
    @foreach (session('flash_notification', collect())->toArray() as $message)
        AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
    @endforeach
    </script>
</body>
</html>
