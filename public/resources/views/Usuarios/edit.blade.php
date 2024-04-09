@extends('adminlte::page')

@section('title', 'Usuários')

@section('content')
    <div class="container">
        @if (session('alerta'))
            <div class="alert alert-warning" role="alert">
                {{ session('alerta') }}
            </div>
        @endif
        <div class="container">

            <div class="row">
                <div class="col-md-12">
                    <div class="float-right">
                        @include('flash::message')
                        <div id="form-update-errors"></div>
                    </div>
                </div>
            </div>

            <section class="row">
                <div class="col-md-3 text-center">
                    <img style="border-radius: 100%" width="180px" height="180px"
                        src="{{ URL::asset('/images/profiles/' . $user->profile_pic) }}">
                    <button data-toggle="modal" data-target="#modal-pic" class="btn btn-principal text-light mt-2">Alterar
                        Imagem</button>
                </div>
                <div class="col-md-9">
                    <form action="{{ route('user.update', $user->id) }}" method="POST" id="form-update">
                        <div class="row">
                            @csrf
                            <div class="col-md-6">
                                <label class="label" for="name">Nome Completo</label>
                                <input value="{{ $user->name }}" name="name" type="text" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="label" for="name">Email</label>
                                <input value="{{ $user->email }}" name="email" type="text" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="label" for="cpf">CNPJ</label>
                                <input value="{{ $user->cpf }}" name="cpf" id="cpf" type="text"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="label" for="name">Empresa</label>
                                <input value="{{ $user->empresa }}" name="empresa" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label" for="name">Senha</label>
                                <input type="password" name="password" placeholder="******" class="form-control">
                            </div>
                        </div>

                </div>
                </form>
                <div style="margin-top: 35px;" class="col-md-6 text-center">
                    <button id="btn-update" class="w-50 btn btn-principal text-light">Salvar</button>

                </div>
        </div>

        </section>


        <div class="modal fade" id="modal-pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-light" id="exampleModalCenterTitle">Selecionar Imagem</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5 text-center">
                                <div id="upload-demo"></div>
                            </div>
                            <div class="col-md-7" style="padding:5%;">

                                <strong>Selecionar imagem:</strong>

                                <input class="form-control" type="file" id="image">

                                <button class="btn btn-principal btn-block text-light upload-image"
                                    style="margin-top:2%">Trocar
                                    Foto
                                    de
                                    Perfil</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <link rel="stylesheet" href="{{ URL::asset('/js/croppie/croppie.css') }}">
    <script src="{{ URL::asset('/js/croppie/croppie.min.js') }}"></script>
    <script src="{{ URL::asset('/js/usuario-edit.js') }}"></script>
    <script>
        $('#btn-update').click(function() {
            var dados = $('#form-update').serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/usuarios/validarUpdate',
                type: 'post',
                dataType: 'json',
                data: dados,
                success: function(data) {
                    $('#form-update').submit();
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    var errorsHtml = '<div class="alert alert-danger"> <ul>';

                    $.each(errors.errors, function(key, value) {
                        errorsHtml += '<li style="color:white;">' + value +
                            '</li>'; //showing only the first error.
                    });
                    errorsHtml += '</ul></div> ';
                    console.log(errors);
                    console.log(errorsHtml);
                    $('#form-update-errors').html(errorsHtml);
                }
            })

        });

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
    </script>

@endsection
