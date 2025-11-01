<?php

namespace App\Http\Controllers;

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;

class UploadController extends Controller
{
    public function uploadPerfil(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
            'id' => 'required|integer|exists:funcionarios,id',
            'tipo' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $id = $request->input('id');

        $funcionario = User::find($id);

        if (!$funcionario || !$funcionario->pessoa) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Funcionário ou pessoa não encontrada.'
            ], 404);
        }

        // Se houver imagem antiga, deletar
        $imgAntiga = $funcionario->pessoa->urlImg;
        if ($imgAntiga && Storage::exists(str_replace('storage/', 'public/', $imgAntiga))) {
            Storage::delete(str_replace('storage/', 'public/', $imgAntiga));
        }

        // Gera nome aleatório com 20 caracteres + extensão
        $randomName = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // Salva arquivo
        $path = $file->storeAs('public/perfil', $randomName);

        // Atualiza caminho da imagem
        $funcionario->pessoa->update([
            'urlImg' => '/perfil/' . $randomName
        ]);

        return response()->json([
            'status' => 'sucesso',
            'path' => $path,
            'url' => asset('storage/perfil/' . $randomName),
            'mensagem' => 'Imagem enviada com sucesso.'
        ]);
    }

    public function perfil(Request $request, $user_id): JsonResponse
    {
        $validated = $request->validate([
            'urlImg' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $pessoa = User::findOrFail($user_id);

            $pessoa->update([
                'urlImg' => $validated['urlImg']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'sucesso',
                'mensagem' => 'Foto de perfil atualizada com sucesso',
                'record' => $pessoa
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar entidade: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar foto de perfil',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function getImagemBase64(Request $request)
    {
        $request->validate([
            'caminho' => 'required|string',
        ]);

        $caminhoRelativo = explode('?', $request->input('caminho'))[0];

        if (!str_starts_with($caminhoRelativo, 'uploads/')) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Caminho inválido. Deve começar com uploads/'
            ], 400);
        }

        // Converte o caminho relativo para caminho absoluto
        $caminhoAbsoluto = public_path($caminhoRelativo);

        // Verifica se a imagem existe
        if (!file_exists($caminhoAbsoluto)) {
            // Tenta a imagem padrão
            $caminhoPadrao = public_path('uploads/logoAngola.png');

            if (!file_exists($caminhoPadrao)) {
                return response()->json([
                    'status' => 'erro',
                    'mensagem' => 'Imagem não encontrada'
                ], 404);
            }

            $caminhoAbsoluto = $caminhoPadrao;
        }

        try {
            $conteudoImagem = file_get_contents($caminhoAbsoluto);
            $mimeType = mime_content_type($caminhoAbsoluto);
            $base64 = base64_encode($conteudoImagem);
            $base64Image = 'data:' . $mimeType . ';base64,' . $base64;

            return response()->json([
                'status' => 'sucesso',
                'imagem_base64' => $base64Image,
                'mime_type' => $mimeType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao converter imagem: ' . $e->getMessage()
            ], 500);
        }
    }
}
