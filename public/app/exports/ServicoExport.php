<?php

namespace App\Exports;

use App\Models\Cupom;
use App\Models\Item;
use App\Models\Servicos;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServicoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Lógica para buscar os dados dos serviços e seus respectivos itens
        $servicos = Servicos::with('itens')->get();

        // Transforma os dados dos serviços e itens em uma coleção para exportar
        $data = new Collection();

        foreach ($servicos as $servico) {
            $rowData = [
                'Funcionário' => $servico->usuario->name ?? 'Não especificado',
                'ID do Cupom' => $servico->cupom_id,
            ];

            // Adiciona as informações dos itens do serviço como colunas dinâmicas
            foreach ($servico->itens as $item) {
                $rowData[$item->descricao] = $item->valor;
            }

            // Adiciona a linha de dados à coleção
            $data->push($rowData);
        }

        return $data;
    }

    public function headings(): array
    {
        // Busca todos os serviços disponíveis na tabela 'servicos' para usar como cabeçalhos
        $servicos = Servicos::pluck('descricao')->toArray();

        // Adiciona os cabeçalhos padrão e os cabeçalhos dinâmicos dos serviços
        $headings = [
            'Funcionário',
            'ID do Cupom',
        ];

        return array_merge($headings, $servicos);
    }
}
