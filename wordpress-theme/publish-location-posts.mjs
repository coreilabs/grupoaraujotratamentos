import { readFileSync } from 'node:fs';
import { basename, join } from 'node:path';

const siteUrl = 'https://grupoaraujotratamentos.com.br';
const xmlrpcUrl = `${siteUrl}/xmlrpc.php`;
const root = process.cwd();
const imageDir = join(root, 'generated-location-images');

function credentials() {
  const lines = readFileSync(join(root, '..', 'credenciais de acesso.md'), 'utf8').split(/\r?\n/);
  const wp = lines.findIndex((line) => line.startsWith('Acesso wordpress:'));
  const section = lines.slice(wp + 1, lines.findIndex((line, index) => index > wp && line.startsWith('ftp:')));
  const username = section.find((line) => /^usu/i.test(line))?.split(/:(.+)/)[1]?.trim();
  const password = section.find((line) => /^senha:/i.test(line))?.split(/:(.+)/)[1]?.trim();
  if (!username || !password) throw new Error('Credenciais WordPress não encontradas.');
  return { username, password };
}

const esc = (value) => String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
function xmlValue(value) {
  if (typeof value === 'number') return `<value><int>${value}</int></value>`;
  if (value?.__base64) return `<value><base64>${value.__base64}</base64></value>`;
  if (Array.isArray(value)) return `<value><array><data>${value.map(xmlValue).join('')}</data></array></value>`;
  if (value && typeof value === 'object') return `<value><struct>${Object.entries(value).map(([key, val]) => `<member><name>${esc(key)}</name>${xmlValue(val)}</member>`).join('')}</struct></value>`;
  return `<value><string>${esc(value ?? '')}</string></value>`;
}
async function rpc(method, params) {
  const body = `<?xml version="1.0"?><methodCall><methodName>${method}</methodName><params>${params.map((p) => `<param>${xmlValue(p)}</param>`).join('')}</params></methodCall>`;
  const response = await fetch(xmlrpcUrl, { method: 'POST', headers: { 'Content-Type': 'text/xml' }, body });
  const text = await response.text();
  if (!response.ok || text.includes('<fault>')) throw new Error(`${method}: ${text.slice(0, 700)}`);
  return text;
}
const resultId = (text) => Number(text.match(/<(?:int|string)>(\d+)<\/(?:int|string)>/)?.[1]);

const locations = [
  { city: 'Goiânia', state: 'Goiás', uf: 'GO', category: 'Goiás', slug: 'clinica-de-recuperacao-em-goiania-goias', image: 'clinica-recuperacao-goiania-goias.png', nearby: 'Aparecida de Goiânia, Senador Canedo, Trindade e região metropolitana' },
  { city: 'Anápolis', state: 'Goiás', uf: 'GO', category: 'Goiás', slug: 'clinica-de-recuperacao-em-anapolis-goias', image: 'clinica-recuperacao-anapolis-goias.png', nearby: 'Goianápolis, Nerópolis, Campo Limpo de Goiás e municípios próximos' },
  { city: 'Brasília', state: 'Distrito Federal', uf: 'DF', category: 'Distrito Federal', slug: 'clinica-de-recuperacao-em-brasilia-df', image: 'clinica-recuperacao-brasilia-df.png', nearby: 'Taguatinga, Ceilândia, Águas Claras, Gama e demais regiões do Distrito Federal' },
  { city: 'Cuiabá', state: 'Mato Grosso', uf: 'MT', category: 'Mato Grosso', slug: 'clinica-de-recuperacao-em-cuiaba-mato-grosso', image: 'clinica-recuperacao-cuiaba-mato-grosso.png', nearby: 'Chapada dos Guimarães, Santo Antônio de Leverger e região metropolitana' },
  { city: 'Várzea Grande', state: 'Mato Grosso', uf: 'MT', category: 'Mato Grosso', slug: 'clinica-de-recuperacao-em-varzea-grande-mato-grosso', image: 'clinica-recuperacao-varzea-grande-mato-grosso.png', nearby: 'Cuiabá, Nossa Senhora do Livramento, Poconé e municípios próximos' },
];

function faqs(place) {
  return [
    { question: `Como escolher uma clínica de recuperação em ${place.city}?`, answer: 'Verifique regularidade, equipe, plano terapêutico individual, participação familiar, estrutura, regras de visita e planejamento de continuidade após a alta.' },
    { question: 'Quando a internação pode ser indicada?', answer: 'A indicação depende de avaliação individual. Ela pode ser considerada quando há perda de controle, riscos à saúde, recaídas frequentes ou dificuldade de manter o cuidado fora de um ambiente protegido.' },
    { question: 'A família participa do tratamento?', answer: 'A participação familiar costuma ser importante para melhorar a comunicação, estabelecer limites e preparar uma rede de apoio mais segura para a continuidade da recuperação.' },
    { question: `O atendimento contempla moradores de ${place.nearby}?`, answer: `A orientação inicial pode atender famílias de ${place.city}, ${place.nearby}, avaliando disponibilidade e a alternativa de cuidado adequada para cada caso.` },
  ];
}

function content(place) {
  const faq = faqs(place);
  return `
<p>Buscar uma <strong>clínica de recuperação em ${place.city}</strong> é uma decisão importante para famílias que convivem com dependência química ou alcoolismo. O atendimento adequado começa com escuta responsável e avaliação individual, porque cada pessoa apresenta histórico, riscos, necessidades clínicas e uma rede familiar diferente.</p>
<p>O Grupo Araújo Tratamentos orienta famílias de ${place.city}, ${place.nearby}. O objetivo do primeiro contato é compreender o momento vivido e explicar, com clareza, as possibilidades de cuidado disponíveis.</p>
<h2>Como funciona o tratamento da dependência química</h2>
<p>Um plano de recuperação consistente não se limita a interromper o uso de álcool ou outras drogas. Ele deve trabalhar rotina, comportamento, saúde emocional, vínculos familiares e prevenção de recaídas. Conforme a avaliação, o cuidado pode incluir acolhimento, acompanhamento multiprofissional, atividades terapêuticas, grupos, orientação familiar e planejamento para a continuidade após a alta.</p>
<p>Também é necessário observar possíveis condições associadas, como ansiedade, depressão, crises de abstinência ou outros problemas de saúde. A modalidade e a intensidade do atendimento devem ser definidas com responsabilidade, respeitando a situação concreta da pessoa.</p>
<h2>Quando procurar uma clínica de recuperação</h2>
<p>Alguns sinais indicam que é hora de buscar orientação: perda de controle sobre o consumo, tentativas frustradas de parar, conflitos familiares, faltas no trabalho, dívidas, isolamento, mudanças bruscas de comportamento e exposição a situações perigosas. Em casos de intoxicação grave, convulsão, falta de ar, dor no peito, surto ou risco de violência, procure imediatamente um serviço de urgência.</p>
<p>Para entender melhor as modalidades possíveis, consulte o artigo sobre <a href="${siteUrl}/internacao-voluntaria-involuntaria-compulsoria-diferencas/">internação voluntária, involuntária e compulsória</a>. A decisão deve se basear em avaliação e nos critérios legais aplicáveis, nunca em promessas de solução imediata.</p>
<h2>O que avaliar antes de escolher</h2>
<ul><li>regularidade e transparência da instituição;</li><li>qualificação da equipe e avaliação inicial;</li><li>plano terapêutico individualizado;</li><li>rotina, estrutura, segurança e regras de visita;</li><li>participação e orientação da família;</li><li>estratégia de alta e prevenção de recaídas.</li></ul>
<p>Desconfie de garantias de cura, prazos iguais para todos ou explicações pouco claras. O tempo de cuidado varia conforme a gravidade, a substância utilizada, o estado de saúde e a evolução durante o processo. Veja também <a href="${siteUrl}/quanto-tempo-dura-tratamento-dependencia-quimica/">quanto tempo pode durar o tratamento</a>.</p>
<h2>A importância da família na recuperação</h2>
<p>A dependência afeta toda a casa. A família precisa aprender a acolher sem encobrir consequências, estabelecer limites e reconhecer sinais de risco. Orientação adequada reduz decisões tomadas no desespero e ajuda a construir um ambiente mais estável. Saiba mais sobre <a href="${siteUrl}/papel-da-familia-na-recuperacao-do-dependente-quimico/">o papel da família na recuperação</a>.</p>
<h2>Atendimento para ${place.city} e região</h2>
<p>Famílias de ${place.city} e de ${place.nearby} podem solicitar uma conversa inicial para apresentar o caso. A equipe avalia as informações, esclarece dúvidas sobre o processo e orienta o próximo passo possível, sempre considerando segurança e necessidade individual.</p>
<p>Depois da fase intensiva, a continuidade é decisiva. Rotina estruturada, acompanhamento, grupos de apoio e participação familiar ajudam a reduzir riscos. Confira as orientações sobre <a href="${siteUrl}/como-prevenir-recaidas-apos-o-tratamento/">prevenção de recaídas após o tratamento</a>.</p>
<h2>Perguntas frequentes</h2>
${faq.map((item) => `<h3>${item.question}</h3><p>${item.answer}</p>`).join('\n')}
<p>Se sua família procura orientação sobre clínica de recuperação em ${place.city}, entre em contato com o Grupo Araújo Tratamentos para uma avaliação inicial e informações sobre atendimento.</p>`;
}

const auth = credentials();
for (const place of locations) {
  const existing = await fetch(`${siteUrl}/wp-json/wp/v2/posts?slug=${place.slug}&_fields=id,link`).then((r) => r.json());
  if (existing[0]) { console.log(`existing ${existing[0].id} ${existing[0].link}`); continue; }
  const title = `Clínica de recuperação em ${place.city} (${place.uf}): tratamento e acolhimento`;
  const excerpt = `Encontre orientação sobre clínica de recuperação em ${place.city}, ${place.state}: tratamento para dependência química, apoio familiar e atendimento responsável.`;
  const upload = await rpc('wp.uploadFile', [1, auth.username, auth.password, { name: basename(place.image), type: 'image/png', bits: { __base64: readFileSync(join(imageDir, place.image)).toString('base64') }, overwrite: false }]);
  const mediaId = resultId(upload);
  if (!mediaId) throw new Error(`Falha no upload de ${place.image}`);
  await rpc('wp.editPost', [1, auth.username, auth.password, mediaId, { post_title: `Mapa de ${place.state} - clínica de recuperação em ${place.city}`, post_excerpt: `Mapa de ${place.state} com a sigla ${place.uf}.`, custom_fields: [{ key: '_wp_attachment_image_alt', value: `Mapa de ${place.state} - atendimento em ${place.city}` }] }]);
  const created = await rpc('wp.newPost', [1, auth.username, auth.password, {
    post_type: 'post', post_status: 'publish', post_title: title, post_name: place.slug,
    post_excerpt: excerpt, post_content: content(place), post_thumbnail: mediaId,
    terms_names: { category: [place.category], post_tag: ['clínica de recuperação', 'dependência química', 'tratamento', place.city, place.state] },
    custom_fields: [
      { key: '_yoast_wpseo_title', value: title }, { key: '_yoast_wpseo_metadesc', value: excerpt },
      { key: '_gat_service_area', value: `${place.city}, ${place.state}` }, { key: '_gat_faq_json', value: JSON.stringify(faqs(place)) },
    ],
  }]);
  const postId = resultId(created);
  console.log(`published ${postId} ${siteUrl}/${place.slug}/`);
}
