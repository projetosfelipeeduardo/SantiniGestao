<?php

namespace App\Exports;

use Illuminate\Http\Request;
use app\Exports\ServicoExport;
use App\Models\Cupom;
use App\Models\Servicos;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CupomExport implements FromCollection, WithHeadings
{
    protected $cupons;

    public function __construct($cupons)
    {
        $this->cupons = $cupons;
    }

    public function collection()
    {
        $cupons = Cupom::all();
        $servicos = Servicos::pluck('nome', 'id')->toArray();

        $data = new Collection();

        $titulos = ['Funcionário', 'ID', 'DATA'];

        // Cria uma linha para os títulos das colunas
        $titulos = ['Funcionário', 'ID', 'DATA'];
        foreach ($servicos as $servico) {
            $titulos[] = $servico; // Adiciona o nome do serviço como título da coluna
        }
        $titulos[] = 'TOTAL';
        $data->push($titulos); // Adiciona os títulos à coleção de dados

        foreach ($cupons as $cupom) {
            $funcionario = $cupom->usuario ? $cupom->usuario->name : 'Não especificado';
            $id = $cupom->id;
            // Inicializa os valores dos serviços como 0
            $valoresServicos = array_fill_keys(array_keys($servicos), 0);

            foreach ($cupom->itens as $item) {
                $servicoId = $item->servico_id; // Supondo que há uma coluna 'servico_id' na tabela de itens
                $descricaoItem = $item->descricao;
                $valor = $item->valor;

                // Encontra o serviço correspondente pela descrição do item
                $servicoEncontrado = Servicos::where('nome', $descricaoItem)->first();
                if ($servicoEncontrado) {
                    $servicoId = $servicoEncontrado->id;
                    // Incrementa o valor do serviço correspondente
                    if (array_key_exists($servicoId, $valoresServicos)) {
                        $valoresServicos[$servicoId] += $valor;
                    }
                }
            }

            $linha = [
                'Funcionário' => $funcionario,
                'ID' => $id,
                'DATA' => date('d/m/Y', strtotime($cupom->data)),
            ];
            foreach ($servicos as $servicoId => $nomeServico) {
                $linha[] = $valoresServicos[$servicoId];
            }
            $linha[] = array_sum($valoresServicos);

            $data->push($linha);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }
}
