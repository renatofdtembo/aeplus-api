<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\{ActivityComent, AssinarAtividade, Atividade, Question, QuestionOption, Quiz, StudentAnswer};

class QuizController extends Controller
{
    // ==================== ATIVIDADES ====================

    /**
     * 📋 Listar todas as atividades
     */
    public function indexAtividades(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $atividades = Atividade::with(['modulo', 'comentarios', 'assinaturas'])
                                ->orderBy('posicao')
                                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $atividades,
                'message' => 'Atividades listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar nova atividade
     */
    public function storeAtividade(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'nullable|string',
                'configuracoes' => 'nullable|json',
                'tipo' => 'required|string',
                'status' => 'boolean',
                'change_aba' => 'boolean',
                'required_camera' => 'boolean',
                'posicao' => 'required|integer',
                'peso' => 'required|numeric|min:0',
                'id_modulo' => 'required|exists:modulos,id'
            ]);

            $atividade = Atividade::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $atividade->load(['modulo', 'comentarios', 'assinaturas']),
                'message' => 'Atividade criada com sucesso.'
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
                'message' => 'Erro ao criar atividade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar atividade específica
     */
    public function showAtividade($id)
    {
        try {
            $atividade = Atividade::with(['modulo', 'comentarios.usuario', 'assinaturas.usuario'])->find($id);

            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $atividade,
                'message' => 'Atividade encontrada com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atividade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar atividade
     */
    public function updateAtividade(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $atividade = Atividade::find($id);

            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada.'
                ], 404);
            }

            $validated = $request->validate([
                'titulo' => 'sometimes|required|string|max:255',
                'conteudo' => 'nullable|string',
                'configuracoes' => 'nullable|json',
                'tipo' => 'sometimes|required|string',
                'status' => 'sometimes|boolean',
                'change_aba' => 'sometimes|boolean',
                'required_camera' => 'sometimes|boolean',
                'posicao' => 'sometimes|required|integer',
                'peso' => 'sometimes|required|numeric|min:0',
                'id_modulo' => 'sometimes|required|exists:modulos,id'
            ]);

            $atividade->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $atividade->load(['modulo', 'comentarios', 'assinaturas']),
                'message' => 'Atividade atualizada com sucesso.'
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
                'message' => 'Erro ao atualizar atividade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar atividade
     */
    public function destroyAtividade($id)
    {
        DB::beginTransaction();
        
        try {
            $atividade = Atividade::with(['comentarios', 'assinaturas'])->find($id);

            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada.'
                ], 404);
            }

            $atividade->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade excluída com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir atividade: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== QUIZZES ====================

    /**
     * 📋 Listar todos os quizzes
     */
    public function indexQuizzes(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $quizzes = Quiz::with(['atividade', 'questions.options'])
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $quizzes,
                'message' => 'Quizzes listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar quizzes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar novo quiz
     */
    public function storeQuiz(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'time_limit' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
                'show_justification' => 'boolean',
                'atividade_id' => 'required|exists:atividades,id'
            ]);

            $quiz = Quiz::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $quiz->load(['atividade', 'questions']),
                'message' => 'Quiz criado com sucesso.'
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
                'message' => 'Erro ao criar quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar quiz específico
     */
    public function showQuiz($id)
    {
        try {
            $quiz = Quiz::with(['atividade', 'questions.options', 'questions.studentAnswers'])->find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $quiz,
                'message' => 'Quiz encontrado com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar quiz
     */
    public function updateQuiz(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $quiz = Quiz::find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz não encontrado.'
                ], 404);
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'time_limit' => 'nullable|integer|min:1',
                'is_active' => 'sometimes|boolean',
                'show_justification' => 'sometimes|boolean',
                'atividade_id' => 'sometimes|required|exists:atividades,id'
            ]);

            $quiz->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $quiz->load(['atividade', 'questions']),
                'message' => 'Quiz atualizado com sucesso.'
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
                'message' => 'Erro ao atualizar quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar quiz
     */
    public function destroyQuiz($id)
    {
        DB::beginTransaction();
        
        try {
            $quiz = Quiz::with('questions')->find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz não encontrado.'
                ], 404);
            }

            $quiz->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz excluído com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== QUESTÕES ====================

    /**
     * 📋 Listar todas as questões de um quiz
     */
    public function indexQuestions(Request $request, $quizId)
    {
        try {
            $quiz = Quiz::find($quizId);
            
            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz não encontrado.'
                ], 404);
            }

            $perPage = $request->get('per_page', 15);
            $questions = Question::with(['options', 'studentAnswers'])
                            ->where('quiz_id', $quizId)
                            ->orderBy('order')
                            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $questions,
                'message' => 'Questões listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar questões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar nova questão
     */
    public function storeQuestion(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'text' => 'required|string',
                'justification' => 'nullable|string',
                'type' => 'required|in:multiple_choice,true_false,open_ended,fill_blank',
                'correct_answer' => 'nullable|string',
                'requires_justification' => 'boolean',
                'points' => 'required|integer|min:1',
                'order' => 'required|integer|min:0',
                'quiz_id' => 'required|exists:quizzes,id'
            ]);

            $question = Question::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $question->load(['options', 'quiz']),
                'message' => 'Questão criada com sucesso.'
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
                'message' => 'Erro ao criar questão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Mostrar questão específica
     */
    public function showQuestion($id)
    {
        try {
            $question = Question::with(['options', 'quiz', 'studentAnswers'])->find($id);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Questão não encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $question,
                'message' => 'Questão encontrada com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar questão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar questão
     */
    public function updateQuestion(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $question = Question::find($id);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Questão não encontrada.'
                ], 404);
            }

            $validated = $request->validate([
                'text' => 'sometimes|required|string',
                'justification' => 'nullable|string',
                'type' => 'sometimes|required|in:multiple_choice,true_false,open_ended,fill_blank',
                'correct_answer' => 'nullable|string',
                'requires_justification' => 'sometimes|boolean',
                'points' => 'sometimes|required|integer|min:1',
                'order' => 'sometimes|required|integer|min:0',
                'quiz_id' => 'sometimes|required|exists:quizzes,id'
            ]);

            $question->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $question->load(['options', 'quiz']),
                'message' => 'Questão atualizada com sucesso.'
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
                'message' => 'Erro ao atualizar questão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar questão
     */
    public function destroyQuestion($id)
    {
        DB::beginTransaction();
        
        try {
            $question = Question::with(['options', 'studentAnswers'])->find($id);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Questão não encontrada.'
                ], 404);
            }

            $question->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Questão excluída com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir questão: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== OPÇÕES DE QUESTÕES ====================

    /**
     * ➕ Criar opção para questão
     */
    public function storeQuestionOption(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'text' => 'required|string',
                'is_correct' => 'boolean',
                'question_id' => 'required|exists:questions,id'
            ]);

            $option = QuestionOption::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $option->load('question'),
                'message' => 'Opção criada com sucesso.'
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
                'message' => 'Erro ao criar opção: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Atualizar opção de questão
     */
    public function updateQuestionOption(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $option = QuestionOption::find($id);

            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opção não encontrada.'
                ], 404);
            }

            $validated = $request->validate([
                'text' => 'sometimes|required|string',
                'is_correct' => 'sometimes|boolean'
            ]);

            $option->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $option->load('question'),
                'message' => 'Opção atualizada com sucesso.'
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
                'message' => 'Erro ao atualizar opção: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar opção de questão
     */
    public function destroyQuestionOption($id)
    {
        DB::beginTransaction();
        
        try {
            $option = QuestionOption::find($id);

            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opção não encontrada.'
                ], 404);
            }

            $option->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Opção excluída com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir opção: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== RESPOSTAS DOS ESTUDANTES ====================

    /**
     * 📋 Listar respostas dos estudantes
     */
    public function indexStudentAnswers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $answers = StudentAnswer::with(['question', 'student', 'question.quiz'])
                            ->orderBy('submitted_at', 'desc')
                            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $answers,
                'message' => 'Respostas listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar respostas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Submeter resposta de estudante
     */
    public function storeStudentAnswer(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'answer' => 'nullable|string',
                'justification' => 'nullable|string',
                'score' => 'nullable|numeric|min:0',
                'feedback' => 'nullable|string',
                'concluido' => 'boolean',
                'question_id' => 'required|exists:questions,id',
                'student_id' => 'required|exists:users,id'
            ]);

            // Verificar se já existe resposta para esta questão e estudante
            $existingAnswer = StudentAnswer::where('question_id', $validated['question_id'])
                                        ->where('student_id', $validated['student_id'])
                                        ->first();

            if ($existingAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma resposta para esta questão.'
                ], 422);
            }

            $studentAnswer = StudentAnswer::create(array_merge($validated, [
                'submitted_at' => now()
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $studentAnswer->load(['question', 'student']),
                'message' => 'Resposta submetida com sucesso.'
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
                'message' => 'Erro ao submeter resposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ Avaliar resposta do estudante
     */
    public function evaluateStudentAnswer(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $studentAnswer = StudentAnswer::find($id);

            if (!$studentAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resposta não encontrada.'
                ], 404);
            }

            $validated = $request->validate([
                'score' => 'required|numeric|min:0',
                'feedback' => 'nullable|string',
                'concluido' => 'sometimes|boolean'
            ]);

            $studentAnswer->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $studentAnswer->load(['question', 'student']),
                'message' => 'Resposta avaliada com sucesso.'
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
                'message' => 'Erro ao avaliar resposta: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== COMENTÁRIOS DE ATIVIDADES ====================

    /**
     * 📋 Listar comentários de uma atividade
     */
    public function indexActivityComents($atividadeId)
    {
        try {
            $atividade = Atividade::find($atividadeId);
            
            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada.'
                ], 404);
            }

            $comentarios = ActivityComent::with(['usuario', 'respostas.usuario'])
                                    ->where('id_atividade', $atividadeId)
                                    ->whereNull('id_pai')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

            return response()->json([
                'success' => true,
                'data' => $comentarios,
                'message' => 'Comentários listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar comentários: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Criar comentário
     */
    public function storeActivityComent(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'message' => 'required|string',
                'tipo' => 'required|string',
                'id_pai' => 'nullable|exists:atividade_coments,id',
                'id_atividade' => 'required|exists:atividades,id',
                'id_usuario' => 'required|exists:users,id'
            ]);

            $comentario = ActivityComent::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $comentario->load(['usuario', 'respostas']),
                'message' => 'Comentário criado com sucesso.'
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
                'message' => 'Erro ao criar comentário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Deletar comentário
     */
    public function destroyActivityComent($id)
    {
        DB::beginTransaction();
        
        try {
            $comentario = ActivityComent::with('respostas')->find($id);

            if (!$comentario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comentário não encontrado.'
                ], 404);
            }

            $comentario->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comentário excluído com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir comentário: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== ASSINATURAS DE ATIVIDADES ====================

    /**
     * 📋 Listar assinaturas de uma atividade
     */
    public function indexAssinaturas($atividadeId)
    {
        try {
            $atividade = Atividade::find($atividadeId);
            
            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada.'
                ], 404);
            }

            $assinaturas = AssinarAtividade::with('usuario')
                                    ->where('id_atividade', $atividadeId)
                                    ->get();

            return response()->json([
                'success' => true,
                'data' => $assinaturas,
                'message' => 'Assinaturas listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar assinaturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ Assinar atividade
     */
    public function storeAssinarAtividade(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'id_atividade' => 'required|exists:atividades,id',
                'id_usuario' => 'required|exists:users,id'
            ]);

            // Verificar se já existe assinatura
            $existingAssinatura = AssinarAtividade::where('id_atividade', $validated['id_atividade'])
                                                ->where('id_usuario', $validated['id_usuario'])
                                                ->first();

            if ($existingAssinatura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário já assinou esta atividade.'
                ], 422);
            }

            $assinatura = AssinarAtividade::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $assinatura->load(['usuario', 'atividade']),
                'message' => 'Atividade assinada com sucesso.'
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
                'message' => 'Erro ao assinar atividade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Cancelar assinatura de atividade
     */
    public function destroyAssinarAtividade($id)
    {
        DB::beginTransaction();
        
        try {
            $assinatura = AssinarAtividade::find($id);

            if (!$assinatura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assinatura não encontrada.'
                ], 404);
            }

            $assinatura->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assinatura cancelada com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar assinatura: ' . $e->getMessage()
            ], 500);
        }
    }
}