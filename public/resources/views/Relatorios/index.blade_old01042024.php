    @extends('adminlte::page')

    @section('title', 'Relatorio')

    @section('content')
        <div class="container">
            @if (session('alerta'))
                <div class="alert alert-warning" role="alert">
                    {{ session('alerta') }}
                </div>
            @endif

            <form method="GET" action="{{ route('relatorios.gerar') }}" class="row mt-2">
                <div class="col-md-3 mt-1 col-6">
                    <input placeholder="DE" id="inicio" name="inicio" type="text" data-toggle="datepicker"
                        class="form-control date">
                </div>
                <div class="col-md-3 mt-1 col-6">
                    <input placeholder="ATÉ" id="fim" name="fim" type="text" data-toggle="datepicker"
                        class="form-control date">
                </div>
                <div class="col-md-1 mt-1 col-6 text-center">
                    <input type="hidden" name="mes_passado" value="true">
                    <input type="hidden" name="acao" value="filtro">
                    <button class="btn-pdf" id="gerar-pdf-btn">GERAR PDF</button>
                </div>
            </form>
            <br>
            <br>
            <div class="progress-container" style="display: none;">
                <div class="progress-bar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

        </div>

        <script>
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

            document.addEventListener('DOMContentLoaded', function() {
                const startProgressBtn = document.getElementById('gerar-pdf-btn');
                const progressBar = document.querySelector('.progress-bar');
                const dataInicialInput = document.getElementById('inicio');
                const dataFinalInput = document.getElementById('fim');

                startProgressBtn.addEventListener('click', function() {

                    // Mostra a barra de progresso
                    const progressContainer = document.querySelector('.progress-container');
                    progressContainer.style.display = 'block';

                    // Calcula a diferença entre as datas em dias
                    let dataInicial = new Date(dataInicialInput.value);
                    let dataFinal = new Date(dataFinalInput.value);

                    // Se a data inicial estiver em branco, define como 30 dias antes da data atual
                    if (!dataInicialInput.value) {
                        dataInicial = new Date();
                        dataInicial.setDate(dataInicial.getDate() - 30);
                    }

                    // Se a data final estiver em branco, define como a data atual
                    if (!dataFinalInput.value) {
                        dataFinal = new Date();
                    }

                    // Calcula a diferença em dias entre as datas
                    const diffEmDias = Math.floor((dataFinal - dataInicial) / (1000 * 60 * 60 * 24));

                    // Calcula o intervalo dinamicamente com base na diferença em dias
                    const interval = Math.max(1, Math.floor(diffEmDias /
                        100)); // Garante que o intervalo seja pelo menos 1

                    // Inicia a atualização da barra de progresso com base no intervalo calculado
                    let width = 0;
                    const updateProgressBar = setInterval(function() {
                        if (width >= 100) {
                            clearInterval(updateProgressBar);
                        } else {
                            width++;
                            progressBar.style.width = `${width}%`;
                            progressBar.setAttribute('aria-valuenow', width);
                        }
                    }, interval);
                });
            });
        </script>

        <style>
            .btn-pdf {
                display: inline-block;
                /* ou 'block' se quiser que ocupe a linha inteira */
                background-color: #007bff;
                /* Cor de fundo azul */
                color: white;
                /* Cor do texto */
                border: 1px solid #007bff;
                /* Borda da mesma cor que o fundo */
                border-radius: 4px;
                /* Bordas arredondadas */
                padding: 8px 15px;
                /* Preenchimento interno para tamanho */
                font-size: 14px;
                /* Tamanho da fonte */
                line-height: 1.42857143;
                /* Altura da linha */
                white-space: nowrap;
                /* Evita que o texto pule linhas */
                cursor: pointer;
                /* Indica que o botão é clicável */
                text-align: center;
                /* Alinha o texto no centro */
                vertical-align: middle;
                /* Alinha o botão verticalmente em relação aos campos de texto */
            }

            /* Ajustes ao passar o mouse por cima do botão */
            .btn-pdf:hover {
                background-color: #0056b3;
                /* Cor de fundo um pouco mais escura */
                border-color: #0056b3;
                /* Cor da borda mais escura */
            }

            .progress-container {
                width: 100%;
                background-color: #eee;
                border-radius: 8px;
                overflow: hidden;
                display: none;
                /* Oculta a barra de progresso inicialmente */
            }

            .progress-bar {
                height: 20px;
                background-color: #4CAF50;
                text-align: center;
                line-height: 20px;
                color: white;
                width: 0%;
                /* Inicia sem progresso */
            }
        </style>

    @endsection
