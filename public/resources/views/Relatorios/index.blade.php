    @extends('adminlte::page')

    @section('title', 'Relatorio')

    @section('content')
        <!-- Modal.blade.php -->
        <div class="container">
            @if (session('alerta'))
                <div class="alert alert-warning" role="alert">
                    {{ session('alerta') }}
                </div>
            @endif
            <div id="meuModal" class="modal-export">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form id="exportForm" method="GET">
                        @csrf
                        <div class="form-group">
                            <label for="de">DE</label>
                            <input placeholder="DE" id="inicio" name="inicio" type="text" data-toggle="datepicker"
                                class="form-control date">
                        </div>
                        <div class="form-group">
                            <label for="ate">ATÉ</label>
                            <input placeholder="ATÉ" id="fim" name="fim" type="text" data-toggle="datepicker"
                                class="form-control date">
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="mes_passado" value="true">
                            <input type="hidden" name="acao" value="filtro">
                            <button type="button" onclick="submitForm('excel')"class="btn btn-excel">GERAR EXCEL</button>
                        </div>
                    </form>
                </div>
            </div>



            <script>
                function submitForm(type) {
                    var form = document.getElementById('exportForm');
                    if (type === 'pdf') {
                        form.action = "{{ route('relatorios.gerar') }}";
                    } else {
                        form.action = "{{ route('relatorios.exportar') }}";
                    }
                    form.submit();
                }

                $.fn.datepicker

                jQuery(document).ready(function($) {
                    $(".clickable").click(function() {
                        window.location = $(this).data("href");
                    });


                });

                $(".date").mask('99/99/9999');

                $('[data-toggle="datepicker"]').datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
                    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
                        'Outubro', 'Novembro', 'Dezembro'
                    ],
                    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
                });
            </script>

            <style>
                /* Modal.css */
                .modal-exportar {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    left: 0;
                    top: 0;
                    width: 80%;
                    height: 80%;
                    overflow: auto;
                    background-color: rgb(0, 0, 0);
                    background-color: rgba(0, 0, 0, 0.4);
                }

                .modal-content {
                    background-color: #112130;
                    margin: 5% auto;
                    padding: 20px;
                    border: 1px solid #888;
                    width: 50%;
                }

                .close {
                    color: #aaaaaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                }

                .close:hover,
                .close:focus {
                    color: #000;
                    text-decoration: none;
                    cursor: pointer;
                }

                .form-group {
                    margin-bottom: 10px;
                }

                .form-control {
                    width: 100%;
                    padding: 12px 20px;
                    margin: 8px 0;
                    display: inline-block;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    box-sizing: border-box;
                }

                .btn {
                    padding: 14px 20px;
                    margin: 8px 0;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    width: 100%;
                }

                .btn-excel {
                    background-color: #AFD701;
                    color: #FFFFFF;
                    /* Define a cor do texto */
                    font-weight: bold;
                    /* Torna o texto em negrito */
                }

                .btn-excel:hover {
                    background-color: #b0d70181;
                }

                /* .btn-excel {
                            background-color: #7F878E;
                            color: #FFFFFF;
                            /* Define a cor do texto */
                /* font-weight: bold;
                    }*/


                /* .btn-excel:hover {
                        background-color: #3a3a3a;
                    } */


                .modal-footer {
                    padding: 10px 0;
                }
            </style>

        @endsection
