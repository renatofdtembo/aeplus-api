<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    // public function entidadesPdf()
    // {
    //     $entidades = Entidade::with(['pessoa', 'processos', 'imoveis'])->get();

    //     $pdf = Pdf::loadView('pdf.entidades', [
    //         'entidades' => $entidades,
    //         'dataEmissao' => now()->format('d/m/Y'),
    //     ])->setPaper('a4', 'landscape');

    //     // Configurar footer diretamente no DomPDF
    //     $pdf->setOption('footer-left', 'Sistema SIGUMUSSULU - Relatório Confidencial');
    //     $pdf->setOption('footer-center', 'Gerado em: ' . now()->format('d/m/Y H:i'));
    //     $pdf->setOption('footer-right', 'Página [page] de [topage]');
    //     $pdf->setOption('footer-font-size', 9);
    //     $pdf->setOption('footer-font-family', 'DejaVu Sans');

    //     return $pdf->stream('relatorio_entidades.pdf');
    // }
    public function reportProcessoPdf(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            Log::info('Gerando PDF para ' . count($items) . ' processos');

            $pdf = Pdf::loadView('pdf.processos', [
                'items' => $items,
                'dataEmissao' => now()->format('d/m/Y H:i:s'),
            ])->setPaper('a4', 'landscape');

            // Configurar footer
            $pdf->setOption('footer-left', 'Sistema SIGUMUSSULU - Relatório Confidencial');
            $pdf->setOption('footer-center', 'Gerado em: ' . now()->format('d/m/Y H:i'));
            $pdf->setOption('footer-right', 'Página [page] de [topage]');
            $pdf->setOption('footer-font-size', 9);
            $pdf->setOption('footer-font-family', 'DejaVu Sans');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf->stream('relatorio_processos.pdf');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF de processos: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }
    public function reportInscricaoPdf(Request $request)
    {
        try {
            $item = $request->input('item', []);
            

            $pdf = Pdf::loadView('pdf.inscricao', [
                'item' => $item,
                'dataEmissao' => now()->format('d/m/Y H:i:s'),
            ])->setPaper('a4', 'portrait');

            // Configurar footer
            $pdf->setOption('footer-left', 'Sistema EDUKAMBA - Comprovativo de Inscrição');
            $pdf->setOption('footer-center', 'Gerado em: ' . now()->format('d/m/Y H:i'));
            $pdf->setOption('footer-right', 'Página [page] de [topage]');
            $pdf->setOption('footer-font-size', 8);
            $pdf->setOption('footer-font-family', 'DejaVu Sans');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf->stream('comprovativo_inscricao.pdf');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF de inscrição: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }

    public function reportImoveisPdf(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            Log::info('Gerando PDF para ' . count($items) . ' imóveis');

            $pdf = Pdf::loadView('pdf.imoveis', [
                'items' => $items,
                'dataEmissao' => now()->format('d/m/Y H:i:s'),
            ])->setPaper('a4', 'landscape');

            // Configurar footer igual ao da entidade
            $pdf->setOption('footer-left', 'Sistema SIGUMUSSULU - Relatório Confidencial');
            $pdf->setOption('footer-center', 'Gerado em: ' . now()->format('d/m/Y H:i'));
            $pdf->setOption('footer-right', 'Página [page] de [topage]');
            $pdf->setOption('footer-font-size', 9);
            $pdf->setOption('footer-font-family', 'DejaVu Sans');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf->stream('relatorio_imoveis.pdf');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF de imóveis: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }
}