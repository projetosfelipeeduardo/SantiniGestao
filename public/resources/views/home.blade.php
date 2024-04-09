@extends('adminlte::page')

@section('title', 'Home')
    

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                <img  width="200"  src="{{asset('/images/oetker.png')}}" alt="">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   <strong style="font-size 18px;">Seja Bem Vindo!</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
