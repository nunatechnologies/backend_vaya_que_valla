@extends('layouts.email-layout')
@section('content')
    <h1 style="font-size: 25px">¡Tu cuenta está lista, {{$user->name}}! 🎉</h1>
    <p>
        Bienvenido a <strong>Vaya que Valla</strong>. Tu cuenta ha sido activada y ya puedes acceder a nuestra plataforma.
    </p>
    <p>
        Tenemos miles de vallas publicitarias y pantallas digitales disponibles en toda Bolivia. Ingresa ahora y empieza a cotizar el espacio perfecto para tu marca.
    </p>
    <p style="text-align: center">
        <a href="{{config('vayaquevalla.panel_url')}}" class="custom_button">Cotizar mi valla o pantalla digital</a>
    </p>
    <p>
        Saludos cordiales<br>
        El equipo de Vaya que Valla
    </p>
@endsection