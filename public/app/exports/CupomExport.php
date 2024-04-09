<?php

namespace App\Exports;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Cupom;
use App\Models\Servicos;
use Illuminate\Support\Collection;
use App\Models\User;

class CupomExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $cupons;

    public function __construct(Request $request, $cupons)
    {
        $this->request = $request;
        $this->cupons = $cupons;
    }

    public function collection()
    {
        $inicio = $this->request->filled('inicio')
            ? date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $this->request->inicio)))
            : date('Y-m-d 00:00:00', strtotime('-30 days'));

        $fim = $this->request->filled('fim')
            ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $this->request->fim)))
            : date('Y-m-d 23:59:59');

        $cupons = $this->cupons->whereBetween('data', [$inicio, $fim]);
        $servicos = Servicos::pluck('nome', 'id')->toArray();

        $data = new Collection();

        // Cria uma linha para os títulos das colunas
        $titulos = ['Funcionário', 'ID', 'DATA', 'KM_INICIAL', 'KM_FINAL', 'CNPJ', 'Empresa'];
        foreach ($servicos as $servico) {
            $titulos[] = $servico; // Adiciona o nome do serviço como título da coluna
        }
        $titulos[] = 'TOTAL';
        $data->push($titulos); // Adiciona os títulos à coleção de dados


        foreach ($cupons as $cupom) {
            $funcionario = $cupom->usuario ? $cupom->usuario->name : 'Não especificado';
            $id = $cupom->id;

            $usuarioEncontrado = User::where('name', $funcionario)->first();


            $valoresServicos = array_fill_keys(array_keys($servicos), 0);

            foreach ($cupom->itens as $item) {
                $servicoId = $item->servico_id; // Supondo que há uma coluna 'servico_id' na tabela de itens
                $descricaoItem = $item->descricao;
                $valor = $item->valor;
                $cpfDoFuncionario = $usuarioEncontrado['cpf'];
                $EmpresadoFuncionario = $usuarioEncontrado['empresa'];
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
                'KM_INICIAL' => $cupom->km_inicial,
                'KM_FINAL' => $cupom->km_final,
                'CNPJ' => $cpfDoFuncionario,
                'EMPRESA' => $EmpresadoFuncionario,
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
