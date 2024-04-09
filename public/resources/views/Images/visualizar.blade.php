@extends('adminlte::page')
@section('title', 'Visualizar')
@section('content')

@php
    $downloads = [];
    if(Session::get('download.images')){
        $downloads = Session::get('download.images');
    }
@endphp

<div class="container">

    <section class="row">
        <div class="col-md-1 text-center">
            <a href="{{route('images.index')}}" class="btn btn-principal text-light">Voltar</a>
        </div>
      
        <div class="col-md-2">
            <a href="#" data-toggle="modal" data-target="#nova-imagem"> <i class="fa fa-plus"></i> Adicionar Imagem</a>
        </div>
        <div class="col-md-2">
            <a href="#" data-toggle="modal" data-target="#nova-imagem-multiplas"> <i class="fa fa-plus"></i> Adicionar Múltiplas</a>
        </div>
        <div class="col-md-2">
            <a href="#" data-toggle="modal" data-target="#nova-pasta"> <i class="fa fa-folder"></i> Nova Pasta</a>
        </div>

        <div class="col-12 col-md-5">
            <div class="float-right text-center w-50">
                @include('flash::message')
            </div>
        </div>
  
    </section>

    <section class="row mt-2">
        <div class="col-md-4">
            @if($pasta->pasta)
            <a  href="{{route('visualizar-imagem', $pasta->pasta->id)}}"><b class="text-primary">{{$pasta->pasta->nome}}</b></a>
            @endif
            <b>/{{$pasta->nome}}</b>
        </div>
     
    </section>

    <section class="ropz row mt-2">
        @if ($pasta->subpasta == 1)
        <div class="col-md-12 ml-2">
            <a href="{{route('visualizar-imagem', $pasta->pasta_pai)}}"><i class="fa fa-arrow-left"
                    aria-hidden="true"></i> Anterior</a>
        </div>
        @endif
        @foreach ($subpastas as $p)
        <div class="col-md-10 col-sm-6 col-6 div-pasta">
            <a style="height: 100%;" oncontextmenu="menu({{$p->id}})" id="pastinha_{{$p->id}}" class="text-dark pasta"
                href="{{route('visualizar-imagem', $p->id)}}">
                <div oncontextmenu="menu({{$p->id}})" class="h-100 w-100">
                    <p style="font-size: 1.1rem"><i class="fa fa-folder" style="color: #f2d06c"></i> {{$p->nome}}</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-sm-6 col-6">
            <p class="float-right text-secondary">Criada em: {{  date('d/m/Y', strtotime($p->created_at)) }}</p>
        </div>

        <div class="context-menu" id="context-menu_{{$p->id}}">
            <div class="item">
                <a onclick="editarPasta({{$p->id}})" data-toggle="modal" data-target="#edit-pasta" class="text-light"
                    href="{{route('visualizar-imagem', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-edit"></i> Editar
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="mover-pasta"  onclick="modalMove({{$p->id}});" class="text-light">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-folder" aria-hidden="true"></i> Mover
                    </div>
                </a>
            </div>
            <div oncontextmenu="return false;" class="item">
                <a id="exclui-pasta" onclick=" return confirm('Tem certeza que seja excluir essa pasta?')"  class="text-light" href="{{route('delete-pasta-image', $p->id)}}">
                    <div class="h-100 w-100" class="col-12">
                        <i class="fa fa-trash"></i> Excluir
                    </div>
                </a>
            </div>
        </div>
   
        @endforeach
    </section>

    <section class="row text-center mt-3">

        @forelse ($arquivos as $a)

        <div class="col-md-4 text-center">
           
            <div class="col-12 text-center img-container">
                <img style="max-width: 90%; max-height: 90%;" class="  image img-responsive img-fluid  "
                    src="{{asset('images/despesas/'.$a->arquivo)}}" alt="">
                <p>{{$a->nome}}</p>
                <div class="after text-center">
                    <div data-toggle="tooltip" data-placement="top" title="Download" class="mt-1 icons">
                        <a  href="{{route('download-image', $a->arquivo)}}" class="btn btn-sm btn-success"
                            style="border-radius: 100%"><i class="fa fa-download"></i></a>
                    </div>
                    <div data-toggle="tooltip" data-placement="top" title="Expandir" class="icons mt-1">
                        <a data-toggle="modal" data-target="#modal-exibir" onclick="retorna2({{$a->id}})"
                            class="btn btn-sm btn-warning text-light" style="border-radius: 100%"><i
                                class="fa fa-image"></i></a>
                    </div>
                 
                    <div data-toggle="tooltip" data-placement="top" title="Editar" class="icons mt-1">
                        <a data-toggle="modal" onclick="retorna({{$a->id}})" data-target="#modal-edit"
                            class="btn btn-sm btn-primary" style="border-radius: 100%"><i class="fa text-light fa-edit"></i></a>
                    </div>
                    <div data-toggle="tooltip" data-placement="top" title="Excluir" class="icons mt-1">
                        <a  href="{{route('delete-image', $a->id)}}" onclick=" return confirm('Tem certeza que seja excluir esse arquivo?')"   class="btn btn-sm btn-danger"
                            style="border-radius: 100%"><i class="fa fa-trash"></i></a>
                    </div>
                
                    <div data-toggle="tooltip" data-placement="top" title="Enviar via Whatsapp" class="icons mt-1">
                        <a target="_blank" href="https://api.whatsapp.com/send?text=Link para Download: {{route('download-image', $a->arquivo)}}"
                        style="border-radius: 100%" href="" class="btn btn-success btn-sm"><i
                                    class="fab fa-whatsapp"></i></a>
                    </div>
                    <div data-toggle="tooltip" data-placement="top" title="Enviar via E-mail" class="icon mt-1">
                        <a href="mailto:?subject={{$a->nome}}&body=Download: {{route('download-image', $a->arquivo)}}"
                            style="border-radius: 100%" href="" class="btn btn-primary btn-sm"><i
                                class="fa fa-envelope"></i></a>
                    </div>
                </div>
            </div>



            <input {{in_array($a->arquivo, $downloads) ? 'checked' : '' }} class="check-select" id="checked_{{$a->id}}" type="checkbox" value="{{$a->arquivo}}" >
        </div>
        @empty 
        <div class="col-md-4">
            <p>Nenhum Arquivo nessa pasta</p>
        </div>
        @endforelse

    </section>
    <section class="row mt-3">
        <div class="col-md-12">
            {{$arquivos->links()}}
        </div>
    </section>


</div>



<div class="modal" id="nova-imagem" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Nova Imagem</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-imagem" enctype="multipart/form-data" method="POST"
                            action="{{route('upload-image')}}">
                            @csrf
                            <input name="nome" type="text" placeholder="Nome da Imagem" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <textarea class="form-control" placeholder="Descrição" name="descricao" rows="5"></textarea>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="file" name="arquivo" class="form-control">
                        <input type="hidden" name="pasta" value="{{$pasta->id}}">
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-imagem" class="btn btn-primary">Salvar Imagem</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="nova-imagem-multiplas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Novas Imagens</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                   <form id="form-multiplo" action="{{route('upload-multiplas')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input multiple type="file" name="arquivos[]" >
                    <input type="hidden" name="pasta" value="{{$pasta->id}}">
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-imagem-multipla" class="btn btn-primary">Salvar Imagens</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal" id="modal-edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Editar Imagem</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-edit" enctype="multipart/form-data" method="POST"
                            action="{{route('edit-image')}}">
                            @csrf
                            <input name="nome" id="edit-nome" type="text" placeholder="Nome da Imagem"
                                class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <textarea class="form-control" id="edit-descricao" placeholder="Descrição" name="descricao"
                            rows="5"></textarea>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="id" id="id-imagem">
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



<div class="modal" id="modal-exibir" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div id="exibicao" class="text-center col-md-12">

                    </div>
                </div>
                <div class="row">
                    <div id="description" class="col-md-12 text-justify">

                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <form id="form-pasta" method="POST" action="{{route('nova-subpasta-image')}}">
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
                <form id="form-edit-pasta" method="POST" action="{{route('edit-pasta-image')}}">
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
                <form id="form-move-pasta" method="POST" action="{{route('move-pasta-image')}}">
                    @csrf
                    <select name="pasta_pai" class="form-control" id="pasta-ppai">
                        <option selected disable select value>Selecionar pasta</option>
                        @foreach ($subpastas as $p)
                            <option value="{{$p->id}}">{{$p->nome}}</option>
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





<style>
    .div-pasta:hover{
        background: aquamarine;
    }

    .img-container {
        overflow: hidden 10px;
        width: 316px;
        height: 260px;
        background: white;
        border-radius: 10px;
        margin: 5px;
        padding: 5px;
    }

    .img-container .after {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        color: rgba(0, 0, 0, .4);
    }

    .img-container:hover .after {
        display: flex;
        padding: 100px 20px;
        justify-content: space-between;
        background: rgba(0, 0, 0, .4);
        border-radius: 10px;
    }

    .after .icons {
        top: 30%;
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


<script>

$('#save-edit-pasta').click(function () {
        $('#form-edit-pasta').submit();
    });

$('#save-imagem').click(function () {
    $('#form-imagem').submit();
});

$('#save-edit').click(function () {
    $('#form-edit').submit();
});

$('#save-pasta').click(function () {
        $('#form-pasta').submit();
    });

    $('#save-imagem-multipla').click(function () {
    $('#form-multiplo').submit();
});

$('#save-move-pasta').click(function () {
        $('#form-move-pasta').submit();
    });

$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        boundary: 'window'
    })
})


function retorna(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/images/retorna',
        type: 'post',
        dataType: 'json',
        data: {
            id: id
        },
        success: function (data) {
            $('#edit-nome').val(data['nome']);
            $('#edit-descricao').val(data['descricao']);
            $('#id-imagem').val(data['id']);
        },
    });
}

function retorna2(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/images/retorna',
        type: 'post',
        dataType: 'json',
        data: {
            id: id
        },
        success: function (data) {
            var url = '{{asset('images/despesas/')}}';
            var arquivo = data['arquivo'];
            var teste = url + '/' + arquivo;
            console.log(url);
            $('#exibicao').html(
                '<img style="max-width: 100%; max-height: 100%;" class="  image img-responsive img-fluid  " src="' +
                teste + '">');
            if (data['descricao'] != null) {
                $('#description').html('<p>' + data['descricao'] + '</p>')
            }
        }
    });
}

function editarPasta(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/images/retorna-pasta-image',
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

function modalMove(id){

$('#move-pasta').modal();
$('#move').val(id);

$("#pasta-ppai option").each(function()
{
    if (this.value ==  $('#move').val()) {
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

    $('.check-select').click(function(){

        var valor = $(this).val(); 

        if($(this).is(":checked")){
           
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/images/adicionar-na-sessao',
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
            url: '/images/remover-da-sessao',
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
                $('#compa').show();
                $('#fazer-download').show();
            }else if( data <= 0){
                $('#nenhum').show();
                $('#compa').hide();
                $('#fazer-download').hide();
                $('#contador-download').html(data);
            }
                
            },
            error: function (data) {

            }
        });

    }


</script>
@endsection
