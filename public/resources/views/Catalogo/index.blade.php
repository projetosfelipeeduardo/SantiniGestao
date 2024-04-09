@extends('adminlte::page')
@section('title', 'Catálogo')

@section('content')

<div class="container">

<section class="row">
    <div class="col-md-12">
        <h3>Catálogo</h3>
    </div>
</section>

<section class="row mt-2">
    <div class="col-md-2 col-sm-6 col-6">
        <a data-toggle="modal" data-target="#novo-arquivo" class="btn btn-principal text-light">Adicionar Página</a>
    </div>
    <div class="col-md-1 col-sm-6 col-6">
        <a href="{{route('catalogo.catalogo')}}" target="_blank" class="btn btn-success text-light">Visualizar</a>
    </div>
</section>


<section class="row mt-3">
    <div class="col-md-12">
        <ul class="row" id="sortable">
            @foreach ($paginas as $item)
            <div  data-index="{{$item->id}}" data-position="{{$item->pagina}}" class="col-md-3 col-lg-3 mt-2 col-sm-12 col-12 text-center">
                <li class="ui-state-default"><a onclick=" return confirm('Tem certeza que seja excluir essa página?')" href="{{route('catalogo.delete', $item->id)}}" style="cursor:pointer;border-radius: 100%;" class="btn btn-sm text-light btn-danger"><i class="fa fa-trash"></i></a><img width="150" height="200" src="{{asset('/storage/catalogo/'.$item->arquivo)}}" alt=""><p>{{$item->pagina}}</p></li>
            </div>
            @endforeach
          </ul>
    </div>
</section>

</div>


<div class="modal" id="novo-arquivo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Nova Pagina</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form id="form-arquivo" enctype="multipart/form-data" method="POST"
                            action="{{route('upload-catalogo')}}">
                            @csrf
                    </div>
                    <div class="col-12 mt-2">
                        <input type="file" name="arquivo" class="form-control">
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

<script src="{{asset('plugins/ui/jquery-ui.js')}}"></script>
<script>


function saveNewPositions(){
        var positions = [];

        $('.updated').each(function(){
            positions.push([$(this).attr('data-index'), $(this).attr('data-position')]);
            $(this).removeClass('updated');
        });
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

        $.ajax({
            url: '/catalogo/update-posicao',
            method: 'POST',
            dataType:'text',
            data:{
                update: 1,
                positions: positions
            },
            success: function(response){
                window.location.assign("http://oetkertecnologia1.hospedagemdesites.ws/catalogo/index");
            }
        });

    }


    $('#save-arquivo').click(function () {
        $('#form-arquivo').submit();
    });


    $('#sortable').sortable({
        update:function(event, ui){
           $(this).children().each(function (index) {
                if($(this).attr('data-position') != (index+1)){
                    $(this).attr('data-position', (index+1)).addClass('updated')
                }
           });

           saveNewPositions();
        }
    });

    $('#sortable').children().each(function (index) {
        $('#sortable').attr('data-position', (index+1)).addClass('updated')
           });

   saveNewPositions();


</script>

<style>
    ul {
    list-style-type: none!important;
}
</style>
@endsection
