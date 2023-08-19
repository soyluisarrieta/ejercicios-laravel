@extends('layouts.plantilla')

@section('title','Curso '.$curso)

@section('content')
  <h1>El curso es: {{$curso}}</h1>
@endsection