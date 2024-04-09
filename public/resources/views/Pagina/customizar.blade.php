@extends('adminlte::page')

@section('title', 'Customizar Página')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Customizar Página</h2>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 text-center">
            <h5>Selecione o seu Logotipo</h5>
            <button class="btn btn-principal text-light">Selecionar</button>
        </div>
        <div class="col-md-2">
            <label for="">Selecione a cor</label>
            <select class="form-control" style="background-color: red;" name="corPrincipal" id="">
                <option class="text-light"  style="background-color: blue; width: 15px;" value="">BLUE</option>
                <option style="background-color: RED; width: 15px;" value="">BLUE</option>
                <option style="background-color: PURPLE; width: 15px;" value="">BLUE</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6"></div>
    </div>
</div>



@endsection
