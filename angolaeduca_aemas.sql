-- --------------------------------------------------------
-- Anfitrião:                    127.0.0.1
-- Versão do servidor:           8.4.1 - MySQL Community Server - GPL
-- SO do servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- A despejar estrutura da base de dados para db_angolaeducamas
CREATE DATABASE IF NOT EXISTS `db_angolaeducamas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_angolaeducamas`;

-- A despejar estrutura para tabela db_angolaeducamas.assinar_atividade
CREATE TABLE IF NOT EXISTS `assinar_atividade` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_atividade` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assinar_atividade_id_atividade_foreign` (`id_atividade`),
  KEY `assinar_atividade_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `assinar_atividade_id_atividade_foreign` FOREIGN KEY (`id_atividade`) REFERENCES `atividades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assinar_atividade_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.assinar_atividade: ~0 rows (aproximadamente)
INSERT INTO `assinar_atividade` (`id`, `id_atividade`, `id_usuario`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2025-10-20 01:51:49', '2025-10-20 01:51:49');

-- A despejar estrutura para tabela db_angolaeducamas.atividades
CREATE TABLE IF NOT EXISTS `atividades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci,
  `configuracoes` json DEFAULT NULL,
  `tipo` enum('VIDEO','FORUM','TEXTO','QUIZ','ARTIGO','ROTULO','CERTIFICADO','PAGINA','TAREFA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TEXTO',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `change_aba` tinyint(1) NOT NULL DEFAULT '0',
  `required_camera` tinyint(1) NOT NULL DEFAULT '0',
  `posicao` int DEFAULT NULL,
  `peso` double NOT NULL DEFAULT '0',
  `id_modulo` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atividades_id_modulo_foreign` (`id_modulo`),
  CONSTRAINT `atividades_id_modulo_foreign` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.atividades: ~0 rows (aproximadamente)
INSERT INTO `atividades` (`id`, `titulo`, `conteudo`, `configuracoes`, `tipo`, `status`, `change_aba`, `required_camera`, `posicao`, `peso`, `id_modulo`, `created_at`, `updated_at`) VALUES
	(1, 'Quiz de Matemática Básica', 'Teste seus conhecimentos em operações matemáticas fundamentais', NULL, 'QUIZ', 1, 0, 0, 1, 20.5, 1, '2025-10-20 01:44:49', '2025-10-20 01:44:49');

-- A despejar estrutura para tabela db_angolaeducamas.atividade_coments
CREATE TABLE IF NOT EXISTS `atividade_coments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `tipo` enum('COMENTARIO','RESPOSTA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COMENTARIO',
  `id_pai` bigint unsigned DEFAULT NULL,
  `id_atividade` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atividade_coments_id_pai_foreign` (`id_pai`),
  KEY `atividade_coments_id_atividade_foreign` (`id_atividade`),
  KEY `atividade_coments_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `atividade_coments_id_atividade_foreign` FOREIGN KEY (`id_atividade`) REFERENCES `atividades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atividade_coments_id_pai_foreign` FOREIGN KEY (`id_pai`) REFERENCES `atividade_coments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atividade_coments_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.atividade_coments: ~0 rows (aproximadamente)
INSERT INTO `atividade_coments` (`id`, `message`, `tipo`, `id_pai`, `id_atividade`, `id_usuario`, `created_at`, `updated_at`) VALUES
	(1, 'Gostei muito deste quiz!', 'COMENTARIO', NULL, 1, 1, '2025-10-20 01:58:56', '2025-10-20 01:58:56');

-- A despejar estrutura para tabela db_angolaeducamas.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pai` bigint unsigned DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_criacao` timestamp NULL DEFAULT NULL,
  `data_atualizacao` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.categorias: ~4 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `pai`, `nome`, `data_criacao`, `data_atualizacao`) VALUES
	(1, NULL, 'Programação', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(2, NULL, 'Design', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(3, NULL, 'Data Science', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(4, NULL, 'Marketing Digital', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(5, NULL, 'Gestão', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(6, NULL, 'Idiomas', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(7, NULL, 'Tecnologia', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(8, NULL, 'Fotografia', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(9, NULL, 'Produtividade', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(10, NULL, 'Culinária', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(11, NULL, 'Finanças', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(12, NULL, 'Bem-estar', '2025-10-31 20:45:37', '2025-10-31 20:45:37'),
	(13, 1, 'HTML, CSS & JS', '2025-10-31 22:03:05', '2025-10-31 22:03:06'),
	(14, 1, 'TypeScript', '2025-10-31 22:42:02', NULL),
	(15, 1, 'Java', '2025-10-31 23:24:57', NULL),
	(16, 1, 'Java Swing', '2025-10-31 23:25:32', NULL);

-- A despejar estrutura para tabela db_angolaeducamas.cursos
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `capa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_breve` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `preco` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gratuito` tinyint(1) NOT NULL DEFAULT '0',
  `inscricao` tinyint(1) NOT NULL DEFAULT '1',
  `data_inicio_inscricao` date DEFAULT NULL,
  `data_fim_inscricao` date DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_termino` date DEFAULT NULL,
  `categoria_id` bigint unsigned DEFAULT NULL,
  `responsavel_id` bigint unsigned DEFAULT NULL,
  `instituicao_id` bigint unsigned DEFAULT NULL,
  `duracao` enum('UM_MES','TRES_MESES','SEIS_MESES','UM_ANO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel` enum('INICIANTE','INTERMEDIARIO','AVANCADO','CURSO_PROFISSIONAL') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacidade` enum('PUBLICO','PRIVADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PUBLICO',
  `oqueaprender` text COLLATE utf8mb4_unicode_ci,
  `sobre` text COLLATE utf8mb4_unicode_ci,
  `video_introducao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibilidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configuracoes` json DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT NULL,
  `data_atualizacao` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cursos_categoria_id_foreign` (`categoria_id`),
  KEY `cursos_responsavel_id_foreign` (`responsavel_id`),
  KEY `cursos_instituicao_id_foreign` (`instituicao_id`),
  CONSTRAINT `cursos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cursos_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cursos_responsavel_id_foreign` FOREIGN KEY (`responsavel_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.cursos: ~2 rows (aproximadamente)
INSERT INTO `cursos` (`id`, `capa`, `url_image`, `titulo`, `nome_breve`, `descricao`, `preco`, `gratuito`, `inscricao`, `data_inicio_inscricao`, `data_fim_inscricao`, `data_inicio`, `data_termino`, `categoria_id`, `responsavel_id`, `instituicao_id`, `duracao`, `nivel`, `privacidade`, `oqueaprender`, `sobre`, `video_introducao`, `tipo`, `visibilidade`, `configuracoes`, `data_criacao`, `data_atualizacao`) VALUES
	(1, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Laravel 10 para Iniciantes', 'Laravel Iniciante', 'Curso completo de Laravel 10', 299.90, 0, 1, NULL, NULL, NULL, NULL, 1, 1, 1, 'TRES_MESES', 'INICIANTE', 'PUBLICO', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-20 01:27:20', NULL),
	(2, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Html Css e JavaScript', 'Html Css e JavaScript Iniciante', 'Curso completo de Html Css e JavaScript', 299.90, 0, 1, NULL, NULL, NULL, NULL, 1, 1, 1, 'TRES_MESES', 'INICIANTE', 'PUBLICO', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-20 01:30:57', NULL),
	(43, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Programação para Iniciantes: Do Zero ao Primeiro Código', 'Programação Básica', 'Aprenda os fundamentos da programação com exemplos práticos e projetos simples para iniciantes.', 0.00, 1, 1, '2024-01-15', '2024-12-31', '2024-02-01', '2024-03-01', 1, 1, 11, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Lógica de programação, Variáveis, Estruturas condicionais, Loops, Funções básicas', 'Curso completo para quem deseja ingressar na área de tecnologia sem conhecimento prévio.', 'https://youtube.com/watch?v=abc123', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(44, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Web Design Moderno: HTML5, CSS3 e Design Responsivo', 'Web Design Avançado', 'Domine as técnicas modernas de web design para criar sites responsivos e atraentes.', 299.90, 0, 1, '2024-01-10', '2024-06-30', '2024-02-15', '2024-05-15', 2, 2, 12, 'TRES_MESES', 'INTERMEDIARIO', 'PUBLICO', 'HTML5 semântico, CSS3 avançado, Flexbox, Grid Layout, Design Responsivo', 'Aprenda a criar layouts modernos e profissionais para a web.', 'https://youtube.com/watch?v=def456', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(45, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Machine Learning para Negócios: Aplicações Práticas', 'ML para Negócios', 'Curso avançado de machine learning focado em aplicações empresariais reais.', 899.00, 0, 1, '2024-02-01', '2024-03-15', '2024-03-01', '2024-06-01', 3, 3, 13, 'TRES_MESES', 'AVANCADO', 'PRIVADO', 'Algoritmos de ML, Python para Data Science, TensorFlow, Análise Preditiva', 'Curso exclusivo para profissionais que desejam aplicar ML em seus negócios.', 'https://youtube.com/watch?v=ghi789', 'EXCLUSIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "experiencia", "value": "2", "operator": ">="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(46, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Marketing Digital Essencial: Estratégias para Redes Sociais', 'Marketing Digital Básico', 'Aprenda as estratégias fundamentais de marketing digital para pequenos negócios.', 0.00, 1, 1, '2024-01-20', '2024-12-31', '2024-02-10', '2024-03-10', 4, 4, 14, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Facebook Ads, Instagram Marketing, Google Analytics, SEO Básico', 'Curso prático para empreendedores e profissionais de marketing.', 'https://youtube.com/watch?v=jkl012', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(47, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'React.js Completo: Do Básico ao Avançado com Projetos Reais', 'React.js Profissional', 'Domine o React.js com hooks, context API e integração com back-end.', 450.00, 0, 1, '2024-02-01', '2024-04-30', '2024-03-01', '2024-05-01', 1, 5, 15, 'TRES_MESES', 'INTERMEDIARIO', 'PUBLICO', 'React Hooks, Context API, Redux, React Router, Testes com Jest', 'Torne-se um desenvolvedor React.js profissional com projetos reais.', 'https://youtube.com/watch?v=mno345', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(48, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Gestão de Projetos Ágeis: Metodologias Scrum e Kanban', 'Gestão Ágil', 'Curso exclusivo para gestores aprenderem metodologias ágeis na prática.', 0.00, 1, 1, '2024-02-15', '2024-03-15', '2024-03-01', '2024-04-01', 5, 6, 16, 'UM_MES', 'INTERMEDIARIO', 'PRIVADO', 'Scrum, Kanban, Cerimônias Ágeis, Product Backlog, Sprint Planning', 'Curso corporativo para equipes de desenvolvimento e gestão.', 'https://youtube.com/watch?v=pqr678', 'CORPORATIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "empresa", "value": "parceira", "operator": "=="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(49, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Data Science na Prática: Análise de Dados com Python', 'Data Science Prático', 'Aprenda análise de dados, visualização e machine learning com Python.', 650.00, 0, 1, '2024-03-01', '2024-05-31', '2024-04-01', '2024-07-01', 3, 7, 17, 'TRES_MESES', 'AVANCADO', 'PUBLICO', 'Pandas, NumPy, Matplotlib, Scikit-learn, Análise Estatística', 'Formação completa em data science com casos reais.', 'https://youtube.com/watch?v=stu901', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(50, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Inglês para Iniciantes: Comunique-se com Confiança', 'Inglês Básico', 'Curso gratuito de inglês focado em conversação do dia a dia.', 0.00, 1, 1, '2024-01-01', '2024-12-31', '2024-02-01', '2024-03-01', 6, 8, 18, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Vocabulário essencial, Gramática básica, Pronúncia, Conversação', 'Aprenda inglês de forma prática e divertida.', 'https://youtube.com/watch?v=vwx234', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(51, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Cybersecurity Avançado: Proteção de Sistemas Corporativos', 'Cybersecurity Corporativo', 'Curso avançado de segurança da informação para profissionais de TI.', 1200.00, 0, 1, '2024-03-01', '2024-04-15', '2024-04-01', '2024-07-01', 7, 9, 19, 'TRES_MESES', 'CURSO_PROFISSIONAL', 'PRIVADO', 'Pentesting, Firewalls, Criptografia, Análise de Vulnerabilidades', 'Curso exclusivo para profissionais de segurança da informação.', 'https://youtube.com/watch?v=yza567', 'EXCLUSIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "certificacao", "value": "TI", "operator": "=="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(52, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Fotografia Digital: Domine Sua Câmera e Composição', 'Fotografia Básica', 'Aprenda os princípios da fotografia e técnicas de composição.', 0.00, 1, 1, '2024-02-01', '2024-08-31', '2024-03-01', '2024-04-01', 8, 10, 20, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Configurações da câmera, Composição, Iluminação, Edição Básica', 'Curso completo para amantes da fotografia.', 'https://youtube.com/watch?v=bcd890', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(53, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Node.js e MongoDB: Desenvolvimento Back-End Completo', 'Node.js Backend', 'Aprenda a criar APIs RESTful com Node.js, Express e MongoDB.', 550.00, 0, 1, '2024-03-15', '2024-06-30', '2024-04-15', '2024-07-15', 1, 1, 11, 'TRES_MESES', 'INTERMEDIARIO', 'PUBLICO', 'Node.js, Express.js, MongoDB, JWT, APIs RESTful, Deploy', 'Torne-se um desenvolvedor back-end com Node.js.', 'https://youtube.com/watch?v=cde901', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(54, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Liderança e Gestão de Equipes de Alta Performance', 'Liderança Corporativa', 'Curso exclusivo para desenvolvimento de habilidades de liderança.', 0.00, 1, 1, '2024-02-20', '2024-03-20', '2024-03-10', '2024-04-10', 5, 2, 12, 'UM_MES', 'AVANCADO', 'PRIVADO', 'Liderança Situacional, Feedback, Gestão de Conflitos, Motivação', 'Programa de desenvolvimento de líderes corporativos.', 'https://youtube.com/watch?v=efg123', 'CORPORATIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "cargo", "value": "gerencial", "operator": "=="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(55, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'UX/UI Design: Pesquisa, Prototipagem e Testes com Usuários', 'UX/UI Design Profissional', 'Aprenda todo o processo de design centrado no usuário.', 750.00, 0, 1, '2024-04-01', '2024-07-31', '2024-05-01', '2024-08-01', 2, 3, 13, 'TRES_MESES', 'AVANCADO', 'PUBLICO', 'Design Thinking, Pesquisa de Usuário, Figma, Testes de Usabilidade', 'Formação completa em experiência do usuário e interface.', 'https://youtube.com/watch?v=fgh456', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(56, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Excel para Negócios: Planilhas e Análise de Dados', 'Excel Empresarial', 'Domine o Excel para análise de dados e relatórios empresariais.', 0.00, 1, 1, '2024-02-01', '2024-10-31', '2024-03-01', '2024-04-01', 9, 4, 14, 'UM_MES', 'INTERMEDIARIO', 'PUBLICO', 'Fórmulas, Gráficos, Tabelas Dinâmicas, Power Query, Dashboards', 'Aprenda Excel do básico ao avançado para negócios.', 'https://youtube.com/watch?v=ghi789', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(57, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'AWS Certified Solutions Architect: Preparação Completa', 'AWS Solutions Architect', 'Curso preparatório para certificação AWS com laboratórios práticos.', 1500.00, 0, 1, '2024-03-01', '2024-05-15', '2024-04-01', '2024-07-01', 7, 5, 15, 'TRES_MESES', 'CURSO_PROFISSIONAL', 'PRIVADO', 'EC2, S3, VPC, IAM, RDS, Lambda, CloudFormation, Segurança AWS', 'Preparação completa para certificação AWS Solutions Architect.', 'https://youtube.com/watch?v=hij012', 'EXCLUSIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "experiencia", "value": "1", "operator": ">="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(58, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Culinária Básica: Técnicas e Receitas para Iniciantes', 'Culinária Básica', 'Aprenda técnicas fundamentais de culinária e receitas práticas.', 0.00, 1, 1, '2024-01-15', '2024-12-31', '2024-02-15', '2024-03-15', 10, 6, 16, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Cortes básicos, Molhos, Técnicas de Cozimento, Receitas Práticas', 'Curso prático para quem quer aprender a cozinhar.', 'https://youtube.com/watch?v=ijk345', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(59, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Flutter e Dart: Desenvolvimento Mobile Multiplataforma', 'Flutter Mobile', 'Crie apps nativos para iOS e Android com Flutter e Dart.', 600.00, 0, 1, '2024-04-01', '2024-08-31', '2024-05-01', '2024-08-01', 1, 7, 17, 'TRES_MESES', 'INTERMEDIARIO', 'PUBLICO', 'Dart, Widgets Flutter, State Management, APIs, Firebase', 'Desenvolva apps mobile profissionais com Flutter.', 'https://youtube.com/watch?v=klm678', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(60, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Gestão Financeira Corporativa: Análise e Tomada de Decisão', 'Finanças Corporativas', 'Curso avançado de gestão financeira para executivos.', 0.00, 1, 1, '2024-03-01', '2024-04-01', '2024-03-15', '2024-04-15', 11, 8, 18, 'UM_MES', 'AVANCADO', 'PRIVADO', 'Análise de Balanços, Fluxo de Caixa, Orçamento, Valuation', 'Programa executivo em gestão financeira.', 'https://youtube.com/watch?v=lmn901', 'EXECUTIVO', 'VISIVEL', '{"operator": "AND", "conditions": [{"field": "empresa", "value": "parceira", "operator": "=="}]}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(61, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Docker e Kubernetes: Containers e Orquestração em Produção', 'Docker & Kubernetes', 'Aprenda a containerizar aplicações e orquestrar com Kubernetes.', 500.00, 0, 1, '2024-05-01', '2024-09-30', '2024-06-01', '2024-09-01', 7, 9, 19, 'TRES_MESES', 'AVANCADO', 'PUBLICO', 'Docker, Docker Compose, Kubernetes, Helm, CI/CD com Containers', 'Domine containers e orquestração para ambientes de produção.', 'https://youtube.com/watch?v=nop234', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09'),
	(62, 'uploads/courseMarketing.jpg', 'uploads/default.png', 'Meditação e Mindfulness: Técnicas para Redução de Estresse', 'Meditação Guiada', 'Aprenda técnicas de meditação e mindfulness para bem-estar mental.', 0.00, 1, 1, '2024-01-01', '2024-12-31', '2024-02-01', '2024-03-01', 12, 10, 20, 'UM_MES', 'INICIANTE', 'PUBLICO', 'Técnicas de Respiração, Meditação Guiada, Mindfulness, Relaxamento', 'Curso prático para reduzir estresse e melhorar qualidade de vida.', 'https://youtube.com/watch?v=opq567', 'REGULAR', 'VISIVEL', '{"operator": "AND", "conditions": []}', '2025-10-31 20:46:09', '2025-10-31 20:46:09');

-- A despejar estrutura para tabela db_angolaeducamas.departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diretor_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.departamentos: ~0 rows (aproximadamente)
INSERT INTO `departamentos` (`id`, `nome`, `categoria`, `diretor_id`, `created_at`, `updated_at`) VALUES
	(1, 'Super Administrador', 'Sistema', NULL, '2025-10-20 01:06:37', '2025-10-20 01:06:37');

-- A despejar estrutura para tabela db_angolaeducamas.enderecos
CREATE TABLE IF NOT EXISTS `enderecos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ponto_referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.enderecos: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.failed_jobs: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.file_items
CREATE TABLE IF NOT EXISTS `file_items` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('file','folder') COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned DEFAULT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modifiedAt` datetime NOT NULL,
  `createdAt` datetime NOT NULL,
  `permissions` json DEFAULT NULL,
  `parent_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_items_parent_id_foreign` (`parent_id`),
  CONSTRAINT `file_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `file_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.file_items: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.funcaos
CREATE TABLE IF NOT EXISTS `funcaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `departamento_id` bigint unsigned DEFAULT NULL,
  `salario_base` decimal(10,2) DEFAULT NULL,
  `nivel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funcaos_departamento_id_foreign` (`departamento_id`),
  CONSTRAINT `funcaos_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.funcaos: ~0 rows (aproximadamente)
INSERT INTO `funcaos` (`id`, `nome`, `descricao`, `departamento_id`, `salario_base`, `nivel`, `ativo`, `created_at`, `updated_at`) VALUES
	(1, 'Super Administrador do Sistema', 'Acesso total ao sistema com todas as permissões', 1, 0.00, 'Sistema', 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37');

-- A despejar estrutura para tabela db_angolaeducamas.inscricao
CREATE TABLE IF NOT EXISTS `inscricao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `configuracoes` json DEFAULT NULL,
  `status` enum('PENDENTE','ATIVO','CONCLUIDO','CANCELADO','TRANCADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDENTE',
  `nota` double NOT NULL DEFAULT '0',
  `curso_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inscricao_curso_id_foreign` (`curso_id`),
  KEY `inscricao_user_id_foreign` (`user_id`),
  CONSTRAINT `inscricao_curso_id_foreign` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inscricao_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.inscricao: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int NOT NULL DEFAULT '0',
  `sort` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.menus: ~6 rows (aproximadamente)
INSERT INTO `menus` (`id`, `label`, `link`, `icone`, `parent`, `sort`, `created_at`, `updated_at`) VALUES
	(1, 'Dashboard', '/dashboard', 'LineChart', 0, 1, NULL, NULL),
	(2, 'Instituições', '/instituicoes', 'Building', 0, 2, NULL, NULL),
	(3, 'Utilizadores', '/utilizadores', 'Users', 0, 3, NULL, NULL),
	(4, 'Meus Cursos', '/meus-cursos', 'BookOpen', 0, 4, NULL, NULL),
	(5, 'Configurações', '/configuracoes', 'Settings', 0, 5, NULL, NULL),
	(6, 'Calendário', '/calendario', 'CalendarDays', 0, 6, NULL, NULL);

-- A despejar estrutura para tabela db_angolaeducamas.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.migrations: ~24 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_06_05_224622_create_menus_table', 1),
	(6, '2025_06_05_225452_create_departamentos_table', 1),
	(7, '2025_06_05_225453_create_funcaos_table', 1),
	(8, '2025_06_05_225468_create_permissoes_table', 1),
	(9, '2025_08_03_222154_create_operation_logs_table', 1),
	(10, '2025_08_11_085612_create_file_items_table', 1),
	(11, '2025_08_19_152457_create_enderecos_table', 1),
	(12, '2025_08_19_152601_create_userdetails_table', 1),
	(13, '2025_08_19_171514_create_user_funcao_table', 1),
	(14, '2025_10_20_012128_create_categorias_table', 1),
	(15, '2025_10_20_012624_create_cursos_table', 1),
	(16, '2025_10_20_013411_create_inscricao_table', 1),
	(17, '2025_10_20_013954_create_modulos_table', 1),
	(18, '2025_10_20_014625_create_atividades_table', 1),
	(19, '2025_10_20_014725_create_atividade_coments_table', 1),
	(20, '2025_10_20_014808_create_assinar_atividade_table', 1),
	(21, '2025_10_20_015447_create_quizzes_table', 1),
	(22, '2025_10_20_015536_create_questions_table', 1),
	(23, '2025_10_20_015742_create_question_options_table', 1),
	(24, '2025_10_20_015823_create_student_answers_table', 1);

-- A despejar estrutura para tabela db_angolaeducamas.modulos
CREATE TABLE IF NOT EXISTS `modulos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` int DEFAULT NULL,
  `peso` double NOT NULL DEFAULT '0',
  `configuracoes` json DEFAULT NULL,
  `curso_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modulos_curso_id_foreign` (`curso_id`),
  CONSTRAINT `modulos_curso_id_foreign` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.modulos: ~2 rows (aproximadamente)
INSERT INTO `modulos` (`id`, `nome`, `ordem`, `peso`, `configuracoes`, `curso_id`, `created_at`, `updated_at`) VALUES
	(1, 'Introdução ao Laravel', 1, 25.5, NULL, 1, '2025-10-20 01:32:29', '2025-10-20 01:32:29'),
	(2, 'Criando uma aplicação de imobilhario', 1, 65.5, NULL, 1, '2025-10-20 01:33:55', '2025-10-20 01:33:55');

-- A despejar estrutura para tabela db_angolaeducamas.operation_logs
CREATE TABLE IF NOT EXISTS `operation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `object_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `operation_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `operation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.operation_logs: ~0 rows (aproximadamente)
INSERT INTO `operation_logs` (`id`, `object_type`, `object_id`, `operation`, `description`, `user_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', '1', 'create', '✅Super Admin criado com sucesso!', NULL, NULL, '{"id": 1, "name": "superadmin", "email": "admin@aeplus.ao", "status": "online", "password": "$2y$10$Z2XoFTPLmjiLjwV10Qy0q.DiFk/imhtz48dskcBCriVrz4TZG32qK", "user_code": "UC7M8QAU8M", "created_at": "2025-10-20 02:06:37", "updated_at": "2025-10-20 02:06:37", "ultimo_acesso": "2025-10-20T02:06:37.317843Z", "update_password": false}', '127.0.0.1', 'Symfony', '2025-10-20 01:06:37', '2025-10-20 01:06:37');

-- A despejar estrutura para tabela db_angolaeducamas.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.password_reset_tokens: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.permissoes
CREATE TABLE IF NOT EXISTS `permissoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `canView` tinyint(1) NOT NULL DEFAULT '0',
  `canCreate` tinyint(1) NOT NULL DEFAULT '0',
  `canUpdate` tinyint(1) NOT NULL DEFAULT '0',
  `canDelete` tinyint(1) NOT NULL DEFAULT '0',
  `menu_id` bigint unsigned NOT NULL,
  `funcao_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissoes_menu_id_foreign` (`menu_id`),
  KEY `permissoes_funcao_id_foreign` (`funcao_id`),
  CONSTRAINT `permissoes_funcao_id_foreign` FOREIGN KEY (`funcao_id`) REFERENCES `funcaos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permissoes_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.permissoes: ~6 rows (aproximadamente)
INSERT INTO `permissoes` (`id`, `canView`, `canCreate`, `canUpdate`, `canDelete`, `menu_id`, `funcao_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 1, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(2, 1, 1, 1, 1, 2, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(3, 1, 1, 1, 1, 3, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(4, 1, 1, 1, 1, 4, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(5, 1, 1, 1, 1, 5, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(6, 1, 1, 1, 1, 6, 1, '2025-10-20 01:06:37', '2025-10-20 01:06:37');

-- A despejar estrutura para tabela db_angolaeducamas.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.personal_access_tokens: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela db_angolaeducamas.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `justification` text COLLATE utf8mb4_unicode_ci,
  `type` enum('multiple_choice','true_false','open_ended','fill_blank') COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` text COLLATE utf8mb4_unicode_ci COMMENT 'Resposta correta para tipos abertos',
  `requires_justification` tinyint(1) NOT NULL DEFAULT '0',
  `points` int NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `quiz_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_quiz_id_order_index` (`quiz_id`,`order`),
  KEY `questions_type_index` (`type`),
  CONSTRAINT `questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.questions: ~2 rows (aproximadamente)
INSERT INTO `questions` (`id`, `text`, `justification`, `type`, `correct_answer`, `requires_justification`, `points`, `order`, `quiz_id`, `created_at`, `updated_at`) VALUES
	(1, 'Quanto é 2 + 2?', NULL, 'multiple_choice', NULL, 0, 10, 1, 1, '2025-10-20 01:46:43', '2025-10-20 01:46:43'),
	(2, 'Qual é a raiz quadrada de 16?', NULL, 'multiple_choice', NULL, 0, 15, 2, 1, '2025-10-20 01:48:07', '2025-10-20 01:48:07');

-- A despejar estrutura para tabela db_angolaeducamas.question_options
CREATE TABLE IF NOT EXISTS `question_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `question_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_options_question_id_is_correct_index` (`question_id`,`is_correct`),
  CONSTRAINT `question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.question_options: ~4 rows (aproximadamente)
INSERT INTO `question_options` (`id`, `text`, `is_correct`, `question_id`, `created_at`, `updated_at`) VALUES
	(1, '4', 1, 1, '2025-10-20 01:49:36', '2025-10-20 01:49:36'),
	(2, '5', 0, 1, '2025-10-20 01:50:01', '2025-10-20 01:50:01'),
	(3, '4', 1, 2, '2025-10-20 01:50:29', '2025-10-20 01:50:29'),
	(4, '8', 0, 2, '2025-10-20 01:50:49', '2025-10-20 01:50:49');

-- A despejar estrutura para tabela db_angolaeducamas.quizzes
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `time_limit` int DEFAULT NULL COMMENT 'Tempo limite em minutos',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_justification` tinyint(1) NOT NULL DEFAULT '0',
  `atividade_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quizzes_atividade_id_foreign` (`atividade_id`),
  KEY `quizzes_is_active_atividade_id_index` (`is_active`,`atividade_id`),
  CONSTRAINT `quizzes_atividade_id_foreign` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.quizzes: ~0 rows (aproximadamente)
INSERT INTO `quizzes` (`id`, `title`, `description`, `instructions`, `time_limit`, `is_active`, `show_justification`, `atividade_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Quiz de Matemática Básica', 'Teste seus conhecimentos em matemática', 'Responda todas as questões', 30, 1, 1, 1, '2025-10-20 01:45:27', '2025-10-20 01:45:27', NULL);

-- A despejar estrutura para tabela db_angolaeducamas.student_answers
CREATE TABLE IF NOT EXISTS `student_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `answer` text COLLATE utf8mb4_unicode_ci COMMENT 'Resposta do estudante',
  `justification` text COLLATE utf8mb4_unicode_ci COMMENT 'Justificação do estudante',
  `score` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Pontuação obtida',
  `feedback` text COLLATE utf8mb4_unicode_ci COMMENT 'Feedback do professor',
  `concluido` tinyint(1) NOT NULL DEFAULT '0',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `question_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_answers_question_id_student_id_unique` (`question_id`,`student_id`),
  KEY `student_answers_student_id_concluido_index` (`student_id`,`concluido`),
  KEY `student_answers_submitted_at_index` (`submitted_at`),
  CONSTRAINT `student_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.student_answers: ~2 rows (aproximadamente)
INSERT INTO `student_answers` (`id`, `answer`, `justification`, `score`, `feedback`, `concluido`, `submitted_at`, `question_id`, `student_id`, `created_at`, `updated_at`) VALUES
	(1, '4', NULL, 15.00, 'Excelente!', 1, '2025-10-20 01:53:03', 1, 1, '2025-10-20 01:53:03', '2025-10-20 01:57:46'),
	(2, '4', NULL, 0.00, NULL, 0, '2025-10-20 01:53:34', 2, 1, '2025-10-20 01:53:34', '2025-10-20 01:53:34');

-- A despejar estrutura para tabela db_angolaeducamas.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultimo_acesso` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_password` tinyint(1) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.users: ~0 rows (aproximadamente)
INSERT INTO `users` (`id`, `user_code`, `name`, `email`, `status`, `email_verified_at`, `password`, `ultimo_acesso`, `update_password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'UC7M8QAU8M', 'superadmin', 'admin@aeplus.ao', 'online', NULL, '$2y$10$Z2XoFTPLmjiLjwV10Qy0q.DiFk/imhtz48dskcBCriVrz4TZG32qK', '2025-10-31 19:55:05', 0, NULL, '2025-10-20 01:06:37', '2025-10-31 19:55:05'),
	(2, 'USER001', 'Professor João Silva', 'joao.silva@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(3, 'USER002', 'Instrutor Maria Santos', 'maria.santos@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(4, 'USER003', 'Dr. Carlos Oliveira', 'carlos.oliveira@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(5, 'USER004', 'Especialista Ana Costa', 'ana.costa@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(6, 'USER005', 'Prof. Pedro Almeida', 'pedro.almeida@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(7, 'USER006', 'Consultor Marta Rocha', 'marta.rocha@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(8, 'USER007', 'Dr. Ricardo Lima', 'ricardo.lima@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(9, 'USER008', 'Instrutor Sofia Pereira', 'sofia.pereira@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(10, 'USER009', 'Especialista Bruno Torres', 'bruno.torres@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(11, 'USER010', 'Prof. Carla Mendes', 'carla.mendes@exemplo.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(12, 'INST001', 'Universidade Tech', 'contato@untech.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(13, 'INST002', 'Instituto Inovação', 'contato@inovacao.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(14, 'INST003', 'Academia Digital', 'contato@academiadigital.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(15, 'INST004', 'Centro Educacional Plus', 'contato@educplus.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(16, 'INST005', 'Escola Profissional', 'contato@escolapro.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(17, 'INST006', 'Instituto Qualificação', 'contato@qualifica.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(18, 'INST007', 'Universidade Virtual', 'contato@univirtual.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(19, 'INST008', 'Centro Tecnológico', 'contato@centrotec.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(20, 'INST009', 'Academia Profissional', 'contato@academiapro.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(21, 'INST010', 'Instituto Excelência', 'contato@excelencia.com', 'ACTIVO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-31 20:44:21', 0, NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21');

-- A despejar estrutura para tabela db_angolaeducamas.user_details
CREATE TABLE IF NOT EXISTS `user_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_fantasia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urlImg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biografia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passaporte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` enum('M','F','OUTRO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estadocivil` enum('SOLTEIRO','CASADO','DIVORCIADO','VIUVO','UNIAO_DE_FACTO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `tipo` enum('PESSOA','EMPRESA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PESSOA',
  `endereco_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_details_email_unique` (`email`),
  UNIQUE KEY `user_details_nif_unique` (`nif`),
  UNIQUE KEY `user_details_bi_unique` (`bi`),
  UNIQUE KEY `user_details_passaporte_unique` (`passaporte`),
  KEY `user_details_user_id_foreign` (`user_id`),
  KEY `user_details_endereco_id_foreign` (`endereco_id`),
  KEY `user_details_nif_bi_tipo_index` (`nif`,`bi`,`tipo`),
  CONSTRAINT `user_details_endereco_id_foreign` FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.user_details: ~0 rows (aproximadamente)
INSERT INTO `user_details` (`id`, `user_id`, `nome`, `nome_fantasia`, `urlImg`, `contacto`, `email`, `biografia`, `nif`, `bi`, `passaporte`, `genero`, `estadocivil`, `nascimento`, `tipo`, `endereco_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Administrador do Sistema', NULL, NULL, '+244900000000', 'admin@sigumussulo.ao', NULL, '1000000000', '000000000LA000', NULL, 'M', NULL, '1990-01-01', 'PESSOA', NULL, '2025-10-20 01:06:37', '2025-10-20 01:06:37'),
	(2, 1, 'Professor João Silva', NULL, NULL, '+244 123 456 789', 'joao.silva@exemplo.com', NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(3, 2, 'Instrutor Maria Santos', NULL, NULL, '+244 123 456 788', 'maria.santos@exemplo.com', NULL, NULL, NULL, NULL, 'F', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(4, 3, 'Dr. Carlos Oliveira', NULL, NULL, '+244 123 456 787', 'carlos.oliveira@exemplo.com', NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(5, 4, 'Especialista Ana Costa', NULL, NULL, '+244 123 456 786', 'ana.costa@exemplo.com', NULL, NULL, NULL, NULL, 'F', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(6, 5, 'Prof. Pedro Almeida', NULL, NULL, '+244 123 456 785', 'pedro.almeida@exemplo.com', NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(7, 6, 'Consultor Marta Rocha', NULL, NULL, '+244 123 456 784', 'marta.rocha@exemplo.com', NULL, NULL, NULL, NULL, 'F', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(8, 7, 'Dr. Ricardo Lima', NULL, NULL, '+244 123 456 783', 'ricardo.lima@exemplo.com', NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(9, 8, 'Instrutor Sofia Pereira', NULL, NULL, '+244 123 456 782', 'sofia.pereira@exemplo.com', NULL, NULL, NULL, NULL, 'F', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(10, 9, 'Especialista Bruno Torres', NULL, NULL, '+244 123 456 781', 'bruno.torres@exemplo.com', NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(11, 10, 'Prof. Carla Mendes', NULL, NULL, '+244 123 456 780', 'carla.mendes@exemplo.com', NULL, NULL, NULL, NULL, 'F', NULL, NULL, 'PESSOA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(12, 11, 'Universidade Tech', NULL, NULL, '+244 123 456 779', 'contato@untech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(13, 12, 'Instituto Inovação', NULL, NULL, '+244 123 456 778', 'contato@inovacao.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(14, 13, 'Academia Digital', NULL, NULL, '+244 123 456 777', 'contato@academiadigital.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(15, 14, 'Centro Educacional Plus', NULL, NULL, '+244 123 456 776', 'contato@educplus.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(16, 15, 'Escola Profissional', NULL, NULL, '+244 123 456 775', 'contato@escolapro.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(17, 16, 'Instituto Qualificação', NULL, NULL, '+244 123 456 774', 'contato@qualifica.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(18, 17, 'Universidade Virtual', NULL, NULL, '+244 123 456 773', 'contato@univirtual.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(19, 18, 'Centro Tecnológico', NULL, NULL, '+244 123 456 772', 'contato@centrotec.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(20, 19, 'Academia Profissional', NULL, NULL, '+244 123 456 771', 'contato@academiapro.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21'),
	(21, 20, 'Instituto Excelência', NULL, NULL, '+244 123 456 770', 'contato@excelencia.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMPRESA', NULL, '2025-10-31 20:44:21', '2025-10-31 20:44:21');

-- A despejar estrutura para tabela db_angolaeducamas.user_funcao
CREATE TABLE IF NOT EXISTS `user_funcao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `funcao_id` bigint unsigned NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_funcao_user_id_foreign` (`user_id`),
  KEY `user_funcao_funcao_id_foreign` (`funcao_id`),
  CONSTRAINT `user_funcao_funcao_id_foreign` FOREIGN KEY (`funcao_id`) REFERENCES `funcaos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_funcao_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A despejar dados para tabela db_angolaeducamas.user_funcao: ~0 rows (aproximadamente)
INSERT INTO `user_funcao` (`id`, `user_id`, `funcao_id`, `data_inicio`, `data_fim`, `data_cadastro`, `data_atualizacao`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2025-10-20', NULL, '2025-10-20 01:06:37', '2025-10-20 01:06:37', '2025-10-20 01:06:37', '2025-10-20 01:06:37');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
