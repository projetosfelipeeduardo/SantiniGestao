@extends('adminlte::page')

@section('title', 'MPDV')
@section('content')


    <form id="login" target="frame" src="http://sistema.axiasolucoes.com.br:8084/" method="post" action="http://sistema.axiasolucoes.com.br:8084/v_login.asp">
        <input type="hidden" name="txt_usuario"  value="{{$login}}" />
        <input type="hidden" name="txt_senha" value="{{$senha}}" />
    </form>
    <iframe style="width: 100%; height: 650px; padding: 10px; border:none; "  id="frame" name="frame"></iframe>



<script type="text/javascript">
    $('body').addClass('sidebar-collapse');
    // submit the form into iframe for login into remote site

    // once you're logged in, change the source url (if needed)
    var iframe = document.getElementById('frame');
    // iframe.onload = function() {
    //     if (iframe.src != "http://sistema.axiasolucoes.com.br:8084/") {
    //         iframe.src = "http://sistema.axiasolucoes.com.br:8084/";
    //     }
    // }

    document.getElementById('login').submit();

</script>

@endsection
