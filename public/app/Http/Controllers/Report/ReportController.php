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




            $html = view('relatorios.exibir_relatorio_cupons', compact('cupons', 'inicio', 'fim'))->render();

            // Retorna o HTML na resposta
            return response($html)->header('Content-Type', 'text/html');
        } else {
            // Caso não encontre cupons, retorna uma mensagem ou faz outra ação
            return redirect()->back()->with('alerta', 'Nenhum cupom encontrado para o período selecionado.');
        }
    }

    public function exportarRelatorio(Request $request)
    {
        $inicio = $request->filled('inicio')
            ? date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $request->inicio)))
            : date('Y-m-d 00:00:00', strtotime('-30 days'));

        $fim = $request->filled('fim')
            ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->fim)))
            : date('Y-m-d 23:59:59');

        $cupons = Cupom::with(['itens', 'usuario'])->whereBetween('data', [$inicio, $fim])->get();

        if ($cupons->isNotEmpty()) {
            $export = new CupomExport($request, $cupons);
            return Excel::download($export, 'relatorio_cupons.xlsx');
        } else {
            return redirect()->back()->with('alerta', 'Nenhum cupom encontrado para o período selecionado.');
        }
    }
    public function downloadPDF(Request $request)
    {
        $inicio = $request->filled('inicio')
            ? date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $request->inicio)))
            : date('Y-m-d 00:00:00', strtotime('-30 days'));

        // Define a data de fim com base no input, adicionando o tempo até o último segundo do dia
        $fim = $request->filled('fim')
            ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->fim)))
            : date('Y-m-d 23:59:59');
        // Coleta de dados para o relatório
        $cupons = Cupom::with(['itens', 'usuario'])->whereBetween('data', [$inicio, $fim])->get();

        // Preparando os dados para a view
        $data = [
            'cupons' => $cupons,
            'inicio' => $inicio, // Certifique-se de que essa variável foi definida anteriormente
            'fim' => $fim,       // Certifique-se de que essa variável foi definida anteriormente
        ];

        // Gerando o PDF
        $pdf = PDF::loadView('relatorios.exibir_relatorio_cupons', $data);
        return $pdf->download('relatorio_cupons.pdf');
    }
}
