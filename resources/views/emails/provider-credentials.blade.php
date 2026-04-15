@extends('layouts.email-layout')
@section('content')
    @if($isNewAccount)
        <h1 style="font-size: 25px">Bienvenido a Vaya que Valla, {{ $user->name }}!</h1>
        <p>
            Se ha creado una cuenta para ti en nuestra plataforma de gestion de vallas publicitarias.
            A continuacion te compartimos tus datos de acceso:
        </p>
    @else
        <h1 style="font-size: 25px">Datos de acceso actualizados</h1>
        <p>
            Hola {{ $user->name }}, tus datos de acceso han sido actualizados.
            A continuacion te compartimos tus nuevos datos:
        </p>
    @endif

    <div style="background-color: #f5f5f5; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <p style="margin: 5px 0;"><strong>Correo:</strong> {{ $user->email }}</p>
        <p style="margin: 5px 0;"><strong>Contrasena:</strong> {{ $password }}</p>
    </div>

    <p>
        Te recomendamos cambiar tu contrasena al iniciar sesion por primera vez.
    </p>

    <p style="text-align: center">
        <a href="{{ $loginUrl }}" class="custom_button">Iniciar sesion</a>
    </p>

    <p>
        Saludos cordiales<br>
        El equipo de Vaya que Valla
    </p>
@endsection
