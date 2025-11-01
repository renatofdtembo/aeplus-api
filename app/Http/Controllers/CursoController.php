<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\{Categoria, Curso, Modulo};

class CursoController extends Controller
{
    // ==================== CATEGORIAS ====================

    /**
     * 📋 Listar todas as categorias
     */
    public function indexCategorias(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);

            // Buscar apenas categorias raiz (onde pai é null)
            $categorias = Categoria::with(['subcategoriasRecursivas'])
                ->whereNull('pai')
                ->orderBy('data_criacao', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categorias,
                'message' => 'Categorias listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar categorias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar nova categoria
     */
    public function storeCategoria(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'pai' => 'nullable|exists:categorias,id'
            ]);

            $categoria = Categoria::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $categoria->load(['categoriaPai', 'subcategorias']),
                'message' => 'Categoria criada com sucesso.'
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
                'message' => 'Erro ao criar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar categoria específica
     */
    public function showCategoria($id)
    {
        try {
            $categoria = Categoria::with(['categoriaPai', 'subcategorias'])->find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $categoria,
                'message' => 'Categoria encontrada com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar categoria
     */
    public function updateCategoria(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }

            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'pai' => 'nullable|exists:categorias,id'
            ]);

            $categoria->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $categoria->load(['categoriaPai', 'subcategorias']),
                'message' => 'Categoria atualizada com sucesso.'
            ]);
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
                'message' => 'Erro ao atualizar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar categoria
     */
    public function destroyCategoria($id)
    {
        DB::beginTransaction();

        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }

            // Verificar se existem cursos usando esta categoria
            $cursosCount = Curso::where('categoria_id', $id)->count();
            if ($cursosCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir a categoria pois existem cursos vinculados a ela.'
                ], 422);
            }

            // Verificar se existem subcategorias
            $subcategoriasCount = $categoria->subcategorias()->count();
            if ($subcategoriasCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir a categoria pois existem subcategorias vinculadas a ela.'
                ], 422);
            }

            $categoria->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída com sucesso.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== CURSOS ====================

    /**
     * 📋 Listar todos os cursos
     */
    public function indexCursos(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $cursos = Curso::with(['categoria', 'responsavel', 'instituicao', 'modulos'])
                ->orderBy('data_criacao', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $cursos,
                'message' => 'Cursos listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar cursos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar novo curso
     */
    public function storeCurso(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'nome_breve' => 'required|string|max:100',
                'descricao' => 'required|string',
                'preco' => 'required|numeric|min:0',
                'gratuito' => 'boolean',
                'inscricao' => 'boolean',
                'data_inicio_inscricao' => 'nullable|date',
                'data_fim_inscricao' => 'nullable|date|after_or_equal:data_inicio_inscricao',
                'data_inicio' => 'nullable|date',
                'data_termino' => 'nullable|date|after_or_equal:data_inicio',
                'categoria_id' => 'required|exists:categorias,id',
                'responsavel_id' => 'required|exists:users,id',
                'instituicao_id' => 'required|exists:users,id',
                'duracao' => 'required|in:' . implode(',', Curso::DURACOES),
                'nivel' => 'required|in:' . implode(',', Curso::NIVEIS),
                'privacidade' => 'required|in:' . implode(',', Curso::PRIVACIDADES),
                'oqueaprender' => 'nullable|string',
                'sobre' => 'nullable|string',
                'video_introducao' => 'nullable|url',
                'tipo' => 'nullable|string',
                'visibilidade' => 'boolean',
                'configuracoes' => 'nullable|json',
                'capa' => 'nullable|string',
                'url_image' => 'nullable|url'
            ]);

            $curso = Curso::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $curso->load(['categoria', 'responsavel', 'instituicao', 'modulos']),
                'message' => 'Curso criado com sucesso.'
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
                'message' => 'Erro ao criar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar curso específico
     */
    public function showCurso($id)
    {
        try {
            $curso = Curso::with(['categoria', 'responsavel', 'instituicao', 'modulos'])->find($id);

            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $curso,
                'message' => 'Curso encontrado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar curso
     */
    public function updateCurso(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $curso = Curso::find($id);

            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso não encontrado.'
                ], 404);
            }

            $validated = $request->validate([
                'titulo' => 'sometimes|required|string|max:255',
                'nome_breve' => 'sometimes|required|string|max:100',
                'descricao' => 'sometimes|required|string',
                'preco' => 'sometimes|required|numeric|min:0',
                'gratuito' => 'sometimes|boolean',
                'inscricao' => 'sometimes|boolean',
                'data_inicio_inscricao' => 'nullable|date',
                'data_fim_inscricao' => 'nullable|date|after_or_equal:data_inicio_inscricao',
                'data_inicio' => 'nullable|date',
                'data_termino' => 'nullable|date|after_or_equal:data_inicio',
                'categoria_id' => 'sometimes|required|exists:categorias,id',
                'responsavel_id' => 'sometimes|required|exists:users,id',
                'instituicao_id' => 'sometimes|required|exists:users,id',
                'duracao' => 'sometimes|required|in:' . implode(',', Curso::DURACOES),
                'nivel' => 'sometimes|required|in:' . implode(',', Curso::NIVEIS),
                'privacidade' => 'sometimes|required|in:' . implode(',', Curso::PRIVACIDADES),
                'oqueaprender' => 'nullable|string',
                'sobre' => 'nullable|string',
                'video_introducao' => 'nullable|url',
                'tipo' => 'nullable|string',
                'visibilidade' => 'sometimes|boolean',
                'configuracoes' => 'nullable|json',
                'capa' => 'nullable|string',
                'url_image' => 'nullable|url'
            ]);

            $curso->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $curso->load(['categoria', 'responsavel', 'instituicao', 'modulos']),
                'message' => 'Curso atualizado com sucesso.'
            ]);
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
                'message' => 'Erro ao atualizar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar curso
     */
    public function destroyCurso($id)
    {
        DB::beginTransaction();

        try {
            $curso = Curso::with('modulos')->find($id);

            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso não encontrado.'
                ], 404);
            }

            // Verificar se existem módulos vinculados
            if ($curso->modulos->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir o curso pois existem módulos vinculados a ele.'
                ], 422);
            }

            $curso->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Curso excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir curso: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MÓDULOS ====================

    /**
     * 📋 Listar todos os módulos de um curso
     */
    public function indexModulos(Request $request, $cursoId)
    {
        try {
            $curso = Curso::find($cursoId);

            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso não encontrado.'
                ], 404);
            }

            $perPage = $request->get('per_page', 15);
            $modulos = Modulo::where('curso_id', $cursoId)
                ->orderBy('ordem')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $modulos,
                'message' => 'Módulos listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar módulos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar novo módulo
     */
    public function storeModulo(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'ordem' => 'required|integer|min:0',
                'peso' => 'required|numeric|min:0',
                'configuracoes' => 'nullable|json',
                'curso_id' => 'required|exists:cursos,id'
            ]);

            $modulo = Modulo::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $modulo->load('curso'),
                'message' => 'Módulo criado com sucesso.'
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
                'message' => 'Erro ao criar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar módulo específico
     */
    public function showModulo($id)
    {
        try {
            $modulo = Modulo::with('curso')->find($id);

            if (!$modulo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $modulo,
                'message' => 'Módulo encontrado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar módulo
     */
    public function updateModulo(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $modulo = Modulo::find($id);

            if (!$modulo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não encontrado.'
                ], 404);
            }

            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'ordem' => 'sometimes|required|integer|min:0',
                'peso' => 'sometimes|required|numeric|min:0',
                'configuracoes' => 'nullable|json',
                'curso_id' => 'sometimes|required|exists:cursos,id'
            ]);

            $modulo->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $modulo->load('curso'),
                'message' => 'Módulo atualizado com sucesso.'
            ]);
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
                'message' => 'Erro ao atualizar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar módulo
     */
    public function destroyModulo($id)
    {
        DB::beginTransaction();

        try {
            $modulo = Modulo::find($id);

            if (!$modulo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não encontrado.'
                ], 404);
            }

            $modulo->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Módulo excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MÉTODOS ADICIONAIS ====================

    /**
     * 🔍 Buscar cursos por título ou descrição
     */
    public function searchCursos(Request $request)
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

            $cursos = Curso::with(['categoria', 'responsavel', 'instituicao'])
                ->where('titulo', 'like', "%{$search}%")
                ->orWhere('descricao', 'like', "%{$search}%")
                ->orWhere('nome_breve', 'like', "%{$search}%")
                ->orderBy('data_criacao', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $cursos,
                'message' => 'Busca realizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar cursos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Listar cursos por categoria
     */
    public function cursosPorCategoria($categoriaId)
    {
        try {
            $categoria = Categoria::find($categoriaId);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }

            $cursos = Curso::with(['categoria', 'responsavel', 'instituicao'])
                ->where('categoria_id', $categoriaId)
                ->orderBy('data_criacao', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cursos,
                'message' => 'Cursos da categoria listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar cursos por categoria: ' . $e->getMessage()
            ], 500);
        }
    }
}
