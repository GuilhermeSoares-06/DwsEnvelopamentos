<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#050505">
<title>Cadastro - DWS Envelopamento</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="../../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body {
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #050505;
    background-image:
        radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.10) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(242, 53, 53, 0.06) 0%, transparent 50%);
    background-attachment: fixed;
    margin: 0;
    padding: 20px;
    font-family: 'Space Grotesk', sans-serif;
    overflow-x: hidden;
}

.bg-grid {
    position: fixed;
    inset: 0;
    z-index: -1;
    background-image:
        linear-gradient(rgba(242, 53, 53, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(242, 53, 53, 0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, #000 0%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at center, #000 0%, transparent 70%);
    animation: gridMove 25s linear infinite;
}
@keyframes gridMove {
    0% { background-position: 0 0; }
    100% { background-position: 60px 60px; }
}

.cadastro-box {
    width: 580px;
    max-width: 100%;
    background: linear-gradient(145deg, rgba(20, 20, 20, 0.95), rgba(10, 10, 10, 0.98));
    backdrop-filter: blur(20px);
    border-radius: 30px;
    padding: 45px 40px;
    position: relative;
    overflow: hidden;
    animation: fadeIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.cadastro-box::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 30px;
    padding: 2px;
    background: linear-gradient(var(--angle, 0deg), transparent, #f23535, #ff6b6b, #f23535, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: ledRotate 4s linear infinite;
    pointer-events: none;
}
@keyframes ledRotate { 0% { --angle: 0deg; } 100% { --angle: 360deg; } }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(40px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.btn-voltar {
    position: absolute;
    top: 25px;
    left: 25px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #aaa;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    padding: 8px 14px;
    border-radius: 10px;
    font-family: inherit;
    z-index: 2;
}
.btn-voltar:hover {
    color: #f23535;
    border-color: rgba(242, 53, 53, 0.4);
    background: rgba(242, 53, 53, 0.08);
    transform: translateX(-3px);
}

.cadastro-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    border-radius: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 15px auto 22px;
    font-size: 2.2rem;
    color: #fff;
    box-shadow: 0 20px 50px rgba(242,53,53,0.4);
    transform: rotate(-5deg);
    animation: floatIcon 3s ease-in-out infinite;
}
@keyframes floatIcon {
    0%, 100% { transform: rotate(-5deg) translateY(0); }
    50% { transform: rotate(-5deg) translateY(-8px); }
}

.cadastro-header { text-align: center; margin-bottom: 25px; }
.cadastro-header h2 {
    color: #fff;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: -0.5px;
    margin-bottom: 8px;
}
.cadastro-header p { color: #888; font-size: 0.9rem; }

.form-section { text-align: left; margin-bottom: 20px; }
.form-section-title {
    color: #f23535;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(242, 53, 53, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-row.cep { grid-template-columns: 140px 1fr; }

.input-box {
    position: relative;
    margin-bottom: 4px;
    width: 100%;
}
.input-box .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 0.9rem;
    transition: 0.3s;
    z-index: 1;
    pointer-events: none;
}
.input-box input {
    width: 100%;
    padding: 14px 44px 14px 44px;
    border: 2px solid rgba(255,255,255,0.08);
    outline: none;
    font-size: 0.9rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.02);
    color: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-family: inherit;
}
.input-box input::placeholder { color: #555; font-weight: 400; }
.input-box input:focus {
    border-color: #f23535;
    background: rgba(242,53,53,0.03);
    box-shadow: 0 0 0 4px rgba(242, 53, 53, 0.1);
}
.input-box input:focus ~ .input-icon { color: #f23535; }

/* ============================================
   ESTADOS VISUAIS
   ============================================ */
.input-box input.estado-ok {
    border-color: rgba(76, 175, 80, 0.7);
    background: rgba(76, 175, 80, 0.04);
}
.input-box input.estado-erro {
    border-color: rgba(244, 67, 54, 0.7);
    background: rgba(244, 67, 54, 0.05);
}
.input-box input.estado-warn {
    border-color: rgba(255, 152, 0, 0.7);
    background: rgba(255, 152, 0, 0.04);
}

.input-box .input-status {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.input-box .input-status.show { opacity: 1; }
.input-box .input-status.ok { color: #4CAF50; }
.input-box .input-status.erro { color: #f44336; }
.input-box .input-status.warn { color: #FF9800; }

/* ============================================
   MENSAGEM POR CAMPO
   ============================================ */
.campo-msg {
    font-size: 0.75rem;
    padding-left: 6px;
    min-height: 18px;
    margin-bottom: 10px;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.35;
    font-weight: 500;
}
.campo-msg.ok { color: #4CAF50; }
.campo-msg.erro { color: #f44336; }
.campo-msg.warn { color: #FF9800; }
.campo-msg.info { color: #888; }
.campo-msg:empty { display: none; }

/* ============================================
   BARRA DE FORÇA
   ============================================ */
.senha-forca {
    display: flex;
    gap: 4px;
    margin-top: 6px;
    margin-bottom: 4px;
}
.senha-forca-bar {
    flex: 1;
    height: 4px;
    background: rgba(255,255,255,0.08);
    border-radius: 4px;
    transition: 0.3s;
}
.senha-forca-bar.fraca { background: #f44336; }
.senha-forca-bar.media { background: #FF9800; }
.senha-forca-bar.boa   { background: #2196F3; }
.senha-forca-bar.forte { background: #4CAF50; }

.senha-forca-texto {
    font-size: 0.72rem;
    color: #888;
    text-align: left;
    margin-bottom: 8px;
    padding-left: 4px;
    min-height: 16px;
}
.senha-forca-texto.fraca { color: #f44336; }
.senha-forca-texto.media { color: #FF9800; }
.senha-forca-texto.boa   { color: #2196F3; }
.senha-forca-texto.forte { color: #4CAF50; }

/* ============================================
   REQUISITOS DA SENHA
   ============================================ */
.senha-requisitos {
    text-align: left;
    font-size: 0.78rem;
    color: #aaa;
    padding: 14px 18px;
    background: rgba(242, 53, 53, 0.05);
    border: 1px solid rgba(242, 53, 53, 0.2);
    border-radius: 12px;
    margin-bottom: 14px;
}
.senha-requisitos-titulo {
    color: #f23535;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.senha-requisitos-lista {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 14px;
}
.senha-requisitos .req {
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    font-size: 0.78rem;
}
.senha-requisitos .req i {
    font-size: 0.7rem;
    color: #555;
    transition: 0.3s;
    width: 14px;
    text-align: center;
}
.senha-requisitos .req.ok { color: #4CAF50; }
.senha-requisitos .req.ok i { color: #4CAF50; }

/* ============================================
   BOTÃO
   ============================================ */
.btn-cadastro {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: white;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(242,53,53,0.3);
}
.btn-cadastro::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
    transition: left 0.6s ease;
}
.btn-cadastro:hover:not(:disabled)::before { left: 100%; }
.btn-cadastro:hover:not(:disabled) {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 25px 55px rgba(242,53,53,0.5);
}
.btn-cadastro:disabled {
    opacity: 0.35;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
    background: linear-gradient(135deg, #444, #333);
}

/* ============================================
   PAINEL DE PENDÊNCIAS
   ============================================ */
.pendencias-box {
    margin-top: 18px;
    padding: 16px 20px;
    background: rgba(255, 152, 0, 0.06);
    border: 1px solid rgba(255, 152, 0, 0.3);
    border-radius: 14px;
    display: none;
}
.pendencias-box.show { display: block; }
.pendencias-box.tudo-ok {
    background: rgba(76, 175, 80, 0.06);
    border-color: rgba(76, 175, 80, 0.35);
}
.pendencias-titulo {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #FF9800;
}
.pendencias-box.tudo-ok .pendencias-titulo { color: #4CAF50; }
.pendencias-lista {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 0.82rem;
    color: #ccc;
}
.pendencias-lista li {
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.3;
}
.pendencias-lista li i {
    color: #FF9800;
    font-size: 0.75rem;
    width: 16px;
    text-align: center;
    flex-shrink: 0;
}

.cadastro-footer {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.05);
    color: #777;
    font-size: 0.85rem;
    text-align: center;
}
.cadastro-footer a {
    color: #f23535;
    text-decoration: none;
    font-weight: 600;
    margin-left: 5px;
}
.cadastro-footer a:hover { color: #ff6b6b; text-decoration: underline; }

@media (max-width: 500px) {
    html, body { padding: 12px; }
    .cadastro-box { padding: 35px 22px; border-radius: 24px; }
    .cadastro-icon { width: 65px; height: 65px; font-size: 1.8rem; margin: 10px auto 18px; }
    .cadastro-header h2 { font-size: 1.2rem; }
    .form-row { grid-template-columns: 1fr; }
    .form-row.cep { grid-template-columns: 110px 1fr; }
    .input-box input { padding: 13px 40px 13px 42px; font-size: 0.88rem; }
    .btn-cadastro { font-size: 0.8rem; padding: 14px; }
    .btn-voltar { top: 15px; left: 15px; padding: 6px 10px; font-size: 0.75rem; }
    .senha-requisitos-lista { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
    <div class="bg-grid"></div>

    <div class="cadastro-box">
        <button class="btn-voltar" onclick="sair()" type="button">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>

        <div class="cadastro-icon">
            <i class="fas fa-user-plus"></i>
        </div>

        <div class="cadastro-header">
            <h2>Criar Conta</h2>
            <p>Preencha seus dados para começar</p>
        </div>

        <form action="../../PHP/Clientes/CadastroCliente.php" method="POST" autocomplete="off" id="formCadastro" novalidate>

            <!-- ===== DADOS PESSOAIS ===== -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user"></i> Dados Pessoais
                </div>

                <!-- NOME -->
                <div class="input-box">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="nome" id="nome" placeholder="NOME COMPLETO" required maxlength="100">
                    <i class="fas fa-circle input-status" id="status-nome"></i>
                </div>
                <div class="campo-msg info" id="msg-nome">Digite seu nome completo</div>

                <!-- EMAIL -->
                <div class="input-box">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" id="email" placeholder="E-MAIL" required maxlength="150">
                    <i class="fas fa-circle input-status" id="status-email"></i>
                </div>
                <div class="campo-msg info" id="msg-email">Exemplo: seu.email@exemplo.com</div>

                <!-- CPF + TELEFONE -->
                <div class="form-row">
                    <div>
                        <div class="input-box">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" name="cpf" id="cpf" placeholder="CPF" required maxlength="14" inputmode="numeric">
                            <i class="fas fa-circle input-status" id="status-cpf"></i>
                        </div>
                        <div class="campo-msg info" id="msg-cpf">Ex: 123.456.789-09</div>
                    </div>
                    <div>
                        <div class="input-box">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" name="telefone" id="telefone" placeholder="TELEFONE" required maxlength="15" inputmode="numeric">
                            <i class="fas fa-circle input-status" id="status-telefone"></i>
                        </div>
                        <div class="campo-msg info" id="msg-telefone">Ex: (14) 99999-9999</div>
                    </div>
                </div>
            </div>

            <!-- ===== ENDEREÇO ===== -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Endereço
                </div>

                <div class="form-row cep">
                    <div>
                        <div class="input-box">
                            <i class="fas fa-mail-bulk input-icon"></i>
                            <input type="text" name="cep" id="cep" placeholder="CEP" maxlength="9" inputmode="numeric" required>
                            <i class="fas fa-circle input-status" id="status-cep"></i>
                        </div>
                        <div class="campo-msg info" id="msg-cep">Ex: 18800-000</div>
                    </div>
                    <div>
                        <div class="input-box">
                            <i class="fas fa-map input-icon"></i>
                            <input type="text" name="rua" id="rua" placeholder="RUA / AVENIDA" required maxlength="150">
                            <i class="fas fa-circle input-status" id="status-rua"></i>
                        </div>
                        <div class="campo-msg" id="msg-rua"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <div class="input-box">
                            <i class="fas fa-hashtag input-icon"></i>
                            <input type="text" name="numero" id="numero" placeholder="NÚMERO" required maxlength="10">
                            <i class="fas fa-circle input-status" id="status-numero"></i>
                        </div>
                        <div class="campo-msg" id="msg-numero"></div>
                    </div>
                    <div>
                        <div class="input-box">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" name="complemento" id="complemento" placeholder="COMPLEMENTO (opcional)" maxlength="50">
                        </div>
                        <div class="campo-msg info" id="msg-complemento">Opcional</div>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <div class="input-box">
                            <i class="fas fa-city input-icon"></i>
                            <input type="text" name="bairro" id="bairro" placeholder="BAIRRO" required maxlength="100">
                            <i class="fas fa-circle input-status" id="status-bairro"></i>
                        </div>
                        <div class="campo-msg" id="msg-bairro"></div>
                    </div>
                    <div>
                        <div class="input-box">
                            <i class="fas fa-globe input-icon"></i>
                            <input type="text" name="cidade" id="cidade" placeholder="CIDADE" required maxlength="100">
                            <i class="fas fa-circle input-status" id="status-cidade"></i>
                        </div>
                        <div class="campo-msg" id="msg-cidade"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <div class="input-box">
                            <i class="fas fa-flag input-icon"></i>
                            <input type="text" name="estado" id="estado" placeholder="ESTADO (UF)" required maxlength="2" style="text-transform: uppercase;">
                            <i class="fas fa-circle input-status" id="status-estado"></i>
                        </div>
                        <div class="campo-msg info" id="msg-estado">Sigla com 2 letras — Ex: SP</div>
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- ===== SEGURANÇA ===== -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-lock"></i> Segurança
                </div>

                <div class="senha-requisitos">
                    <div class="senha-requisitos-titulo">
                        <i class="fas fa-shield-alt"></i> Sua senha precisa ter:
                    </div>
                    <div class="senha-requisitos-lista">
                        <div class="req" id="req-maiuscula"><i class="fas fa-circle"></i> 1 letra MAIÚSCULA</div>
                        <div class="req" id="req-minuscula"><i class="fas fa-circle"></i> 1 letra minúscula</div>
                        <div class="req" id="req-numero"><i class="fas fa-circle"></i> 1 número</div>
                        <div class="req" id="req-especial"><i class="fas fa-circle"></i> 1 caractere especial (!@#$…)</div>
                        <div class="req" id="req-tamanho"><i class="fas fa-circle"></i> Mínimo 8 caracteres</div>
                    </div>
                </div>

                <div class="input-box">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="Senha" id="senha" placeholder="SENHA" autocomplete="new-password" required>
                    <i class="fas fa-circle input-status" id="status-senha"></i>
                </div>
                <div class="campo-msg" id="msg-senha"></div>

                <div class="senha-forca">
                    <div class="senha-forca-bar" id="bar1"></div>
                    <div class="senha-forca-bar" id="bar2"></div>
                    <div class="senha-forca-bar" id="bar3"></div>
                    <div class="senha-forca-bar" id="bar4"></div>
                </div>
                <div class="senha-forca-texto" id="senhaForcaTexto"></div>

                <div class="input-box">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="Senha2" id="senha2" placeholder="CONFIRMAR SENHA" autocomplete="new-password" required>
                    <i class="fas fa-circle input-status" id="status-senha2"></i>
                </div>
                <div class="campo-msg" id="msg-senha2"></div>
            </div>

            <div class="pendencias-box" id="pendenciasBox">
                <div class="pendencias-titulo" id="pendenciasTitulo">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="pendenciasTexto">Falta preencher:</span>
                </div>
                <ul class="pendencias-lista" id="pendenciasLista"></ul>
            </div>

            <button type="submit" class="btn-cadastro" id="btnCadastrar" disabled>
                <i class="fas fa-user-plus"></i> CADASTRAR
            </button>
        </form>

        <div class="cadastro-footer">
            Já tem uma conta?<a href="loginClientes.html">Entrar</a>
        </div>
    </div>

    <script>
        function sair() {
            if (history.length > 1) history.back();
            else window.location.href = 'principal.html';
        }

        /* ============================================
           VALIDADORES DETALHADOS
           ============================================ */

        // CPF — dígitos verificadores
        function validarCPFCompleto(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11) return false;
            if (/^(\d)\1{10}$/.test(cpf)) return false;

            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i - 1]) * (11 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf[9])) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i - 1]) * (12 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf[10])) return false;

            return true;
        }

        /* ============================================
           ANALISADORES DETALHADOS POR CAMPO
           Retornam { estado, msg, icone }
           ============================================ */

        // ---------- NOME ----------
        function analisarNome(v) {
            v = v.trim();
            if (!v) return { estado: '', msg: 'Digite seu nome completo', icone: 'fa-circle' };
            if (v.length < 3) return { estado: 'warn', msg: `Faltam ${3 - v.length} letra(s)`, icone: 'fa-exclamation-circle' };
            if (/\d/.test(v)) return { estado: 'erro', msg: 'Nome não pode ter números', icone: 'fa-times-circle' };
            if (!/^[A-Za-zÀ-ÿ\s]+$/.test(v)) return { estado: 'erro', msg: 'Use apenas letras e espaços', icone: 'fa-times-circle' };
            if (!/\s/.test(v)) return { estado: 'warn', msg: 'Digite nome e sobrenome (ex: João Silva)', icone: 'fa-exclamation-circle' };
            return { estado: 'ok', msg: 'Nome válido', icone: 'fa-check-circle' };
        }

        // ---------- EMAIL — análise passo a passo ----------
        function analisarEmail(v) {
            v = v.trim();
            if (!v) return { estado: '', msg: 'Exemplo: seu.email@exemplo.com', icone: 'fa-circle' };

            // Sem @
            if (!v.includes('@')) {
                return { estado: 'erro', msg: '❌ Falta o @ (arroba) — ex: nome@gmail.com', icone: 'fa-times-circle' };
            }

            const partes = v.split('@');

            // Mais de um @
            if (partes.length > 2) {
                return { estado: 'erro', msg: '❌ Só pode ter 1 @ (você digitou vários)', icone: 'fa-times-circle' };
            }

            const [local, dominio] = partes;

            // Falta parte antes do @
            if (!local) {
                return { estado: 'erro', msg: '❌ Falta o nome antes do @ — ex: joao@gmail.com', icone: 'fa-times-circle' };
            }

            // Falta parte depois do @
            if (!dominio) {
                return { estado: 'erro', msg: '❌ Falta o domínio depois do @ — ex: joao@gmail.com', icone: 'fa-times-circle' };
            }

            // Domínio sem ponto
            if (!dominio.includes('.')) {
                return { estado: 'erro', msg: `❌ Falta o .com depois do "${dominio}" — ex: ${local}@${dominio}.com`, icone: 'fa-times-circle' };
            }

            const dParts = dominio.split('.');

            // Termina com ponto
            if (dominio.endsWith('.')) {
                return { estado: 'erro', msg: '❌ Domínio incompleto — falta a extensão (.com, .br…)', icone: 'fa-times-circle' };
            }

            // Termina com ponto depois do ponto (ex: joao@gmail..com)
            if (dParts.some(p => p === '')) {
                return { estado: 'erro', msg: '❌ Domínio com pontos duplos (..)', icone: 'fa-times-circle' };
            }

            // Extensão muito curta
            const extensao = dParts[dParts.length - 1];
            if (extensao.length < 2) {
                return { estado: 'erro', msg: `❌ Extensão ".${extensao}" é curta demais`, icone: 'fa-times-circle' };
            }

            // Espaços
            if (/\s/.test(v)) {
                return { estado: 'erro', msg: '❌ E-mail não pode ter espaços', icone: 'fa-times-circle' };
            }

            // Caracteres inválidos no local
            if (!/^[A-Za-z0-9._-]+$/.test(local)) {
                return { estado: 'erro', msg: '❌ O nome antes do @ só pode ter letras, números, . _ -', icone: 'fa-times-circle' };
            }

            // Regex final
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) {
                return { estado: 'erro', msg: '❌ Formato de e-mail inválido', icone: 'fa-times-circle' };
            }

            return { estado: 'ok', msg: 'E-mail válido', icone: 'fa-check-circle' };
        }

        // ---------- CPF — análise passo a passo ----------
        function analisarCPF(v) {
            const num = v.replace(/\D/g, '');
            if (!num) return { estado: '', msg: 'Ex: 123.456.789-09', icone: 'fa-circle' };
            if (num.length < 11) {
                return { estado: 'warn', msg: `⚠️ Faltam ${11 - num.length} dígito(s) — CPF tem 11`, icone: 'fa-exclamation-circle' };
            }
            if (/^(\d)\1{10}$/.test(num)) {
                return { estado: 'erro', msg: '❌ CPF repetido (111.111.111-11) não existe', icone: 'fa-times-circle' };
            }
            if (!validarCPFCompleto(num)) {
                return { estado: 'erro', msg: '❌ CPF inválido — verifique os números', icone: 'fa-times-circle' };
            }
            return { estado: 'ok', msg: 'CPF válido', icone: 'fa-check-circle' };
        }

        // ---------- TELEFONE — análise passo a passo ----------
        function analisarTelefone(v) {
            const num = v.replace(/\D/g, '');
            if (!num) return { estado: '', msg: 'Ex: (14) 99999-9999', icone: 'fa-circle' };

            if (num.length < 2) {
                return { estado: 'warn', msg: '⚠️ Digite o DDD primeiro — ex: 14', icone: 'fa-exclamation-circle' };
            }
            if (num.length < 10) {
                return { estado: 'warn', msg: `⚠️ Faltam ${10 - num.length} dígito(s)`, icone: 'fa-exclamation-circle' };
            }

            const ddd = num.substring(0, 2);
            if (parseInt(ddd) < 11 || parseInt(ddd) > 99) {
                return { estado: 'erro', msg: '❌ DDD inválido (deve ser entre 11 e 99)', icone: 'fa-times-circle' };
            }

            if (num.length === 11) {
                // Celular: 9 na 3ª posição
                if (num[2] !== '9') {
                    return { estado: 'erro', msg: '❌ Celular deve ter 9 após o DDD', icone: 'fa-times-circle' };
                }
                return { estado: 'ok', msg: 'Telefone válido', icone: 'fa-check-circle' };
            }

            if (num.length === 10) {
                // Fixo: começa com 2,3,4,5
                if (!/^[2-5]/.test(num[2])) {
                    return { estado: 'warn', msg: '⚠️ Fixo começa com 2, 3, 4 ou 5', icone: 'fa-exclamation-circle' };
                }
                return { estado: 'ok', msg: 'Telefone válido', icone: 'fa-check-circle' };
            }

            return { estado: 'erro', msg: '❌ Telefone longo demais', icone: 'fa-times-circle' };
        }

        // ---------- CEP ----------
        function analisarCEP(v) {
            const num = v.replace(/\D/g, '');
            if (!num) return { estado: '', msg: 'Ex: 18800-000', icone: 'fa-circle' };
            if (num.length < 8) {
                return { estado: 'warn', msg: `⚠️ Faltam ${8 - num.length} dígito(s) — CEP tem 8`, icone: 'fa-exclamation-circle' };
            }
            return { estado: 'ok', msg: 'CEP preenchido', icone: 'fa-check-circle' };
        }

        // ---------- ESTADO (UF) ----------
        const UFS = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
        function analisarEstado(v) {
            v = v.toUpperCase().trim();
            if (!v) return { estado: '', msg: 'Sigla com 2 letras — Ex: SP', icone: 'fa-circle' };
            if (v.length < 2) return { estado: 'warn', msg: '⚠️ Faltam letras (precisa 2)', icone: 'fa-exclamation-circle' };
            if (!/^[A-Z]{2}$/.test(v)) return { estado: 'erro', msg: '❌ Use apenas 2 letras', icone: 'fa-times-circle' };
            if (!UFS.includes(v)) return { estado: 'erro', msg: `❌ "${v}" não é uma UF válida`, icone: 'fa-times-circle' };
            return { estado: 'ok', msg: 'UF válida', icone: 'fa-check-circle' };
        }

        // ---------- SENHA — diz exatamente o que falta ----------
        function analisarSenha(v) {
            if (!v) return { estado: '', msg: '', icone: 'fa-circle', pontos: 0 };

            const tem = {
                mai: /[A-Z]/.test(v),
                min: /[a-z]/.test(v),
                num: /[0-9]/.test(v),
                esp: /[^A-Za-z0-9]/.test(v),
                tam: v.length >= 8
            };

            const faltando = [];
            if (!tem.mai) faltando.push('letra maiúscula');
            if (!tem.min) faltando.push('letra minúscula');
            if (!tem.num) faltando.push('número');
            if (!tem.esp) faltando.push('caractere especial (@ # $ …)');
            if (!tem.tam) faltando.push('8+ caracteres');

            const pontos = Object.values(tem).filter(Boolean).length;

            if (faltando.length === 0) {
                return { estado: 'ok', msg: 'Senha forte!', icone: 'fa-check-circle', pontos: 5 };
            }

            return {
                estado: 'warn',
                msg: '⚠️ Falta: ' + faltando.slice(0, 3).join(', ') + (faltando.length > 3 ? '…' : ''),
                icone: 'fa-exclamation-circle',
                pontos
            };
        }

        // ---------- CONFIRMAR SENHA ----------
        function analisarSenha2(v, senhaOriginal) {
            if (!v) return { estado: '', msg: '', icone: 'fa-circle' };
            if (senhaOriginal !== v) {
                return { estado: 'erro', msg: '❌ As senhas não são iguais', icone: 'fa-times-circle' };
            }
            return { estado: 'ok', msg: 'Senhas conferem ✓', icone: 'fa-check-circle' };
        }

        // ---------- CAMPOS GENÉRICOS (rua, numero, bairro, cidade) ----------
        function analisarObrigatorio(v, nomeCampo) {
            v = (v || '').trim();
            if (!v) return { estado: '', msg: '', icone: 'fa-circle' };
            if (v.length < 2) return { estado: 'warn', msg: '⚠️ Muito curto', icone: 'fa-exclamation-circle' };
            return { estado: 'ok', msg: '', icone: 'fa-check-circle' };
        }

        /* ============================================
           APLICAR RESULTADO NO CAMPO
           ============================================ */
        function aplicar(campoId, statusId, msgId, resultado) {
            const input = document.getElementById(campoId);
            const status = statusId ? document.getElementById(statusId) : null;
            const msg = msgId ? document.getElementById(msgId) : null;

            input.classList.remove('estado-ok', 'estado-erro', 'estado-warn');
            if (resultado.estado) input.classList.add('estado-' + resultado.estado);

            if (status) {
                if (resultado.estado) {
                    status.className = `fas ${resultado.icone} input-status show ${resultado.estado}`;
                } else {
                    status.className = 'fas fa-circle input-status';
                }
            }

            if (msg) {
                msg.textContent = resultado.msg || '';
                msg.className = 'campo-msg ' + (resultado.estado || 'info');
            }
        }

        /* ============================================
           MÁSCARAS
           ============================================ */
        document.getElementById('cpf').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v;
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
            else if (v.length > 0) v = v.replace(/^(\d{0,2}).*/, '($1');
            e.target.value = v;
        });

        document.getElementById('cep').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 5) v = v.replace(/(\d{5})(\d{0,3})/, '$1-$2');
            e.target.value = v;
            if (v.replace(/\D/g, '').length === 8) buscarCep(v.replace(/\D/g, ''));
        });

        document.getElementById('cep').addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) buscarCep(cep);
        });

        document.getElementById('estado').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 2);
        });

        /* ============================================
           BUSCA CEP
           ============================================ */
        async function buscarCep(cep) {
            const msg = document.getElementById('msg-cep');
            const status = document.getElementById('status-cep');

            msg.textContent = '⏳ Buscando endereço...';
            msg.className = 'campo-msg warn';
            status.className = 'fas fa-spinner fa-spin input-status show warn';

            try {
                const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();

                if (data.erro) {
                    msg.textContent = '❌ CEP não encontrado';
                    msg.className = 'campo-msg erro';
                    status.className = 'fas fa-times-circle input-status show erro';
                    return;
                }

                if (data.logradouro) document.getElementById('rua').value = data.logradouro;
                if (data.bairro)     document.getElementById('bairro').value = data.bairro;
                if (data.localidade) document.getElementById('cidade').value = data.localidade;
                if (data.uf)         document.getElementById('estado').value = data.uf.toUpperCase();

                msg.textContent = '✅ Endereço preenchido automaticamente';
                msg.className = 'campo-msg ok';
                status.className = 'fas fa-check-circle input-status show ok';

                document.getElementById('numero').focus();
                atualizarTudo();
            } catch (e) {
                msg.textContent = '❌ Erro ao buscar CEP';
                msg.className = 'campo-msg erro';
                status.className = 'fas fa-times-circle input-status show erro';
            }
        }

        /* ============================================
           ATUALIZAR REQUISITOS DA SENHA
           ============================================ */
        function atualizarRequisito(id, ok) {
            const el = document.getElementById(id);
            el.classList.toggle('ok', ok);
            el.querySelector('i').className = ok ? 'fas fa-check-circle' : 'fas fa-circle';
        }

        /* ============================================
           ATUALIZA TUDO AO VIVO
           ============================================ */
        function atualizarTudo() {
            const pendencias = [];

            // NOME
            const rNome = analisarNome(document.getElementById('nome').value);
            aplicar('nome', 'status-nome', 'msg-nome', rNome);
            if (rNome.estado !== 'ok') pendencias.push(rNome.msg || 'Preencha o nome');

            // EMAIL
            const rEmail = analisarEmail(document.getElementById('email').value);
            aplicar('email', 'status-email', 'msg-email', rEmail);
            if (rEmail.estado !== 'ok') pendencias.push(rEmail.msg || 'Preencha o e-mail');

            // CPF
            const rCpf = analisarCPF(document.getElementById('cpf').value);
            aplicar('cpf', 'status-cpf', 'msg-cpf', rCpf);
            if (rCpf.estado !== 'ok') pendencias.push(rCpf.msg || 'Preencha o CPF');

            // TELEFONE
            const rTel = analisarTelefone(document.getElementById('telefone').value);
            aplicar('telefone', 'status-telefone', 'msg-telefone', rTel);
            if (rTel.estado !== 'ok') pendencias.push(rTel.msg || 'Preencha o telefone');

            // CEP
            const rCep = analisarCEP(document.getElementById('cep').value);
            const statusCepEl = document.getElementById('status-cep');
            // Se já está com status ok do viacep, mantém
            if (!statusCepEl.classList.contains('ok')) {
                aplicar('cep', 'status-cep', 'msg-cep', rCep);
            }
            if (!statusCepEl.classList.contains('ok') && rCep.estado !== 'ok') {
                pendencias.push(rCep.msg || 'Preencha o CEP');
            }

            // RUA
            const rRua = analisarObrigatorio(document.getElementById('rua').value);
            aplicar('rua', 'status-rua', 'msg-rua', rRua);
            if (rRua.estado !== 'ok') pendencias.push('Preencha a rua');

            // NÚMERO
            const rNum = analisarObrigatorio(document.getElementById('numero').value);
            aplicar('numero', 'status-numero', 'msg-numero', rNum);
            if (rNum.estado !== 'ok') pendencias.push('Preencha o número');

            // BAIRRO
            const rBai = analisarObrigatorio(document.getElementById('bairro').value);
            aplicar('bairro', 'status-bairro', 'msg-bairro', rBai);
            if (rBai.estado !== 'ok') pendencias.push('Preencha o bairro');

            // CIDADE
            const rCid = analisarObrigatorio(document.getElementById('cidade').value);
            aplicar('cidade', 'status-cidade', 'msg-cidade', rCid);
            if (rCid.estado !== 'ok') pendencias.push('Preencha a cidade');

            // ESTADO
            const rEst = analisarEstado(document.getElementById('estado').value);
            aplicar('estado', 'status-estado', 'msg-estado', rEst);
            if (rEst.estado !== 'ok') pendencias.push(rEst.msg || 'Preencha a UF');

            // SENHA
            const senhaVal = document.getElementById('senha').value;
            const rSenha = analisarSenha(senhaVal);
            aplicar('senha', 'status-senha', 'msg-senha', rSenha);

            // Atualiza requisitos individuais
            atualizarRequisito('req-maiuscula', /[A-Z]/.test(senhaVal));
            atualizarRequisito('req-minuscula', /[a-z]/.test(senhaVal));
            atualizarRequisito('req-numero', /[0-9]/.test(senhaVal));
            atualizarRequisito('req-especial', /[^A-Za-z0-9]/.test(senhaVal));
            atualizarRequisito('req-tamanho', senhaVal.length >= 8);

            // Barra de força
            const barras = [1,2,3,4].map(i => document.getElementById('bar' + i));
            barras.forEach(b => b.className = 'senha-forca-bar');

            const pontos = rSenha.pontos || 0;
            const forcaTxt = document.getElementById('senhaForcaTexto');
            let nivel = '', label = '';

            if (senhaVal.length === 0) {
                label = '';
            } else if (pontos <= 2) {
                nivel = 'fraca'; label = '🔴 Fraca';
                barras[0].classList.add('fraca');
            } else if (pontos === 3) {
                nivel = 'media'; label = '🟠 Média';
                barras[0].classList.add('media'); barras[1].classList.add('media');
            } else if (pontos === 4) {
                nivel = 'boa'; label = '🔵 Boa';
                barras[0].classList.add('boa'); barras[1].classList.add('boa'); barras[2].classList.add('boa');
            } else {
                nivel = 'forte'; label = '🟢 Forte';
                barras.forEach(b => b.classList.add('forte'));
            }
            forcaTxt.textContent = label;
            forcaTxt.className = 'senha-forca-texto ' + nivel;

            if (rSenha.estado !== 'ok' && senhaVal.length > 0) {
                pendencias.push(rSenha.msg);
            } else if (!senhaVal) {
                pendencias.push('Crie uma senha');
            }

            // CONFIRMAR SENHA
            const rSenha2 = analisarSenha2(document.getElementById('senha2').value, senhaVal);
            aplicar('senha2', 'status-senha2', 'msg-senha2', rSenha2);
            if (rSenha2.estado !== 'ok') {
                pendencias.push(rSenha2.msg || 'Confirme a senha');
            }

            /* ============================================
               PAINEL DE PENDÊNCIAS
               ============================================ */
            const box = document.getElementById('pendenciasBox');
            const titulo = document.getElementById('pendenciasTitulo');
            const lista = document.getElementById('pendenciasLista');

            // Remove duplicatas
            const unicas = [...new Set(pendencias)];

            if (unicas.length === 0) {
                box.classList.add('show', 'tudo-ok');
                titulo.innerHTML = '<i class="fas fa-check-circle"></i> <span>Tudo pronto! Pode cadastrar.</span>';
                lista.innerHTML = '';
                document.getElementById('btnCadastrar').disabled = false;
            } else {
                box.classList.add('show');
                box.classList.remove('tudo-ok');
                titulo.innerHTML = `<i class="fas fa-exclamation-circle"></i> <span>Falta ${unicas.length} coisa(s):</span>`;
                lista.innerHTML = unicas.map(p => `<li><i class="fas fa-circle"></i> ${p}</li>`).join('');
                document.getElementById('btnCadastrar').disabled = true;
            }
        }

        /* ============================================
           LIGAR EVENTOS
           ============================================ */
        document.querySelectorAll('#formCadastro input').forEach(input => {
            input.addEventListener('input', atualizarTudo);
            input.addEventListener('blur', atualizarTudo);
        });

        setTimeout(atualizarTudo, 50);

        /* ============================================
           SUBMIT
           ============================================ */
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnCadastrar');
            if (btn.disabled) { e.preventDefault(); return; }
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CADASTRANDO...';
            btn.disabled = true;
        });
    </script>
</body>
</html>