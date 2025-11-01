<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CursoController, EmailController, QuizController, UserController};

Route::prefix('users')->group(function () {
    // Cadastro
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/me', [UserController::class, 'me'])->middleware('auth:api');
    Route::post('/create-complete', [UserController::class, 'storeUserWithDetails']);
        
    Route::post('/send-contact-email', [EmailController::class, 'sendContactEmail']);
    Route::post('/send-simple-email', [EmailController::class, 'sendSimpleEmail']);

});

Route::middleware(['auth:api'])->group(function () {
    //Usuários Para usuario autenticado
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/search', [UserController::class, 'search']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{userId}/details', [UserController::class, 'showUserDetails']);
        
        // Cadastro
        Route::post('/create-user', [UserController::class, 'storeUserOnly']);
        Route::post('/create-details', [UserController::class, 'storeUserDetailsOnly']);
    });

    // ==================== ROTAS PARA CATEGORIAS ====================
    Route::prefix('categorias')->group(function () {
        Route::get('/', [CursoController::class, 'indexCategorias']);
        Route::post('/', [CursoController::class, 'storeCategoria']);
        Route::get('/{id}', [CursoController::class, 'showCategoria']);
        Route::put('/{id}', [CursoController::class, 'updateCategoria']);
        Route::delete('/{id}', [CursoController::class, 'destroyCategoria']);
    });

    // ==================== ROTAS PARA CURSOS ====================
    Route::prefix('cursos')->group(function () {
        Route::get('/', [CursoController::class, 'indexCursos']);
        Route::post('/', [CursoController::class, 'storeCurso']);
        Route::get('/search', [CursoController::class, 'searchCursos']);
        Route::get('/categoria/{categoriaId}', [CursoController::class, 'cursosPorCategoria']);
        Route::get('/{id}', [CursoController::class, 'showCurso']);
        Route::put('/{id}', [CursoController::class, 'updateCurso']);
        Route::delete('/{id}', [CursoController::class, 'destroyCurso']);
    });

    // ==================== ROTAS PARA MÓDULOS ====================
    Route::prefix('modulos')->group(function () {
        Route::get('/curso/{cursoId}', [CursoController::class, 'indexModulos']);
        Route::post('/', [CursoController::class, 'storeModulo']);
        Route::get('/{id}', [CursoController::class, 'showModulo']);
        Route::put('/{id}', [CursoController::class, 'updateModulo']);
        Route::delete('/{id}', [CursoController::class, 'destroyModulo']);
    });

    // ==================== ROTAS PARA ATIVIDADES ====================
    Route::prefix('atividades')->group(function () {
        Route::get('/', [QuizController::class, 'indexAtividades']);
        Route::post('/', [QuizController::class, 'storeAtividade']);
        Route::get('/{id}', [QuizController::class, 'showAtividade']);
        Route::put('/{id}', [QuizController::class, 'updateAtividade']);
        Route::delete('/{id}', [QuizController::class, 'destroyAtividade']);
    });

    // ==================== ROTAS PARA QUIZZES ====================
    Route::prefix('quizzes')->group(function () {
        Route::get('/', [QuizController::class, 'indexQuizzes']);
        Route::post('/', [QuizController::class, 'storeQuiz']);
        Route::get('/{id}', [QuizController::class, 'showQuiz']);
        Route::put('/{id}', [QuizController::class, 'updateQuiz']);
        Route::delete('/{id}', [QuizController::class, 'destroyQuiz']);
    });

    // ==================== ROTAS PARA QUESTÕES ====================
    Route::prefix('questions')->group(function () {
        Route::get('/quiz/{quizId}', [QuizController::class, 'indexQuestions']);
        Route::post('/', [QuizController::class, 'storeQuestion']);
        Route::get('/{id}', [QuizController::class, 'showQuestion']);
        Route::put('/{id}', [QuizController::class, 'updateQuestion']);
        Route::delete('/{id}', [QuizController::class, 'destroyQuestion']);
    });

    // ==================== ROTAS PARA OPÇÕES DE QUESTÕES ====================
    Route::prefix('question-options')->group(function () {
        Route::post('/', [QuizController::class, 'storeQuestionOption']);
        Route::put('/{id}', [QuizController::class, 'updateQuestionOption']);
        Route::delete('/{id}', [QuizController::class, 'destroyQuestionOption']);
    });

    // ==================== ROTAS PARA RESPOSTAS DOS ESTUDANTES ====================
    Route::prefix('student-answers')->group(function () {
        Route::get('/', [QuizController::class, 'indexStudentAnswers']);
        Route::post('/', [QuizController::class, 'storeStudentAnswer']);
        Route::put('/{id}/evaluate', [QuizController::class, 'evaluateStudentAnswer']);
    });

    // ==================== ROTAS PARA COMENTÁRIOS ====================
    Route::prefix('activity-coments')->group(function () {
        Route::get('/atividade/{atividadeId}', [QuizController::class, 'indexActivityComents']);
        Route::post('/', [QuizController::class, 'storeActivityComent']);
        Route::delete('/{id}', [QuizController::class, 'destroyActivityComent']);
    });

    // ==================== ROTAS PARA ASSINATURAS ====================
    Route::prefix('assinaturas')->group(function () {
        Route::get('/atividade/{atividadeId}', [QuizController::class, 'indexAssinaturas']);
        Route::post('/', [QuizController::class, 'storeAssinarAtividade']);
        Route::delete('/{id}', [QuizController::class, 'destroyAssinarAtividade']);
    });

});    

// // Relatorios
// Route::prefix('relatorios')->group(function () {
//     Route::get('/entidades', [ReportController::class, 'entidadesPdf']);
//     Route::post('/processos', [ReportController::class, 'reportProcessoPdf']);
//     Route::post('/imoveis', [ReportController::class, 'reportImoveisPdf']);
//     Route::post('/pagamentos', [ReportController::class, 'reportPagamentosPdf']);
//     Route::post('/gerar-comprovativo', [ReportController::class, 'reportInscricaoPdf']);
// });

// Route::middleware(['auth:api'])->group(function () {
//     Route::get('/logs', [LogController::class, 'index']);
//     Route::get('/imagem/base64', [UploadController::class, 'getImagemBase64']);
//     Route::put('/add-perfil/{id}/perfil', [UploadController::class, 'perfil']);

//     //Controller departamentos
//     Route::get('departamentos/all', [DepartamentoController::class, 'allDepartamento']);
//     Route::post('departamentos/add', [DepartamentoController::class, 'addDepartamento']);
//     Route::post('departamentos/add/{id}', [DepartamentoController::class, 'addDepartamento']);
//     Route::get('departamentos/funcoes/{id}', [DepartamentoController::class, 'funcoesDep']);
//     Route::put('departamentos/add/{id}', [DepartamentoController::class, 'addDepartamento']);
//     Route::delete('departamentos/delete/{id}', [DepartamentoController::class, 'deleteDepartamento']);

//     //Controller funcoes
//     Route::get('funcoes/all', [DepartamentoController::class, 'allFuncoes']);
//     Route::get('funcoes/funcionario/{id}', [DepartamentoController::class, 'funcionarioFuncoes']);
//     Route::post('funcoes/add', [DepartamentoController::class, 'addFuncao']);
//     Route::put('funcoes/add/{id}', [DepartamentoController::class, 'addFuncao']);
//     Route::delete('funcoes/delete/{id}', [DepartamentoController::class, 'deleteFuncao']);
//     Route::get('/permissoes', [DepartamentoController::class, 'getPermissoes']);
//     Route::get('/permissoes/menu/{id_menu}/{id_funcao}', [DepartamentoController::class, 'getMenuPermissoes']);
//     Route::post('/permissoes/bulk', [DepartamentoController::class, 'storePermissoes']);
//     Route::get('/permissoes/funcao/{id}', [DepartamentoController::class, 'showPermissoes']);
//     Route::get('/permissoes/funcao/{id}/inicialize', [DepartamentoController::class, 'initializePermissoes']);

//     // Rotas comuns para menus
//     Route::get('/menus', [UsuarioController::class, 'allMenus']);
//     Route::get('/menus/organized', [DepartamentoController::class, 'allMenuOrganized']);
//     Route::post('/menus', [UsuarioController::class, 'addMenu']);
//     Route::post('/menus/bulk-update', [UsuarioController::class, 'addBulkMenu']);
//     Route::put('/menus', [UsuarioController::class, 'addMenu']);
//     Route::delete('/menus/{id}', [UsuarioController::class, 'deleteMenu']);

//     // Rotas para File Items
//     Route::prefix('file-items')->group(function () {
//         Route::post('/upload', [FileItemController::class, 'upload']);
//         Route::post('/move-file', [FileItemController::class, 'moveFile']);
//         Route::post('/perfil', [FileItemController::class, 'updatePerfil']);
//         Route::put('/pessoa-perfil', [FileItemController::class, 'pessoaPerfil']);
//         // Listar toda a estrutura (GET /api/file-items)
//         Route::get('', [FileItemController::class, 'index']);
//         // Obter um item específico (GET /api/file-items/{id})
//         Route::get('/{id}', [FileItemController::class, 'show']);
//         // Criar novo item (POST /api/file-items)
//         Route::post('/', [FileItemController::class, 'store']);
//         // Atualizar item (PUT /api/file-items/{id})
//         Route::put('/{id}', [FileItemController::class, 'update']);
//         // Deletar item (DELETE /api/file-items/{id})
//         Route::delete('/{id}', [FileItemController::class, 'destroy']);
//         // Popular dados iniciais (POST /api/file-items/seed)
//         Route::post('/seed', [FileItemController::class, 'seed']);
//         // Obter estrutura a partir de um item específico
//         Route::get('/{id}/structure', [FileItemController::class, 'structure']);
//         // Listar itens por tipo
//         Route::get('/type/{type}', [FileItemController::class, 'byType']);
//         // Busca de itens
//         Route::get('/search/{term}', [FileItemController::class, 'search']);
//         Route::post('/upload-perfil', [FileItemController::class, 'uploadPerfil']);
//         Route::get('/imagem/base64', [FileItemController::class, 'getImagemBase64']);
//     });

//     Route::prefix('departamentos')->group(function () {
//         //Controller departamentos
//         Route::get('/all', [DepartamentoController::class, 'allDepartamento']);
//         Route::post('/add', [DepartamentoController::class, 'addDepartamento']);
//         Route::post('/add/{id}', [DepartamentoController::class, 'addDepartamento']);
//         Route::get('/funcoes/{id}', [DepartamentoController::class, 'funcoesDep']);
//         Route::put('/add/{id}', [DepartamentoController::class, 'addDepartamento']);
//         //Controller funcoes
//         Route::get('/funcoes/all', [DepartamentoController::class, 'allFuncoes']);
//         Route::get('/funcoes/funcionario/{id}', [DepartamentoController::class, 'funcionarioFuncoes']);
//         Route::post('/funcoes/add', [DepartamentoController::class, 'addFuncao']);
//         Route::put('/funcoes/add/{id}', [DepartamentoController::class, 'addFuncao']);
//         // Rotas comuns para menus
//         Route::get('/menus', [DepartamentoController::class, 'allMenus']);
//         Route::post('/menus', [DepartamentoController::class, 'addMenu']);
//     });

//     // Grupo de rotas para Usuários
//     Route::prefix('users')->group(function () {
//         Route::get('/all', [UsuarioController::class, 'index']);
//         Route::get('/tecnicos', [UsuarioController::class, 'tecnicos']);
//         Route::get('/{id}', [UsuarioController::class, 'index']);
//         Route::post('/add', [UsuarioController::class, 'registerUser']);
//         Route::put('/update/{id}', [UsuarioController::class, 'updateUser']);
//         Route::delete('/{id}/delete', [UsuarioController::class, 'destroy']);
//         Route::post('/recover', [UsuarioController::class, 'recover']);
//         Route::post('/new-user', [UsuarioController::class, 'newUser']);
//     });
// });
