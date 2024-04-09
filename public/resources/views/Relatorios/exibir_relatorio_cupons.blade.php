<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Cupons</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .cupom-container {
            page-break-after: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
            /* Espaço após cada cupom */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 20mm;
                /* Adiciona margem para a impressão */
            }

            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .cupom-container {
                page-break-after: always;
            }

            table {
                page-break-inside: avoid;
                margin-bottom: 16px;
                /* Espaço após cada cupom na impressão */
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 style="text-align: center;">Relatório de Cupons</h1>
        {{-- <a href="{{ route('relatorios.download') }}" class="btn btn-primary">Baixar Relatório como PDF</a> --}}
        <p style="text-align: center;">
            Período: {{ date('d/m/Y', strtotime($inicio)) }} até {{ date('d/m/Y', strtotime($fim)) }}
        </p>

        @foreach ($cupons as $cupom)
            <div class="cupom-container">
                <table>
                    <tr>
                        <th>Funcionário:</th>
                        <td>{{ $cupom->usuario ? $cupom->usuario->name : 'Não especificado' }}</td>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>Cidade</th>
                        <th>Data</th>
                        <th>Valor Total</th>
                    </tr>
                    <tr>
                        <td>{{ $cupom->id }}</td>
                        <td>{{ $cupom->cidade }}</td>
                        <td>{{ date('d/m/Y', strtotime($cupom->data)) }}</td>
                        <td>R$ {{ number_format($cupom->valor_total, 2, ',', '.') }}</td>
                    </tr>
                </table>

                @php
                    $itensConsolidados = [];
                    foreach ($cupom->itens as $item) {
                        $descricao = $item->descricao;
                        $valor = $item->valor;

                        if (isset($itensConsolidados[$descricao])) {
                            $itensConsolidados[$descricao] += $valor;
                        } else {
                            $itensConsolidados[$descricao] = $valor;
                        }
                    }
                @endphp

                <table>
                    <tr>
                        <th>Descrição do Item</th>
                        <th>Valor Total</th>
                    </tr>
                    @foreach ($itensConsolidados as $descricao => $valorTotal)
                        <tr>
                            <td>{{ $descricao }}</td>
                            <td>R$ {{ number_format($valorTotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>
</body>

</html>
