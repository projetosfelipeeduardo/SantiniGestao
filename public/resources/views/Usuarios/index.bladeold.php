@extends('adminlte::page')

@section('title', 'Usuários')

@section('content')

    <div class="container">

        <section class="row">
            <div class="col-3">
                <a data-toggle="modal" data-target="#modal-novo" class="btn btn-principal  text-light btn-flat">Adicionar <i
                        class="fa fa-plus"></i>
                </a>
            </div>
            <div class="col-9">
                <div class="float-right text-center w-25">
                    @include('flash::message')
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-md-12">
                <form action="{{ route('user.index') }}">
                    <div style="width: 100px;" class="col-md-2 float-right">
                        <button type="submit" class="btn btn-light border border-secondary float-right">Filtrar</button>
                    </div>
                    <div class="col-md-3 float-right">
                        <input autocomplete="off" class="form-control"placeholder="Nome" name="nome" type="text">
                    </div>
                    <div class="col-md-2 float-right">
                        <input autocomplete="off" id="cpf-filtro" class="form-control" placeholder="CPF" name="cpf"
                            type="text">
                        <input type="hidden" name="filtro" value="acao">
                    </div>
                </form>



            </div>
        </section>

        <section class="row mt-2">
            <div class="col-12">
                <table class="table  w-100 table-responsive">
                    <thead class="thead-dark">
                        <tr>
                            <th class="w-25 text-center">Nome</th>
                            <th class="w-25 text-center">CPF</th>
                            <th class="w-25 text-center">Email</th>
                            <th class="w-25 text-center">Nivel de Acesso</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $u)
                            <tr>
                                <td class="text-center">{{ $u->name }}</td>
                                <td class="text-center">{{ $u->cpf }}</td>
                                <td class="text-center">{{ $u->email }}</td>
                                <td class="text-center">{{ $u->nivel_acesso == 2 ? 'Funcionário' : 'Administrador' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('user.edit', $u->id) }}"> <i class="fa fa-edit"></i> </a>
                                    <a> <i class="fa fa-times text-danger"></i> </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $usuarios->links() }}
        </section>
        </section>

    </div>


    <div class="modal" id="modal-novo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-light">
                    <h5 class="modal-title ">Cadastrar novo
                        usuário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i style="font-size: 16px" class="fas text-warning fa-circle"> </i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="form-errors"></div>
                    <form id="form-novo" action="{{ Route('user.store') }}" method="POST">
                        @csrf
                        <section class="row">
                            <div class="col-12">
                                <label class="label" for="name">Nome</label>
                                <input name="name" type="text" class="form-control">
                            </div>
                        </section>
                        <section class="row mt-2">
                            <div class="col-6 mt-2">
                                <label class="label" for="email">E-mail</label>
                                <input name="email" type="email" class="form-control">
                            </div>
                            <div class="col-6 mt-2">
                                <label class="label" for="cpf">CPF</label>
                                <input id="cpf" name="cpf" type="cnpj" class="form-control">
                            </div>
                        </section>
                        <section class="row mt-2">
                            <div class="col-6">
                                <label class="label" for="password">Senha</label>
                                <input name="password" type="password" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="label" for="nivel_acesso">Nivel de Acesso</label>
                                <select name="nivel_acesso" class="form-control" id="">
                                    <option value="1">Administrador</option>
                                    <option value="2">Funcionário</option>
                                </select>
                            </div>
                        </section>
                    </form>
                </div>
                <div style="background: #eee" class="modal-footer">
                    <button id="btn-save" class="btn botao-save ">Salvar</button>
                    <button type="button" class="btn botao-close" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $("#cpf").keydown(function() {
                try {
                    $("#cpf").unmask();
                } catch (e) {}

                var tamanho = $("#cpf").val().length;

                if (tamanho < 11) {
                    $("#cpf").mask("999.999.999-99");
                } else {
                    $("#cpf").mask("99.999.999/9999-99");
                }

                // ajustando foco
                var elem = this;
                setTimeout(function() {
                    // mudo a posição do seletor
                    elem.selectionStart = elem.selectionEnd = 10000;
                }, 0);
                // reaplico o valor para mudar o foco
                var currentValue = $(this).val();
                $(this).val('');
                $(this).val(currentValue);
            });
        });


        $('#btn-save').click(function() {
            var dados = $('#form-novo').serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/usuarios/validar',
                type: 'post',
                dataType: 'json',
                data: dados,
                success: function(data) {
                    $('#form-novo').submit();
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    var errorsHtml = '<div class="alert alert-danger"><ul>';

                    $.each(errors.errors, function(key, value) {
                        errorsHtml += '<li style="color:white;">' + value +
                            '</li>'; //showing only the first error.
                    });
                    errorsHtml += '</ul></div>';
                    console.log(errors);
                    console.log(errorsHtml);
                    $('#form-errors').html(errorsHtml);
                }
            })

        });
    </script>

@endsection
