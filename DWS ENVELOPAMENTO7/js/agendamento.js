// =============================================
// agendamento.js - VERSÃO FINAL CORRIGIDA
// =============================================
(function () {
  "use strict";

  const DEBUG = location.hostname === "localhost" || location.hostname === "127.0.0.1";
  const log = (...args) => DEBUG && console.log(...args);
  const logErro = (...args) => console.error(...args);

  document.addEventListener("DOMContentLoaded", function () {
    log("=== AGENDAMENTO.JS v11.0 ===");

    // =============================================
    // ESTADO
    // =============================================
    let precosDinamicos = { carro: 800, moto: 500, caminhao: 2500, aquatico: 1800, mobilia: 300 };
    let acabamentosDinamicos = { "1.0": 1.0, "1.15": 1.15, "1.30": 1.3, "1.40": 1.4 };
    let veiculosCache = {};
    let veiculosTodos = null;
    let dataSelecionada = null;
    let horarioSelecionado = null;
    let servicoSelecionado = null;
    let precoBaseSelecionado = 0;
    let calendar = null;
    let usuarioLogado = false;
    let modeloSelecionado = null;
    let tipoSelecionado = null;
    let filtroAtual = "todos";
    let termoBusca = "";
    let modalModelos = null;

    const NOMES_SERVICOS = {
      carro: "Carro",
      moto: "Moto",
      caminhao: "Caminhão",
      aquatico: "Aquático",
      mobilia: "Mobília",
    };

    // =============================================
    // HELPERS
    // =============================================
    const getTipoLabel = (t) => NOMES_SERVICOS[t] || t;
    const getTipoIcon = (t) =>
      ({ carro: "🚗", moto: "🏍️", caminhao: "🚛", aquatico: "⛵", mobilia: "🪑" }[t] || "📦");

    function formatarPrecoCard(preco) {
      const n = parseFloat(preco);
      if (isNaN(n)) return "0";
      if (n % 1 === 0) return n.toFixed(0);
      return n.toFixed(2).replace(".", ",");
    }

    function formatarData(d) {
      if (!d) return "Nenhuma";
      try {
        return new Date(d + "T00:00:00").toLocaleDateString("pt-BR");
      } catch {
        return d;
      }
    }

    function formatarMoeda(v) {
      return "R$ " + parseFloat(v || 0).toFixed(2).replace(".", ",");
    }

    function formatarCPF(cpf) {
      const limpo = String(cpf || "").replace(/\D/g, "");
      if (limpo.length !== 11) return cpf || "";
      return limpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }

    function formatarTelefone(tel) {
      const limpo = String(tel || "").replace(/\D/g, "");
      if (limpo.length === 11) return `(${limpo.slice(0, 2)}) ${limpo.slice(2, 7)}-${limpo.slice(7)}`;
      if (limpo.length === 10) return `(${limpo.slice(0, 2)}) ${limpo.slice(2, 6)}-${limpo.slice(6)}`;
      return tel || "";
    }

    function hojeStr() {
      const h = new Date();
      return h.getFullYear() + "-" + String(h.getMonth() + 1).padStart(2, "0") + "-" + String(h.getDate()).padStart(2, "0");
    }

    function podeAgendarHoje(h) {
      const a = new Date();
      const minAtuais = a.getHours() * 60 + a.getMinutes();
      const [hh, mm] = h.split(":").map(Number);
      return hh * 60 + mm - minAtuais >= 120;
    }

    function filtrarHorarios(hs, d) {
      return d === hojeStr() ? hs.filter(podeAgendarHoje) : hs;
    }

    function escapeHtml(t) {
      const d = document.createElement("div");
      d.textContent = t;
      return d.innerHTML;
    }

    // =============================================
    // TOAST
    // =============================================
    function mostrarToast(msg, tipo = "success") {
      const toast = document.getElementById("toastMsg");
      const text = document.getElementById("toastText");
      if (!toast || !text) {
        alert(msg);
        return;
      }
      text.textContent = msg;
      toast.className = "toast-notification" + (tipo === "error" ? " error" : "");
      toast.style.display = "flex";
      toast.style.opacity = "1";
      clearTimeout(toast._timeout);
      toast._timeout = setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => {
          toast.style.display = "none";
          toast.className = "toast-notification";
        }, 300);
      }, 4000);
    }

    // =============================================
    // CARREGAR PREÇOS
    // =============================================
    async function carregarPrecos() {
      try {
        const res = await fetch("../../PHP/Clientes/precos.php", {
          credentials: "include",
          cache: "no-store",
        });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const data = await res.json();
        if (data.status === "sucesso") {
          precosDinamicos = data.precos;
          acabamentosDinamicos = data.acabamentos;
          document.querySelectorAll(".servico-card").forEach((card) => {
            const t = card.dataset.tipo;
            if (precosDinamicos[t] !== undefined) {
              card.dataset.precoBase = precosDinamicos[t];
              const el = card.querySelector(".servico-preco-base");
              if (el) el.textContent = "R$ " + formatarPrecoCard(precosDinamicos[t]) + "+";
            }
          });
        }
      } catch (e) {
        logErro("Erro preços:", e);
      }
    }

    // =============================================
    // CARREGAR VEÍCULOS
    // =============================================
    async function carregarVeiculos(tipo = null) {
      if (tipo && veiculosCache[tipo]) return veiculosCache[tipo];
      if (!tipo && veiculosTodos) return veiculosTodos;
      try {
        const url = tipo
          ? `../../PHP/Clientes/veiculos.php?tipo=${encodeURIComponent(tipo)}`
          : `../../PHP/Clientes/veiculos.php`;
        const res = await fetch(url, { credentials: "include", cache: "no-store" });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const data = await res.json();
        if (data.status === "sucesso") {
          const lista = data.veiculos || [];
          if (tipo) veiculosCache[tipo] = lista;
          else veiculosTodos = lista;
          return lista;
        }
        return [];
      } catch (e) {
        logErro("Erro veículos:", e);
        return [];
      }
    }

    // =============================================
    // SESSÃO
    // =============================================
    async function verificarUsuarioLogado() {
      try {
        let data;
        if (window.DWS_Sessao?.verificar) {
          data = await window.DWS_Sessao.verificar(false);
        } else {
          const res = await fetch("../../PHP/Clientes/sessao_cliente.php?t=" + Date.now(), {
            credentials: "include",
            cache: "no-store",
          });
          if (!res.ok) throw new Error("HTTP " + res.status);
          data = await res.json();
        }
        usuarioLogado = !!data.logado;
        return usuarioLogado;
      } catch (e) {
        logErro("Erro sessão:", e);
        usuarioLogado = false;
        return false;
      }
    }

    // =============================================
    // PREENCHER DADOS DO CLIENTE
    // =============================================
    async function preencherDadosCliente() {
      const card = document.getElementById("clienteInfoCard");
      if (!card) return;

      try {
        const res = await fetch("../../PHP/Clientes/buscar_perfil.php", {
          credentials: "include",
          cache: "no-store",
        });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const data = await res.json();

        if (data.status === "sucesso" && data.cliente) {
          const cli = data.cliente;
          window._emailCliente = cli.cliemail || "";
          const nome = cli.clinome || "Cliente";
          const email = cli.cliemail || "Não cadastrado";
          const cpf = formatarCPF(cli.clicpf || "");
          const tel = cli.clitel || "";

          card.innerHTML = `
            <div class="cliente-info-nome">
              <i class="fas fa-user-circle"></i>
              <span>${escapeHtml(nome)}</span>
            </div>
            <div class="cliente-info-linha">
              <i class="fas fa-envelope"></i>
              <span><strong>${escapeHtml(email)}</strong></span>
            </div>
            <div class="cliente-info-linha">
              <i class="fas fa-id-card"></i>
              <span>CPF: <strong>${escapeHtml(cpf)}</strong></span>
            </div>
            ${tel ? `
            <div class="cliente-info-linha">
              <i class="fas fa-phone"></i>
              <span>${escapeHtml(formatarTelefone(tel))}</span>
            </div>` : ""}
          `;
          log("📋 Cliente logado:", { nome, email, cpf });
        } else {
          card.innerHTML = `
            <div class="cliente-info-vazio">
              <i class="fas fa-sign-in-alt"></i>
              <span>Faça login para agendar</span>
            </div>
          `;
        }
      } catch (e) {
        logErro("Erro ao buscar perfil:", e);
        card.innerHTML = `
          <div class="cliente-info-vazio">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Erro ao carregar dados</span>
          </div>
        `;
      }
    }

    // =============================================
    // CALENDÁRIO
    // =============================================
    function inicializarCalendario() {
      const el = document.getElementById("calendario");
      if (!el) return;
      if (typeof FullCalendar === "undefined") {
        logErro("FullCalendar não carregado");
        return;
      }

      calendar = new FullCalendar.Calendar(el, {
        locale: "pt-br",
        initialView: "dayGridMonth",
        headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth" },
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        height: "auto",
        select: (info) => {
          if (new Date(info.startStr + "T00:00:00") < new Date().setHours(0, 0, 0, 0)) {
            mostrarToast("❌ Data passada não permitida.", "error");
            calendar.unselect();
            return;
          }
          selecionarData(info.startStr);
        },
        dateClick: (info) => {
          if (new Date(info.dateStr + "T00:00:00") < new Date().setHours(0, 0, 0, 0)) {
            mostrarToast("❌ Data passada não permitida.", "error");
            return;
          }
          selecionarData(info.dateStr);
        },
        dayCellDidMount: (info) => {
          if (info.date < new Date().setHours(0, 0, 0, 0)) {
            info.el.style.backgroundColor = "#2a2a2a";
            info.el.style.opacity = "0.4";
          }
        },
      });
      calendar.render();
    }

    function selecionarData(data) {
      dataSelecionada = data;
      const span = document.getElementById("dataSelecionada");
      if (span) span.textContent = formatarData(data);
      const input = document.getElementById("dataAgendamentoHidden");
      if (input) input.value = data;

      document.querySelectorAll(".fc-daygrid-day.data-selecionada").forEach((el) => {
        el.classList.remove("data-selecionada");
        ["background", "border", "borderRadius", "boxShadow"].forEach((p) => (el.style[p] = ""));
        const n = el.querySelector(".fc-daygrid-day-number");
        if (n)
          ["color", "background", "borderRadius", "fontWeight", "width", "height", "display", "alignItems", "justifyContent", "margin"].forEach(
            (p) => (n.style[p] = "")
          );
      });

      const cell = document.querySelector(`.fc-daygrid-day[data-date="${data}"]`);
      if (cell) {
        cell.classList.add("data-selecionada");
        cell.style.setProperty("background", "rgba(242, 53, 53, 0.3)", "important");
        cell.style.setProperty("border", "2px solid #f23535", "important");
        cell.style.setProperty("border-radius", "8px", "important");
        cell.style.setProperty("box-shadow", "0 0 15px rgba(242, 53, 53, 0.5)", "important");
        const n = cell.querySelector(".fc-daygrid-day-number");
        if (n) {
          n.style.setProperty("color", "#fff", "important");
          n.style.setProperty("background", "#f23535", "important");
          n.style.setProperty("border-radius", "50%", "important");
          n.style.setProperty("font-weight", "900", "important");
          n.style.setProperty("width", "32px", "important");
          n.style.setProperty("height", "32px", "important");
          n.style.setProperty("display", "flex", "important");
          n.style.setProperty("align-items", "center", "important");
          n.style.setProperty("justify-content", "center", "important");
          n.style.setProperty("margin", "4px", "important");
        }
      }
      carregarHorarios(data);
    }

    // =============================================
    // HORÁRIOS
    // =============================================
    async function carregarHorarios(data) {
      const container = document.getElementById("horariosList");
      if (!container) return;
      container.innerHTML = '<div class="text-center text-white p-4">⏳ Carregando...</div>';

      const todos = [
        "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
        "12:00", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00",
      ];
      const disponiveis = filtrarHorarios(todos, data);

      try {
        const res = await fetch(`../../PHP/Servico/horarios_ocupados.php?data=${data}`, { credentials: "include" });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const ocupados = await res.json();
        container.innerHTML = "";

        if (disponiveis.length === 0) {
          container.innerHTML = `<div class="text-center text-warning p-4" style="grid-column:1/-1;">
            <i class="fas fa-clock fa-2x mb-2"></i><br>
            Nenhum horário disponível hoje.<br>
            <small>Mínimo de 2h de antecedência.</small>
          </div>`;
          return;
        }

        disponiveis.forEach((hora) => {
          const btn = document.createElement("button");
          btn.type = "button";
          btn.className = "horario-btn";
          if (ocupados.includes(hora)) {
            btn.classList.add("ocupado");
            btn.disabled = true;
            btn.textContent = `${hora} ❌`;
          } else {
            btn.textContent = hora;
            btn.dataset.horario = hora;
            btn.addEventListener("click", () => selecionarHorario(hora));
          }
          container.appendChild(btn);
        });
      } catch (e) {
        logErro("Erro horários:", e);
        container.innerHTML = '<div class="text-center text-danger p-4">Erro ao carregar. Tente novamente.</div>';
      }
    }

    function selecionarHorario(hora) {
      if (dataSelecionada === hojeStr() && !podeAgendarHoje(hora)) {
        mostrarToast("❌ Mínimo 2h de antecedência.", "error");
        return;
      }
      horarioSelecionado = hora;
      const span = document.getElementById("horarioSelecionado");
      if (span) span.textContent = hora;
      const input = document.getElementById("horarioAgendamentoHidden");
      if (input) input.value = hora;

      document.querySelectorAll(".horario-btn").forEach((btn) => {
        btn.classList.remove("selecionado");
        if (btn.dataset.horario === hora) btn.classList.add("selecionado");
      });
      atualizarResumo();
    }

    // =============================================
    // SERVIÇOS
    // =============================================
    function selecionarServico(card) {
      document.querySelectorAll(".servico-card").forEach((c) => c.classList.remove("selecionado"));
      card.classList.add("selecionado");

      const tipo = card.dataset.tipo;
      servicoSelecionado = tipo;
      tipoSelecionado = tipo;
      precoBaseSelecionado = parseFloat(card.dataset.precoBase) || 0;

      const servicoSpan = document.getElementById("servicoResumo");
      if (servicoSpan) servicoSpan.textContent = NOMES_SERVICOS[tipo] || tipo;
      const tipoInput = document.getElementById("tipoServicoHidden");
      if (tipoInput) tipoInput.value = tipo;

      if (modeloSelecionado && modeloSelecionado.tipo !== tipo) {
        modeloSelecionado = null;
        atualizarInterfaceModelo();
        mostrarToast(`Tipo alterado. Selecione um modelo de ${NOMES_SERVICOS[tipo]}.`, "success");
      }

      filtroAtual = tipo;
      document.querySelectorAll(".filter-tipo-btn").forEach((b) => {
        b.classList.toggle("active", b.dataset.tipo === tipo);
      });
      const modalEl = document.getElementById("modalModelos");
      if (modalEl) modalEl.classList.add("tipo-travado");
      atualizarResumo();
    }

    // =============================================
    // RESUMO
    // =============================================
    function atualizarResumo() {
      let base = precoBaseSelecionado || 0;
      const selAcab = document.getElementById("acabamento");
      if (!selAcab) return;
      const fator = acabamentosDinamicos[selAcab.value] || 1.0;
      const total = Math.round(base * fator * 100) / 100;
      base = Math.round(base * 100) / 100;

      const elBase = document.getElementById("valorBase");
      const elTotal = document.getElementById("valorTotal");
      const elAcab = document.getElementById("valorAcabamento");
      if (elBase) elBase.textContent = formatarMoeda(base);
      if (elTotal) elTotal.textContent = formatarMoeda(total);
      if (elAcab) elAcab.textContent = fator > 1 ? `+${Math.round((fator - 1) * 100)}%` : "0%";

      const inpBase = document.getElementById("valorBaseHidden");
      const inpTotal = document.getElementById("valorTotalHidden");
      if (inpBase) inpBase.value = base.toFixed(2);
      if (inpTotal) inpTotal.value = total.toFixed(2);
    }

    // =============================================
    // MODAL
    // =============================================
    const modalEl = document.getElementById("modalModelos");
    const modalList = document.getElementById("modalModelosList");
    const modalSearch = document.getElementById("modalSearchInput");
    const modalConfirmBtn = document.getElementById("modalConfirmBtn");
    const modalCountBadge = document.getElementById("modalCountBadge");
    const btnAbrirModal = document.getElementById("btnAbrirModalModelos");
    const btnRemoverModelo = document.getElementById("btnRemoverModelo");
    const veiculoHidden = document.getElementById("veiculo");
    const veiculoInput = document.getElementById("veiculoInput");
    const tipoServicoHidden = document.getElementById("tipoServicoHidden");
    const infoModelo = document.getElementById("modeloSelecionadoInfo");
    const nomeModelo = document.getElementById("modeloSelecionadoNome");
    const resumoModelo = document.getElementById("modeloResumo");

    if (modalEl && typeof bootstrap !== "undefined") {
      modalModelos = new bootstrap.Modal(modalEl);
    }

    function atualizarInterfaceModelo() {
      if (modeloSelecionado) {
        infoModelo?.classList.add("visivel");
        if (nomeModelo) nomeModelo.textContent = `${getTipoIcon(modeloSelecionado.tipo)} ${modeloSelecionado.label}`;
        if (veiculoHidden) veiculoHidden.value = modeloSelecionado.label;
        if (veiculoInput) {
          veiculoInput.value = `${getTipoIcon(modeloSelecionado.tipo)} ${modeloSelecionado.label}`;
          veiculoInput.classList.add("visivel", "selecionado");
        }
        if (tipoServicoHidden) tipoServicoHidden.value = modeloSelecionado.tipo;
        if (btnAbrirModal) btnAbrirModal.style.display = "none";
        if (resumoModelo) resumoModelo.textContent = modeloSelecionado.label;
      } else {
        infoModelo?.classList.remove("visivel");
        if (veiculoHidden) veiculoHidden.value = "";
        if (veiculoInput) {
          veiculoInput.value = "";
          veiculoInput.classList.remove("visivel", "selecionado");
        }
        if (tipoServicoHidden && servicoSelecionado) tipoServicoHidden.value = servicoSelecionado;
        if (btnAbrirModal) btnAbrirModal.style.display = "block";
        if (resumoModelo) resumoModelo.textContent = "Nenhum";
      }
    }

    async function renderizarModal() {
      if (!modalList) return;
      modalList.innerHTML = '<div class="text-center text-white-50 p-4">⏳ Carregando modelos...</div>';

      let lista = [];
      if (filtroAtual === "todos") lista = await carregarVeiculos(null);
      else lista = await carregarVeiculos(filtroAtual);

      if (termoBusca.trim()) {
        const termo = termoBusca.toLowerCase().trim();
        lista = lista.filter(
          (v) => (v.modelo || "").toLowerCase().includes(termo) || (v.marca || "").toLowerCase().includes(termo)
        );
      }

      lista.sort((a, b) => {
        const ma = (a.marca || "").toLowerCase();
        const mb = (b.marca || "").toLowerCase();
        if (ma !== mb) return ma.localeCompare(mb);
        return (a.modelo || "").toLowerCase().localeCompare((b.modelo || "").toLowerCase());
      });

      if (modalCountBadge) modalCountBadge.textContent = lista.length;

      if (lista.length === 0) {
        modalList.innerHTML = `
          <div class="text-center text-white-50 p-4">
            <i class="fas fa-search mb-2" style="font-size:2rem;display:block;"></i>
            Nenhum modelo encontrado
          </div>`;
        return;
      }

      let html = "";
      let currentTipo = "";
      lista.forEach((item) => {
        if (item.tipo !== currentTipo) {
          currentTipo = item.tipo;
          html += `<div class="mt-2 mb-2"><span class="badge bg-danger" style="font-size:0.75rem;padding:6px 14px;letter-spacing:1px;">${getTipoIcon(currentTipo)} ${getTipoLabel(currentTipo).toUpperCase()}</span></div>`;
        }
        const label = item.marca ? `${item.marca} ${item.modelo}` : item.modelo;
        const isSelected =
          modeloSelecionado && modeloSelecionado.id === item.id && modeloSelecionado.tipo === item.tipo;
        html += `
          <div class="modelo-item ${isSelected ? "selecionado" : ""}"
               data-id="${item.id}"
               data-tipo="${item.tipo}"
               data-label="${escapeHtml(label)}">
            <div class="modelo-info">
              <span class="modelo-nome">${escapeHtml(label)}</span>
              <span class="modelo-detalhe">${escapeHtml(getTipoLabel(item.tipo))}</span>
            </div>
            <button type="button" class="modelo-select-btn"
                    data-id="${item.id}"
                    data-tipo="${item.tipo}"
                    data-label="${escapeHtml(label)}">
              ${isSelected ? "✓ Selecionado" : "Selecionar"}
            </button>
          </div>`;
      });
      modalList.innerHTML = html;

      modalList.querySelectorAll(".modelo-item").forEach((el) => {
        el.addEventListener("click", (e) => {
          if (e.target.classList.contains("modelo-select-btn")) return;
          selecionarNoModal(parseInt(el.dataset.id), el.dataset.tipo, el.dataset.label);
        });
      });
      modalList.querySelectorAll(".modelo-select-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          selecionarNoModal(parseInt(btn.dataset.id), btn.dataset.tipo, btn.dataset.label);
        });
      });
    }

    function selecionarNoModal(id, tipo, label) {
      modeloSelecionado = { id, tipo, label };
      modalList.querySelectorAll(".modelo-item").forEach((el) => {
        const sel = parseInt(el.dataset.id) === id && el.dataset.tipo === tipo;
        el.classList.toggle("selecionado", sel);
        const btn = el.querySelector(".modelo-select-btn");
        if (btn) btn.textContent = sel ? "✓ Selecionado" : "Selecionar";
      });
      if (modalConfirmBtn) {
        modalConfirmBtn.disabled = false;
        modalConfirmBtn.innerHTML = `<i class="fas fa-check"></i> Selecionar: ${escapeHtml(label)}`;
      }
    }

    function aplicarSelecao() {
      if (!modeloSelecionado) {
        mostrarToast("Selecione um modelo.", "error");
        return;
      }
      atualizarInterfaceModelo();
      modalModelos?.hide();
      mostrarToast(`✅ "${modeloSelecionado.label}" selecionado!`, "success");
    }

    function removerModelo() {
      modeloSelecionado = null;
      atualizarInterfaceModelo();
    }

    btnAbrirModal?.addEventListener("click", function () {
      if (tipoSelecionado) {
        filtroAtual = tipoSelecionado;
        modalEl?.classList.add("tipo-travado");
      } else {
        filtroAtual = "todos";
        modalEl?.classList.remove("tipo-travado");
      }
      document.querySelectorAll(".filter-tipo-btn").forEach((b) => {
        b.classList.toggle("active", b.dataset.tipo === filtroAtual);
      });
      termoBusca = "";
      if (modalSearch) modalSearch.value = "";
      renderizarModal();
      if (modalModelos) modalModelos.show();
    });

    modalConfirmBtn?.addEventListener("click", aplicarSelecao);
    btnRemoverModelo?.addEventListener("click", removerModelo);
    modalSearch?.addEventListener("input", (e) => {
      termoBusca = e.target.value;
      renderizarModal();
    });
    modalEl?.addEventListener("hidden.bs.modal", function () {
      modalEl?.classList.remove("tipo-travado");
    });

    // =============================================
    // ENVIAR AGENDAMENTO
    // =============================================
    async function enviarAgendamento() {
      await verificarUsuarioLogado();
      if (!usuarioLogado) {
        mostrarToast("⚠️ Faça login para agendar.", "error");
        setTimeout(() => (window.location.href = "loginClientes.html"), 1500);
        return;
      }

      const descricao = document.getElementById("descricao")?.value || "";

      if (!dataSelecionada) return mostrarToast("Selecione uma data.", "error");
      if (!horarioSelecionado) return mostrarToast("Selecione um horário.", "error");
      if (!servicoSelecionado) return mostrarToast("Selecione um serviço.", "error");
      if (!modeloSelecionado) return mostrarToast("Selecione o modelo.", "error");
      if (!descricao.trim()) return mostrarToast("Descreva o serviço.", "error");

      const modal = document.getElementById("modalPagamento");
      if (!modal) return alert("Erro: modal não encontrado.");
      if (typeof bootstrap === "undefined") return alert("Erro: Bootstrap não carregado.");

      window._dadosAgendamento = { descricao: descricao.trim() };
      bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    // =============================================
    // FINALIZAR
    // =============================================
    async function finalizarAgendamento(tipoPagamento) {
      const dados = window._dadosAgendamento;
      if (!dados) return;

      const modal = document.getElementById("modalPagamento");
      if (modal) bootstrap.Modal.getInstance(modal)?.hide();

      const fd = new FormData();
      fd.append("email", window._emailCliente || "");
      fd.append("veiculo", modeloSelecionado.label);
      fd.append("descricao", dados.descricao);
      fd.append("tipo_servico", servicoSelecionado);
      fd.append("acabamento", document.getElementById("acabamento")?.value || "1.0");
      fd.append("data_agendamento", dataSelecionada);
      fd.append("horario_agendamento", horarioSelecionado);
      fd.append("valor_base", document.getElementById("valorBaseHidden")?.value || "0");
      fd.append("valor_total", document.getElementById("valorTotalHidden")?.value || "0");
      fd.append("tipo_pagamento", tipoPagamento);

      const btn = document.getElementById("btnAgendar");
      const txtOriginal = btn?.innerHTML || "";
      if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
        btn.disabled = true;
      }

      try {
        const res = await fetch("../../PHP/Servico/agendar.php", {
          method: "POST",
          body: fd,
          credentials: "include",
        });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const data = await res.json();

        if (btn) {
          btn.innerHTML = txtOriginal;
          btn.disabled = false;
        }

        if (data.status === "sucesso") {
          mostrarToast(data.mensagem || "Agendamento realizado!", "success");
          sessionStorage.setItem("ultimo_servico_id", data.servico_id);
          if (data.whatsapp_url) sessionStorage.setItem("ultimo_whatsapp_url", data.whatsapp_url);

          setTimeout(() => {
            if (tipoPagamento === "online" && typeof iniciarPagamento === "function") {
              iniciarPagamento(data.servico_id, window._emailCliente || "");
            } else {
              if (data.whatsapp_url) window.open(data.whatsapp_url, "_blank");
              setTimeout(() => (window.location.href = "meus_agendamentos.html"), 1500);
            }
          }, 1200);
        } else {
          let msg = data.mensagem || "Erro ao agendar.";
          if (data.debug?.erro) {
            msg += "\n\n🔴 " + data.debug.erro;
            logErro("Debug:", data.debug);
          }
          mostrarToast(msg, "error");
        }
      } catch (e) {
        logErro("Erro fetch:", e);
        if (btn) {
          btn.innerHTML = txtOriginal;
          btn.disabled = false;
        }
        mostrarToast("Erro de conexão. Tente novamente.", "error");
      }
    }

    // =============================================
    // EVENTOS
    // =============================================
    document.getElementById("btnPagarOnline")?.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      finalizarAgendamento("online");
    });
    document.getElementById("btnPagarLocal")?.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      finalizarAgendamento("local");
    });
    document.querySelectorAll(".servico-card").forEach((c) => {
      c.addEventListener("click", () => selecionarServico(c));
    });
    const selAcab = document.getElementById("acabamento");
    if (selAcab) {
      selAcab.addEventListener("change", atualizarResumo);
      setTimeout(atualizarResumo, 100);
    }
    document.getElementById("btnAgendar")?.addEventListener("click", (e) => {
      e.preventDefault();
      enviarAgendamento();
    });

    // =============================================
    // INICIALIZAÇÃO (ÚNICA!)
    // =============================================
    carregarPrecos();
    inicializarCalendario();
    verificarUsuarioLogado().then((logado) => {
      if (logado) preencherDadosCliente();
      else {
        const card = document.getElementById("clienteInfoCard");
        if (card) {
          card.innerHTML = `
            <div class="cliente-info-vazio">
              <i class="fas fa-sign-in-alt"></i>
              <span>Faça login para agendar</span>
            </div>
          `;
        }
      }
    });

    log("✅ Sistema pronto");
  });
})();