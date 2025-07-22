@extends('layouts.email-layout')
@section('content')
    <h1 style="font-size: 25px">¡Hola! 👋</h1>
    
    <p>
        Haz clic en el botón para verificar tu dirección de correo electrónico.
    </p>
    <p style="text-align: center">
        <a href="{{$verificationUrl}}" class="custom_button">Verificar correo</a>
    </p>
        
    <p>
        Si no creaste una cuenta, no es necesario hacer nada.
    </p>
    <p>
        Luego de que hayas verificado tu correo, nuestro equipo har&aacute; una verificaci&oacute;n para activar tu cuenta, ser&aacute;s notificado a trav&eacute;s de un correo cuando eso ocurra. 
    </p>
    <p>
        Saludos cordiales<br>
        {{env('APP_NAME')}}
    </p>
    <hr style="margin-bottom: 30px; margin-top:30px">
    <p>
        Si estas teniendo problemas haciendo click en el bot&oacute;n "Verificar correo", copia y pega la url de abajo en tu navegador web: 
        <a href="{{$verificationUrl}}" style="word-break: break-all">{{$verificationUrl}}</a>
    </p>
@endsection
