<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Relatorio;
use PDF;
use App\Models\Cupom;
use App\Models\Itens;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Exports\CupomExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function index()
    {
        return view('relatorios.index');
    }

    public function gerarRelatorio(Request $request)
    {
        // Verifica se os parâmetros foram enviados

        // Define a data de início com base no input ou usa o período padrão dos últimos 30 dias
        $inicio = $request->filled('inicio')
            ? date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $request->inicio)))
            : date('Y-m-d 00:00:00', strtotime('-30 days'));

        // Define a data de fim com base no input, adicionando o tempo até o último segundo do dia
        $fim = $request->filled('fim')
            ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->fim)))
            : date('Y-m-d 23:59:59');

        // Cria um array com as datas convertidas para usar na validação
        $datas = [
            'inicio' => $inicio,
            'fim' => $fim,
        ];

        // Valida as datas convertidas
        $validator = Validator::make($datas, [
            'inicio' => 'required|date',
            'fim' => 'required|date|after_or_equal:inicio'
        ]);

        if ($validator->fails()) {
            // Pega a primeira mensagem de erro
            $firstError = $validator->errors()->first();

            // Redireciona de volta com a primeira mensagem de erro como um alerta.
            return redirect()->back()
                ->with('alerta', $firstError)
                ->withInput();
        }

        // Busca os cupons com base no intervalo de datas, e carrega os itens relacionados
        $cupons = Cupom::with(['itens', 'usuario'])->whereBetween('data', [$inicio, $fim])->get();

        // Verifica se há cupons encontrados
        if ($cupons->isNotEmpty()) {
            // Gera o HTML do relatório
            $html = '<style>';
            $html .= 'table { width: 100%; border-collapse: collapse; }';
            $html .= 'th, td { border: 1px solid #000; padding: 8px; }';
            $html .= '</style>';
            $html .= '<h1 style="text-align: center;">Relatório de Cupons</h1>';
            $html .= '<p style="text-align: center;">Período: ' . date('d/m/Y', strtotime($inicio)) . ' até ' . date('d/m/Y', strtotime($fim)) . '</p>';


            foreach ($cupons as $cupom) {
                $html .= '<table border="1" cellpadding="10" cellspacing="0">';
                // Verifica se o cupom tem um usuário associado
                $html .= '<tr><th>Funcionário:</th>';
                $html .= '<td>' . ($cupom->usuario ? $cupom->usuario->name : 'Não especificado') . '</td>';
                $html .= '</tr>';
                $html .= '</table>';
                $html .= '<table>';
                $html .= '<br>';
                $html .= '<tr><th>ID</th><th>Cidade</th><th>Data</th><th>Valor Total</th></tr>';
                $html .= '<td>' . $cupom->id . '</td>';
                $html .= '<td>' . $cupom->cidade . '</td>';
                $html .= '<td>' . date('d/m/Y', strtotime($cupom->data)) . '</td>';
                $html .= '<td>R$ ' . number_format($cupom->valor_total, 2, ',', '.') . '</td>';
                $html .= '</tr>';
                $html .= '</table>';
                $html .= '<br>';

                // Adiciona os itens consolidados à tabela do relatório
                $itensConsolidados = [];
                foreach ($cupom->itens as $item) {
                    $descricao = $item->descricao;
                    $valor = $item->valor;

                    if (array_key_exists($descricao, $itensConsolidados)) {
                        $itensConsolidados[$descricao] += $valor;
                    } else {
                        $itensConsolidados[$descricao] = $valor;
                    }
                }

                $html .= '<table>';
                $html .= '<tr><th>Descrição do Item</th><th>Valor Total</th></tr>';
                foreach ($itensConsolidados as $descricao => $valorTotal) {
                    $html .= '<tr>';
                    $html .= '<td>' . $descricao . '</td>';
                    $html .= '<td>R$ ' . number_format($valorTotal, 2, ',', '.') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';

                // Adicione quebras de página para cada cupom
                $html .= '<div style="page-break-after: always;"></div>';
            }
            // Gerar o PDF usando DomPDF
            $pdf = PDF::loadHTML($html);

            // Salva o PDF em um arquivo temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf'); // Cria um arquivo temporário
            file_put_contents($tempFile, $pdf->output()); // Salva o conteúdo do PDF no arquivo temporário

            // Obtém o tamanho do arquivo
            $fileSize = filesize($tempFile); // Em bytes

            // Retorna o PDF para download ou visualização
            return response()->file($tempFile, [
                'Content-Type' => 'application/pdf',
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'attachment; filename="relatorio_cupons.pdf"',
            ]);
        } else {
            // Caso não encontre cupons, retorna uma mensagem ou faz outra ação
            return redirect()->back()->with('alerta', 'Nenhum cupom encontrado para o período selecionado.');
        }
    }

    public function exportarRelatorio(Request $request)
    {
        // Define a data de início com base no input ou usa o período padrão dos últimos 30 dias
        $inicio = $request->filled('inicio')
            ? date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $request->inicio)))
            : date('Y-m-d 00:00:00', strtotime('-30 days'));

        // Define a data de fim com base no input, adicionando o tempo até o último segundo do dia
        $fim = $request->filled('fim')
            ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->fim)))
            : date('Y-m-d 23:59:59');

        // Busca os cupons com base no intervalo de datas, e carrega os itens relacionados
        $cupons = Cupom::with(['itens', 'usuario'])->whereBetween('data', [$inicio, $fim])->get();

        // Verifica se há cupons encontrados
        if ($cupons->isNotEmpty()) {
            // Cria uma exportação com os dados dos cupons
            $export = new CupomExport($cupons);

            // Retorna o arquivo para download em formato Excel
            return Excel::download($export, 'relatorio_cupons.xlsx');
        } else {
            // Caso não encontre cupons, retorna uma mensagem ou faz outra ação
            return redirect()->back()->with('alerta', 'Nenhum cupom encontrado para o período selecionado.');
        }
    }
}
