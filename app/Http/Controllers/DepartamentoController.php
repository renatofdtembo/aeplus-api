<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Departamento, Funcao, Funcionario, Menu, Permissoes};

class DepartamentoController extends Controller
{
    // 🔸 Lista todos os departamentos
    public function allDepartamento()
    {
        $departamentos = Departamento::with(['diretor.pessoa', 'funcoes'])->get();
        return response()->json($departamentos);
    }
    // 🔸 Criar ou atualizar departamento
    public function addDepartamento(Request $request, $id = null)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:200',
            'categoria' => 'required|string|max:200',
            'diretor_id' => 'nullable|integer|exists:funcionarios,id',
        ]);

        if (empty($id)) {
            $departamento = new Departamento();
            $departamento->nome = $validated['nome'];
            $departamento->categoria = $validated['categoria'];
            $departamento->diretor_id = $validated['diretor_id'] ?? null;
            $departamento->save();
            return response()->json([
                'recordId' => $departamento->id,
                'status' => "sucesso",
                'mensagem' => 'Departamento criado com sucesso',
            ]);
        }

        $departamento = Departamento::find($id) ?? new Departamento();
        $departamento->nome = $validated['nome'];
        $departamento->categoria = $validated['categoria'];
        $departamento->diretor_id = $validated['diretor_id'] ?? null;
        $departamento->update();

        return response()->json([
            'recordId' => $departamento->id,
            'status' => "sucesso",
            'mensagem' => $id ? 'Departamento atualizado com sucesso' : 'Departamento criado com sucesso',
        ]);
    }
    // 🔸 Lista funções por departamento
    public function funcoesDep($id)
    {
        $departamento = Departamento::with(['funcoes'])->findOrFail($id);
        return response()->json($departamento->funcoes);
    }

    // 🔹 Lista todas as funções do sistema
    public function allFuncoes()
    {
        $funcoes = Funcao::all();
        return response()->json($funcoes);
    }

    // 🔹 Lista todas as funções do sistema
    public function funcionarioFuncoes($id)
    {
        return response()->json([]);
    }
 
    // 🔹 Criar ou atualizar função
    public function addFuncao(Request $request, $id = null)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'salario_base' => 'nullable|numeric',
            'nivel' => 'nullable',
            'ativo' => 'boolean',
            'departamento_id' => 'nullable|exists:departamentos,id',
        ]);

        $funcao = Funcao::find($id) ?? new Funcao();
        $funcao->fill($validated);
        $funcao->save();

        return response()->json([
            'recordId' => $funcao->id,
            'status' => "sucesso",
            'mensagem' => $id ? 'Função atualizada com sucesso' : 'Função criada com sucesso',
        ]);
    }

    // 🔹 Criar ou atualizar função
    public function deleteFuncao($id)
    {
        DB::beginTransaction();

        try {
            $funcao = Funcao::find($id);

            if (!$funcao) {
                return response()->json([
                    'success' => false,
                    'message' => 'funcao não encontrado.'
                ], 404);
            }

            $funcao->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'funcao deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar funcao: ' . $e->getMessage()
            ], 500);
        }
    }
    public function deleteDepartamento($id)
    {
        DB::beginTransaction();

        try {
            $dep = Departamento::find($id);

            if (!$dep) {
                return response()->json([
                    'success' => false,
                    'message' => 'Departamento não encontrado.'
                ], 404);
            }

            $dep->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Departamento deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar Departamento: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Lista allMenuOrganized
     */
    public function allMenuOrganized()
    {
        // Buscar apenas menus de nível raiz (parent = 0)
        $menus = Menu::where('parent', 0)
            ->with('childrenRecursive') // recursivo
            ->orderBy('sort', 'asc')
            ->get();

        return response()->json($menus);
    }

    public function allMenus()
    {
        $users = Menu::get();
        return response()->json($users);
    }

    public function addMenu(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:menus,id',
            'label' => 'required|string|max:255',
            'icone' => 'required|string|max:100',
            'link' => 'nullable|string|max:255',
            'parent' => 'nullable|integer|min:0',
            'sort' => 'nullable|integer',
        ]);

        $parentId = $validated['parent'] ?? 0;
        $menuId = $validated['id'] ?? null;

        // Se o parent for diferente de 0, verifica se ele existe
        if ($parentId > 0 && !Menu::where('id', $parentId)->exists()) {
            return response()->json(['error' => 'O menu pai informado não existe.'], 422);
        }

        // Verifica duplicidade no mesmo nível hierárquico
        $existing = Menu::where('label', $validated['label'])
            ->where('parent', $parentId)
            ->when($menuId, function ($query) use ($menuId) {
                return $query->where('id', '!=', $menuId);
            })
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'Já existe um menu com esse nome no mesmo nível de hierarquia.'
            ], 422);
        }

        $menu = $menuId ? Menu::find($menuId) : new Menu();

        $menu->label = $validated['label'];
        $menu->icone = $validated['icone'];
        $menu->link = $validated['link'] ?? '';
        $menu->parent = $parentId;
        $menu->sort = $validated['sort'] ?? 0;
        $menu->save();

        return response()->json([
            'message' => $menuId ? 'Menu atualizado com sucesso' : 'Menu criado com sucesso',
            'menu' => $menu
        ]);
    }

    public function showPermissoes($funcaoId = null)
    {
        try {
            $query = Menu::with(['permissoes']);

            if ($funcaoId) {
                $query->whereHas('permissoes', function ($q) use ($funcaoId) {
                    $q->where('funcao_id', $funcaoId);
                });
            } else {
                $query->whereHas('permissoes');
            }

            $menus = $query->get();

            return response()->json([
                'status' => 'sucesso',
                'dados' => $menus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao buscar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMenuPermissoes($menu_id, $funcao_id)
    {
        try {
            $permissao = Permissoes::where([
                ['menu_id', '=', $menu_id],
                ['funcao_id', '=', $funcao_id]
            ])->first();

            return response()->json([
                'status' => 'sucesso',
                'data' => $permissao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao buscar permissão'
            ], 500);
        }
    }

    public function initializePermissoes($funcaoId = null)
    {
        try {
            return response()->json(Permissoes::where('funcao_id', $funcaoId)->get());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao buscar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storePermissoes(Request $request)
    {
        DB::beginTransaction();

        try {
            $permissoes = $request->all();

            // Verifica se há permissões no request
            if (empty($permissoes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma permissão foi enviada'
                ], 400);
            }

            // Pega o funcao_id da primeira permissão (assumindo que todas são da mesma função)
            $funcaoId = $permissoes[0]['funcao_id'] ?? null;

            if (!$funcaoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'funcao_id não especificado'
                ], 400);
            }

            // Busca todas as permissões existentes para esta função
            $permissoesExistentes = Permissoes::where('funcao_id', $funcaoId)->get();

            // Cria array com os IDs das novas permissões
            $novosIds = [];
            foreach ($permissoes as $permissaoData) {
                if (isset($permissaoData['id']) && $permissaoData['id']) {
                    $novosIds[] = $permissaoData['id'];
                }
            }

            // Remove permissões que não estão no novo array
            foreach ($permissoesExistentes as $permissaoExistente) {
                if (!in_array($permissaoExistente->id, $novosIds)) {
                    $permissaoExistente->delete();
                }
            }

            // Atualiza ou cria as novas permissões
            foreach ($permissoes as $permissaoData) {
                if (isset($permissaoData['id']) && $permissaoData['id']) {
                    // Atualiza permissão existente
                    Permissoes::where('id', $permissaoData['id'])->update([
                        'canView' => $permissaoData['canView'] ?? false,
                        'canCreate' => $permissaoData['canCreate'] ?? false,
                        'canUpdate' => $permissaoData['canUpdate'] ?? false,
                        'canDelete' => $permissaoData['canDelete'] ?? false
                    ]);
                } else {
                    // Cria nova permissão
                    Permissoes::create([
                        'menu_id' => $permissaoData['menu_id'],
                        'funcao_id' => $permissaoData['funcao_id'],
                        'canView' => $permissaoData['canView'] ?? false,
                        'canCreate' => $permissaoData['canCreate'] ?? false,
                        'canUpdate' => $permissaoData['canUpdate'] ?? false,
                        'canDelete' => $permissaoData['canDelete'] ?? false
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permissões salvas com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar permissões: ' . $e->getMessage()
            ], 500);
        }
    }
}
