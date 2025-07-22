@extends('layouts.email-layout')
@section('content')
    <h1 style="font-size: 25px">¡Hola! 👋</h1>
    <p>
        Estimado {{$user->name}}, tu cuenta ya ha sido activada.
    </p>
    <p style="text-align: center">
        <a href="{{config('vayaquevalla.panel_url')}}" class="custom_button">Ir al inicio de sesi&oacute;n</a>
    </p>
    <p>
        Saludos cordiales<br>
        {{env('APP_NAME')}}
    </p>
@endsection