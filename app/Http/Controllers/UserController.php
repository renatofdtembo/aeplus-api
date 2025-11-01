<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use App\Models\{Permissoes, User, UserDetail};
use App\Services\OperationLogger;

class UserController extends Controller
{
    /**
     * 📋 Listar todos os usuários com paginação
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $users = User::with('detail.endereco')
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Usuários listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar usuários: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 👤 Cadastrar apenas dados do usuário (tabela users)
     */
    public function storeUserOnly(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'status' => 'required|string|in:ativo,inativo,pendente',
                'update_password' => 'boolean'
            ]);

            // Cria o usuário
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'],
                'update_password' => $validated['update_password'] ?? false,
                'ultimo_acesso' => now(),
            ]);

            DB::commit();

            OperationLogger::log('create', $user, 'Usuário criado com sucesso!', null, request(), $user->user_code);
            
            return response()->json([
                'success' => true,
                'data' => $user->load('detail'),
                'message' => 'Usuário criado com sucesso.'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📝 Cadastrar apenas detalhes do usuário (tabela user_details)
     */
    public function storeUserDetailsOnly(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'nome' => 'required|string|max:255',
                'nome_fantasia' => 'nullable|string|max:255',
                'urlImg' => 'nullable|url',
                'contacto' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:user_details,email',
                'nif' => 'nullable|string|unique:user_details,nif',
                'bi' => 'nullable|string|unique:user_details,bi',
                'passaporte' => 'nullable|string|unique:user_details,passaporte',
                'genero' => 'nullable|in:M,F,OUTRO',
                'estadocivil' => 'nullable|in:SOLTEIRO,CASADO,DIVORCIADO,VIUVO,UNIAO_DE_FACTO',
                'nascimento' => 'nullable|date',
                'biografia' => 'nullable|string',
                'tipo' => 'required|in:PESSOA,EMPRESA',
                'endereco_id' => 'nullable|exists:enderecos,id'
            ]);

            // Verifica se já existe detalhe para este usuário
            $existingDetail = UserDetail::where('user_id', $validated['user_id'])->first();
            if ($existingDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existem detalhes cadastrados para este usuário.'
                ], 422);
            }

            // Cria os detalhes do usuário
            $userDetail = UserDetail::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $userDetail->load(['user', 'endereco']),
                'message' => 'Detalhes do usuário criados com sucesso.'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar detalhes do usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 👥 Cadastrar usuário e detalhes simultaneamente
     */
    public function storeUserWithDetails(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validação para usuário
            $userValidated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'status' => 'required|string|in:ativo,inativo,pendente',
                'biografia' => 'nullable|string',
                'update_password' => 'boolean'
            ]);

            // Validação para detalhes
            $detailValidated = $request->validate([
                'nome' => 'required|string|max:255',
                'nome_fantasia' => 'nullable|string|max:255',
                'urlImg' => 'nullable|url',
                'contacto' => 'nullable|string|max:20',
                'detail_email' => 'nullable|email|unique:user_details,email',
                'nif' => 'nullable|string|unique:user_details,nif',
                'bi' => 'nullable|string|unique:user_details,bi',
                'passaporte' => 'nullable|string|unique:user_details,passaporte',
                'genero' => 'nullable|in:M,F,OUTRO',
                'estadocivil' => 'nullable|in:SOLTEIRO,CASADO,DIVORCIADO,VIUVO,UNIAO_DE_FACTO',
                'nascimento' => 'nullable|date',
                'tipo' => 'required|in:PESSOA,EMPRESA',
                'endereco_id' => 'nullable|exists:enderecos,id'
            ]);

            // Cria o usuário
            $user = User::create([
                'name' => $userValidated['name'],
                'email' => $userValidated['email'],
                'password' => Hash::make($userValidated['password']),
                'status' => $userValidated['status'],
                'biografia' => $userValidated['biografia'] ?? null,
                'update_password' => $userValidated['update_password'] ?? false,
                'ultimo_acesso' => now(),
            ]);

            // Prepara dados para user_details
            $detailData = array_merge($detailValidated, [
                'user_id' => $user->id,
                'email' => $detailValidated['detail_email'] ?? null
            ]);

            // Remove detail_email do array se existir
            unset($detailData['detail_email']);

            // Cria os detalhes do usuário
            $userDetail = UserDetail::create($detailData);

            DB::commit();

            OperationLogger::log('create', $user, 'Usuário criado com sucesso!', null, request(), $user->user_code);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'user_detail' => $userDetail
                ],
                'message' => 'Usuário e detalhes criados com sucesso.'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar usuário com detalhes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Consultar usuário específico por ID
     */
    public function show($id)
    {
        try {
            $user = User::with(['detail.endereco'])->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário encontrado com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Consultar detalhes do usuário por user_id
     */
    public function showUserDetails($userId)
    {
        try {
            $userDetail = UserDetail::with(['user', 'endereco'])
                                ->where('user_id', $userId)
                                ->first();

            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalhes do usuário não encontrados.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $userDetail,
                'message' => 'Detalhes do usuário encontrados com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar detalhes do usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Buscar usuários por nome ou email
     */
    public function search(Request $request)
    {
        try {
            $search = $request->get('search');
            $perPage = $request->get('per_page', 15);

            if (!$search) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetro de busca é obrigatório.'
                ], 422);
            }

            $users = User::with('detail')
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('detail', function($query) use ($search) {
                            $query->where('nome', 'like', "%{$search}%")
                                  ->orWhere('nome_fantasia', 'like', "%{$search}%")
                                  ->orWhere('contacto', 'like', "%{$search}%");
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Busca realizada com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Token válido por 8 horas (480 minutos)
        if (!$token = Auth::setTTL(480)->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Atualizar último acesso
        $user->update([
            'ultimo_acesso' => Carbon::now(),
            'status' => 'online'
        ]);

        // Obter permissões e menus

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => 480 * 60, // segundos
            ]
        ]);
    }

    public function me()
    {
        // ✅ OBTER PERMISSÕES DO USUÁRIO
        $permissoes = $this->getUserPermissions(Auth::user());
        $menus = $this->getUserMenusWithHierarchy(Auth::user());

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'permissoes' => $permissoes,
            'menus' => $menus
        ]);
    }

    public function refresh()
    {
        try {
            // Obter o usuário autenticado atual
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado.',
                ], 401);
            }

            // Atualizar o último acesso e o status
            $user->update([
                'ultimo_acesso' => Carbon::now(),
                'status' => 'online',
            ]);

            // Gerar um novo token JWT
            $newToken = Auth::refresh();

            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                ]
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token inválido.',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar o token.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        // ✅ ATUALIZAR ÚLTIMO ACESSO AQUI - SIMPLES E DIRETO
        /** @var \App\Models\User $user */
        $user->update(['status' => 'offline']);

        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    // ✅ NOVO MÉTODO PARA OBTER PERMISSÕES
    private function getUserPermissions($user)
    {
        // Obter todas as funções do funcionário
        $funcoes = $user->funcoes()->with('permissoes.menu')->get();

        $permissoesFormatadas = [];

        foreach ($funcoes as $funcao) {
            foreach ($funcao->permissoes as $permissao) {
                $menuLink = $permissao->menu->link;

                // Se o menu já não existe no array, adicionar
                if (!isset($permissoesFormatadas[$menuLink])) {
                    $permissoesFormatadas[$menuLink] = [
                        'canView' => false,
                        'canCreate' => false,
                        'canUpdate' => false,
                        'canDelete' => false
                    ];
                }

                // Combinar permissões (usar OR lógico)
                $permissoesFormatadas[$menuLink]['canView'] = $permissoesFormatadas[$menuLink]['canView'] || $permissao->canView;
                $permissoesFormatadas[$menuLink]['canCreate'] = $permissoesFormatadas[$menuLink]['canCreate'] || $permissao->canCreate;
                $permissoesFormatadas[$menuLink]['canUpdate'] = $permissoesFormatadas[$menuLink]['canUpdate'] || $permissao->canUpdate;
                $permissoesFormatadas[$menuLink]['canDelete'] = $permissoesFormatadas[$menuLink]['canDelete'] || $permissao->canDelete;
            }
        }

        return $permissoesFormatadas;
    }

    private function getUserMenusWithHierarchy($user)
    {
        // Busca as permissões com menus para todas as funções do funcionário
        $permissoes = Permissoes::whereIn('funcao_id', $user->funcoes()->pluck('funcao_id'))
            ->where('canView', true)
            ->with('menu')
            ->get();
        $allMenus = [];
        foreach ($permissoes as $perm) {
            $allMenus[] = $perm->menu;
        }

        return $this->buildMenuHierarchy($allMenus);
    }

    function buildMenuHierarchy(array $menus, $parentId = 0)
    {
        $result = [];

        foreach ($menus as $menu) {
            if ($menu['parent'] == $parentId) {
                $children = $this->buildMenuHierarchy($menus, $menu['id']);

                $formattedMenu = [
                    'name' => $menu['label'],
                    'icon' => $menu['icone'],
                    'path' => $menu['link'] ?? null,
                    'children' => !empty($children) ? $children : [],
                    'badge' => false,
                    'sort' => $menu['sort'] ?? 999, // Manter o sort para ordenação posterior
                ];

                // Removendo 'path' se for null para manter o formato limpo
                if (is_null($formattedMenu['path'])) {
                    unset($formattedMenu['path']);
                }

                // Removendo 'children' se estiver vazio
                if (empty($formattedMenu['children'])) {
                    unset($formattedMenu['children']);
                }

                $result[] = $formattedMenu;
            }
        }

        // Aplicar ordenação APENAS para menus de nível superior (parentId = 0)
        if ($parentId === 0) {
            usort($result, function ($a, $b) {
                return ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0);
            });
        }

        // Remover o campo 'sort' do resultado final
        $result = array_map(function ($menu) {
            unset($menu['sort']);
            return $menu;
        }, $result);

        return $result;
    }
}