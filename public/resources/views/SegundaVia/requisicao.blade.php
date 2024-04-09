@extends('adminlte::page')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-muted">Consulta</h3>
        </div>
    </div>
</div>

<iframe id="itau-frmae" src="https://ww2.itau.com.br/2viabloq/pesquisa.asp" class="w-100" height="600" frameborder="0"></iframe>

<body onload="Enviar('itau'), carregabrw()">

    <form target="#itau-frame" onsubmit=”carregabrw()” ACTION='https://ww2.itau.com.br/2viabloq/pesquisa.asp' METHOD='Post' name='itau'>
        <INPUT type=hidden name=DC value="{{$dc}}">
        <INPUT type=hidden name=msg value="S">
    </form>

    <script language="JavaScript">
        function Enviar(NomeDoForm){
           document.forms[NomeDoForm].submit();
        }

        function carregabrw() {
            window.open('','BLOQUETO',
            'toolbar=yes,menubar=yes,resizable=yes,status=no,scrollbars=yes,left=0,top=0,width=600,height=430');
        }
    </script>
@endsection
