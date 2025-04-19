<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>correo</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <style>
        @font-face 
        {
        font-family: 'tt-octosquares';
        src: url("/public/fonts/tt_octosquares/TT Octosquares Trial Condensed Regular.ttf");
        }
        body_ {
            background: #D8D8D8 !important;
            font-family: 'Helvetica';
            position: relative;

            line-height: 1.5;
        }
        body{
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            color: #333333;
            font-size: 21px;
            line-height: 1.6;
            text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.2);
        }
        h1{
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
        }
        .h3,
        h3 {
            font-size: 1.75rem;
            color: #101010;
            font-weight: 100 !important;
            margin-bottom: 0;
        }

        p {
            color: #4E4E4E;
            font-weight: 100 !important;
            margin-top: 0;
            font-size: 17px!important;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .mb-30 {
            margin-bottom: 30px;
        }

        hr.hr_title {
            color: #8D0795;
            width: 40%;
            margin-top: 0px;
            margin-bottom: 20px;
            height: 1px;
            background-color: #8D0795;
        }

        .section-t8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .container {
            width: 100%;
            padding-right: var(--bs-gutter-x, .75rem);
            padding-left: var(--bs-gutter-x, .75rem);
            margin-right: auto;
            margin-left: auto;
        }

        .justify-content-md-center {
            justify-content: center !important;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(var(--bs-gutter-y) * -1);
            margin-right: calc(var(--bs-gutter-x) / -2);
            margin-left: calc(var(--bs-gutter-x) / -2);
        }

        .container_email {
            max-width: 800px;
            text-align: center;
            padding: 0px 30px;
        }

        .class_cabecera {
            background: #ffffff;
            min-height: 90px;
            padding: 40px;
            text-align: left;
            border-radius: 15px 15px 0px 0px;
        }

        .class_cuerpo {
            background: white;
            padding: 40px 40px;
            border-radius: 15px 15px 15px 15px;
        }

        .class_footer {
            background: #1A3966;
            min-height: 90px;
            padding: 40px;
            text-align: left
        }

        img {
            height: auto;
        }
        .custom_button{
            background-color: #FF5845; /* rojo-anaranjado */
            color: #FFFFFF;
            padding: 10px 50px;
            text-decoration: none;
            font-weight: bold;
            font-family: sans-serif;
            font-size: 16px;
            border-radius: 15px;
            border: 2px solid #000000;
            display: inline-block;
            box-shadow: 0 6px 0 #062A56; /* sombra azul oscura debajo */
        }

        @media (max-width: 768px) {
            .class_cuerpo {
                padding: 5px 5px !important;
            }

            .custom_button {
                padding: 10px 10px !important;
                border-radius: 15px !important;
                font-size: 15px !important;
            }

            .h3,
            h3 {
                font-size: 1.25rem !important;
            }
        }

        .class_url {
            font-size: 10px;
        }

        .class_url>a {
            color: #8D0795;
        }

        .card {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
        }

        .card-body {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
            background: #f2f2f2;
            border-radius: 20px;
            padding: 20px;
        }

        .logimaq-text-blue {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
        }
    </style>
</head>

<body style="background: #D8D8D8 !important">
    <div class="section-main section-t8">
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="container_email">
                    <a class="navbar-brand text-brand" href="{{ url('/') }}">
                        <img src="{{ asset('images/vqvlogo.png') }}"  style="margin-bottom: 20px" width="100" alt="Logo {{ ENV('APP_NAME') }}">
                    </a>
                    {{-- <div class="class_cabecera">
                        
                    </div> --}}
                    <div class="class_cuerpo">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
