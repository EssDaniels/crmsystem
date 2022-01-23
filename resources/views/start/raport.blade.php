@extends('template')

@section('content')
<a href="{{ url('/importers/inserttask1') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Task 1</a><br>
<a href="{{ url('/importers/insert') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Task 2</a><br>
@if($titleRaport !== '')
Raport {{$titleRaport}} został wygenerowany<br>
@endif

@endsection('content')