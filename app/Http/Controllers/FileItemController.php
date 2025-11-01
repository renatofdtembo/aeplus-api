<?php

namespace App\Http\Controllers;

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Storage, Validator};
use Illuminate\Support\Str;
use App\Models\{FileItem, Pessoa, UserDetail, UserDetails};
use App\Services\FileService;
use Carbon\Carbon;

class FileItemController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    // Listar estrutura completa
    public function index()
    {
        $tree = FileItem::getFullStructure(FileItem::get());

        return response()->json([
            'success' => true,
            'data' => $tree,
        ]);
    }

    // Obter item específico
    public function show($id)
    {
        $fileItem = FileItem::with('children')->find($id);

        if (! $fileItem) {
            return response()->json([
                'success' => false,
                'message' => 'File item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $fileItem->id,
                'name' => $fileItem->name,
                'type' => $fileItem->type,
                'path' => $fileItem->path,
                'size' => $fileItem->size,
                'extension' => $fileItem->extension,
                'modifiedAt' => $fileItem->modifiedAt->toISOString(),
                'createdAt' => $fileItem->createdAt->toISOString(),
                'permissions' => $fileItem->permissions,
                'children' => $fileItem->children,
            ],
        ]);
    }

    // Criar novo item
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|unique:file_items',
            'name' => 'required|string',
            'type' => 'required|in:file,folder',
            'path' => 'required|string',
            'size' => 'nullable|integer',
            'extension' => 'nullable|string',
            'parent_id' => 'nullable|exists:file_items,id',
            'entity_id' => 'nullable|string',
            'permissions.departments' => 'nullable|array',
            'permissions.departments.*' => 'string',
            'permissions.isPublic' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileItem = FileItem::create([
            'id' => $request->id,
            'name' => $request->name,
            'type' => $request->type,
            'path' => $request->path,
            'size' => $request->size,
            'extension' => $request->extension,
            'parent_id' => $request->parent_id,
            'entity_id' => $request->entity_id,
            'modifiedAt' => now(),
            'createdAt' => now(),
            'permissions' => [
                'departments' => $request->input('permissions.departments', []),
                'isPublic' => $request->input('permissions.isPublic', false),
            ],
        ]);

        if ($fileItem->type == 'folder') {
            $this->fileService->createDirectory($request->path);
        }

        $structure = FileItem::getFullStructure(FileItem::get());

        return response()->json([
            'recordId' => $fileItem->id,
            'record' => $structure,
            'status' => 'sucesso',
            'mensagem' => $request->type == 'folder' ? 'Foi criado um directório com sucesso!' : 'Upload de arquivo feito com sucesso!',
        ]);
    }

    public function updatePerfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|unique:file_items',
            'name' => 'required|string',
            'type' => 'required|in:file,folder',
            'path' => 'required|string',
            'size' => 'nullable|integer',
            'extension' => 'nullable|string',
            'parent_id' => 'nullable|exists:file_items,id',
            'entity_id' => 'nullable|string',
            'permissions.departments' => 'nullable|array',
            'permissions.departments.*' => 'string',
            'permissions.isPublic' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $dir = dirname($request->path);

        // Verifica se já existe um diretório com esse nome
        $fileParent = FileItem::where('path', $dir)->first();
        if (! $fileParent) {
            $this->fileService->createDirectory($dir);
            $fileParent = FileItem::create([
                'id' => 'folder_171625120825_247',
                'name' => 'Perfil',
                'type' => 'folder',
                'path' => '/Perfil',
                'parent_id' => 'root',
                'modifiedAt' => optional(Carbon::now())->toISOString(),
                'createdAt' => optional(Carbon::now())->toISOString(),
                // 'permissions' => $item->permissions ?? [
                //     'departments' => ['all'],
                //     'isPublic' => true
                // ],
                'children' => [],
            ]);
        }
        // 1. Busca todos os registros com o mesmo entity_id
        $existingFiles = FileItem::where('entity_id', $request->entity_id)->get();
        // 2. Deleta todos os que começam com /Perfil
        foreach ($existingFiles as $file) {
            if (Str::startsWith($file->path, '/Perfil')) {
                $file->delete();
                $this->fileService->deleteFiles($file->path);
            }
        }
        UserDetail::find($request->entity_id)->update([
            'urlImg' => $request->path,
        ]);
        $fileItem = FileItem::create([
            'id' => $request->id,
            'name' => $request->name,
            'type' => $request->type,
            'path' => $request->path,
            'size' => $request->size,
            'extension' => $request->extension,
            'parent_id' => $fileParent->id,
            'entity_id' => $request->entity_id,
            'modifiedAt' => now(),
            'createdAt' => now(),
            'permissions' => [
                'departments' => $request->input('permissions.departments', ['all']),
                'isPublic' => $request->input('permissions.isPublic', true),
            ],
        ]);

        if ($fileItem->type == 'folder') {
            $this->fileService->createDirectory($request->path);
        }

        $structure = FileItem::getFullStructure(FileItem::get());

        return response()->json([
            'recordId' => $fileItem->id,
            'record' => $structure,
            'status' => 'sucesso',
            'mensagem' => $request->type == 'folder' ? 'Foi criado um directório com sucesso!' : 'Upload de arquivo feito com sucesso!',
        ]);
    }

    public function pessoaPerfil(Request $request)
    {
        $request->validate([
            'pessoa_id' => 'required|integer',
            'path' => 'required|string',
        ]);

        $pessoa = UserDetail::find($request->pessoa_id);

        if (! $pessoa) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        }

        $pessoa->update([
            'urlImg' => $request->path,
        ]);

        return response()->json([
            'status' => 'sucesso',
        ]);
    }

    /**
     * Upload de arquivos usando FileService
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            // VALIDAÇÃO SIMPLIFICADA E CORRETA
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:202400',
                'path' => 'required|string'
            ]);

            $file = $request->file('file');
            $fullPath = $request->input('path');

            // Extrair diretório e nome do arquivo
            $directory = dirname($fullPath);
            $customFilename = basename($fullPath);

            // Garantir que o diretório existe
            $this->fileService->createDirectory($directory);

            // Salvar arquivo
            $savedPath = $this->fileService->saveWithCustomName(
                $file,
                $directory,
                $customFilename
            );

            if (!$savedPath) {
                return response()->json([
                    'success' => false,
                    'message' => "Falha ao salvar o arquivo.",
                    'path' => null,
                    'data' => null
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => "Arquivo salvo com sucesso.",
                'path' => $savedPath,
                'data' => [
                    'original_name' => $file->getClientOriginalName(),
                    'saved_name' => basename($savedPath),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Erro no upload: " . $e->getMessage(),
                'path' => null,
                'data' => null
            ], 500);
        }
    }

    public function moveFile(Request $request)
    {
        $request->validate([
            'currentPath' => 'required|string',
            'newPath' => 'required|string',
            'disk' => 'sometimes|string',
        ]);

        $this->fileService->moveFile(
            $request->input('currentPath'),
            $request->input('newPath'),
            $request->input('disk', 'public')
        );

        return response()->json([
            'recordId' => null,
            'record' => $request->input('newPath'),
            'status' => 'sucesso',
            'mensagem' => 'Arquivo movido com sucesso.',
        ]);
    }

    // Atualizar item
    public function update(Request $request, $id)
    {
        $fileItem = FileItem::find($id);

        if (! $fileItem) {
            return response()->json([
                'success' => false,
                'message' => 'File item not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'newscription' => 'nullable|string',
            'id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'name' => $request->newscription,
            'type' => $request->input('type', $fileItem->type),
            'path' => $request->input('path', $fileItem->path),
            'size' => $request->input('size', $fileItem->size),
            'extension' => $request->input('extension', $fileItem->extension),
            'parent_id' => $request->input('parent_id', $fileItem->parent_id),
            'modifiedAt' => now(),
        ];

        // Atualiza permissions apenas se foram enviadas no request
        if ($request->has('permissions')) {
            $updateData['permissions'] = [
                'departments' => $request->input('permissions.departments', $fileItem->permissions['departments'] ?? []),
                'isPublic' => $request->input('permissions.isPublic', $fileItem->permissions['isPublic'] ?? false),
            ];
        }

        $fileItem->update($updateData);

        if ($request->has('newscription') && $request->newscription !== $fileItem->name) {
            $oldPath = $fileItem->path;
            $newPath = dirname($oldPath).'/'.$request->newscription;

            $this->fileService->moveFile($oldPath, $newPath); // move fisicamente o arquivo/pasta

            $updateData['path'] = $newPath;
            $updateData['name'] = $request->newscription;
        }
        $structure = FileItem::getFullStructure(FileItem::get());

        return response()->json([
            'success' => true,
            'data' => $structure,
        ]);
    }

    // Deletar item
    public function destroy($id)
    {
        $fileItem = FileItem::find($id);

        if (! $fileItem) {
            return response()->json([
                'success' => false,
                'message' => 'File item not found',
            ], 404);
        }

        if ($fileItem->type === 'folder') {
            // Deleta recursivamente todos os filhos primeiro
            $this->deleteChildrenRecursively($fileItem);
            $this->fileService->deleteDirectory($fileItem->path);
        } else {
            $this->fileService->deleteFiles($fileItem->path);
        }

        $deleted = $fileItem->delete();

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao deletar o item',
            ], 500);
        }

        $structure = FileItem::getFullStructure(FileItem::get());

        return response()->json([
            'recordId' => $fileItem->id,
            'record' => $structure,
            'status' => 'sucesso',
            'mensagem' => $fileItem->type === 'folder'
                ? 'Foi removido um diretório com sucesso!'
                : 'Foi removido um arquivo com sucesso!',
        ]);
    }

    /**
     * Deleta todos os filhos recursivamente de um folder
     */
    private function deleteChildrenRecursively(FileItem $folder)
    {
        foreach ($folder->children as $child) {
            if ($child->type === 'folder') {
                $this->deleteChildrenRecursively($child); // recursivo
            }
            $child->delete();
            $this->fileService->deleteDirectory($child->path);
        }
    }

    public function uploadPerfil(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
            'id' => 'required|integer|exists:pessoas,id',
            'tipo' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $id = $request->input('id');

        $funcionario = UserDetail::find($id);

        if (! $funcionario || ! $funcionario->pessoa) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Pessoa não encontrada.',
            ], 404);
        }

        // Se houver imagem antiga, deletar
        $imgAntiga = $funcionario->pessoa->urlImg;
        if ($imgAntiga && Storage::exists(str_replace('storage/', 'public/', $imgAntiga))) {
            Storage::delete(str_replace('storage/', 'public/', $imgAntiga));
        }

        // Gera nome aleatório com 20 caracteres + extensão
        $randomName = Str::random(20).'.'.$file->getClientOriginalExtension();

        // Salva arquivo
        $path = $file->storeAs('public/perfil', $randomName);

        // Atualiza caminho da imagem
        $funcionario->pessoa->update([
            'urlImg' => '/perfil/'.$randomName,
        ]);

        return response()->json([
            'status' => 'sucesso',
            'path' => $path,
            'url' => asset('storage/perfil/'.$randomName),
            'mensagem' => 'Imagem enviada com sucesso.',
        ]);
    }

    public function getImagemBase64(Request $request)
    {
        $request->validate([
            'caminho' => 'required|string',
        ]);

        // Remove parâmetros extras que podem vir na URL (como ?t=timestamp)
        $caminhoRelativo = explode('?', $request->input('caminho'))[0];

        // Verifica se o caminho começa com storage/
        if (! str_starts_with($caminhoRelativo, 'storage/')) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Caminho inválido. Deve começar com storage/',
            ], 400);
        }

        $caminhoStorage = str_replace('storage/', 'public/', $caminhoRelativo);

        // Verifica se a imagem solicitada existe, senão usa a default
        if (! Storage::exists($caminhoStorage)) {
            $caminhoStorage = 'public/'.env('DEFAULT_PROFILE_IMAGE', 'perfil/default.jpg');

            // Verifica se a imagem padrão existe
            if (! Storage::exists($caminhoStorage)) {
                return response()->json([
                    'status' => 'erro',
                    'mensagem' => 'Imagem padrão não encontrada.',
                ], 404);
            }
        }

        try {
            $conteudoImagem = Storage::get($caminhoStorage);
            $mimeType = Storage::mimeType($caminhoStorage);
            $base64 = base64_encode($conteudoImagem);
            $base64Image = 'data:'.$mimeType.';base64,'.$base64;

            return response()->json([
                'status' => 'sucesso',
                'imagem_base64' => $base64Image,
                'mime_type' => $mimeType,
                'usou_default' => ! Storage::exists(str_replace('storage/', 'public/', $caminhoRelativo)), // Indica se usou a imagem padrão
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao converter imagem: '.$e->getMessage(),
            ], 500);
        }
    }

    // Popular dados iniciais
    public function seed()
    {
        FileItem::seedInitialData();

        return response()->json([
            'success' => true,
            'message' => 'Initial data seeded',
        ]);
    }
}
