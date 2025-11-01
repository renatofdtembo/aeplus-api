<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Inscrição - EDUKAMBA</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .comprovativo-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px 40px;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(100px, -100px);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 2;
        }
        
        .titulo h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .titulo p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .numero-comprovativo {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .numero-comprovativo span {
            font-size: 24px;
            font-weight: 700;
        }
        
        .content {
            padding: 40px;
        }
        
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: var(--light);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid var(--border);
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }
        
        .icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 18px;
        }
        
        .section-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
        }
        
        .info-grid {
            display: grid;
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .parents-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .parent-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .parent-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .parent-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
        }
        
        .academic-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .academic-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            text-align: center;
        }
        
        .academic-label {
            font-size: 12px;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .academic-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pendente {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-confirmado {
            background: #d1fae5;
            color: #059669;
        }
        
        .alert-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .alert-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 8px;
        }
        
        .alert-content {
            color: #92400e;
            font-size: 14px;
        }
        
        .bolsista-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            margin-top: 20px;
        }
        
        .footer {
            background: var(--light);
            padding: 25px 40px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        
        .footer p {
            color: var(--secondary);
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .data-emissao {
            font-weight: 600;
            color: var(--primary);
            margin-top: 10px;
        }
        
        /* Responsividade */
        @media (max-width: 1024px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
            
            .academic-info {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .parents-grid {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="comprovativo-card">
            <!-- Cabeçalho -->
            <div class="header">
                <div class="header-content">
                    <div class="titulo">
                        <h1>COMPROVATIVO DE INSCRIÇÃO</h1>
                        <p>Sistema EDUKAMBA - Ano Letivo {{ date('Y') }}</p>
                    </div>
                    <div class="numero-comprovativo">
                        Nº: <span>{{ str_pad(1 + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="content">
                <div class="grid-layout">
                    <!-- Dados do Aluno -->
                    <div class="info-section">
                        <div class="section-header">
                            <div class="icon">👤</div>
                            <h2>DADOS DO ALUNO</h2>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nome Completo</span>
                                <span class="info-value">{{ $item['nome'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Data de Nascimento</span>
                                <span class="info-value">{{ \Carbon\Carbon::parse($item['nascimento'])->format('d/m/Y') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contacto</span>
                                <span class="info-value">{{ $item['contacto'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $item['email'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Endereço</span>
                                <span class="info-value">{{ $item['rua'] }}, {{ $item['bairro'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dados do Encarregado -->
                    <div class="info-section">
                        <div class="section-header">
                            <div class="icon">📋</div>
                            <h2>ENCARREGADO DE EDUCAÇÃO</h2>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nome Completo</span>
                                <span class="info-value">{{ $item['encarregado_nome'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">BI/Identificação</span>
                                <span class="info-value">{{ $item['encarregado_bi'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Parentesco</span>
                                <span class="info-value">{{ $item['encarregado_grauparentesco'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contacto</span>
                                <span class="info-value">{{ $item['encarregado_telefone'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $item['encarregado_email'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dados dos Pais -->
                <div class="info-section">
                    <div class="section-header">
                        <div class="icon">👨‍👩‍👧‍👦</div>
                        <h2>DADOS DOS PAIS</h2>
                    </div>
                    <div class="parents-grid">
                        <div class="parent-card">
                            <div class="parent-header">
                                <div class="parent-icon">👨</div>
                                <h3>Pai</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Nome</span>
                                    <span class="info-value">{{ $item['pai'] ?: 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Contacto</span>
                                    <span class="info-value">{{ $item['tel_pai'] ?: 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Profissão</span>
                                    <span class="info-value">{{ $item['profissao_pai'] ?: 'Não informado' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="parent-card">
                            <div class="parent-header">
                                <div class="parent-icon">👩</div>
                                <h3>Mãe</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Nome</span>
                                    <span class="info-value">{{ $item['mae'] ?: 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Contacto</span>
                                    <span class="info-value">{{ $item['tel_mae'] ?: 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Profissão</span>
                                    <span class="info-value">{{ $item['profissao_mae'] ?: 'Não informado' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações Acadêmicas -->
                <div class="info-section">
                    <div class="section-header">
                        <div class="icon">🎓</div>
                        <h2>INFORMAÇÕES ACADÉMICAS</h2>
                    </div>
                    
                    <div class="academic-info">
                        <div class="academic-card">
                            <div class="academic-label">Tipo de Inscrição</div>
                            <div class="academic-value">{{ $item['tipo'] }}</div>
                        </div>
                        <div class="academic-card">
                            <div class="academic-label">Turma</div>
                            <div class="academic-value">Turma {{ $item['turma_id'] }}</div>
                        </div>
                        <div class="academic-card">
                            <div class="academic-label">Status</div>
                            <div class="status-badge {{ $item['status'] == 'pendente' ? 'status-pendente' : 'status-confirmado' }}">
                                {{ $item['status'] }}
                            </div>
                        </div>
                    </div>

                    @if($item['obs_saude'])
                    <div class="alert-box">
                        <div class="alert-title">Observações de Saúde</div>
                        <div class="alert-content">{{ $item['obs_saude'] }}</div>
                    </div>
                    @endif
                    
                    @if($item['bolsista'])
                    <div class="bolsista-badge">
                        ✅ ALUNO BOLSISTA
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rodapé -->
            <div class="footer">
                <p>Este comprovativo deve ser apresentado no ato da matrícula definitiva</p>
                <p>Para mais informações, contacte a secretaria da escola</p>
                <p class="data-emissao">Documento gerado em {{ $dataEmissao }}</p>
            </div>
        </div>
    </div>
</body>
</html>