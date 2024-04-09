@extends('adminlte::page')
@section('title', 'Broadside')

@section('content')

<div id="teste" class="container">

@if (Auth::user()->nivel_acesso == 1)
    <section class="row">
        <div class="col-md-3">
            <a data-toggle="modal" data-target="#nova-pasta" href="#"><i class="fa fa-plus"></i> Nova Pasta</a>
        </div>
        <div class="col-9 col-sm-12 col-12">
            <div class="float-right text-center w-50">
                @include('flash::message')
            </div>
        </div>
    </section>
@endif

    <section class="row mt-2">
        <div class="col-md-7"></div>
        <div class="col-md-3 col-10 col-sm-10 ">
            <div>
                <form method="GET" action="{{route('arquivo.index')}}">
                    <input value="{{$nome}}" name="nome" placeholder="Buscar Pasta ou arquivo" class="form-control"
                        type="text">
                    <input type="hidden" name="acao" value="filtro">
            </div>
        </div>
        <div class="col-md-2 col-sm-12 col-2">
            <button type="submit" style="border-radius: 100%" class="btn btn-primary"><i
                    class="fa fa-search"></i></button>
            </form>
        </div>
    </section>

    @if ($filtro)
    <section class="row mt-2 rowpz">
        <div class="col-md-12 text-center">
            <b>Resultados da sua Busca</b>
        </div>
    </section>
    <section class="row ropz2">
        @forelse ($pastasBusca as $pb)
        <div class="col-md-10 col-sm-6 col-6 div-pasta">
            <a style="height: 100%;" oncontextmenu="menu({{$pb->id}})" id="pastinha_{{$pb->id}}" class="text-dark pasta"
                href="{{route('visualizar-arquivo', $pb->id)}}">
                <div oncontextmenu="menu({{$pb->id}})" class="h-100 w-100">
                    <p  style="font-size: 1.1rem"><i class="fa fa-folder" style="color: #f2d06c"></i> {{$pb->nome}}</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-sm-6 col-6">
            <p class="float-right text-secondary">Criada em: {{  date('d/m/Y', strtotime($pb->created_at)) }}</p>
        </div>
        @if (Auth::user()->nivel_acesso == 1)
        <div class="context-menu" id="context-menu_{{$pb->id}}">
            <div oncontextmenu="return false;" class="item">
                <a onclick="editar({{$pb->id}})" data-toggle="modal" data-target="#edit-pasta" class="text-light"
                    href="{{route('visualizar-arquivo', $pb->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-edit"></i> Editar
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a onclick=" return confirm('Tem certeza que seja excluir essa pasta?')" id="exclui-pasta" class="text-light" href="{{route('delete-pasta', $pb->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-trash"></i> Excluir
                    </div>
                </a>
            </div>
        </div>
        @endif
        @empty
        <div style="margin-left: 10px;" class="col-md-12">
            <p>Nenhuma pasta corresponde a busca</p>
        </div>
        @endforelse
    </section>
    <section class="row ropz2">
        <div class="col-md-12">
            <table class="table table-responsive table-light table-stripped">
                <tbody>
                    @forelse ($arquivosBusca as $ab)
                    <tr>
                        <td class="text-center w-100">{{$ab->nome}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Extensão do arquivo" class="text-center">
                            .{{$ab->extensao}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Data de upload" class="text-center">
                            {{ date('d/m/Y', strtotime($ab->created_at))}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Fazer Download" class="text-center"> <a
                                href="{{route('download-arquivo', $ab->arquivo)}}" class="btn btn-sm btn-info"
                                style="border-radius: 100%"><i class="fa fa-download"></i></a></td>
                        <td data-toggle="tooltip" data-placement="top" title="Enviar via Whatsapp" class="text-center">
                            <a target="_blank"
                                href="https://api.whatsapp.com/send?text=Link para Download: {{route('download-arquivo', $ab->arquivo)}}"
                                style="border-radius: 100%" href="" class="btn btn-success btn-sm"><i
                                    class="fab fa-whatsapp"></i></a></td>
                        <td data-toggle="tooltip" data-placement="top" title="Enviar via E-mail" class="text-center">
                            <a href="mailto:?subject={{$ab->nome}}&body=Download: {{route('download-arquivo', $ab->arquivo)}}"
                                style="border-radius: 100%" href="" class="btn btn-primary btn-sm"><i
                                    class="fa fa-envelope"></i></a></td>
                                    @if (Auth::user()->nivel_acesso == 1)
                        <td data-toggle="tooltip" data-placement="top" title="Editar" class="text-center"> <a
                                onclick="editarArquivo({{$ab->id}})" data-toggle="modal" data-target="#modal-edit"
                                class="btn btn-sm btn-warning" style="border-radius: 100%"><i
                                    class="fa fa-edit text-light"></i></a></td>
                        <td data-toggle="tooltip" data-placement="top" title="Excluir" class="text-center"> <a
                                id="delete-confirm" onclick=" return confirm('Tem certeza que seja excluir esse arquivo?')" href="{{route('delete-arquivo', $ab->id)}}"
                                class="btn btn-sm btn-danger" style="border-radius: 100%"><i
                                    class="fa fa-trash"></i></a></td>
                                    @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="w-100">
                            <p>Nenhum arquivo corresponde a busca</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif



    <section class="row mt-2 border rowpz ">
        @forelse ($pastas as $p)
        <div class="col-md-10 col-sm-6 col-6 div-pasta">
            <a style="height: 100%;" oncontextmenu="menu({{$p->id}})" id="pastinha_{{$p->id}}" class="text-dark pasta"
                href="{{route('visualizar-arquivo', $p->id)}}">
                <div oncontextmenu="menu({{$p->id}})" class="h-100 w-100">
                    <p style="font-size: 1.1rem"><i class="fa fa-folder" style="color: #f2d06c"></i> {{$p->nome}}</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-sm-6 col-6">
            <p class="float-right text-secondary">Criada em: {{  date('d/m/Y', strtotime($p->created_at)) }}</p>
        </div>
        @if (Auth::user()->nivel_acesso == 1)
        <div class="context-menu" id="context-menu_{{$p->id}}">
            <div oncontextmenu="return false;" class="item">
                <a onclick="editar({{$p->id}})" data-toggle="modal" data-target="#edit-pasta" class="text-light"
                    href="{{route('visualizar-arquivo', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-edit"></i> Editar
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="mover-pasta"  onclick="modalMove({{$p->id}});" class="text-light" href="#">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-folder" aria-hidden="true"></i> Mover
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="exclui-pasta" onclick=" return confirm('Tem certeza que seja excluir essa pasta?')" class="text-light" href="{{route('delete-pasta', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-trash"></i> Excluir
                    </div>
                </a>
            </div>
        </div>
        @endif
        @empty
        <div class="col-md-12">
            Não existem pastas
        </div>
        @endforelse

    </section>
    <section class="row mt-2">
        <div class="col-md-12 text-right">
            {{ $pastas->links() }}
        </div>
    </section>
</div>

<div class="modal" id="nova-pasta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Nova Pasta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-pasta" method="POST" action="{{route('nova-pasta')}}">
                    @csrf
                    <input name="nome" type="text" placeholder="Nome da Pasta" class="form-control">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-pasta" class="btn btn-primary">Salvar Pasta</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="edit-pasta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Editar Pasta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit-pasta" method="POST" action="{{route('edit-pasta')}}">
                    @csrf
                    <input id="nome-pasta" name="nome" type="text" placeholder="Nome da Pasta" class="form-control">
                    <input type="hidden" name="id" id="id-pasta">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-edit-pasta" class="btn btn-primary">Salvar Pasta</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Editar Nome</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-edit" method="POST" action="{{route('edit-arquivo')}}">
                            @csrf
                            <input name="nome" id="edit-nome" type="text" placeholder="Nome da Imagem"
                                class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <input  id="edit-descricao" placeholder="Descrição" name="descricao" type="hidden">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="id" id="id-arquivo">
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-edit" class="btn btn-primary">Salvar</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

{{-- <div class="modal" id="modal-descricao" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div id="conteudo-descricao" class="text-justify col-md-12 col-12 col-sm-12">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="modal" id="move-pasta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Mover Para</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-move-pasta" method="POST" action="{{route('move-pasta')}}">
                    @csrf
                    <select name="pasta_pai" class="form-control" id="pasta-ppai">
                        <option selected disable select value>Selecionar pasta</option>
                        @foreach ($listagem as $p)
                            <option data-pai="{{($p->pasta) ? $p->pasta->id : 'xizt'}}" value="{{$p->id}}"><i class="fa fa-folder"></i>{{($p->pasta) ? $p->pasta->nome.'/'.$p->nome : $p->nome }}</option>
                        @endforeach
                    </select>   
                    <input type="hidden" name="pasta" id="move">
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-move-pasta" class="btn btn-primary">Mover</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#save-pasta').click(function () {
        $('#form-pasta').submit();
    });

    $('#save-edit').click(function () {
        $('#form-edit').submit();
    });

    $('#save-edit-pasta').click(function () {
        $('#form-edit-pasta').submit();
    });

    $('#save-move-pasta').click(function () {
        $('#form-move-pasta').submit();
    });


    function modalMove(id){

        
       
        $('#move-pasta').modal();
        $('#move').val(id);

        $("#pasta-ppai option").each(function()
        {
            console.log(this.value);
            console.log($(this).data('pai'));

            if (this.value ==  $('#move').val() || $('#move').val() == $(this).data('pai') ) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
       
    }



    function menu(id) {
        var pastinha = 'pastinha_';
        var ids = id;
        var pasta = pastinha + ids;
        var container = document.getElementById(pasta);
        var context = 'context-menu_' + ids;

        var contextElement = document.getElementById(context);

        container.addEventListener("contextmenu", function (event) {

            var elems = document.querySelectorAll(".active");
            event.preventDefault();
            [].forEach.call(elems, function (el) {
                el.classList.remove("active");
            });
            contextElement.style.top = $('#' + context).offset({
                left: event.pageX,
                top: event.pageY
            });
            contextElement.style.left = $('#' + context).offset({
                left: event.pageX,
                top: event.pageY
            });
            contextElement.classList.add("active");
        });
        window.addEventListener("click", function () {
            document.getElementById(context).classList.remove("active");
        });

    }


    function editar(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/broadside/retorna-pasta',
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            },
            success: function (data) {
                $('#nome-pasta').val(data['nome']);
                $('#id-pasta').val(data['id']);
            },
        });
    }

    // function descricao(id) {
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     $.ajax({
    //         url: '/broadside/retorna',
    //         type: 'post',
    //         dataType: 'json',
    //         data: {
    //             id: id
    //         },
    //         success: function (data) {
    //             if (data['descricao'] != null) {
    //                 $('#conteudo-descricao').html('<b>' + data['descricao'] + '</b>');
    //             } else {
    //                 $('#conteudo-descricao').html('<b>Sem Descrição</b>');
    //             }

    //         },
    //     });
    // }

    function editarArquivo(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/broadside/retorna',
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            },
            success: function (data) {
                $('#edit-nome').val(data['nome']);
                $('#edit-descricao').val(data['descricao']);
                $('#id-arquivo').val(data['id']);
            },
        });
    }

</script>

<style>
    .div-pasta:hover {
        background-color: aquamarine;
    }

    .rowpz {
        background: #fdfdfe;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .ropz2 {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        background: #fdfdfe;
    }

    .context-menu {
        position: fixed;
        z-index: 10000;
        width: 100px;
        background: #1b1a1a;
        border-radius: 5px;
        transform: scale(0);
        transform-origin: top left;
    }

    .context-menu.active {
        transform: scale(1);
        transition: transform 300ms ease-in-out;
    }

    .context-menu .item {
        padding: 8px 10px;
        font-size: 15px;
        color: #eee;
    }

    .context-menu .item:hover {
        background: #555;
    }

    .context-menu .item i {
        display: inline-block;
        margin-right: 5px;
    }

    .context-menu hr {
        margin: 2px 0px;
        border-color: #555;
    }

</style>


@endsection
