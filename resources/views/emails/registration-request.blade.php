@extends('layouts.email-layout')
@section('content')
    <h1 style="font-size: 25px">¡Hola! 👋</h1>
    
    <p>
        El usuario **{{ $newUser->name }}** ({{ $newUser->email }}) se ha registrado y requiere activación.
    </p>
    <p style="text-align: center">
        <a href="{{$accountConfirmationUrl}}" class="custom_button">Activar</a>
    </p>
    <p>
        Saludos cordiales<br>
        {{env('APP_NAME')}}
    </p>
@endsection
