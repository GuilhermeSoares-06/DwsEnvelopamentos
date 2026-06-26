/**
 * agendamento.js - JavaScript da página de agendamento
 * Usado em: contato.html
 */

let calendario;
let dataSelecionada = null;
let horarioSelecionado = null;
let servicoSelecionado = null;
let precoBaseAtual = 0;

const servicos = {
  carro: { nome: "Carro", precoBase: 800 },
  moto: { nome: "Moto", precoBase: 500 },
  caminhao: { nome: "Caminhão", precoBase: 2500 },
  aquatico: { nome: "Aquático", precoBase: 1800 },
  mobilia: { nome: "Mobília", precoBase: 300 },
};

const horariosDisponiveis = [
  "08:00",
  "09:00",
  "10:00",
  "11:00",
  "13:00",
  "14:00",
  "15:00",
  "16:00",
  "17:00",
];

// Carregar agendamentos do localStorage
function carregarAgendamentos() {
  let agendamentos = localStorage.getItem("dws_agendamentos_completo");
  if (agendamentos) {
    return JSON.parse(agendamentos);
  }
  const hoje = new Date().toISOString().split("T")[0];
  const amanha = new Date(Date.now() + 86400000).toISOString().split("T")[0];
  const depois = new Date(Date.now() + 172800000).toISOString().split("T")[0];
  const exemplos = [
    {
      id: 1,
      data: hoje,
      horario: "09:00",
      servicoNome: "Carro",
      valorTotal: "R$ 920,00",
    },
    {
      id: 2,
      data: amanha,
      horario: "14:00",
      servicoNome: "Moto",
      valorTotal: "R$ 575,00",
    },
    {
      id: 3,
      data: depois,
      horario: "10:00",
      servicoNome: "Caminhão",
      valorTotal: "R$ 2.875,00",
    },
  ];
  localStorage.setItem("dws_agendamentos_completo", JSON.stringify(exemplos));
  return exemplos;
}

function salvarAgendamentos(agendamentos) {
  localStorage.setItem(
    "dws_agendamentos_completo",
    JSON.stringify(agendamentos),
  );
}

function isHorarioOcupado(data, horario) {
  const agendamentos = carregarAgendamentos();
  return agendamentos.some((ag) => ag.data === data && ag.horario === horario);
}

function obterEventosCalendario() {
  const agendamentos = carregarAgendamentos();
  return agendamentos.map((ag) => ({
    extendedProps: { horario: ag.horario, servico: ag.servicoNome },
  }));
}

function inicializarCalendario() {
    const calendarEl = document.getElementById("calendario");
    if (!calendarEl) return;

    calendario = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "pt-br",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth",
        },
        buttonText: {
            today: "Hoje",
            month: "Mês",
            prev: "Anterior",
            next: "Próximo",
        },
        events: obterEventosCalendario(),
        
        dateClick: function(info) {
            const dataSelecionadaObj = new Date(info.dateStr);
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);

            if (dataSelecionadaObj < hoje) {
                showToast("⚠️ Não é possível agendar em datas passadas!", true);
                return;
            }

            // LIMPA TODOS OS DESTAQUES
            limparTodosDestaques();

            // DESTACA A DATA CLICADA (com setTimeout para garantir)
            setTimeout(function() {
                const dia = new Date(info.dateStr).getDate();
                const mes = new Date(info.dateStr).getMonth();
                const ano = new Date(info.dateStr).getFullYear();

                document.querySelectorAll('.fc-daygrid-day').forEach(function(el) {
                    const dayNumber = el.querySelector('.fc-daygrid-day-number');
                    if (dayNumber) {
                        const dayText = dayNumber.innerText.trim();
                        const isOtherMonth = el.classList.contains('fc-day-other');
                        
                        if (parseInt(dayText) === dia && !isOtherMonth) {
                            el.style.backgroundColor = 'rgba(242, 53, 53, 0.25)';
                            el.style.border = '2px solid #f23535';
                            el.style.borderRadius = '8px';
                            el.style.boxShadow = '0 0 20px rgba(242, 53, 53, 0.3)';
                            el.classList.add('data-selecionada');
                            
                            dayNumber.style.color = '#f23535';
                            dayNumber.style.fontWeight = '800';
                            dayNumber.style.fontSize = '1.2rem';
                            dayNumber.style.background = 'rgba(242, 53, 53, 0.1)';
                            dayNumber.style.borderRadius = '50%';
                            dayNumber.style.padding = '2px 6px';
                            dayNumber.style.display = 'inline-block';
                        }
                    }
                });
            }, 100);

            dataSelecionada = info.dateStr;
            horarioSelecionado = null;
            document.getElementById("horarioSelecionado").innerText = "Nenhum";
            atualizarHorarios();
            atualizarResumo();
            showToast(`📅 Data selecionada: ${dataSelecionada}`);
        },
        
        eventClick: function(info) {
            const horario = info.event.extendedProps.horario;
            const servico = info.event.extendedProps.servico;
            showToast(`🔴 Horário ocupado: ${horario} - ${servico}`, false);
        },
        
        validRange: function(nowDate) {
            return { start: nowDate };
        },
        
        datesSet: function() {
            limparTodosDestaques();
        }
    });
    
    calendario.render();
}

function atualizarHorarios() {
  const container = document.getElementById("horariosList");
  if (!dataSelecionada) {
    container.innerHTML =
      '<div class="text-center text-white p-4">📅 Selecione uma data para ver os horários disponíveis</div>';
    return;
  }
  let html = "";
  horariosDisponiveis.forEach((horario) => {
    const ocupado = isHorarioOcupado(dataSelecionada, horario);
    const selecionado = horarioSelecionado === horario;
    const classe = ocupado ? "ocupado" : selecionado ? "selecionado" : "";
    const onClick = !ocupado ? `onclick="selecionarHorario('${horario}')"` : "";
    html += `<div class="horario-btn ${classe}" ${onClick}>${horario}</div>`;
  });
  container.innerHTML = html;
}

function selecionarHorario(horario) {
  if (isHorarioOcupado(dataSelecionada, horario)) {
    showToast("❌ Horário já ocupado! Escolha outro horário.", true);
    return;
  }
  horarioSelecionado = horario;
  document.getElementById("horarioSelecionado").innerText = horario;
  atualizarHorarios();
  showToast(`✅ Horário selecionado: ${horario}`, false);
}

function calcularOrcamento() {
  if (!servicoSelecionado || !precoBaseAtual) {
    document.getElementById("valorBase").innerText = "R$ 0,00";
    document.getElementById("valorAcabamento").innerText = "+0%";
    document.getElementById("valorTotal").innerText = "R$ 0,00";
    return;
  }
  const fatorAcabamento = parseFloat(
    document.getElementById("acabamento").value,
  );
  const percentAcabamento = (fatorAcabamento - 1) * 100;
  const valorFinal = precoBaseAtual * fatorAcabamento;
  document.getElementById("valorBase").innerText =
    `R$ ${precoBaseAtual.toFixed(2)}`;
  document.getElementById("valorAcabamento").innerText =
    `+${percentAcabamento.toFixed(0)}%`;
  document.getElementById("valorTotal").innerText =
    `R$ ${valorFinal.toFixed(2)}`;
}

function atualizarResumo() {
  document.getElementById("dataSelecionada").innerText =
    dataSelecionada || "Nenhuma";
  document.getElementById("horarioSelecionado").innerText =
    horarioSelecionado || "Nenhum";
  document.getElementById("servicoResumo").innerText = servicoSelecionado
    ? servicos[servicoSelecionado].nome
    : "Nenhum";
  calcularOrcamento();
}

function atualizarListaAgendamentos() {
  const agendamentos = carregarAgendamentos();
  const container = document.getElementById("listaAgendamentos");
  if (agendamentos.length === 0) {
    container.innerHTML =
      '<div class="text-center text-secondary">Nenhum agendamento</div>';
    return;
  }
  const ordenados = [...agendamentos].sort((a, b) => {
    if (a.data === b.data) return a.horario.localeCompare(b.horario);
    return a.data.localeCompare(b.data);
  });
  let html = "";
  ordenados.slice(0, 5).forEach((ag) => {
    html += `
            <div class="agendamento-item">
                <div class="data-hora">📅 ${ag.data} às ${ag.horario}</div>
                <div class="servico-info">🔧 ${ag.servicoNome} - ${ag.valorTotal || "R$ ---"}</div>
                <div class="servico-info">🟡 Status: Agendado</div>
            </div>
        `;
  });
  if (ordenados.length > 5) {
    html +=
      '<div class="text-center text-secondary small mt-2">+ mais agendamentos</div>';
  }
  container.innerHTML = html;
}

function showToast(message, isError = false) {
  const toastMsg = document.getElementById("toastMsg");
  const toastText = document.getElementById("toastText");
  if (!toastMsg || !toastText) return;
  toastText.innerText = message;
  toastMsg.style.backgroundColor = isError ? "#dc3545" : "#28a745";
  toastMsg.style.display = "block";
  setTimeout(() => {
    toastMsg.style.display = "none";
  }, 3000);
}

function confirmarAgendamento() {
  const nome = document.getElementById("nome").value.trim();
  const telefone = document.getElementById("telefone").value.trim();
  const email = document.getElementById("email").value.trim();
  const veiculo = document.getElementById("veiculo").value.trim();
  const descricao = document.getElementById("descricao").value.trim();
  const acabamento = document.getElementById("acabamento").value;
  const valorBase = document
    .getElementById("valorBase")
    .innerText.replace("R$ ", "")
    .replace(",", ".");
  const valorTotal = document
    .getElementById("valorTotal")
    .innerText.replace("R$ ", "")
    .replace(",", ".");

  if (!dataSelecionada) {
    showToast("📅 Selecione uma data!", true);
    return false;
  }
  if (!horarioSelecionado) {
    showToast("⏰ Selecione um horário!", true);
    return false;
  }
  if (!servicoSelecionado) {
    showToast("🔧 Selecione um tipo de serviço!", true);
    return false;
  }
  if (!nome) {
    showToast("👤 Informe seu nome!", true);
    return false;
  }
  if (!telefone) {
    showToast("📞 Informe seu telefone!", true);
    return false;
  }
  if (!veiculo) {
    showToast("🚗 Informe o veículo/mobília!", true);
    return false;
  }
  if (!descricao) {
    showToast("📝 Descreva o serviço desejado!", true);
    return false;
  }
  if (isHorarioOcupado(dataSelecionada, horarioSelecionado)) {
    showToast("⚠️ Este horário foi ocupado! Escolha outro.", true);
    horarioSelecionado = null;
    atualizarHorarios();
    atualizarResumo();
    return false;
  }

  const dados = new FormData();
  dados.append("nome", nome);
  dados.append("telefone", telefone);
  dados.append("email", email);
  dados.append("veiculo", veiculo);
  dados.append("descricao", descricao);
  dados.append("tipo_servico", servicoSelecionado);
  dados.append("acabamento", acabamento);
  dados.append("data_agendamento", dataSelecionada);
  dados.append("horario_agendamento", horarioSelecionado);
  dados.append("valor_total", valorTotal);
  dados.append("valor_base", valorBase);

  const btnAgendar = document.getElementById("btnAgendar");
  btnAgendar.disabled = true;
  btnAgendar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

  fetch("../../PHP/Servico/agendar.php", {
    method: "POST",
    body: dados,
  })
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      return response.text();
    })
    .then((text) => {
      try {
        const data = JSON.parse(text);
        if (data.status === "sucesso") {
          showToast(`✅ ${data.mensagem}`, false);
          if (data.whatsapp_url) window.open(data.whatsapp_url, "_blank");
          horarioSelecionado = null;
          document.getElementById("horarioSelecionado").innerText = "Nenhum";
          atualizarHorarios();
          atualizarResumo();
          if (calendario) calendario.refetchEvents();
          atualizarListaAgendamentos();
        } else {
          showToast(`❌ ${data.mensagem}`, true);
        }
      } catch (e) {
        throw new Error("Resposta não é um JSON válido");
      }
    })
    .catch((error) => {
      showToast(`❌ ${error.message}`, true);
    })
    .finally(() => {
      btnAgendar.disabled = false;
      btnAgendar.innerHTML =
        '<i class="fas fa-check-circle"></i> Confirmar Agendamento';
    });
  return true;
}

function mascaraTelefone() {
  const telefoneInput = document.getElementById("telefone");
  if (telefoneInput) {
    telefoneInput.addEventListener("input", (e) => {
      let valor = e.target.value.replace(/\D/g, "");
      if (valor.length > 11) valor = valor.slice(0, 11);
      if (valor.length >= 2 && valor.length <= 10) {
        valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
        if (valor.length > 10) valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
      } else if (valor.length > 10) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
      }
      e.target.value = valor;
    });
  }
}

function inicializarCardsServico() {
  const cards = document.querySelectorAll(".servico-card");
  cards.forEach((card) => {
    card.addEventListener("click", () => {
      cards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      const tipo = card.getAttribute("data-tipo");
      const precoBase = parseFloat(card.getAttribute("data-preco-base"));
      servicoSelecionado = tipo;
      precoBaseAtual = precoBase;
      atualizarResumo();
      showToast(`🔧 Serviço selecionado: ${servicos[tipo].nome}`, false);
    });
  });
}

function inicializarSelects() {
  const selectAcabamento = document.getElementById("acabamento");
  if (selectAcabamento) {
    selectAcabamento.addEventListener("change", () => calcularOrcamento());
  }
}

function inicializarMenuUser() {
  const iconeUser = document.getElementById("userIcon");
  const menuUser = document.getElementById("menuUser");
  if (iconeUser && menuUser) {
    iconeUser.addEventListener("click", (e) => {
      e.stopPropagation();
      menuUser.style.display =
        menuUser.style.display === "flex" ? "none" : "flex";
    });
    document.addEventListener("click", (e) => {
      if (!iconeUser.contains(e.target) && !menuUser.contains(e.target)) {
        menuUser.style.display = "none";
      }
    });
  }
}

function limparTodosDestaques() {
  document.querySelectorAll(".fc-daygrid-day").forEach(function (el) {
    el.style.backgroundColor = "";
    el.style.border = "";
    el.style.borderRadius = "";
    el.style.boxShadow = "";
    el.classList.remove("data-selecionada");
  });
  document.querySelectorAll(".fc-daygrid-day-number").forEach(function (el) {
    el.style.color = "";
    el.style.fontWeight = "";
    el.style.fontSize = "";
    el.style.background = "";
    el.style.borderRadius = "";
    el.style.padding = "";
    el.style.display = "";
  });
}

function limparAgendamentos() {
  if (confirm("⚠️ Isso irá apagar TODOS os agendamentos. Deseja continuar?")) {
    localStorage.removeItem("dws_agendamentos_completo");
    location.reload();
  }
}

// Inicialização
document.addEventListener("DOMContentLoaded", () => {
  inicializarCalendario();
  inicializarCardsServico();
  inicializarSelects();
  inicializarMenuUser();
  mascaraTelefone();
  atualizarListaAgendamentos();

  const btnAgendar = document.getElementById("btnAgendar");
  if (btnAgendar) {
    btnAgendar.addEventListener("click", confirmarAgendamento);
  }

  window.limparAgendamentos = limparAgendamentos;
});
