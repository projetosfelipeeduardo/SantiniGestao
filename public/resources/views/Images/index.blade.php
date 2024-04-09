@extends('adminlte::page')


@section('title', 'Imagens')
@section('content')


<div class="container">

 
<section class="row">
    <div class="col-md-3">
        <a data-toggle="modal" data-target="#nova-pasta" href="#" ><i class="fa fa-plus"></i> Nova Pasta</a>
    </div>
    <div class="col-md-9 col-sm-12 col-12">
        <div class="float-right text-center w-50">
            @include('flash::message')
        </div>
    </div>
</section>


<section class="row mt-2">
    <div class="col-md-7"></div>
    <div class="col-md-3 col-10 col-sm-10 ">
        <div>
            <form method="GET" action="{{route('images.index')}}">
                <input value="" name="nome" placeholder="Buscar Pasta ou Imagem" class="form-control"
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
            href="{{route('visualizar-imagem', $pb->id)}}">
            <div oncontextmenu="menu({{$pb->id}})" class="h-100 w-100">
                <p style="font-size: 1.1rem" ><i class="fa fa-folder" style="color: #f2d06c"></i> {{$pb->nome . ' ('. $pb->usuario->name . ')'}}</p>
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
                href="{{route('visualizar-imagem', $pb->id)}}">
                <div class="h-100 w-100" class="col-12">
                    <i class="fa fa-edit"></i> Editar
                </div>
            </a>
        </div>
        <div oncontextmenu="return false;" class="item">
            <a id="exclui-pasta" onclick=" return confirm('Tem certeza que seja excluir essa pasta?')"  class="text-light" href="{{route('delete-pasta-image', $pb->id)}}">
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
                    <td data-toggle="tooltip" data-placement="top" title="Expandir" class="text-center">
                            <a data-toggle="modal" data-target="#modal-exibir" onclick="retorna2({{$ab->id}})"
                                class="btn btn-sm btn-warning text-light" style="border-radius: 100%"><i
                                    class="fa fa-image"></i></a>
                            </td>
                    <td data-toggle="tooltip" data-placement="top" title="Fazer Download" class="text-center"> <a
                            href="{{route('download-image', $ab->arquivo)}}" class="btn btn-sm btn-success"
                            style="border-radius: 100%"><i class="fa fa-download"></i></a></td>
                    <td data-toggle="tooltip" data-placement="top" title="Enviar via Whatsapp" class="text-center">
                        <a target="_blank"
                            href="https://api.whatsapp.com/send?text=Link para Download: {{route('download-image', $ab->arquivo)}}"
                            style="border-radius: 100%" href="" class="btn btn-success btn-sm"><i
                                class="fab fa-whatsapp"></i></a></td>
                    <td data-toggle="tooltip" data-placement="top" title="Enviar via E-mail" class="text-center">
                        <a href="mailto:?subject={{$ab->nome}}&body=Download: {{route('download-image', $ab->arquivo)}}"
                            style="border-radius: 100%" href="" class="btn btn-primary btn-sm"><i
                                class="fa fa-envelope"></i></a></td>
                  @if (Auth::user()->nivel_acesso == 1)
                    <td data-toggle="tooltip" data-placement="top" title="Editar" class="text-center"> <a
                            onclick="retorna({{$ab->id}})" data-toggle="modal" data-target="#modal-edit"
                            class="btn btn-sm btn-primary" style="border-radius: 100%"><i
                                class="fa fa-edit text-light"></i></a></td>
                    <td data-toggle="tooltip" data-placement="top" title="Excluir" class="text-center"> <a
                            id="delete-confirm" href="{{route('delete-image', $ab->id)}}"
                            class="btn btn-sm btn-danger" onclick=" return confirm('Tem certeza que seja excluir esse arquivo?')"  style="border-radius: 100%"><i
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
            href="{{route('visualizar-imagem', $p->id)}}">
            <div oncontextmenu="menu({{$p->id}})" class="h-100 w-100">
                <p style="font-size: 1.1rem"><i class="fa fa-folder" style="color: #f2d06c"></i> {{$p->nome . ' ('. $p->usuario->name . ')'}}</p>
            </div>
        </a>
    </div>
    <div class="col-md-2 col-sm-6 col-6">
        <p class="float-right text-secondary">Criada em: {{  date('d/m/Y', strtotime($p->created_at)) }}</p>
    </div>
  
    <div class="context-menu" id="context-menu_{{$p->id}}">
        <div oncontextmenu="return false;" class="item">
            <a onclick="editar({{$p->id}})" data-toggle="modal" data-target="#edit-pasta" class="text-light"
                href="{{route('visualizar-imagem', $p->id)}}">
                <div class="h-100 w-100" class="col-12">
                    <i class="fa fa-edit"></i> Editar
                </div>
            </a>
        </div>
        @if (Auth::user()->nivel_acesso == 1)
        <div oncontextmenu="return false;" class="item">
            <a id="mover-pasta"  onclick="modalMove({{$p->id}});" class="text-light">
                <div class="h-100 w-100" class="col-12">
                    <i class="fa fa-folder" aria-hidden="true"></i> Mover
                </div>
            </a>
        </div>
        @endif
        <div oncontextmenu="return false;" class="item">
            <a id="exclui-pasta" onclick=" return confirm('Tem certeza que seja excluir essa pasta?')"  class="text-light" href="{{route('delete-pasta-image', $p->id)}}">
                <div class="h-100 w-100" class="col-12">
                    <i class="fa fa-trash"></i> Excluir
                </div>
            </a>
        </div>
    </div>
    
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
            <form id="form-pasta" method="POST" action="{{route('nova-pasta-imagem')}}">
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
                        @foreach ($pastas as $p)
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



  <script>

    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            boundary: 'window'
        })
    })

    $('#save-pasta').click(function(){
    $('#form-pasta').submit();
    });

    $('#save-edit-pasta').click(function () {
        $('#form-edit-pasta').submit();
    });

    $('#save-edit').click(function () {
    $('#form-edit').submit();
    });

    $('#save-move-pasta').click(function () {
        $('#form-move-pasta').submit();
    });

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

    function editar(id) {
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
            var url = '{{asset('storage/images/')}}';
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
  </script>

  <style>
      .div-pasta:hover{
          background-color: aquamarine;
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

  </style>
@endsection
