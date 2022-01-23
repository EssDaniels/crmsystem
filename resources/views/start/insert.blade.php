@extends('template')

@section('content')
<form action="{{action('App\Modules\Importer\Http\Controllers\ImporterController@uploadFile')}}" enctype='multipart/form-data' method="post">
    {{csrf_field()}}

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">File <span class="required">*</span></label>
        <div class="col-md-6 col-sm-6 col-xs-12">

            <input type='file' name='file' class="form-control">

            @if ($errors->has('file'))
            <span class="errormsg text-danger">{{ $errors->first('file') }}</span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <input type="submit" name="submit" value='Dodaj plik' class='btn btn-success'>
        </div>
    </div>

</form>
@if($titleRaport !== '')
Raport {{$titleRaport}} został wygenerowany
@endif
@endsection('content')