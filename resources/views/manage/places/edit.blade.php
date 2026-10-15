@extends('manage.layout')
@section('title','Gestion - Modifier lieu')
@section('header','Modifier lieu')
@section('content')
@include('manage.places._form',['editing'=>true])
@endsection
