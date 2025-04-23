@extends('layouts.email-layout')

@section('content')
    <h1>¡Hola! 👋</h1>

    <p>Recibimos una solicitud para restablecer tu contraseña.</p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="{{ $url }}" style="
            background-color: #ff544a;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 20px;
            font-weight: bold;
            box-shadow: 3px 3px 0 #001f4d;
            display: inline-block;
        ">
            Restablecer Contraseña
        </a>
    </p>

    <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>

    <p>Saludos cordiales,<br>{{ config('app.name') }}</p>
@endsection
