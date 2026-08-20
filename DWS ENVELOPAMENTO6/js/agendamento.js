document.addEventListener('DOMContentLoaded', function() {
    console.log('=== AGENDAMENTO.JS INICIALIZADO ===');
    console.log('🔍 Versão com correção de acabamento - 2.0');

    // =============================================
    // VARIÁVEIS GLOBAIS
    // =============================================
    let dataSelecionada = null;
    let horarioSelecionado = null;
    let servicoSelecionado = null;
    let precoBaseSelecionado = 0;
    let calendar = null;

    let usuarioLogado = false;
    let dadosUsuario = { cliid: null, nome: '', telefone: '', cpf: '' };

    // =============================================
    // MAPEAMENTO DE PREÇOS
    // =============================================
    const PRECOS_BASE = {
        'carro': 800,
        'moto': 500,
        'caminhao': 2500,
        'aquatico': 1800,
        'mobilia': 300
    };

    const NOMES_SERVICOS = {
        'carro': 'Carro',
        'moto': 'Moto',
        'caminhao': 'Caminhão',
        'aquatico': 'Aquático',
        'mobilia': 'Mobília'
    };

    // =============================================
    // VERIFICA SE USUÁRIO ESTÁ LOGADO
    // =============================================
    async function verificarUsuarioLogado() {
        console.log('🔍 Verificando usuário logado (sessão)...');
        try {
            const res = await fetch('../../PHP/Clientes/sessãocliente.php', {
                credentials: 'include'
            });
            const data = await res.json();
            console.log('📋 Resposta da sessão:', data);

            usuarioLogado = !!data.logado;
            dadosUsuario = {
                cliid: data.cliid || null,
                nome: data.nome || '',
                telefone: data.telefone || '',
                cpf: data.cpf || ''
            };

            atualizarUIStatusLogin();
            return usuarioLogado;
        } catch (e) {
            console.error('❌ Erro ao verificar sessão:', e);
            usuarioLogado = false;
            return false;
        }
    }

    function atualizarUIStatusLogin() {
        const statusEl = document.getElementById('statusLoginAgendamento');
        if (!statusEl) return;

        if (usuarioLogado) {
            statusEl.textContent = `Agendando como: ${dadosUsuario.nome}`;
            statusEl.classList.remove('erro');
        } else {
            statusEl.textContent = 'Você precisa estar logado para agendar. Faça login para continuar.';
            statusEl.classList.add('erro');
        }
    }

    // =============================================
    // FUNÇÃO PARA VERIFICAR SE PODE AGENDAR HOJE
    // =============================================
    function podeAgendarHoje(horario) {
        const agora = new Date();
        const horaAtual = agora.getHours();
        const minutoAtual = agora.getMinutes();
        
        const [horaSelecionada, minutoSelecionado] = horario.split(':').map(Number);
        const minutosSelecionados = horaSelecionada * 60 + minutoSelecionado;
        const minutosAtuais = horaAtual * 60 + minutoAtual;
        
        const diferenca = minutosSelecionados - minutosAtuais;
        return diferenca >= 120;
    }

    // =============================================
    // FILTRA HORÁRIOS DISPONÍVEIS
    // =============================================
    function filtrarHorariosDisponiveis(horarios, data) {
        const hoje = new Date();
        const hojeStr = hoje.getFullYear() + '-' + 
                        String(hoje.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(hoje.getDate()).padStart(2, '0');
        
        if (data !== hojeStr) {
            return horarios;
        }
        
        return horarios.filter(horario => podeAgendarHoje(horario));
    }

    // =============================================
    // INICIALIZA CALENDÁRIO
    // =============================================
    function inicializarCalendario() {
        const calendarEl = document.getElementById('calendario');
        if (!calendarEl) {
            console.error('❌ Elemento calendario não encontrado!');
            return;
        }

        console.log('✅ Inicializando calendário...');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            
            select: function(info) {
                const dataSelecionada = info.startStr;
                const hoje = new Date();
                const dataClick = new Date(dataSelecionada + 'T00:00:00');
                
                if (dataClick < new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate())) {
                    mostrarToast('❌ Não é possível selecionar datas passadas. Escolha uma data a partir de hoje.', 'error');
                    calendar.unselect();
                    return;
                }
                
                console.log('📅 Data selecionada (select):', dataSelecionada);
                selecionarData(dataSelecionada);
            },
            
            dateClick: function(info) {
                const dataSelecionada = info.dateStr;
                const hoje = new Date();
                const dataClick = new Date(dataSelecionada + 'T00:00:00');
                
                if (dataClick < new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate())) {
                    mostrarToast('❌ Não é possível selecionar datas passadas. Escolha uma data a partir de hoje.', 'error');
                    return;
                }
                
                console.log('📅 Data selecionada (dateClick):', dataSelecionada);
                selecionarData(dataSelecionada);
            },
            
            dayCellDidMount: function(info) {
                const hoje = new Date();
                const dataCell = info.date;
                
                if (dataCell < new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate())) {
                    info.el.style.backgroundColor = '#2a2a2a';
                    info.el.style.opacity = '0.4';
                }
            }
        });
        
        calendar.render();
        console.log('✅ Calendário renderizado com sucesso!');
    }

    // =============================================
    // SELECIONA DATA
    // =============================================
    function selecionarData(data) {
        console.log('📅 selecionarData chamado com:', data);
        
        const hoje = new Date();
        const dataSelecionadaObj = new Date(data + 'T00:00:00');
        if (dataSelecionadaObj < new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate())) {
            mostrarToast('❌ Não é possível selecionar datas passadas.', 'error');
            return;
        }
        
        dataSelecionada = data;
        
        const dataSpan = document.getElementById('dataSelecionada');
        if (dataSpan) {
            dataSpan.textContent = formatarData(data);
        }
        
        const dataInput = document.getElementById('dataAgendamentoHidden');
        if (dataInput) {
            dataInput.value = data;
        }
        
        carregarHorarios(data);
    }

    // =============================================
    // CARREGA HORÁRIOS
    // =============================================
    function carregarHorarios(data) {
        console.log('🕐 carregarHorarios chamado com data:', data);
        const container = document.getElementById('horariosList');
        if (!container) return;
        
        container.innerHTML = '<div class="text-center text-white p-4">⏳ Carregando horários...</div>';
        
        const todosHorarios = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '12:00', '13:00', '13:30', '14:00',
            '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'
        ];
        
        const horariosDisponiveis = filtrarHorariosDisponiveis(todosHorarios, data);
        console.log('🕐 Horários disponíveis (após filtro):', horariosDisponiveis);
        
        fetch(`../../PHP/Servico/horarios_ocupados.php?data=${data}`, {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(ocupados => {
            console.log('📋 Horários ocupados no banco:', ocupados);
            
            container.innerHTML = '';
            
            const hoje = new Date();
            const hojeStr = hoje.getFullYear() + '-' + 
                            String(hoje.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(hoje.getDate()).padStart(2, '0');
            
            if (data === hojeStr) {
                const horariosBloqueados = todosHorarios.filter(h => !horariosDisponiveis.includes(h));
                if (horariosBloqueados.length > 0) {
                    const msg = document.createElement('div');
                    msg.className = 'text-warning text-center p-2 mb-2';
                    msg.style.fontSize = '0.85rem';
                    msg.innerHTML = `⚠️ Horários antes das ${new Date().getHours() + 2}:00 não estão disponíveis (mínimo 2h de antecedência)`;
                    container.appendChild(msg);
                }
            }
            
            horariosDisponiveis.forEach(hora => {
                const btn = document.createElement('button');
                const estaOcupado = ocupados.includes(hora);
                
                btn.className = 'horario-btn';
                if (estaOcupado) {
                    btn.classList.add('ocupado');
                    btn.disabled = true;
                    btn.textContent = `${hora} ❌`;
                    btn.title = 'Horário indisponível';
                } else {
                    btn.textContent = hora;
                    btn.dataset.horario = hora;
                    btn.addEventListener('click', function() {
                        console.log('🕐 Horário selecionado:', hora);
                        selecionarHorario(hora);
                    });
                }
                
                container.appendChild(btn);
            });
            
            if (horariosDisponiveis.length === 0) {
                container.innerHTML += `
                    <div class="text-center text-warning p-4">
                        <i class="fas fa-clock fa-2x mb-2"></i><br>
                        Nenhum horário disponível para hoje.<br>
                        <small>Mínimo de 2 horas de antecedência.</small>
                    </div>
                `;
            }
            
            console.log('✅ Horários carregados:', horariosDisponiveis.length, 'Ocupados:', ocupados.length);
        })
        .catch(error => {
            console.error('❌ Erro ao carregar horários ocupados:', error);
            container.innerHTML = '';
            
            const hoje = new Date();
            const hojeStr = hoje.getFullYear() + '-' + 
                            String(hoje.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(hoje.getDate()).padStart(2, '0');
            
            if (data === hojeStr) {
                const msg = document.createElement('div');
                msg.className = 'text-warning text-center p-2 mb-2';
                msg.style.fontSize = '0.85rem';
                msg.innerHTML = `⚠️ Horários antes das ${new Date().getHours() + 2}:00 não estão disponíveis (mínimo 2h de antecedência)`;
                container.appendChild(msg);
            }
            
            horariosDisponiveis.forEach(hora => {
                const btn = document.createElement('button');
                btn.className = 'horario-btn';
                btn.textContent = hora;
                btn.dataset.horario = hora;
                btn.addEventListener('click', function() {
                    selecionarHorario(hora);
                });
                container.appendChild(btn);
            });
        });
    }

    // =============================================
    // SELECIONA HORÁRIO
    // =============================================
    function selecionarHorario(hora) {
        console.log('🕐 selecionarHorario chamado com:', hora);
        
        const hoje = new Date();
        const hojeStr = hoje.getFullYear() + '-' + 
                        String(hoje.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(hoje.getDate()).padStart(2, '0');
        
        if (dataSelecionada === hojeStr && !podeAgendarHoje(hora)) {
            mostrarToast('❌ Para agendamentos no dia atual, é necessário no mínimo 2 horas de antecedência.', 'error');
            return;
        }
        
        horarioSelecionado = hora;
        
        const horarioSpan = document.getElementById('horarioSelecionado');
        if (horarioSpan) {
            horarioSpan.textContent = hora;
        }
        
        const horaInput = document.getElementById('horarioAgendamentoHidden');
        if (horaInput) {
            horaInput.value = hora;
        }
        
        document.querySelectorAll('.horario-btn').forEach(btn => {
            btn.classList.remove('selecionado');
            if (btn.dataset.horario === hora) {
                btn.classList.add('selecionado');
            }
        });
        
        atualizarResumo();
    }

    // =============================================
    // SELECIONA SERVIÇO - CORRIGIDO
    // =============================================
    function selecionarServico(card) {
        console.log('🔧 ===== SELECIONANDO SERVIÇO =====');
        
        // Remove seleção anterior
        document.querySelectorAll('.servico-card').forEach(c => c.classList.remove('selecionado'));
        card.classList.add('selecionado');
        
        // Pega os dados do card
        const tipo = card.dataset.tipo;
        const precoBase = parseFloat(card.dataset.precoBase) || 0;
        
        console.log('🔧 Tipo:', tipo);
        console.log('🔧 Preço Base (dataset):', precoBase);
        
        // Atualiza variáveis globais
        servicoSelecionado = tipo;
        precoBaseSelecionado = precoBase;
        
        console.log('🔧 Serviço selecionado:', servicoSelecionado);
        console.log('🔧 Preço base selecionado:', precoBaseSelecionado);
        
        // Atualiza o resumo na sidebar
        const servicoSpan = document.getElementById('servicoResumo');
        if (servicoSpan) {
            const nomeServico = NOMES_SERVICOS[tipo] || tipo;
            servicoSpan.textContent = nomeServico;
            console.log('🔧 Nome do serviço no resumo:', nomeServico);
        }
        
        // Atualiza campo hidden
        const tipoInput = document.getElementById('tipoServicoHidden');
        if (tipoInput) {
            tipoInput.value = tipo;
            console.log('🔧 tipoServicoHidden:', tipo);
        }
        
        // 🔥 FORÇA ATUALIZAÇÃO DO RESUMO (com o preço base)
        console.log('🔧 Chamando atualizarResumo()...');
        atualizarResumo();
        console.log('🔧 ===== FIM SELEÇÃO SERVIÇO =====');
    }

    // =============================================
    // ATUALIZA RESUMO - VERSÃO SIMPLES E FUNCIONAL
    // =============================================
    function atualizarResumo() {
        console.log('💰 ===== ATUALIZANDO RESUMO =====');
        
        // 1. PEGA O PREÇO BASE
        console.log('💰 precoBaseSelecionado (global):', precoBaseSelecionado);
        let valorBase = precoBaseSelecionado || 0;
        console.log('💰 valorBase (após verificação):', valorBase);
        
        // 2. PEGA O FATOR DE ACABAMENTO
        const acabamentoSelect = document.getElementById('acabamento');
        if (!acabamentoSelect) {
            console.error('❌ Select de acabamento não encontrado!');
            return;
        }
        
        const fator = parseFloat(acabamentoSelect.value);
        console.log('💰 Fator de acabamento:', fator);
        console.log('💰 Select value:', acabamentoSelect.value);
        
        // 3. CALCULA O VALOR FINAL
        let valorFinal = valorBase * fator;
        console.log('💰 Cálculo: ' + valorBase + ' x ' + fator + ' = ' + valorFinal);
        
        // 4. ARREDONDA
        valorBase = Math.round(valorBase * 100) / 100;
        valorFinal = Math.round(valorFinal * 100) / 100;
        console.log('💰 Arredondado: Base=' + valorBase + ' | Final=' + valorFinal);
        
        // 5. ATUALIZA OS ELEMENTOS NA TELA
        const valorBaseSpan = document.getElementById('valorBase');
        const valorTotalSpan = document.getElementById('valorTotal');
        const valorAcabamentoSpan = document.getElementById('valorAcabamento');
        
        if (valorBaseSpan) {
            valorBaseSpan.textContent = formatarMoeda(valorBase);
            console.log('💰 valorBaseSpan atualizado:', formatarMoeda(valorBase));
        } else {
            console.error('❌ valorBaseSpan não encontrado!');
        }
        
        if (valorTotalSpan) {
            valorTotalSpan.textContent = formatarMoeda(valorFinal);
            console.log('💰 valorTotalSpan atualizado:', formatarMoeda(valorFinal));
        } else {
            console.error('❌ valorTotalSpan não encontrado!');
        }
        
       const acrescimo = ((fator - 1) * 100);
if (valorAcabamentoSpan) {
    // 🔥 ARREDONDA PARA O NÚMERO INTEIRO MAIS PRÓXIMO
    const acrescimoArredondado = Math.round(acrescimo);
    valorAcabamentoSpan.textContent = acrescimoArredondado > 0 ? `+${acrescimoArredondado}%` : '0%';
    console.log('💰 Acréscimo:', acrescimo, '→ Arredondado:', acrescimoArredondado);
}else {
            console.error('❌ valorAcabamentoSpan não encontrado!');
        }
        
        // 6. ATUALIZA OS CAMPOS HIDDEN
        const valorBaseInput = document.getElementById('valorBaseHidden');
        const valorTotalInput = document.getElementById('valorTotalHidden');
        
        if (valorBaseInput) {
            valorBaseInput.value = valorBase.toFixed(2);
            console.log('💰 valorBaseHidden:', valorBase.toFixed(2));
        } else {
            console.error('❌ valorBaseHidden não encontrado!');
        }
        
        if (valorTotalInput) {
            valorTotalInput.value = valorFinal.toFixed(2);
            console.log('💰 valorTotalHidden:', valorFinal.toFixed(2));
        } else {
            console.error('❌ valorTotalHidden não encontrado!');
        }
        
        console.log('💰 ===== FIM ATUALIZAÇÃO RESUMO =====');
    }

    // =============================================
    // FORMATADORES
    // =============================================
    function formatarData(data) {
        if (!data) return 'Nenhuma';
        try {
            const d = new Date(data + 'T00:00:00');
            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        } catch (e) {
            return data;
        }
    }

    function formatarMoeda(valor) {
        return 'R$ ' + parseFloat(valor || 0).toFixed(2).replace('.', ',');
    }

    // =============================================
    // ENVIA AGENDAMENTO
    // =============================================
    function enviarAgendamento() {
        console.log('=== 📤 ENVIANDO AGENDAMENTO ===');
        
        const nome = dadosUsuario.nome;
        const telefone = dadosUsuario.telefone;
        const cpf = dadosUsuario.cpf;
        const email = document.getElementById('email')?.value || '';
        const veiculo = document.getElementById('veiculo')?.value || '';
        const descricao = document.getElementById('descricao')?.value || '';
        const acabamento = document.getElementById('acabamento')?.value || '1.0';
        
        const valorBaseInput = document.getElementById('valorBaseHidden');
        const valorTotalInput = document.getElementById('valorTotalHidden');
        const valorBase = valorBaseInput?.value || '0';
        const valorTotal = valorTotalInput?.value || '0';
        
        console.log('💰 Valores a serem enviados:');
        console.log('💰 Base:', valorBase);
        console.log('💰 Total:', valorTotal);
        console.log('💰 Acabamento:', acabamento);
        
        if (!dataSelecionada) {
            mostrarToast('Selecione uma data.', 'error');
            return;
        }
        if (!horarioSelecionado) {
            mostrarToast('Selecione um horário.', 'error');
            return;
        }
        if (!servicoSelecionado) {
            mostrarToast('Selecione um serviço.', 'error');
            return;
        }
        if (!usuarioLogado || !nome.trim()) {
            mostrarToast('Você precisa estar logado para agendar.', 'error');
            setTimeout(() => { window.location.href = 'loginClientes.html'; }, 1200);
            return;
        }
        if (!veiculo.trim()) {
            mostrarToast('Preencha o veículo/mobília.', 'error');
            return;
        }
        if (!descricao.trim()) {
            mostrarToast('Preencha a descrição do serviço.', 'error');
            return;
        }
        
        const hoje = new Date();
        const hojeStr = hoje.getFullYear() + '-' + 
                        String(hoje.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(hoje.getDate()).padStart(2, '0');
        
        if (dataSelecionada === hojeStr && !podeAgendarHoje(horarioSelecionado)) {
            mostrarToast('❌ Para agendamentos no dia atual, é necessário no mínimo 2 horas de antecedência.', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('email', email.trim());
        formData.append('veiculo', veiculo.trim());
        formData.append('descricao', descricao.trim());
        formData.append('tipo_servico', servicoSelecionado);
        formData.append('acabamento', acabamento);
        formData.append('data_agendamento', dataSelecionada);
        formData.append('horario_agendamento', horarioSelecionado);
        formData.append('valor_base', valorBase);
        formData.append('valor_total', valorTotal);
        
        const btn = document.getElementById('btnAgendar');
        const textoOriginal = btn?.innerHTML || 'Confirmar Agendamento';
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
            btn.disabled = true;
        }
        
        fetch('../../PHP/Servico/agendar.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (btn) {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
            
            if (data.status === 'sucesso') {
                mostrarToast(data.mensagem || 'Agendamento realizado com sucesso! Redirecionando para o pagamento...', 'success');

                // Guarda o ID do serviço para a tela de retorno conseguir consultar o status
                sessionStorage.setItem('ultimo_servico_id', data.servico_id);
                // Guarda o link do WhatsApp para usar como confirmação DEPOIS do pagamento aprovado
                if (data.whatsapp_url) {
                    sessionStorage.setItem('ultimo_whatsapp_url', data.whatsapp_url);
                }

                setTimeout(() => {
                    iniciarPagamento(data.servico_id, email.trim());
                }, 1200);
            } else {
                mostrarToast(data.mensagem || 'Erro ao agendar. Tente novamente.', 'error');
            }
        })
        .catch(error => {
            console.error('❌ ERRO na requisição:', error);
            if (btn) {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
            mostrarToast('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
        });
    }

    // =============================================
    // RESETA FORMULÁRIO
    // =============================================
    function resetarFormulario() {
        console.log('🔄 Resetando formulário...');
        dataSelecionada = null;
        horarioSelecionado = null;
        servicoSelecionado = null;
        precoBaseSelecionado = 0;
        
        const dataSpan = document.getElementById('dataSelecionada');
        const horarioSpan = document.getElementById('horarioSelecionado');
        const servicoSpan = document.getElementById('servicoResumo');
        const valorBaseSpan = document.getElementById('valorBase');
        const valorTotalSpan = document.getElementById('valorTotal');
        const valorAcabamentoSpan = document.getElementById('valorAcabamento');
        
        if (dataSpan) dataSpan.textContent = 'Nenhuma';
        if (horarioSpan) horarioSpan.textContent = 'Nenhum';
        if (servicoSpan) servicoSpan.textContent = 'Nenhum';
        if (valorBaseSpan) valorBaseSpan.textContent = 'R$ 0,00';
        if (valorTotalSpan) valorTotalSpan.textContent = 'R$ 0,00';
        if (valorAcabamentoSpan) valorAcabamentoSpan.textContent = '0%';
        
        document.querySelectorAll('.horario-btn').forEach(btn => btn.classList.remove('selecionado'));
        document.querySelectorAll('.servico-card').forEach(c => c.classList.remove('selecionado'));
        
        if (calendar) {
            calendar.unselect();
        }
        
        const hiddenFields = ['dataAgendamentoHidden', 'horarioAgendamentoHidden', 'tipoServicoHidden', 'valorBaseHidden', 'valorTotalHidden'];
        hiddenFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        
        const acabamentoSelect = document.getElementById('acabamento');
        if (acabamentoSelect) {
            acabamentoSelect.value = '1.0';
        }
    }

    // =============================================
    // TOAST
    // =============================================
    function mostrarToast(mensagem, tipo = 'success') {
        console.log('📢 Toast:', tipo, '-', mensagem);
        const toast = document.getElementById('toastMsg');
        const text = document.getElementById('toastText');
        
        if (!toast || !text) {
            alert(mensagem);
            return;
        }
        
        text.textContent = mensagem;
        toast.className = 'toast-notification';
        if (tipo === 'error') {
            toast.classList.add('error');
        }
        toast.style.display = 'flex';
        toast.style.opacity = '1';
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
                toast.className = 'toast-notification';
            }, 500);
        }, 4000);
    }

    // =============================================
    // CONFIGURAÇÃO DOS EVENTOS
    // =============================================
    
    // 🔥 SERVIÇOS - CLIQUE PARA SELECIONAR
    document.querySelectorAll('.servico-card').forEach(card => {
        card.addEventListener('click', function() {
            console.log('🖱️ CLICK no card:', this.dataset.tipo);
            selecionarServico(this);
        });
    });

    // 🔥 ACABAMENTO - MUDANÇA NO SELECT
    const acabamentoSelect = document.getElementById('acabamento');
    if (acabamentoSelect) {
        acabamentoSelect.addEventListener('change', function() {
            console.log('🔄 ===== ACABAMENTO ALTERADO =====');
            console.log('🔄 Novo valor:', this.value);
            console.log('🔄 Fator:', parseFloat(this.value));
            console.log('🔄 Chamando atualizarResumo()...');
            atualizarResumo();
            console.log('🔄 ===== FIM ALTERAÇÃO ACABAMENTO =====');
        });
        console.log('✅ Select de acabamento configurado');
        
        // 🔥 Dispara a atualização inicial
        console.log('🔄 Disparando atualização inicial do acabamento...');
        setTimeout(() => {
            atualizarResumo();
        }, 100);
    } else {
        console.error('❌ Select de acabamento não encontrado!');
    }

    // 🔥 BOTÃO DE AGENDAMENTO
    const btnAgendar = document.getElementById('btnAgendar');
    if (btnAgendar) {
        btnAgendar.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔘 Botão Confirmar Agendamento clicado!');
            enviarAgendamento();
        });
        console.log('✅ Botão de agendamento configurado');
    } else {
        console.error('❌ Botão btnAgendar não encontrado!');
    }

    // =============================================
    // INICIALIZAÇÃO
    // =============================================
    console.log('🚀 Inicializando agendamento...');
    
    if (typeof FullCalendar === 'undefined') {
        console.error('❌ FullCalendar NÃO está carregado! Verifique os scripts.');
    } else {
        console.log('✅ FullCalendar carregado com sucesso!');
        inicializarCalendario();
    }
    
    verificarUsuarioLogado();
    
    console.log('=== ✅ AGENDAMENTO.JS INICIALIZADO COM SUCESSO ===');
});