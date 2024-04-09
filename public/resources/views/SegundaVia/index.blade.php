@extends('adminlte::page')

@section('title', '2° via de Boleto')

@section('content')


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <h3 class="text-muted">Segunda via Ita&uacute; Shopline</h3>
      </div>
        <form action="{{route('boleto.requisicao')}}" method="POST">
            @csrf
        <div class="jumbotron">
            <h1>CPF/CNPJ</h1>
            <p class="lead">
            <input  type="text" id="fiscal" name="fiscal" class="form-control" placeholder="Informe o CPF/CNPJ usado no Pedido!" required>
            </p>
            <p><button class="btn btn-lg btn-success">Consultar</button></p>
        </div>
	  </form>


    </div>



    <script>
    $('#fiscal').mask('00.000.000/0000-00', {reverse: true});

    </script>

@endsection
