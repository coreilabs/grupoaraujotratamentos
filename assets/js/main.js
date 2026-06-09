const WHATSAPP_ATENDENTE = "5562999999999";
const NOME_ATENDENTE = "Atendente";

const MENSAGEM_PADRAO =
  `Olá, ${NOME_ATENDENTE}. Vim pelo site do Grupo Araújo Tratamentos e preciso de orientação sobre encaminhamento para clínica de reabilitação.`;

const MENSAGEM_UNIDADE_INDEFINIDA =
  `Olá, ${NOME_ATENDENTE}. Vim pelo site do Grupo Araújo Tratamentos. Preciso de orientação, mas ainda não sei qual unidade seria mais adequada para o caso da minha família.`;

const MENSAGEM_OUTRAS_UNIDADES =
  `Olá, ${NOME_ATENDENTE}. Vim pelo site do Grupo Araújo Tratamentos e gostaria de verificar disponibilidade de outras unidades.`;

const unidades = [
  {
    id: "aparecida-goiania-go-masculina-1",
    cidade: "Aparecida de Goiânia",
    estado: "GO",
    modalidade: "Masculina",
    descricao:
      "Unidade parceira em Aparecida de Goiânia com áreas de convivência, lazer, terapias e apoio para o processo de encaminhamento.",
    recursos: [
      "Área de lazer",
      "Piscina",
      "Acomodações",
      "Espaços terapêuticos"
    ],
    fotos: [
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/area-lazer.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/acomodacoes.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/cozinha.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/enfermaria.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/terapias-interacao.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/reunioes.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/escritorio-real.webp",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/1/area-verde-real.webp"
    ]
  },
  {
    id: "aparecida-goiania-go-masculina-2",
    cidade: "Aparecida de Goiânia",
    estado: "GO",
    modalidade: "Masculina",
    descricao:
      "Unidade parceira com estrutura para acolhimento conforme avaliação inicial, perfil do caso e disponibilidade.",
    recursos: [
      "Estrutura ampla",
      "Ambiente reservado",
      "Rotina terapêutica",
      "Apoio no encaminhamento"
    ],
    fotos: [
      "unidades/Aparecida de Goiânia - Goiás/Masculina/2/IMG-20260310-WA0214.jpg",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/2/IMG-20260310-WA0216.jpg",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/2/IMG-20260310-WA0218.jpg",
      "unidades/Aparecida de Goiânia - Goiás/Masculina/2/IMG-20260310-WA0219.jpg"
    ]
  },
  {
    id: "caturai-go-feminina-1",
    cidade: "Caturaí",
    estado: "GO",
    modalidade: "Feminina",
    descricao:
      "Unidade parceira feminina com ambiente acolhedor e encaminhamento conforme disponibilidade e perfil do caso.",
    recursos: [
      "Atendimento feminino",
      "Ambiente reservado",
      "Apoio à família",
      "Encaminhamento responsável"
    ],
    fotos: [
      "unidades/Caturaí - Goiás/Feminina/1/IMG-20251226-WA0161.jpg",
      "unidades/Caturaí - Goiás/Feminina/1/IMG-20251226-WA0163.jpg",
      "unidades/Caturaí - Goiás/Feminina/1/IMG-20251226-WA0165.jpg",
      "unidades/Caturaí - Goiás/Feminina/1/IMG-20251226-WA0171.jpg"
    ]
  },
  {
    id: "goiania-go-feminina-1",
    cidade: "Goiânia",
    estado: "GO",
    modalidade: "Feminina",
    descricao:
      "Unidade parceira direcionada ao público feminino, com acolhimento humanizado e ambiente reservado.",
    recursos: [
      "Atendimento feminino",
      "Ambiente acolhedor",
      "Equipe preparada",
      "Apoio à família"
    ],
    fotos: [
      "unidades/Goiânia - Goiás/Feminina/1/IMG-20251204-WA0044.jpg",
      "unidades/Goiânia - Goiás/Feminina/1/IMG-20251204-WA0045.jpg",
      "unidades/Goiânia - Goiás/Feminina/1/VID-20251204-WA0052.mp4",
      "unidades/Goiânia - Goiás/Feminina/1/VID-20251204-WA0056.mp4"
    ]
  },
  {
    id: "goiania-go-masculina-1",
    cidade: "Goiânia",
    estado: "GO",
    modalidade: "Masculina",
    descricao:
      "Unidade parceira em Goiânia com estrutura para acolhimento conforme avaliação inicial e disponibilidade.",
    recursos: [
      "Ambiente reservado",
      "Rotina terapêutica",
      "Apoio à família",
      "Encaminhamento responsável"
    ],
    fotos: [
      "unidades/Goiânia - Goiás/Masculina/1/IMG-20251130-WA0054(1).jpg",
      "unidades/Goiânia - Goiás/Masculina/1/IMG-20251130-WA0055(1).jpg",
      "unidades/Goiânia - Goiás/Masculina/1/IMG-20251130-WA0057(1).jpg",
      "unidades/Goiânia - Goiás/Masculina/1/IMG-20251130-WA0059(1).jpg"
    ]
  },
  {
    id: "senador-canedo-go-feminina-1",
    cidade: "Senador Canedo",
    estado: "GO",
    modalidade: "Feminina",
    descricao:
      "Unidade parceira feminina em Senador Canedo, com acolhimento e orientação para encaminhamento responsável.",
    recursos: [
      "Atendimento feminino",
      "Ambiente acolhedor",
      "Equipe preparada",
      "Apoio no encaminhamento"
    ],
    fotos: [
      "unidades/Senador Canedo - Goiás/feminina/1/IMG-20260604-WA0169.jpg",
      "unidades/Senador Canedo - Goiás/feminina/1/IMG-20260604-WA0171.jpg",
      "unidades/Senador Canedo - Goiás/feminina/1/IMG-20260604-WA0174.jpg",
      "unidades/Senador Canedo - Goiás/feminina/1/IMG-20260604-WA0175.jpg"
    ]
  }
];

let galeriaUnidades = null;

function gerarLinkWhatsappPadrao(texto = MENSAGEM_PADRAO) {
  return `https://wa.me/${WHATSAPP_ATENDENTE}?text=${encodeURIComponent(texto)}`;
}

function gerarLinkWhatsappUnidade(unidade) {
  const texto = `Olá, ${NOME_ATENDENTE}. Vim pelo site do Grupo Araújo Tratamentos e gostaria de mais informações sobre uma unidade em ${unidade.cidade}/${unidade.estado}, modalidade ${unidade.modalidade}. Pode me orientar?`;
  return gerarLinkWhatsappPadrao(texto);
}

function escaparHtml(valor) {
  return String(valor)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function definirLinksWhatsapp() {
  document.querySelectorAll("[data-whatsapp-default]").forEach((link) => {
    link.href = gerarLinkWhatsappPadrao();
  });

  document.querySelectorAll("[data-whatsapp-unknown]").forEach((link) => {
    link.href = gerarLinkWhatsappPadrao(MENSAGEM_UNIDADE_INDEFINIDA);
  });

  document.querySelectorAll("[data-whatsapp-other-units]").forEach((link) => {
    link.href = gerarLinkWhatsappPadrao(MENSAGEM_OUTRAS_UNIDADES);
  });
}

function midiaEhVideo(caminho) {
  return /\.(mp4|webm|ogg)$/i.test(caminho);
}

function criarSlide(unidade, foto, indice) {
  const rotulo = escaparHtml(`Unidade em ${unidade.cidade}/${unidade.estado} - ${unidade.modalidade}`);
  const caminho = escaparHtml(foto);
  const posicao = indice + 1;

  if (midiaEhVideo(foto)) {
    return `
      <div class="swiper-slide">
        <div class="unit-slide-link unit-slide-video" aria-label="Vídeo ${posicao} de unidade em ${escaparHtml(unidade.cidade)}/${escaparHtml(unidade.estado)}, modalidade ${escaparHtml(unidade.modalidade)}">
          <video class="unit-slide-media" src="${caminho}" controls preload="metadata" playsinline></video>
        </div>
      </div>
    `;
  }

  return `
    <div class="swiper-slide">
      <a class="unit-slide-link glightbox" href="${caminho}" data-gallery="galeria-${unidade.id}" data-title="${rotulo}">
        <img class="unit-slide-media" src="${caminho}" alt="Foto ${posicao} de unidade em ${escaparHtml(unidade.cidade)}/${escaparHtml(unidade.estado)}, modalidade ${escaparHtml(unidade.modalidade)}" loading="lazy" width="640" height="480">
      </a>
    </div>
  `;
}

function criarCardUnidade(unidade) {
  const recursos = unidade.recursos
    .map((recurso) => `<li>${escaparHtml(recurso)}</li>`)
    .join("");
  const slides = unidade.fotos
    .map((foto, indice) => criarSlide(unidade, foto, indice))
    .join("");

  return `
    <article class="unit-card" data-aos="fade-up" data-modalidade="${escaparHtml(unidade.modalidade)}" data-cidade="${escaparHtml(unidade.cidade)}" data-unidade="${escaparHtml(unidade.id)}">
      <div class="unit-carousel swiper swiper-${unidade.id}" aria-label="Fotos de unidade em ${escaparHtml(unidade.cidade)}/${escaparHtml(unidade.estado)}, modalidade ${escaparHtml(unidade.modalidade)}">
        <div class="swiper-wrapper">
          ${slides}
        </div>
        <button class="swiper-nav swiper-prev prev-${unidade.id}" type="button" aria-label="Foto anterior da unidade em ${escaparHtml(unidade.cidade)}">
          <i data-lucide="chevron-left"></i>
        </button>
        <button class="swiper-nav swiper-next next-${unidade.id}" type="button" aria-label="Próxima foto da unidade em ${escaparHtml(unidade.cidade)}">
          <i data-lucide="chevron-right"></i>
        </button>
        <div class="swiper-pagination pagination-${unidade.id}"></div>
      </div>
      <div class="unit-body">
        <div class="unit-meta">
          <span class="tag"><i data-lucide="map-pin"></i>${escaparHtml(unidade.cidade)}/${escaparHtml(unidade.estado)}</span>
          <span class="tag accent"><i data-lucide="users"></i>${escaparHtml(unidade.modalidade)}</span>
        </div>
        <p>${escaparHtml(unidade.descricao)}</p>
        <ul class="resource-list">
          ${recursos}
        </ul>
        <a class="btn btn-primary full" href="${gerarLinkWhatsappUnidade(unidade)}" target="_blank" rel="noopener">
          <i data-lucide="message-circle"></i>
          <span>Quero informações desta unidade</span>
        </a>
      </div>
    </article>
  `;
}

function renderizarUnidades() {
  const grid = document.querySelector("#unidadesGrid");
  if (!grid) return;

  grid.innerHTML = unidades.map(criarCardUnidade).join("");
  inicializarSwipers();
  inicializarGaleria();
  aplicarFiltroUnidades();

  if (window.lucide) {
    window.lucide.createIcons();
  }

  if (window.AOS) {
    window.AOS.refresh();
  }
}

function inicializarSwipers() {
  if (!window.Swiper) return;

  unidades.forEach((unidade) => {
    new Swiper(`.swiper-${unidade.id}`, {
      loop: unidade.fotos.length > 1,
      slidesPerView: 1,
      spaceBetween: 12,
      on: {
        slideChangeTransitionStart(swiper) {
          swiper.el.querySelectorAll("video").forEach((video) => video.pause());
        }
      },
      pagination: {
        el: `.pagination-${unidade.id}`,
        clickable: true
      },
      navigation: {
        nextEl: `.next-${unidade.id}`,
        prevEl: `.prev-${unidade.id}`
      }
    });
  });
}

function inicializarGaleria() {
  if (!window.GLightbox) return;

  if (galeriaUnidades) {
    galeriaUnidades.destroy();
  }

  galeriaUnidades = GLightbox({
    selector: ".glightbox",
    touchNavigation: true,
    loop: true
  });
}

function renderizarFiltroCidades() {
  const select = document.querySelector("#cityFilter");
  if (!select) return;

  const cidades = [...new Set(unidades.map((unidade) => unidade.cidade))].sort();
  select.innerHTML = [
    '<option value="Todas">Todas as cidades</option>',
    ...cidades.map((cidade) => `<option value="${escaparHtml(cidade)}">${escaparHtml(cidade)}</option>`)
  ].join("");
}

function atualizarContadorUnidades() {
  const contador = document.querySelector("#unitCount");
  const vazio = document.querySelector("#emptyState");
  const cardsVisiveis = document.querySelectorAll(".unit-card:not(.is-hidden)").length;
  if (!contador) return;

  contador.textContent =
    cardsVisiveis === 1
      ? "1 unidade exibida"
      : `${cardsVisiveis} unidades exibidas`;

  if (vazio) {
    vazio.hidden = cardsVisiveis !== 0;
  }
}

function obterFiltrosUnidades() {
  return {
    tipo: document.querySelector("#typeFilter")?.value || "Todas",
    cidade: document.querySelector("#cityFilter")?.value || "Todas"
  };
}

function aplicarFiltroUnidades() {
  const { tipo, cidade } = obterFiltrosUnidades();
  const cards = document.querySelectorAll(".unit-card");

  cards.forEach((card) => {
    const correspondeModalidade =
      tipo === "Todas" || card.dataset.modalidade === tipo;
    const correspondeCidade =
      cidade === "Todas" || card.dataset.cidade === cidade;
    const visivel = correspondeModalidade && correspondeCidade;

    card.hidden = !visivel;
    card.classList.toggle("is-hidden", !visivel);
    card.setAttribute("aria-hidden", String(!visivel));
  });

  atualizarContadorUnidades();

  if (window.AOS) {
    window.AOS.refresh();
  }
}

function inicializarFiltros() {
  renderizarFiltroCidades();

  const controles = [
    document.querySelector("#typeFilter"),
    document.querySelector("#cityFilter")
  ].filter(Boolean);

  controles.forEach((controle) => {
    ["input", "change"].forEach((evento) => {
      controle.addEventListener(evento, aplicarFiltroUnidades);
    });
  });

  document.querySelector("[data-clear-filters]")?.addEventListener("click", () => {
    const tipo = document.querySelector("#typeFilter");
    const cidade = document.querySelector("#cityFilter");

    if (tipo) tipo.value = "Todas";
    if (cidade) cidade.value = "Todas";
    aplicarFiltroUnidades();
  });
}

function enviarFormularioWhatsapp(event) {
  event.preventDefault();

  const nome = document.querySelector("#nome").value.trim();
  const telefone = document.querySelector("#telefone").value.trim();
  const cidade = document.querySelector("#cidade").value.trim();
  const tipoUnidade = document.querySelector("#tipoUnidade").value;
  const mensagem = document.querySelector("#mensagem").value.trim();

  if (!nome || !telefone || !cidade) {
    alert("Preencha nome, telefone e cidade antes de enviar.");
    return;
  }

  const texto = `
Olá, ${NOME_ATENDENTE}. Vim pelo site do Grupo Araújo Tratamentos.

Nome: ${nome}
Telefone: ${telefone}
Cidade: ${cidade}
Tipo de unidade desejada: ${tipoUnidade}

Mensagem:
${mensagem || "Sem mensagem adicional."}

Gostaria de orientação sobre encaminhamento para clínica de reabilitação.
`;

  window.open(gerarLinkWhatsappPadrao(texto), "_blank", "noopener");
}

function inicializarFormulario() {
  document
    .querySelector("[data-contact-form]")
    ?.addEventListener("submit", enviarFormularioWhatsapp);
}

function inicializarAOS() {
  if (!window.AOS) return;

  AOS.init({
    duration: 650,
    easing: "ease-out",
    once: true,
    offset: 80
  });
}

function inicializarParallax() {
  const secoes = document.querySelectorAll("[data-parallax]");
  const movimentoReduzido = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!secoes.length || movimentoReduzido) return;

  let agendado = false;

  const atualizar = () => {
    const mobile = window.matchMedia("(max-width: 767px)").matches;
    const intensidade = mobile ? -0.035 : -0.06;
    const limite = mobile ? 28 : 64;

    secoes.forEach((secao) => {
      const rect = secao.getBoundingClientRect();
      const distanciaCentro = rect.top + rect.height / 2 - window.innerHeight / 2;
      const deslocamento = Math.max(Math.min(distanciaCentro * intensidade, limite), -limite);
      secao.style.setProperty("--parallax-y", `${deslocamento}px`);
    });
    agendado = false;
  };

  const aoRolar = () => {
    if (!agendado) {
      window.requestAnimationFrame(atualizar);
      agendado = true;
    }
  };

  window.addEventListener("scroll", aoRolar, { passive: true });
  window.addEventListener("resize", aoRolar);
  atualizar();
}

function inicializarMenuMobile() {
  const botao = document.querySelector("[data-nav-toggle]");
  const menu = document.querySelector("[data-nav-menu]");
  const header = document.querySelector("[data-header]");

  if (!botao || !menu) return;

  botao.addEventListener("click", () => {
    const aberto = botao.getAttribute("aria-expanded") === "true";
    botao.setAttribute("aria-expanded", String(!aberto));
    menu.classList.toggle("is-open", !aberto);
    document.body.classList.toggle("nav-open", !aberto);
  });

  menu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      botao.setAttribute("aria-expanded", "false");
      menu.classList.remove("is-open");
      document.body.classList.remove("nav-open");
    });
  });

  const atualizarHeader = () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 12);
  };

  window.addEventListener("scroll", atualizarHeader, { passive: true });
  atualizarHeader();
}

function inicializarFAQ() {
  document.querySelectorAll(".faq-item button").forEach((botao) => {
    botao.addEventListener("click", () => {
      const resposta = document.querySelector(`#${botao.getAttribute("aria-controls")}`);
      const aberto = botao.getAttribute("aria-expanded") === "true";

      botao.setAttribute("aria-expanded", String(!aberto));
      if (resposta) {
        resposta.hidden = aberto;
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  definirLinksWhatsapp();
  inicializarAOS();
  inicializarFiltros();
  renderizarUnidades();
  inicializarFormulario();
  inicializarParallax();
  inicializarMenuMobile();
  inicializarFAQ();

  if (window.lucide) {
    window.lucide.createIcons();
  }
});
