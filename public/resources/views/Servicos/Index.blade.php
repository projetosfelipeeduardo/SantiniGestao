@extends('adminlte::page')

@section('title', 'Serviços')

@section('content')

<div class="container">

    <section class="row">
        <div class="col-3">
            <a data-toggle="modal" data-target="#modal-novo" class="btn btn-principal  text-light btn-flat">Adicionar <i
                    class="fa fa-plus"></i>
            </a>
        </div>
        <div class="col-9">
            <div class="float-right text-center w-50">
                @include('flash::message')
            </div>
        </div>
    </section>

    <section class="row mt-2">
        <div class="col-12">
            <table class="table  w-100 table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th class="w-50 text-center">Nome</th>
                        <th class="text-center">Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicos as $s)
                    <tr>
                        <td class="text-center">{{$s->nome}}</td>
                        <td class="text-center"><a onclick="return confirm('Tem certeza que deseja excluir?')" href="{{route('servicos.excluir', $s->id)}}"><i class="fa fa-times text-danger"></i></a></td>
                    </tr>   
                    @endforeach
                </tbody>
            </table>
            {{ $servicos->links() }}
    </section>
    </section>

</div>


<div class="modal" id="modal-novo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-light">
                <h5 class="modal-title ">Cadastrar novo
                    serviço</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i style="font-size: 16px" class="fas text-warning fa-circle"> </i> </span>
                </button>
            </div>
            <div class="modal-body">
                <div id="form-errors"></div>
                <form id="form-novo" action="{{Route('servicos.store')}}" method="POST">
                    @csrf
                    <section class="row">
                        <div class="col-12">
                            <label class="label" for="name">Nome</label>
                            <input name="nome" type="text" class="form-control">
                        </div>
                    </section>
            </div>
            <div style="background: #eee" class="modal-footer">
                <button id="btn-save" class="btn botao-save ">Salvar</button>
                <button type="button" class="btn botao-close" data-dismiss="modal">Cancelar</button>
            </form>
            </div>
        </div>
    </div>
</div>

<style>
     tr:hover{
          background-color: aquamarine;
      }

</style>

@endsection
