<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Формы, отчеты</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/w3.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/demos.css') }}" />
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    </head>
    <body>
        <div id="app">
            <v-app>
                <top_forms mainpath="{{ url('/') }}"></top_forms>
                    <v-main>
                        <div class="fill-height">