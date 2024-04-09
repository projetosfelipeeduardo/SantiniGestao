@extends('adminlte::page')
@section('title', 'Broadside')

@section('content')

@php
    $downloads = [];
    if(Session::get('download.arquivos')){
        $downloads = Session::get('download.arquivos');
    }
@endphp

<div class="container">

    <section class="row">
        <div class="col-md-1 text-center">
            <a href="{{route('arquivo.index')}}" class="btn btn-principal text-light">Voltar</a>
        </div>
        @if (Auth::user()->nivel_acesso == 1)
        <div class="col-md-2">
            <a href="#" data-toggle="modal" data-target="#novo-arquivo"> <i class="fa fa-plus"></i> Adicionar
                Arquivo</a>
        </div>
        <div class="col-md-2 text-center">
            <a href="#" data-toggle="modal" data-target="#novo-multiplos"> <i class="fa fa-plus"></i> Adicionar Múltiplos</a>
        </div>
        <div class="col-md-2 text-center">
            <a href="#" data-toggle="modal" data-target="#nova-pasta"> <i class="fa fa-folder"></i> Nova Pasta</a>
        </div>
        <div class="col-md-2 text-center">
            <a href="#" data-toggle="modal" data-target="#novo-link"> <i class="fa fa-folder"></i> Novo link</a>
        </div>
        <div class="col-md-3 col-sm-12 col-12">
            <div class="float-right text-center">
                @include('flash::message')
            </div>
        </div>
        @endif
    </section>



    <section class="ropz row mt-2">
        <div class="col-md-12">
            @if($pasta->pasta)
            <a  href="{{route('visualizar-arquivo', $pasta->pasta->id)}}"><b class="text-primary">{{$pasta->pasta->nome}}</b></a>
            @endif
            <b>/{{$pasta->nome}}</b>
        </div>
        @if ($pasta->subpasta == 1)
        @endif
        @foreach ($subpastas as $p)
        <div class="col-md-10 col-sm-6 col-6 div-pasta">
            <a style="height: 100%;" oncontextmenu="menu({{$p->id}})" id="pastinha_{{$p->id}}" class="text-dark pasta"
                href="{{route('visualizar-arquivo', $p->id)}}">
                <div oncontextmenu="menu({{$p->id}})" class="h-100 w-100">
                    <p  style="font-size: 1.1rem" ><i class="fa fa-folder" style="color:  #f2d06c"></i> {{$p->nome}}</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-sm-6 col-6">
            <p class="float-right text-secondary">Criada em: {{  date('d/m/Y', strtotime($p->created_at)) }}</p>
        </div>
        @if (Auth::user()->nivel_acesso == 1)
        <div class="context-menu" id="context-menu_{{$p->id}}">
            <div class="item">
                <a onclick="editarPasta({{$p->id}})" data-toggle="modal" data-target="#edit-pasta" class="text-light"
                    href="{{route('visualizar-arquivo', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-edit"></i> Editar
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="mover-pasta"  onclick="modalMove({{$p->id}}, {{($p->pasta) ? $p->pasta->id : 'null'}});" class="text-light" href="#">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-folder" aria-hidden="true"></i> Mover
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="exclui-pasta" class="text-light" href="{{route('delete-pasta', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-trash"></i> Excluir
                    </div>
                </a>
            </div>
        </div>
        @endif
        @endforeach
    </section>
    <section class="row ropz2">
        <div class="col-md-12">
            <table class="table table-responsive table-light table-stripped">
                <tbody>
                    @forelse ($arquivos as $a)

                    @php
                    $icon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
                    foreach($icones as $key => $i){
                        if(strtolower($key) == strtolower(trim($a->extensao))) {
                            $icon = $i;
                        }

                    }
                       
                    @endphp



                    <tr>
                        <td class="text-center"> <input {{in_array($a->arquivo, $downloads) ? 'checked' : '' }} class="check-select" id="checked_{{$a->id}}" type="checkbox" value="{{$a->arquivo}}" ></td>
                        <td class="text-center" >{!!$icon!!}</td>
                        <td class="text-center w-100">{{$a->nome}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Extensão do arquivo" class="text-center">
                            .{{$a->extensao}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Data de upload" class="text-center">
                            {{ date('d/m/Y', strtotime($a->created_at))}}</td>
                        <td data-toggle="tooltip" data-placement="top" title="Fazer Download/Acessar" class="text-center">
                            @if($a->extensao == 'mp4')
                            <a data-toggle="modal" data-target="#modal-video" href="#" class="btn btn-sm btn-info"
                                style="border-radius: 100%"><i class="text-light fa fa-play"></i></a>
                            @elseif($a->extensao == 'link')
                                <a class="btn btn-info btn-sm" style="border-radius: 100%;" target="_blank" href="//{{$a->descricao}}">
                                    <i class="fas fa-link"></i>
                                </a>
                            @elseif($a->extensao != 'link' and $a->extensao != 'mp4')
                            <a href="{{route('download-arquivo', $a->arquivo)}}" class="btn btn-sm btn-info"
                                style="border-radius: 100%"><i class="fa fa-download"></i></a>
                            @endif
                        </td>
                        <td data-toggle="tooltip" data-placement="top" title="Enviar via Whatsapp" class="text-center">
                            @if($a->extensao == 'link')
                            <a target="_blank"  href="https://api.whatsapp.com/send?text=Link para Acesso: {{$a->descricao}}" style="border-radius: 100%" href="" class="btn btn-success btn-sm"><i
                                class="fab fa-whatsapp"></i></a>


                            @else
                            <a target="_blank"  href="https://api.whatsapp.com/send?text=Link para Download: {{route('download-arquivo', $a->arquivo)}}" style="border-radius: 100%" href="" class="btn btn-success btn-sm"><i
                                class="fab fa-whatsapp"></i></a>
                            @endif
                        </td>
                        <td data-toggle="tooltip" data-placement="top" title="Enviar via E-mail" class="text-center">
                            @if($a->extensao == 'link')
                                <a  href="mailto:?subject={{$a->nome}}&body=Link para acesso: {{$a->descricao}}" style="border-radius: 100%" href="" class="btn btn-primary btn-sm"><i
                                    class="fa fa-envelope"></i>
                                </a>
                            @else
                                <a  href="mailto:?subject={{$a->nome}}&body=Download: {{route('download-arquivo', $a->arquivo)}}" style="border-radius: 100%" href="" class="btn btn-primary btn-sm"><i
                                        class="fa fa-envelope"></i>
                                </a>
                            @endif
                        </td>
                                    @if (Auth::user()->nivel_acesso == 1)
                        <td data-toggle="tooltip" data-placement="top" title="Editar" class="text-center"> <a
                                onclick="editar({{$a->id}})" data-toggle="modal" data-target="#modal-edit"
                                class="btn btn-sm btn-warning" style="border-radius: 100%"><i
                                    class="fa fa-edit text-light"></i></a></td>
                        <td data-toggle="tooltip" data-placement="top" title="Excluir" class="text-center"> <a
                        onclick=" return confirm('Tem certeza que seja excluir esse arquivo?')" id="delete-confirm" href="{{route('delete-arquivo', $a->id)}}"
                                class="btn btn-sm btn-danger" style="border-radius: 100%"><i
                                    class="fa fa-trash"></i></a></td>
                                    @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center w-100">
                            <p>Não existem arquivos nessa pasta</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="row">
        <div class="col-md-12 text-right">
            <div class="float-right">
                {{$arquivos->links()}}
            </div>
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
                <form id="form-pasta" method="POST" action="{{route('nova-subpasta')}}">
                    @csrf
                    <input name="nome" type="text" placeholder="Nome da Pasta" class="form-control">
                    <input type="hidden" name="pasta_pai" value="{{$pasta->id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-pasta" class="btn btn-primary">Salvar Pasta</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-video" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Visualizar video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                    <video width="500" src="https://www.youtube.com/watch?v=-xrNbdySG-Y"></video>
                
            </div>
            <div class="modal-footer">
                <button type="button" id="save-pasta" class="btn btn-primary">Salvar Pasta</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal" id="novo-arquivo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Novo Arquivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-arquivo" enctype="multipart/form-data" method="POST"
                            action="{{route('upload-arquivo')}}">
                            @csrf
                            <input name="nome" type="text" placeholder="Nome do Arquivo" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="descricao">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="file" name="arquivo" class="form-control">
                        <input type="hidden" name="pasta" value="{{$pasta->id}}">
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-arquivo" class="btn btn-primary">Salvar Arquivo</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="novo-multiplos" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Novos arquivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                   <form id="form-multiplo" action="{{route('upload-arquivos-multiplos')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input multiple type="file" name="arquivos[]" >
                    <input type="hidden" name="pasta" value="{{$pasta->id}}">
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-arquivo-multipla" class="btn btn-primary">Salvar Arquivos</button>

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
                        <form id="form-edit" enctype="multipart/form-data" method="POST"
                            action="{{route('edit-arquivo')}}">
                            @csrf
                            <input name="nome" id="edit-nome" type="text" placeholder="Nome da Imagem"
                                class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" id="edit-descricao" name="descricao">
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
                        <option value="raiz">Mover para Raiz</option>
                        @foreach ($listagem as $p)
                        <option data-pai="{{($p->pasta) ? $p->pasta->id : 'xizt'}}" value="{{$p->id}}">{{($p->pasta) ? $p->pasta->nome.'/'.$p->nome :  $p->nome }}</option>
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

<div class="modal" id="novo-link" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Novo Link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-arquivo"  method="POST"
                            action="{{route('cadastrar-link')}}">
                            @csrf
                            <input name="nome" type="text" placeholder="Nome do link" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        
                        <input type="text" class="form-control" placeholder="Link" name="descricao">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="pasta" value="{{$pasta->id}}">
                       
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="save-arquivo" class="btn btn-primary">Salvar Link</button>
            </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            boundary: 'window'
        })
    })

    $('#save-arquivo-multipla').click(function () {
        $('#form-multiplo').submit();
    });

    $('#save-edit-pasta').click(function () {
        $('#form-edit-pasta').submit();
    });

    $('#save-arquivo').click(function () {
        $('#form-arquivo').submit();
    });

    $('#save-pasta').click(function () {
        $('#form-pasta').submit();
    });

    $('#save-edit').click(function () {
        $('#form-edit').submit();
    });

    $('#save-move-pasta').click(function () {
        $('#form-move-pasta').submit();
    });

    $('#delete-confirm').click(function () {
        return confirm("Tem certeza que seja excluir esse arquivo?");
    });

    $('#exclui-pasta').click(function () {
        return confirm("Tem certeza que seja excluir essa pasta?");
    });


    function modalMove(id, pai){
        
       
        $('#move-pasta').modal();
        $('#move').val(id);

        $("#pasta-ppai option").each(function()
        {
            if (this.value ==  $('#move').val() || $('#move').val() == $(this).data('pai')  || this.value == pai) {
                $(this).hide();
            } else {
                $(this).show();
            }
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
    //                 $('#conteudo-descricao').html('<p>' + data['descricao'] + '</pre>')
    //             }else{
    //                 $('#conteudo-descricao').html('<b>Sem Descrição</b>');
    //             }   
    //         },
    //     });
    // }

    function editarPasta(id) {
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


    function editar(id) {
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

    function menu(id) {
        var pastinha = 'pastinha_';
        var ids = id;
        var pasta = pastinha + ids;
        console.log(pasta);
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


    $('.check-select').click(function(){

var valor = $(this).val(); 

if($(this).is(":checked")){
   
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url: '/broadside/adicionar-na-sessao',
    type: 'post',
    dataType: 'json',
    data: {valor :valor},
    success: function (data) {
        atualizarSessao()
    },
    error: function (data) {
        atualizarSessao()
    }
});

}else{
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url: '/broadside/remover-da-sessao',
    type: 'post',
    dataType: 'json',
    data: {valor :valor},
    success: function (data) {
        atualizarSessao()
    },
    error: function (data) {
        atualizarSessao()
    }
});
}



});

function atualizarSessao(){

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url: '/images/atualizar-sessao',
    type: 'get',
    dataType: 'json',
    success: function (data) {

    if(data > 0){
        $('#contador-download').html(data);
        $('#nenhum').hide();
        $('#fazer-download').show();
    }else if( data <= 0){
        $('#nenhum').show();
        $('#fazer-download').hide();
        $('#contador-download').html(data);
    }
        
    },
    error: function (data) {

    }
});

}


</script>

<style>
    .div-pasta:hover {
        background-color: aquamarine;
    }

    .ropz {
        background: #fdfdfe;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .tooltip {
        pointer-events: none;

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

    /*  */

</style>
@endsection
