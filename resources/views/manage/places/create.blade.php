@extends('manage.layout')
@section('title','Gestion - Nouveau lieu')
@section('header','Nouveau lieu')
@section('content')
@include('manage.places._form',['editing'=>false])
@endsection
