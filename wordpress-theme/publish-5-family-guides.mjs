import { readFileSync } from 'node:fs';
import { basename, join } from 'node:path';

const siteUrl = 'https://grupoaraujotratamentos.com.br';
const xmlrpcUrl = `${siteUrl}/xmlrpc.php`;
const root = process.cwd();
const imageDir = join(root, 'generated-blog-images-2026-07');
const credentialsPath = join(root, '..', 'credenciais de acesso.md');

function readCredentials() {
  const lines = readFileSync(credentialsPath, 'utf8').split(/\r?\n/);
  const wpStart = lines.findIndex((line) => line.startsWith('Acesso wordpress:'));
  const wpLines = lines.slice(wpStart + 1);
  const username = wpLines.find((line) => line.startsWith('usu'))?.split(/:(.+)/)[1]?.trim();
  const password = wpLines.find((line) => line.startsWith('senha:'))?.split(/:(.+)/)[1]?.trim();

  if (!username || !password) {
    throw new Error('Credenciais do WordPress não encontradas.');
  }

  return { username, password };
}

function escapeXml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

function xmlValue(value) {
  if (typeof value === 'number') return `<value><int>${value}</int></value>`;
  if (typeof value === 'boolean') return `<value><boolean>${value ? 1 : 0}</boolean></value>`;
  if (value && value.__base64) return `<value><base64>${value.__base64}</base64></value>`;
  if (Array.isArray(value)) {
    return `<value><array><data>${value.map(xmlValue).join('')}</data></array></value>`;
  }
  if (value && typeof value === 'object') {
    return `<value><struct>${Object.entries(value).map(([key, val]) => (
      `<member><name>${escapeXml(key)}</name>${xmlValue(val)}</member>`
    )).join('')}</struct></value>`;
  }
  return `<value><string>${escapeXml(value ?? '')}</string></value>`;
}

async function xmlrpc(methodName, params) {
  const body = `<?xml version="1.0"?><methodCall><methodName>${methodName}</methodName><params>${params.map((param) => `<param>${xmlValue(param)}</param>`).join('')}</params></methodCall>`;
  const response = await fetch(xmlrpcUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'text/xml' },
    body,
  });
  const text = await response.text();

  if (!response.ok || text.includes('<fault>')) {
    throw new Error(`XML-RPC ${methodName} falhou: ${text.slice(0, 800)}`);
  }

  return text;
}

function member(text, name) {
  const match = text.match(new RegExp(`<member>\\s*<name>${name}</name>\\s*<value>([\\s\\S]*?)</value>\\s*</member>`));
  return match?.[1]?.replace(/<[^>]+>/g, '').trim() || '';
}

function resultId(text) {
  return Number(text.match(/<int>(\d+)<\/int>|<string>(\d+)<\/string>/)?.slice(1).find(Boolean));
}

function contentWithFaqSchema(post) {
  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    '@id': `${siteUrl}/${post.slug}/#faq`,
    mainEntity: post.faq.map(({ question, answer }) => ({
      '@type': 'Question',
      name: question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: answer,
      },
    })),
  };
  return `${post.content}
<script type="application/ld+json">${JSON.stringify(faqSchema)}</script>`;
}

async function findPost(slug) {
  const response = await fetch(`${siteUrl}/wp-json/wp/v2/posts?slug=${encodeURIComponent(slug)}&_fields=id,link,status`);
  if (!response.ok) throw new Error(`Não foi possível consultar o slug ${slug}.`);
  const posts = await response.json();
  return posts[0] || null;
}

async function uploadImage(credentials, post) {
  const filePath = join(imageDir, post.image);
  const bits = readFileSync(filePath).toString('base64');
  const mimeType = post.image.endsWith('.webp') ? 'image/webp' : 'image/png';
  const result = await xmlrpc('wp.uploadFile', [
    1,
    credentials.username,
    credentials.password,
    {
      name: basename(filePath),
      type: mimeType,
      bits: { __base64: bits },
      overwrite: false,
      title: post.imageTitle,
      caption: post.imageCaption,
      description: post.imageDescription,
    },
  ]);
  const mediaId = Number(member(result, 'id'));
  if (!mediaId) throw new Error(`Upload sem ID para ${post.image}.`);

  await xmlrpc('wp.editPost', [
    1,
    credentials.username,
    credentials.password,
    mediaId,
    {
      post_title: post.imageTitle,
      post_excerpt: post.imageCaption,
      post_content: post.imageDescription,
      custom_fields: [
        { key: '_wp_attachment_image_alt', value: post.imageAlt },
      ],
    },
  ]);

  return mediaId;
}

async function createPost(credentials, post, mediaId) {
  const result = await xmlrpc('wp.newPost', [
    1,
    credentials.username,
    credentials.password,
    {
      post_type: 'post',
      post_status: 'publish',
      post_title: post.title,
      post_name: post.slug,
      post_excerpt: post.excerpt,
      post_content: contentWithFaqSchema(post),
      post_thumbnail: mediaId,
      comment_status: 'closed',
      terms_names: {
        category: [post.category],
        post_tag: post.tags,
      },
      custom_fields: [
        { key: '_yoast_wpseo_title', value: post.seoTitle },
        { key: '_yoast_wpseo_metadesc', value: post.metaDescription },
        { key: '_gat_faq_json', value: JSON.stringify(post.faq) },
      ],
    },
  ]);
  const postId = resultId(result);
  if (!postId) throw new Error(`Criação sem ID para ${post.slug}.`);
  return postId;
}

async function updatePost(credentials, post, postId) {
  await xmlrpc('wp.editPost', [
    1,
    credentials.username,
    credentials.password,
    postId,
    {
      post_title: post.title,
      post_name: post.slug,
      post_excerpt: post.excerpt,
      post_content: contentWithFaqSchema(post),
      comment_status: 'closed',
      terms_names: {
        category: [post.category],
        post_tag: post.tags,
      },
      custom_fields: [
        { key: '_yoast_wpseo_title', value: post.seoTitle },
        { key: '_yoast_wpseo_metadesc', value: post.metaDescription },
        { key: '_gat_faq_json', value: JSON.stringify(post.faq) },
      ],
    },
  ]);
}

const posts = [
  {
    title: 'Como ajudar um dependente químico',
    seoTitle: 'Como ajudar um dependente químico: guia para a família',
    slug: 'como-ajudar-dependente-quimico-sem-se-anular',
    category: 'Dependência Química',
    image: 'como-ajudar-dependente-quimico.webp',
    imageTitle: 'Apoio familiar a uma pessoa com dependência química',
    imageAlt: 'Familiar acolhe um adulto que precisa de ajuda para dependência química',
    imageCaption: 'Acolhimento, limites e orientação profissional ajudam a família a agir com mais segurança.',
    imageDescription: 'Imagem editorial exclusiva criada por inteligência artificial nas cores do Grupo Araújo Tratamentos, sem texto.',
    excerpt: 'Saiba como apoiar um dependente químico com diálogo, limites e ajuda profissional, sem encobrir consequências nem abandonar o próprio cuidado.',
    metaDescription: 'Saiba como ajudar um dependente químico com diálogo, limites e apoio profissional, sem encobrir consequências nem se anular.',
    tags: ['dependência química', 'ajuda familiar', 'tratamento', 'limites', 'acolhimento'],
    faq: [
      {
        question: 'O que fazer primeiro para ajudar um dependente químico?',
        answer: 'Observe os riscos, escolha um momento de sobriedade para conversar e proponha um primeiro passo concreto, como uma avaliação profissional. Em urgências, acione o SAMU pelo 192.',
      },
      {
        question: 'Ajudar significa pagar dívidas e encobrir faltas?',
        answer: 'Não. Resolver repetidamente as consequências do uso pode manter o ciclo. Apoio saudável combina respeito, limites claros e acesso a tratamento.',
      },
      {
        question: 'É possível ajudar quem ainda nega o problema?',
        answer: 'Sim. A família pode buscar orientação, organizar fatos, mudar a própria forma de agir e oferecer opções de avaliação, mesmo que a aceitação não seja imediata.',
      },
      {
        question: 'Quando procurar atendimento de urgência?',
        answer: 'Em caso de inconsciência, dificuldade para respirar, convulsão, dor no peito, intoxicação grave, tentativa de suicídio ou risco imediato de violência, ligue 192.',
      },
    ],
    content: `
<p><strong>Para ajudar um dependente químico, combine acolhimento, limites consistentes e orientação profissional.</strong> A família não consegue controlar as escolhas de outra pessoa, mas pode interromper atitudes que mantêm o ciclo, reconhecer urgências e facilitar o acesso ao cuidado.</p>
<p>A dependência química não se resume à quantidade consumida. Ela envolve perda de controle, prioridade crescente dada à substância e continuidade do uso apesar dos prejuízos. Por isso, broncas, promessas e vigilância permanente raramente resolvem sozinhas.</p>

<h2>1. Verifique se existe risco imediato</h2>
<p>Antes de planejar uma conversa, observe a segurança. Inconsciência, respiração lenta ou difícil, lábios arroxeados, convulsão, confusão intensa, dor no peito, tentativa de suicídio ou ameaça concreta de violência exigem atendimento de urgência. Nesses casos, ligue para o <strong>SAMU 192</strong>, siga as orientações e não deixe a pessoa sozinha se isso puder ser feito com segurança.</p>
<p>Não ofereça café, banho frio, medicamentos ou qualquer “receita caseira” para reverter uma intoxicação. Também não provoque vômito. Informe à equipe, se souber, quais substâncias foram usadas, em que horário e em qual quantidade aproximada.</p>

<h2>2. Escolha um momento em que a pessoa esteja sóbria</h2>
<p>Conversar durante intoxicação, abstinência intensa ou uma briga aumenta a chance de defesa e escalada. Espere um momento mais estável, escolha um lugar privado e limite o número de participantes. Uma pessoa calma e preparada costuma ser mais eficaz do que vários familiares falando ao mesmo tempo.</p>
<p>Comece por vínculo e fatos: “Eu me importo com você e fiquei preocupado quando você faltou ao trabalho três vezes e dirigiu depois de usar”. Isso é mais útil do que rótulos como “irresponsável” ou “sem força de vontade”.</p>

<h2>3. Fale do que você observou, não do que imagina</h2>
<p>Organize exemplos verificáveis: mudanças de rotina, problemas de saúde, acidentes, dívidas, faltas, isolamento ou tentativas frustradas de parar. Evite investigar cada detalhe ou exigir uma confissão. O objetivo é mostrar que existe um problema que merece avaliação, não vencer uma discussão.</p>
<p>Faça perguntas abertas: “O que mudou no seu uso nos últimos meses?”, “O que mais tem preocupado você?” e “Que ajuda você aceitaria conhecer?”. Escutar não significa concordar com tudo; significa entender onde existe alguma abertura para mudança.</p>

<h2>4. Ofereça um próximo passo concreto</h2>
<p>“Procure ajuda” é um pedido amplo. Facilite uma ação possível: telefonar para uma equipe, marcar avaliação, ir a um CAPS AD, conversar com médico ou psicólogo, ou conhecer modalidades de cuidado. A Rede de Atenção Psicossocial do SUS inclui serviços voltados a necessidades relacionadas ao uso prejudicial de álcool e outras drogas.</p>
<p>Nem toda pessoa precisa da mesma modalidade. O plano pode envolver atendimento ambulatorial, suporte médico e psicológico, participação familiar, grupos de apoio e, quando tecnicamente indicado, cuidado em ambiente protegido. A avaliação deve considerar riscos, saúde física e mental, histórico e rede de apoio.</p>

<h2>5. Estabeleça limites que você realmente consegue cumprir</h2>
<p>Limites protegem a família e tornam as consequências mais claras. Exemplos possíveis incluem não fornecer dinheiro sem finalidade verificável, não permitir direção após uso, não mentir para empregadores e não aceitar agressões dentro de casa. O limite deve descrever o que <em>você</em> fará: “Se houver ameaça, sairei do local e chamarei ajuda”.</p>
<p>Evite ameaças vagas ou impossíveis. Combine os limites entre os familiares principais para reduzir mensagens contraditórias. Segurança vem antes de confronto: se houver histórico de violência, prepare a conversa com orientação profissional.</p>

<h2>6. Apoie sem encobrir as consequências</h2>
<p>Pagar dívidas repetidamente, justificar faltas, assumir todas as responsabilidades ou entregar dinheiro para encerrar conflitos pode aliviar a crise do dia, mas também reduzir a percepção do problema. Ajudar é oferecer alimentação, transporte para atendimento, companhia em uma avaliação e apoio a decisões de recuperação — não sustentar o uso.</p>
<p>Essa diferença é difícil porque culpa e medo pesam sobre a família. O artigo sobre <a href="${siteUrl}/papel-da-familia-na-recuperacao-do-dependente-quimico/">o papel da família na recuperação</a> aprofunda como participar sem reforçar comportamentos de risco.</p>

<h2>7. Cuide da sua própria saúde</h2>
<p>Familiares podem desenvolver ansiedade, insônia, exaustão e isolamento. Procure psicoterapia, grupos de apoio ou orientação familiar. Preserve trabalho, descanso, finanças e relações importantes. Cuidar de si não é abandonar: é recuperar condições para tomar decisões mais seguras.</p>
<p>Também é aceitável reconhecer limites. Você pode apoiar o tratamento sem permanecer em situações de violência, coerção financeira ou risco para crianças e idosos.</p>

<h2>O que não fazer</h2>
<ul>
  <li>não discutir enquanto a pessoa estiver intoxicada;</li>
  <li>não humilhar, filmar ou expor a situação;</li>
  <li>não fazer ameaças que a família não sustentará;</li>
  <li>não dar medicamentos por conta própria;</li>
  <li>não confundir recaída com fracasso definitivo;</li>
  <li>não esperar uma catástrofe para pedir orientação.</li>
</ul>

<h2>Como o Grupo Araújo pode orientar a família</h2>
<p>O Grupo Araújo Tratamentos orienta famílias e avalia possibilidades de encaminhamento conforme o perfil e a segurança de cada caso. Há unidades parceiras masculinas e femininas em <strong>Goiânia, Aparecida de Goiânia, Caturaí e Senador Canedo</strong>, em Goiás, sujeitas a avaliação e disponibilidade. O primeiro contato serve para organizar informações e entender o próximo passo; não substitui atendimento de urgência.</p>

<h2>Perguntas frequentes</h2>
<h3>O que fazer primeiro para ajudar um dependente químico?</h3>
<p>Observe os riscos, escolha um momento de sobriedade para conversar e proponha um primeiro passo concreto, como uma avaliação profissional. Em urgências, acione o SAMU pelo 192.</p>
<h3>Ajudar significa pagar dívidas e encobrir faltas?</h3>
<p>Não. Resolver repetidamente as consequências do uso pode manter o ciclo. Apoio saudável combina respeito, limites claros e acesso a tratamento.</p>
<h3>É possível ajudar quem ainda nega o problema?</h3>
<p>Sim. A família pode buscar orientação, organizar fatos, mudar a própria forma de agir e oferecer opções de avaliação, mesmo que a aceitação não seja imediata.</p>
<h3>Quando procurar atendimento de urgência?</h3>
<p>Em caso de inconsciência, dificuldade para respirar, convulsão, dor no peito, intoxicação grave, tentativa de suicídio ou risco imediato de violência, ligue 192.</p>

<h2>Fontes e revisão</h2>
<p>Conteúdo educativo baseado em orientações da <a href="https://www.gov.br/saude/pt-br/composicao/saes/desmad/desmad" target="_blank" rel="noopener">Rede de Atenção Psicossocial do Ministério da Saúde</a> e do <a href="https://www.gov.br/saude/pt-br/composicao/saes/samu-192" target="_blank" rel="noopener">SAMU 192</a>. Não substitui avaliação médica, psicológica ou de urgência.</p>`,
  },
  {
    title: 'Meu filho usa drogas: o que fazer?',
    seoTitle: 'Meu filho usa drogas: o que fazer? Passos para os pais',
    slug: 'meu-filho-usa-drogas-como-agir',
    category: 'Família',
    image: 'meu-filho-usa-drogas-o-que-fazer.webp',
    imageTitle: 'Conversa entre mãe e filho sobre uso de drogas',
    imageAlt: 'Mãe conversa com o filho adolescente com calma e preocupação',
    imageCaption: 'Uma conversa segura começa com fatos, escuta e um plano de cuidado.',
    imageDescription: 'Imagem editorial exclusiva criada por inteligência artificial nas cores do Grupo Araújo Tratamentos, sem texto.',
    excerpt: 'Descobriu ou suspeita que seu filho usa drogas? Veja como conversar, avaliar riscos, buscar ajuda e proteger o vínculo sem ignorar o problema.',
    metaDescription: 'Seu filho usa drogas? Saiba como conversar, avaliar riscos, buscar ajuda profissional e proteger o vínculo sem ignorar o problema.',
    tags: ['filho usa drogas', 'adolescência', 'família', 'prevenção', 'tratamento'],
    faq: [
      {
        question: 'Como conversar com meu filho se suspeito que ele usa drogas?',
        answer: 'Escolha um momento de calma, descreva fatos observados, faça perguntas abertas e escute antes de definir consequências. Evite interrogatório, humilhação e conversa durante intoxicação.',
      },
      {
        question: 'Devo fazer um teste toxicológico escondido?',
        answer: 'Testes não devem substituir avaliação clínica e vínculo. Em geral, a decisão precisa ter finalidade de cuidado, consentimento e orientação profissional, respeitando idade, contexto e direitos.',
      },
      {
        question: 'Todo adolescente que experimenta drogas é dependente?',
        answer: 'Não. Experimentação não confirma dependência, mas qualquer uso por adolescentes merece atenção porque pode trazer riscos e indicar outras dificuldades. A avaliação considera padrão, consequências e contexto.',
      },
      {
        question: 'Quando o uso de drogas por um filho é uma emergência?',
        answer: 'Inconsciência, dificuldade respiratória, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem o SAMU 192.',
      },
    ],
    content: `
<p><strong>Se você descobriu ou suspeita que seu filho usa drogas, primeiro proteja a segurança e depois converse com calma.</strong> Uma única mudança de comportamento não prova consumo, e experimentar uma substância não confirma dependência. Ainda assim, o uso na adolescência merece atenção e uma avaliação responsável.</p>
<p>O objetivo não é arrancar uma confissão. É entender o que está acontecendo, reduzir riscos e conectar o jovem a cuidado adequado, preservando o vínculo necessário para que ele aceite ajuda.</p>

<h2>Se houver intoxicação ou risco, aja primeiro</h2>
<p>Verifique consciência e respiração. Ligue para o <strong>SAMU 192</strong> diante de desmaio, dificuldade para respirar, lábios arroxeados, convulsão, dor no peito, confusão intensa, agitação com risco, tentativa de suicídio ou suspeita de intoxicação grave. Diga o que foi usado, se souber, e siga as orientações.</p>
<p>Não provoque vômito, não dê medicamentos e não tente “acordar” a pessoa com café ou banho frio. Se estiver inconsciente, mas respirando, mantenha-a de lado enquanto aguarda orientação, desde que seja seguro movimentá-la.</p>

<h2>Confirme fatos sem transformar a casa em investigação</h2>
<p>Mudanças de sono, queda de rendimento, novos conflitos, gastos sem explicação, isolamento ou olhos vermelhos têm muitas causas possíveis. Registre o que foi observado e em quais datas, sem invadir conversas, expor o jovem ou acusá-lo com base em um único sinal.</p>
<p>Se você encontrou uma substância ou recebeu informação concreta, guarde o foco na segurança. Evite publicar, fotografar para constranger ou ameaçar chamar várias pessoas. Em situações de risco físico, peça orientação profissional.</p>

<h2>Como iniciar a conversa</h2>
<p>Escolha um momento em que todos estejam sóbrios e relativamente calmos. Comece com uma frase direta: “Eu encontrei isto e estou preocupado com sua segurança. Quero entender o que aconteceu”. Use perguntas abertas:</p>
<ul>
  <li>O que você usou e com que frequência?</li>
  <li>Você já tentou parar ou reduzir?</li>
  <li>Já passou mal, apagou ou se colocou em risco?</li>
  <li>Está usando para lidar com ansiedade, tristeza, sono ou pressão social?</li>
  <li>Existe dívida, ameaça, violência ou coerção relacionada ao uso?</li>
</ul>
<p>Escute a resposta inteira. Corrigir cada frase imediatamente pode encerrar a conversa. Depois, resuma o que entendeu e explique quais medidas serão tomadas.</p>

<h2>Evite dois extremos: minimizar e punir sem plano</h2>
<p>Dizer “é só uma fase” pode atrasar ajuda. No outro extremo, retirar tudo, ameaçar expulsão ou humilhar sem avaliar o contexto pode aumentar isolamento e risco. Consequências precisam ser proporcionais, relacionadas à segurança e acompanhadas de um plano.</p>
<p>É razoável suspender acesso a carro se houve direção após uso, controlar dinheiro quando existe risco de compra e revisar horários. Explique o motivo, o prazo e o que precisa acontecer para reavaliar o limite.</p>

<h2>Procure uma avaliação completa</h2>
<p>A avaliação deve investigar substâncias, frequência, quantidade, prejuízos, abstinência, saúde física, ansiedade, depressão, trauma, TDAH, risco de suicídio e ambiente familiar. Para adolescentes, o cuidado deve ser apropriado à idade e envolver responsáveis sem eliminar todo espaço de confidencialidade clínica.</p>
<p>Você pode procurar a atenção básica, pediatra ou médico de família, profissional de saúde mental e serviços da Rede de Atenção Psicossocial. CAPS e CAPS AD integram a rede pública; a disponibilidade e o fluxo variam por município.</p>

<h2>E o teste toxicológico?</h2>
<p>Um teste pode detectar algumas substâncias em determinadas janelas de tempo, mas não explica padrão, risco ou motivo do uso. Resultado negativo não exclui todos os consumos; resultado positivo não define sozinho dependência.</p>
<p>Evite testes secretos ou usados como armadilha. Quando houver indicação, faça com orientação profissional, finalidade clara e respeito aos direitos do adolescente. O exame deve apoiar uma avaliação, não substituir conversa e cuidado.</p>

<h2>Quando o filho é adulto</h2>
<p>Os pais continuam podendo expressar preocupação e estabelecer regras dentro da própria casa, mas não controlam decisões de um filho adulto. Foque em limites sobre dinheiro, convivência e segurança. Ofereça ajuda concreta para avaliação e não encubra consequências.</p>
<p>Se ele recusar, a família pode receber orientação mesmo assim. Aprender a conversar e alinhar limites prepara uma nova oportunidade. Veja também <a href="${siteUrl}/como-convencer-familiar-aceitar-tratamento/">como abordar um familiar que resiste ao tratamento</a>.</p>

<h2>Construa um plano para os próximos sete dias</h2>
<ol>
  <li>Reduza acesso a carro, armas, medicamentos e grandes quantias se houver risco.</li>
  <li>Marque uma avaliação de saúde.</li>
  <li>Defina um adulto de referência para acompanhar o caso.</li>
  <li>Comunique limites de forma simples e consistente.</li>
  <li>Observe sono, alimentação, faltas e mudanças emocionais.</li>
  <li>Proteja irmãos menores de conflitos e responsabilidades que não são deles.</li>
  <li>Marque uma revisão da conversa, em vez de tentar resolver tudo em uma noite.</li>
</ol>

<h2>Orientação para famílias em Goiás</h2>
<p>O Grupo Araújo Tratamentos oferece orientação inicial a famílias e encaminhamento conforme avaliação, perfil e disponibilidade, com unidades parceiras masculinas e femininas em <strong>Goiânia, Aparecida de Goiânia, Caturaí e Senador Canedo</strong>. Em adolescentes, a indicação precisa considerar idade, proteção integral, necessidades clínicas e participação dos responsáveis. Emergências devem ser atendidas pela rede de urgência.</p>

<h2>Perguntas frequentes</h2>
<h3>Como conversar com meu filho se suspeito que ele usa drogas?</h3>
<p>Escolha um momento de calma, descreva fatos observados, faça perguntas abertas e escute antes de definir consequências. Evite interrogatório, humilhação e conversa durante intoxicação.</p>
<h3>Devo fazer um teste toxicológico escondido?</h3>
<p>Testes não devem substituir avaliação clínica e vínculo. Em geral, a decisão precisa ter finalidade de cuidado, consentimento e orientação profissional, respeitando idade, contexto e direitos.</p>
<h3>Todo adolescente que experimenta drogas é dependente?</h3>
<p>Não. Experimentação não confirma dependência, mas qualquer uso por adolescentes merece atenção porque pode trazer riscos e indicar outras dificuldades. A avaliação considera padrão, consequências e contexto.</p>
<h3>Quando o uso de drogas por um filho é uma emergência?</h3>
<p>Inconsciência, dificuldade respiratória, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem o SAMU 192.</p>

<h2>Fontes e revisão</h2>
<p>Conteúdo educativo baseado nas informações do <a href="https://www.gov.br/saude/pt-br/assuntos/saude-brasil/glossario/substancias-psicoativas" target="_blank" rel="noopener">Ministério da Saúde sobre substâncias psicoativas</a>, da <a href="https://www.gov.br/saude/pt-br/composicao/saes/desmad/desmad" target="_blank" rel="noopener">Rede de Atenção Psicossocial</a> e do <a href="https://www.gov.br/saude/pt-br/composicao/saes/samu-192" target="_blank" rel="noopener">SAMU 192</a>. Não substitui avaliação profissional.</p>`,
  },
  {
    title: 'Como convencer alguém a aceitar tratamento?',
    seoTitle: 'Como convencer alguém a aceitar tratamento? Guia prático',
    slug: 'como-ajudar-alguem-aceitar-tratamento-dependencia',
    category: 'Tratamento',
    image: 'como-convencer-aceitar-tratamento.webp',
    imageTitle: 'Conversa familiar para aceitação de tratamento',
    imageAlt: 'Família e profissional conversam com adulto sobre aceitar tratamento',
    imageCaption: 'Uma abordagem preparada transforma confronto em um primeiro passo possível.',
    imageDescription: 'Imagem editorial exclusiva criada por inteligência artificial nas cores do Grupo Araújo Tratamentos, sem texto.',
    excerpt: 'Aprenda a preparar uma conversa, reduzir resistência e oferecer opções concretas para alguém aceitar avaliação e tratamento da dependência.',
    metaDescription: 'Veja como conversar, reduzir resistência e oferecer caminhos concretos para alguém aceitar avaliação e tratamento da dependência.',
    tags: ['aceitar tratamento', 'dependência química', 'intervenção familiar', 'motivação', 'família'],
    faq: [
      {
        question: 'Existe uma frase certa para convencer alguém a se tratar?',
        answer: 'Não existe frase garantida. A chance melhora quando a família fala em momento de sobriedade, usa fatos concretos, escuta a resistência e oferece um primeiro passo viável.',
      },
      {
        question: 'A família deve fazer uma intervenção surpresa?',
        answer: 'Abordagens com várias pessoas podem aumentar vergonha e defesa. Quando consideradas, precisam de planejamento e condução profissional, com segurança e sem humilhação.',
      },
      {
        question: 'O que fazer se a pessoa disser não?',
        answer: 'Encerre sem escalar o conflito, mantenha limites, registre a opção de ajuda e busque orientação familiar para preparar outra oportunidade. Em urgência, acione o SAMU 192.',
      },
      {
        question: 'A internação é a única forma de tratamento?',
        answer: 'Não. O cuidado deve ser definido por avaliação e pode incluir modalidades ambulatoriais, médicas, psicológicas e apoio familiar. A internação é excepcional e depende de critérios clínicos e legais.',
      },
    ],
    content: `
<p><strong>Você não pode garantir que outra pessoa aceite tratamento, mas pode aumentar a chance de adesão.</strong> O caminho mais eficaz costuma ser uma conversa preparada, em momento de sobriedade, baseada em fatos, escuta e uma proposta concreta de avaliação.</p>
<p>Resistência não significa apenas “teimosia”. Medo de abstinência, vergonha, negação, experiências ruins, receio de perder trabalho ou filhos e a própria ação da dependência podem pesar. Entender a objeção ajuda a responder melhor.</p>

<h2>Troque a meta “convencer de tudo” por “aceitar o próximo passo”</h2>
<p>Pedir que alguém aceite imediatamente meses de tratamento pode parecer impossível. Um objetivo menor reduz a barreira: conversar por telefone com uma equipe, fazer uma avaliação, conhecer opções ou permitir que a família apresente preocupações a um profissional.</p>
<p>O primeiro “sim” não precisa resolver o processo inteiro. Ele precisa abrir uma porta segura para uma decisão mais informada.</p>

<h2>Prepare fatos, participantes e limites</h2>
<p>Antes da conversa, anote três ou quatro episódios concretos: acidente, falta, dívida, problema de saúde, abandono de responsabilidade ou tentativa frustrada de parar. Separe fatos de suposições.</p>
<p>Escolha uma pessoa para conduzir. Participantes demais podem produzir sensação de cerco. Todos precisam combinar a mesma mensagem, evitar acusações antigas e saber quais limites sustentarão se a resposta for negativa.</p>

<h2>Escolha o momento e o ambiente</h2>
<p>Não faça a abordagem durante intoxicação, abstinência intensa, direção de veículo ou crise de agressividade. Prefira local privado, sem crianças presentes e com rota segura de saída. Se houver histórico de violência, peça orientação profissional antes e não organize confronto surpresa.</p>
<p>Converse quando houver tempo, mas mantenha foco. Uma reunião longa, com repetição e acusações, tende a aumentar a defesa.</p>

<h2>Use uma estrutura de conversa em cinco partes</h2>
<ol>
  <li><strong>Vínculo:</strong> “Eu me importo com você e quero falar porque sua segurança é importante”.</li>
  <li><strong>Fatos:</strong> “Nas últimas semanas aconteceram estas três situações”.</li>
  <li><strong>Impacto:</strong> “Isso afetou sua saúde, o trabalho e a segurança em casa”.</li>
  <li><strong>Escuta:</strong> “Como você enxerga isso? O que mais teme em procurar ajuda?”.</li>
  <li><strong>Pedido concreto:</strong> “Você aceita fazer uma avaliação hoje e ouvir as opções antes de decidir?”.</li>
</ol>
<p>Evite “você sempre” e “você nunca”. Fale em tom firme, mas não moralista. Repita a proposta em vez de entrar em debates laterais sobre cada detalhe do passado.</p>

<h2>Trabalhe com a resistência, não contra ela</h2>
<p>Se a pessoa disser “não sou dependente”, você pode responder: “Talvez você não concorde com esse nome. Mesmo assim, concorda que os desmaios e as faltas precisam ser avaliados?”. Se disser “tratamento não funciona”, pergunte o que aconteceu antes e o que teria de ser diferente.</p>
<p>Se o medo for trabalho, filhos, distância ou abstinência, busque informações reais sobre modalidades e logística. Não prometa resultados, prazo exato ou ausência de desconforto. Transparência cria mais confiança do que garantias impossíveis.</p>

<h2>Apresente opções sem transformar tudo em negociação</h2>
<p>Oferecer duas opções viáveis pode devolver senso de autonomia: avaliação presencial ou por telefone; conversar hoje ou no dia seguinte; conhecer serviço público ou instituição privada. Não ofereça alternativas que a família não considera seguras.</p>
<p>O tratamento deve ser individualizado. No Brasil, a legislação prioriza modalidades ambulatoriais e prevê internação de forma excepcional, quando recursos extra-hospitalares forem insuficientes e houver critérios técnicos e legais. Conheça as <a href="${siteUrl}/internacao-voluntaria-involuntaria-compulsoria-diferencas/">diferenças entre internação voluntária, involuntária e compulsória</a>.</p>

<h2>Defina limites sem usar cuidado como ameaça</h2>
<p>Um limite descreve a ação da família, não tenta controlar a mente do outro: “Não vou mais entregar dinheiro”, “Não permitirei direção após uso” ou “Se houver ameaça, chamarei ajuda”. Não condicione comida, atendimento de urgência ou dignidade ao aceite.</p>
<p>Limites precisam ser legais, proporcionais e consistentes. Se cada familiar age de um jeito, a pessoa tende a procurar a resposta mais conveniente e a conversa perde clareza.</p>

<h2>Se a resposta for não</h2>
<p>Não transforme a recusa em batalha. Diga que a oferta permanece, explique os limites e encerre se a conversa estiver escalando. Depois, registre o que funcionou, busque orientação e prepare outra oportunidade. Mudança pode ocorrer em etapas.</p>
<p>A família também pode iniciar acompanhamento. Isso ajuda a reduzir ações impulsivas, reconhecer manipulações e construir uma resposta conjunta.</p>

<h2>Quando não é hora de persuadir</h2>
<p>Inconsciência, falta de ar, convulsão, dor no peito, intoxicação grave, tentativa de suicídio, surto ou violência imediata são situações de urgência. Ligue para o SAMU 192. A prioridade é proteger a vida, não obter concordância.</p>

<h2>Avaliação e encaminhamento em Goiás</h2>
<p>O Grupo Araújo Tratamentos orienta famílias e avalia caminhos de encaminhamento conforme o caso, com unidades parceiras masculinas e femininas em <strong>Goiânia, Aparecida de Goiânia, Caturaí e Senador Canedo</strong>. A disponibilidade de vaga não substitui indicação: riscos, condições associadas e modalidade adequada precisam ser considerados.</p>

<h2>Perguntas frequentes</h2>
<h3>Existe uma frase certa para convencer alguém a se tratar?</h3>
<p>Não existe frase garantida. A chance melhora quando a família fala em momento de sobriedade, usa fatos concretos, escuta a resistência e oferece um primeiro passo viável.</p>
<h3>A família deve fazer uma intervenção surpresa?</h3>
<p>Abordagens com várias pessoas podem aumentar vergonha e defesa. Quando consideradas, precisam de planejamento e condução profissional, com segurança e sem humilhação.</p>
<h3>O que fazer se a pessoa disser não?</h3>
<p>Encerre sem escalar o conflito, mantenha limites, registre a opção de ajuda e busque orientação familiar para preparar outra oportunidade. Em urgência, acione o SAMU 192.</p>
<h3>A internação é a única forma de tratamento?</h3>
<p>Não. O cuidado deve ser definido por avaliação e pode incluir modalidades ambulatoriais, médicas, psicológicas e apoio familiar. A internação é excepcional e depende de critérios clínicos e legais.</p>

<h2>Fontes e revisão</h2>
<p>Conteúdo educativo baseado na <a href="https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2019/lei/l13840.htm" target="_blank" rel="noopener">Lei nº 13.840/2019</a> e nas informações da <a href="https://www.gov.br/saude/pt-br/composicao/saes/desmad/desmad" target="_blank" rel="noopener">Rede de Atenção Psicossocial do Ministério da Saúde</a>. Não constitui diagnóstico ou indicação individual de modalidade.</p>`,
  },
  {
    title: 'Sinais da dependência química: como reconhecer',
    seoTitle: 'Sinais da dependência química: físicos e comportamentais',
    slug: 'sinais-dependencia-quimica-fisicos-comportamentais',
    category: 'Dependência Química',
    image: 'sinais-dependencia-quimica.webp',
    imageTitle: 'Mudanças que podem indicar dependência química',
    imageAlt: 'Familiar observa com preocupação mudanças de comportamento de um adulto jovem',
    imageCaption: 'Um sinal isolado não confirma dependência; observe padrões, perda de controle e consequências.',
    imageDescription: 'Imagem editorial exclusiva criada por inteligência artificial nas cores do Grupo Araújo Tratamentos, sem texto.',
    excerpt: 'Conheça sinais físicos, emocionais e comportamentais da dependência química e entenda quais padrões indicam necessidade de avaliação.',
    metaDescription: 'Conheça sinais físicos, emocionais e comportamentais da dependência química e saiba quando buscar avaliação especializada.',
    tags: ['sinais da dependência', 'sintomas', 'perda de controle', 'abstinência', 'avaliação'],
    faq: [
      {
        question: 'Qual é o principal sinal de dependência química?',
        answer: 'Não existe um único sinal. Perda de controle, prioridade dada ao uso e continuidade apesar dos prejuízos são padrões centrais que precisam ser avaliados em conjunto.',
      },
      {
        question: 'Olhos vermelhos e mudança de humor confirmam uso de drogas?',
        answer: 'Não. Esses sinais têm várias causas. A preocupação aumenta quando aparecem em conjunto, persistem e se associam a mudanças de rotina, consequências e perda de controle.',
      },
      {
        question: 'O que são tolerância e abstinência?',
        answer: 'Tolerância é precisar de mais substância para obter efeito semelhante. Abstinência é o conjunto de sintomas que pode surgir ao reduzir ou interromper. Ambas exigem avaliação, e algumas abstinências podem ser perigosas.',
      },
      {
        question: 'Quando os sinais exigem atendimento imediato?',
        answer: 'Inconsciência, respiração alterada, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem o SAMU 192.',
      },
    ],
    content: `
<p><strong>Os sinais mais importantes da dependência química são perda de controle, prioridade crescente dada à substância e continuidade do uso apesar dos prejuízos.</strong> Mudanças físicas, emocionais e sociais podem reforçar a suspeita, mas nenhum sinal isolado confirma o diagnóstico.</p>
<p>Olhos vermelhos, irritação ou sono alterado também aparecem em estresse, doenças e outros transtornos. O que merece atenção é o <em>padrão</em>: vários sinais juntos, repetidos ao longo do tempo e acompanhados de consequências.</p>

<h2>Sinais relacionados ao controle do uso</h2>
<ul>
  <li>usar mais quantidade ou por mais tempo do que pretendia;</li>
  <li>tentar reduzir ou parar e não conseguir manter;</li>
  <li>gastar muito tempo obtendo, usando ou se recuperando;</li>
  <li>sentir desejo intenso ou urgência para usar;</li>
  <li>organizar a rotina em torno da substância.</li>
</ul>
<p>A pessoa pode manter emprego e aparência por algum tempo e ainda assim ter perda de controle. O funcionamento externo não exclui sofrimento ou risco.</p>

<h2>Mudanças comportamentais e sociais</h2>
<p>Isolamento, faltas, atrasos, queda de rendimento, mentiras frequentes, mudanças bruscas de grupo social, abandono de atividades e conflitos repetidos podem aparecer. Também são relevantes gastos sem explicação, venda de objetos e comportamento de risco, como dirigir após usar.</p>
<p>Observe consequências concretas, não apenas um estilo de vida diferente do esperado pela família. O foco deve ser saúde, segurança e funcionamento.</p>

<h2>Sinais emocionais e cognitivos</h2>
<ul>
  <li>irritabilidade e mudanças intensas de humor;</li>
  <li>ansiedade, tristeza ou apatia persistentes;</li>
  <li>dificuldade de concentração e memória;</li>
  <li>impulsividade ou decisões fora do padrão habitual;</li>
  <li>negação de consequências evidentes.</li>
</ul>
<p>Esses sinais podem ser efeito do uso, da abstinência ou de uma condição de saúde mental associada. A avaliação precisa investigar as possibilidades, em vez de presumir uma única causa.</p>

<h2>Sinais físicos que merecem atenção</h2>
<p>Alteração marcada de sono ou apetite, perda ou ganho de peso, tremores, suor excessivo, fala arrastada, falta de coordenação, pupilas muito dilatadas ou contraídas, ferimentos frequentes e descuido progressivo podem ocorrer conforme a substância e o padrão de uso.</p>
<p>A ausência desses sinais não exclui dependência. Algumas alterações são discretas ou aparecem apenas em determinados momentos.</p>

<h2>Tolerância e abstinência</h2>
<p><strong>Tolerância</strong> significa precisar de doses maiores para obter efeito semelhante. <strong>Abstinência</strong> é o conjunto de sintomas que surge ao reduzir ou interromper. Ansiedade, tremor, náusea, suor, insônia, agitação e alterações de humor são exemplos possíveis, mas variam conforme a substância.</p>
<p>Algumas abstinências podem evoluir com convulsões, confusão ou risco grave. Não tente conduzir desintoxicação intensa em casa sem avaliação. Procure atendimento, especialmente com uso frequente de álcool, sedativos ou múltiplas substâncias.</p>

<h2>Um checklist de perguntas úteis</h2>
<p>Em vez de contar apenas sintomas, pergunte:</p>
<ol>
  <li>A pessoa perdeu a capacidade de decidir quando parar?</li>
  <li>O uso passou a ocupar mais tempo e prioridade?</li>
  <li>Existem prejuízos na saúde, trabalho, estudo, finanças ou relações?</li>
  <li>Ela continua usando apesar de reconhecer esses prejuízos?</li>
  <li>Há tolerância, abstinência ou repetidas tentativas frustradas de parar?</li>
  <li>O comportamento coloca a própria pessoa ou terceiros em risco?</li>
</ol>
<p>Quanto mais respostas positivas, maior a necessidade de avaliação. Ainda assim, diagnóstico não deve ser feito por checklist online.</p>

<h2>Como conversar sobre os sinais</h2>
<p>Escolha momento de sobriedade. Cite dois ou três fatos e o impacto: “Percebi que você faltou, dirigiu depois de usar e tentou parar sem conseguir”. Pergunte como a pessoa entende a situação e proponha avaliação.</p>
<p>Não revire pertences durante a conversa, não acumule acusações antigas e não discuta o rótulo. É possível concordar sobre os riscos antes de concordar sobre a palavra “dependência”.</p>

<h2>Sinais de urgência</h2>
<p>Ligue para o <strong>SAMU 192</strong> em caso de inconsciência, respiração lenta ou difícil, lábios arroxeados, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou risco imediato de violência. Não deixe a pessoa dirigir e não administre medicamentos por conta própria.</p>

<h2>Onde buscar avaliação em Goiás</h2>
<p>A atenção básica e a Rede de Atenção Psicossocial podem orientar o cuidado. O Grupo Araújo Tratamentos também realiza orientação inicial a famílias e avaliação para encaminhamento conforme perfil, segurança e disponibilidade, com unidades parceiras em <strong>Goiânia, Aparecida de Goiânia, Caturaí e Senador Canedo</strong>. Avaliação precoce permite comparar modalidades antes que o risco aumente.</p>
<p>Se você já reconhece um conjunto de sinais, veja <a href="${siteUrl}/como-ajudar-dependente-quimico-sem-se-anular/">como ajudar sem se anular</a> e organize um primeiro contato.</p>

<h2>Perguntas frequentes</h2>
<h3>Qual é o principal sinal de dependência química?</h3>
<p>Não existe um único sinal. Perda de controle, prioridade dada ao uso e continuidade apesar dos prejuízos são padrões centrais que precisam ser avaliados em conjunto.</p>
<h3>Olhos vermelhos e mudança de humor confirmam uso de drogas?</h3>
<p>Não. Esses sinais têm várias causas. A preocupação aumenta quando aparecem em conjunto, persistem e se associam a mudanças de rotina, consequências e perda de controle.</p>
<h3>O que são tolerância e abstinência?</h3>
<p>Tolerância é precisar de mais substância para obter efeito semelhante. Abstinência é o conjunto de sintomas que pode surgir ao reduzir ou interromper. Ambas exigem avaliação, e algumas abstinências podem ser perigosas.</p>
<h3>Quando os sinais exigem atendimento imediato?</h3>
<p>Inconsciência, respiração alterada, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem o SAMU 192.</p>

<h2>Fontes e revisão</h2>
<p>Conteúdo educativo baseado nas informações do <a href="https://www.gov.br/saude/pt-br/assuntos/saude-brasil/glossario/substancias-psicoativas" target="_blank" rel="noopener">Ministério da Saúde sobre substâncias psicoativas</a> e do <a href="https://www.gov.br/saude/pt-br/composicao/saes/samu-192" target="_blank" rel="noopener">SAMU 192</a>. Não substitui diagnóstico ou avaliação individual.</p>`,
  },
  {
    title: 'Como agir em uma recaída',
    seoTitle: 'Como agir em uma recaída: o que fazer nas primeiras horas',
    slug: 'recaida-dependencia-quimica-o-que-fazer',
    category: 'Recuperação',
    image: 'como-agir-recaida.webp',
    imageTitle: 'Pedido de ajuda após recaída na dependência química',
    imageAlt: 'Adulto pede ajuda pelo telefone e recebe apoio após uma recaída',
    imageCaption: 'Agir cedo, avaliar riscos e retomar o cuidado evita que um episódio se transforme em abandono.',
    imageDescription: 'Imagem editorial exclusiva criada por inteligência artificial nas cores do Grupo Araújo Tratamentos, sem texto.',
    excerpt: 'Veja o que fazer nas primeiras horas após uma recaída, como avaliar riscos, retomar o tratamento e revisar o plano sem culpa ou permissividade.',
    metaDescription: 'Saiba como agir após uma recaída, avaliar riscos, retomar o tratamento e rever o plano de prevenção sem culpa nem permissividade.',
    tags: ['recaída', 'recuperação', 'prevenção de recaída', 'dependência química', 'família'],
    faq: [
      {
        question: 'Uma recaída significa que o tratamento fracassou?',
        answer: 'Não necessariamente. Ela indica que o plano precisa ser retomado e revisto. Agir cedo reduz danos e evita transformar um episódio em abandono prolongado.',
      },
      {
        question: 'O que a família deve fazer logo após descobrir uma recaída?',
        answer: 'Avalie urgência, impeça direção, retire meios de risco quando possível, contate a equipe de referência e converse quando a pessoa estiver sóbria.',
      },
      {
        question: 'É preciso voltar para internação após toda recaída?',
        answer: 'Não. A conduta depende de risco, substância, intensidade, abstinência, saúde mental, suporte disponível e histórico. Uma nova avaliação define o nível de cuidado.',
      },
      {
        question: 'Quando a recaída exige o SAMU 192?',
        answer: 'Inconsciência, dificuldade respiratória, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem atendimento de urgência.',
      },
    ],
    content: `
<p><strong>Depois de uma recaída, aja cedo: verifique a segurança, interrompa riscos imediatos, avise a equipe de referência e retome o plano.</strong> Recaída não precisa virar abandono, mas também não deve ser minimizada.</p>
<p>Culpa, vergonha e brigas podem atrasar o pedido de ajuda. O foco das primeiras horas é proteger a vida e criar condições para uma avaliação honesta do que aconteceu.</p>

<h2>Primeiro: verifique se é uma urgência</h2>
<p>Ligue para o <strong>SAMU 192</strong> diante de inconsciência, respiração lenta ou difícil, lábios arroxeados, convulsão, dor no peito, confusão intensa, tentativa de suicídio, intoxicação grave ou risco imediato de violência. Informe substâncias, horário e quantidade aproximada, se souber.</p>
<p>Não deixe a pessoa dirigir. Não provoque vômito, não ofereça café, banho frio ou medicamentos e não a abandone se for seguro permanecer. Misturas de substâncias aumentam a imprevisibilidade.</p>

<h2>Nas primeiras 24 horas</h2>
<ol>
  <li><strong>Reduza acesso a meios de risco:</strong> carro, armas, grandes quantias e medicamentos sem supervisão.</li>
  <li><strong>Contate a referência de cuidado:</strong> médico, terapeuta, serviço, grupo ou pessoa definida no plano.</li>
  <li><strong>Proteja necessidades básicas:</strong> local seguro, hidratação e alimentação quando a pessoa estiver consciente e puder ingerir com segurança.</li>
  <li><strong>Adie discussões longas:</strong> converse sobre decisões quando houver sobriedade e estabilidade.</li>
  <li><strong>Registre fatos:</strong> o que aconteceu antes, durante e depois, sem transformar o registro em acusação.</li>
</ol>

<h2>Diferencie lapso, recaída e retorno prolongado</h2>
<p>Algumas equipes usam “lapso” para um episódio pontual e “recaída” para retorno mais amplo ao padrão anterior. Na prática, o rótulo importa menos do que a resposta: houve perda de controle? Existem riscos médicos? O uso continua? A pessoa abandonou acompanhamento?</p>
<p>Não use a diferença para minimizar um episódio. Mesmo um consumo único pode ser perigoso, especialmente após período de abstinência, quando a tolerância pode estar menor.</p>

<h2>Como conversar depois</h2>
<p>Escolha momento de sobriedade e faça perguntas que ajudem a reconstruir a sequência:</p>
<ul>
  <li>O que estava acontecendo nas horas e dias anteriores?</li>
  <li>Quais emoções, lugares, pessoas ou pensamentos apareceram?</li>
  <li>Em que ponto seria possível ter pedido ajuda?</li>
  <li>O que dificultou usar o plano de prevenção?</li>
  <li>Qual ação precisa ocorrer hoje?</li>
</ul>
<p>Evite “jogar fora todo o progresso”. A pessoa mantém habilidades e aprendizados, mas precisa assumir responsabilidade e retomar ações concretas.</p>

<h2>Reavalie o nível de cuidado</h2>
<p>Nem toda recaída exige internação, e nem toda recaída pode ser manejada apenas com uma conversa. A decisão considera substância, intensidade, abstinência, risco de overdose, saúde mental, violência, moradia, suporte e histórico.</p>
<p>A equipe pode aumentar frequência de consultas, revisar medicamentos, fortalecer grupos e participação familiar, indicar desintoxicação supervisionada ou considerar cuidado em ambiente protegido. A conduta deve ser individualizada.</p>

<h2>Revise o plano de prevenção</h2>
<p>Um plano útil não diz apenas “evitar gatilhos”. Ele define respostas observáveis:</p>
<ul>
  <li>três sinais precoces pessoais, como isolamento, faltas e interrupção do sono;</li>
  <li>três pessoas ou serviços para contatar;</li>
  <li>ambientes e contatos de maior risco;</li>
  <li>ações para fissura nas primeiras horas;</li>
  <li>quem acompanha dinheiro, transporte e medicamentos temporariamente;</li>
  <li>o que fazer se a pessoa parar de responder.</li>
</ul>
<p>Leia também o guia sobre <a href="${siteUrl}/como-prevenir-recaidas-apos-o-tratamento/">como prevenir recaídas após o tratamento</a>.</p>

<h2>O papel da família: firmeza sem humilhação</h2>
<p>A família pode acolher o pedido de ajuda e, ao mesmo tempo, restabelecer limites. Não forneça dinheiro, não encubra faltas e não aceite direção após uso. Evite insultos, exposição e ameaças que não serão cumpridas.</p>
<p>Se houve violência, priorize a segurança de todos. Crianças, idosos e outros vulneráveis não devem participar de confrontos nem assumir vigilância.</p>

<h2>O que costuma piorar a situação</h2>
<ul>
  <li>tratar o episódio como prova de que “nada funciona”;</li>
  <li>esperar vários dias para avisar a equipe por vergonha;</li>
  <li>tentar desintoxicação intensa em casa sem avaliação;</li>
  <li>devolver imediatamente todo acesso a dinheiro e veículo;</li>
  <li>romper acompanhamento porque houve uma falha;</li>
  <li>concentrar tudo na substância e ignorar ansiedade, depressão, dor ou conflitos.</li>
</ul>

<h2>Retomada do cuidado em Goiás</h2>
<p>O Grupo Araújo Tratamentos orienta famílias e reavalia possibilidades de encaminhamento após recaída, conforme risco, perfil e disponibilidade. Há unidades parceiras masculinas e femininas em <strong>Goiânia, Aparecida de Goiânia, Caturaí e Senador Canedo</strong>. Em emergência, procure a rede de urgência; para reorganização do plano, reúna informações sobre o episódio e tratamentos anteriores.</p>

<h2>Perguntas frequentes</h2>
<h3>Uma recaída significa que o tratamento fracassou?</h3>
<p>Não necessariamente. Ela indica que o plano precisa ser retomado e revisto. Agir cedo reduz danos e evita transformar um episódio em abandono prolongado.</p>
<h3>O que a família deve fazer logo após descobrir uma recaída?</h3>
<p>Avalie urgência, impeça direção, retire meios de risco quando possível, contate a equipe de referência e converse quando a pessoa estiver sóbria.</p>
<h3>É preciso voltar para internação após toda recaída?</h3>
<p>Não. A conduta depende de risco, substância, intensidade, abstinência, saúde mental, suporte disponível e histórico. Uma nova avaliação define o nível de cuidado.</p>
<h3>Quando a recaída exige o SAMU 192?</h3>
<p>Inconsciência, dificuldade respiratória, convulsão, dor no peito, confusão intensa, intoxicação grave, tentativa de suicídio ou violência imediata exigem atendimento de urgência.</p>

<h2>Fontes e revisão</h2>
<p>Conteúdo educativo baseado nas informações sobre cuidado contínuo da <a href="https://www.gov.br/saude/pt-br/composicao/saes/desmad/desmad" target="_blank" rel="noopener">Rede de Atenção Psicossocial do Ministério da Saúde</a>, sobre <a href="https://www.gov.br/mds/pt-br/obid/tratamento-e-reinsercao-social" target="_blank" rel="noopener">tratamento e reinserção social</a> e nas orientações do <a href="https://www.gov.br/saude/pt-br/composicao/saes/samu-192" target="_blank" rel="noopener">SAMU 192</a>. Não substitui avaliação individual.</p>`,
  },
];

const credentials = readCredentials();
const results = [];

for (const post of posts) {
  const existing = await findPost(post.slug);
  if (existing) {
    await updatePost(credentials, post, existing.id);
    results.push({ id: existing.id, slug: post.slug, link: existing.link, status: 'updated' });
    continue;
  }

  const mediaId = await uploadImage(credentials, post);
  const postId = await createPost(credentials, post, mediaId);
  const created = await findPost(post.slug);
  results.push({
    id: postId,
    mediaId,
    slug: post.slug,
    link: created?.link || `${siteUrl}/${post.slug}/`,
    status: 'published',
  });
}

console.log(JSON.stringify(results, null, 2));
