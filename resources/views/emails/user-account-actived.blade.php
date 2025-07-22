@extends('layouts.email-layout')
@section('content')
    <h1 style="font-size: 25px">¡Hola! 👋</h1>
    <p>
        {{$message}}
    </p>
    <p style="text-align: center">
        <a href="{{config('vayaquevalla.admin_url')}}" class="custom_button">Ir al inicio de sesi&oacute;n</a>
    </p>
    <p>
        Saludos cordiales<br>
        {{env('APP_NAME')}}
    </p>
@endsection