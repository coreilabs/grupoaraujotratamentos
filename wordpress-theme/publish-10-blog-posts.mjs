import { readFileSync } from 'node:fs';
import { basename, join } from 'node:path';

const siteUrl = 'https://grupoaraujotratamentos.com.br';
const xmlrpcUrl = `${siteUrl}/xmlrpc.php`;
const root = process.cwd();
const imageDir = join(root, 'generated-blog-images');
const credentialsPath = join(root, '..', 'credenciais de acesso.md');

function readCredentials() {
  const lines = readFileSync(credentialsPath, 'utf8').split(/\r?\n/);
  const wpStart = lines.findIndex((line) => line.startsWith('Acesso wordpress:'));
  const wpLines = lines.slice(wpStart + 1);
  const username = wpLines.find((line) => line.startsWith('usu'))?.split(/:(.+)/)[1]?.trim();
  const password = wpLines.find((line) => line.startsWith('senha:'))?.split(/:(.+)/)[1]?.trim();

  if (!username || !password) {
    throw new Error('Credenciais do WordPress nao encontradas.');
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

async function postExists(slug) {
  const response = await fetch(`${siteUrl}/wp-json/wp/v2/posts?slug=${encodeURIComponent(slug)}&_fields=id,link`);
  if (!response.ok) return null;
  const posts = await response.json();
  return posts[0] || null;
}

async function uploadImage({ username, password }, post) {
  const filePath = join(imageDir, post.image);
  const bits = readFileSync(filePath).toString('base64');
  const result = await xmlrpc('wp.uploadFile', [
    1,
    username,
    password,
    {
      name: basename(filePath),
      type: 'image/png',
      bits: { __base64: bits },
      overwrite: false,
    },
  ]);
  return Number(member(result, 'id'));
}

async function createPost({ username, password }, post, mediaId) {
  const result = await xmlrpc('wp.newPost', [
    1,
    username,
    password,
    {
      post_type: 'post',
      post_status: 'publish',
      post_title: post.title,
      post_name: post.slug,
      post_excerpt: post.excerpt,
      post_content: post.content,
      post_thumbnail: mediaId,
      terms_names: {
        category: ['Dependencia quimica'],
        post_tag: post.tags,
      },
      custom_fields: [
        { key: '_yoast_wpseo_metadesc', value: post.excerpt },
      ],
    },
  ]);
  return result.match(/<int>(\d+)<\/int>|<string>(\d+)<\/string>/)?.slice(1).find(Boolean);
}

const posts = [
  {
    title: 'Como identificar os primeiros sinais da dependência química',
    slug: 'como-identificar-primeiros-sinais-dependencia-quimica',
    image: '01-sinais-dependencia-quimica.png',
    excerpt: 'Entenda sinais físicos, emocionais e comportamentais que podem indicar dependência química e saiba quando buscar orientação especializada.',
    tags: ['dependência química', 'sinais de alerta', 'família', 'tratamento'],
    content: `
<p>Identificar os primeiros sinais da dependência química nem sempre é simples. Muitas famílias percebem mudanças, mas ficam em dúvida se estão diante de uma fase difícil, de estresse emocional ou de um problema que já exige ajuda especializada. A atenção precoce, porém, pode reduzir riscos e facilitar o início do cuidado.</p>
<h2>Mudanças de comportamento que merecem atenção</h2>
<p>Entre os sinais mais comuns estão isolamento, irritabilidade, perda de interesse por atividades importantes, mentiras frequentes, alterações bruscas de humor e dificuldade de cumprir compromissos. A pessoa também pode se afastar da família, mudar repentinamente de grupo social ou passar a esconder horários, gastos e objetos pessoais.</p>
<h2>Sinais físicos e emocionais</h2>
<p>Alterações de sono, perda ou ganho de peso, olhos vermelhos, tremores, cansaço extremo, descuido com higiene e aparência podem aparecer em alguns casos. No campo emocional, ansiedade, tristeza persistente, impulsividade e sensação de descontrole também são sinais relevantes, principalmente quando surgem junto ao uso repetido de álcool ou outras drogas.</p>
<h2>Quando o uso passa a preocupar</h2>
<p>Um ponto importante é observar consequências. O uso começa a indicar dependência quando a pessoa continua consumindo mesmo após prejuízos na saúde, no trabalho, nos estudos, nas finanças ou nas relações familiares. Outro alerta é a perda de controle: prometer parar, reduzir por pouco tempo e voltar ao padrão anterior.</p>
<h2>Como a família deve agir</h2>
<p>Evite acusações e conversas em tom de confronto. Procure falar em um momento de sobriedade, com frases objetivas e exemplos concretos. Em vez de rotular, descreva o que foi observado: faltas, mudanças de rotina, agressividade, dívidas ou riscos. A dependência química é uma condição complexa, com fatores biológicos, psicológicos e sociais, e precisa de orientação responsável.</p>
<h2>Buscar ajuda cedo faz diferença</h2>
<p>Nem todo caso exige a mesma abordagem. Algumas pessoas precisam de acompanhamento ambulatorial; outras, de cuidado mais intensivo. Uma avaliação especializada ajuda a definir o caminho com mais segurança, considerando histórico de uso, saúde mental, apoio familiar e riscos imediatos.</p>
<p>Se você reconhece esses sinais em alguém próximo, converse com uma equipe preparada. O acolhimento adequado pode ser o primeiro passo para proteger a pessoa e reorganizar a vida familiar.</p>`,
  },
  {
    title: 'Alcoolismo: quando o consumo deixa de ser social e se torna uma doença',
    slug: 'alcoolismo-quando-consumo-deixa-de-ser-social-e-se-torna-doenca',
    image: '02-alcoolismo-consumo-social-doenca.png',
    excerpt: 'Saiba diferenciar consumo social de sinais de alcoolismo e entenda por que o uso abusivo de álcool deve ser tratado com seriedade.',
    tags: ['alcoolismo', 'álcool', 'dependência alcoólica', 'tratamento'],
    content: `
<p>O álcool está presente em muitos contextos sociais, o que pode dificultar a percepção de quando o consumo deixa de ser ocasional e passa a representar um problema de saúde. O alcoolismo não é falta de caráter nem simples exagero: é uma condição que pode afetar corpo, mente, família e vida social.</p>
<h2>O limite não está apenas na quantidade</h2>
<p>Embora beber em grande quantidade seja um alerta, a dependência não é definida somente pelo volume consumido. O mais importante é observar a relação da pessoa com a bebida. Ela consegue parar quando decide? Precisa beber para relaxar, dormir ou enfrentar problemas? Continua bebendo apesar de brigas, faltas, riscos no trânsito ou prejuízos no trabalho?</p>
<h2>Sinais de que o consumo virou problema</h2>
<p>Alguns sinais comuns incluem beber escondido, aumentar a tolerância, ter lapsos de memória, sentir culpa após beber, prometer parar e não conseguir, apresentar tremores ou irritação quando fica sem álcool e priorizar a bebida em vez de responsabilidades. Também é comum a família perceber mudanças antes da própria pessoa reconhecer o problema.</p>
<h2>Por que o alcoolismo precisa de tratamento</h2>
<p>O uso nocivo de álcool pode causar prejuízos físicos, emocionais e sociais. Pode piorar quadros de ansiedade e depressão, aumentar conflitos familiares, favorecer acidentes e comprometer a saúde do fígado, do coração e do sistema nervoso. Por isso, quanto mais cedo houver orientação, maiores são as chances de reorganizar a rotina e reduzir danos.</p>
<h2>Como conversar com quem bebe demais</h2>
<p>Escolha um momento em que a pessoa esteja sóbria. Fale com respeito e use exemplos concretos. Frases como “estamos preocupados com sua saúde” costumam funcionar melhor do que acusações. Também é importante estabelecer limites: apoiar não significa encobrir consequências ou sustentar comportamentos de risco.</p>
<h2>Tratamento é cuidado contínuo</h2>
<p>O tratamento pode envolver avaliação médica, psicoterapia, grupos de apoio, participação familiar e, em alguns casos, internação. O caminho depende da gravidade do quadro, da presença de abstinência, dos riscos e da rede de apoio disponível.</p>
<p>Se o álcool deixou de ser uma escolha eventual e passou a controlar decisões, relações e rotina, é hora de buscar ajuda especializada.</p>`,
  },
  {
    title: 'Como convencer um familiar a aceitar tratamento',
    slug: 'como-convencer-familiar-aceitar-tratamento',
    image: '03-convencer-familiar-aceitar-tratamento.png',
    excerpt: 'Veja estratégias cuidadosas para conversar com um familiar sobre dependência química e aumentar a chance de aceitação do tratamento.',
    tags: ['família', 'tratamento', 'dependência química', 'acolhimento'],
    content: `
<p>Convencer um familiar a aceitar tratamento é uma das situações mais delicadas para quem convive com a dependência química. A resistência é comum: medo, vergonha, negação e experiências negativas anteriores podem fazer a pessoa recusar ajuda mesmo quando os prejuízos são evidentes.</p>
<h2>Prepare a conversa</h2>
<p>Antes de conversar, organize os fatos. Evite falar apenas de impressões gerais. Liste situações concretas: faltas no trabalho, conflitos, riscos, perdas financeiras, mudanças de comportamento e episódios que colocaram a saúde em perigo. Isso ajuda a manter a conversa objetiva e reduz discussões baseadas em acusações.</p>
<h2>Escolha o momento certo</h2>
<p>Não tente convencer alguém durante intoxicação, abstinência intensa ou crise de agressividade. Prefira um momento de maior lucidez e privacidade. O objetivo inicial não precisa ser “resolver tudo”, mas abrir uma porta para avaliação profissional.</p>
<h2>Fale com firmeza e respeito</h2>
<p>Uma abordagem acolhedora não significa ausência de limites. A família pode dizer que ama a pessoa, que está preocupada e que não vai mais colaborar com comportamentos que mantêm o ciclo de uso. Evite humilhações, ameaças vazias e comparações. Quanto mais a conversa parecer julgamento, maior tende a ser a defesa.</p>
<h2>Ofereça caminhos práticos</h2>
<p>Em vez de apenas pedir que a pessoa “procure ajuda”, apresente opções: conversar com uma equipe, fazer uma avaliação, conhecer uma unidade, participar de uma reunião ou aceitar acompanhamento familiar. Muitas vezes, dar o primeiro passo é mais fácil quando a família já organizou informações e contatos.</p>
<h2>Cuide também da família</h2>
<p>A dependência afeta todos ao redor. Familiares podem adoecer emocionalmente, viver em alerta constante e perder a noção dos próprios limites. Orientação profissional ajuda a família a agir com mais segurança, sem reforçar o problema e sem abandonar a pessoa.</p>
<h2>E se houver risco imediato?</h2>
<p>Quando há ameaça à vida, surto, violência, intoxicação grave ou risco de overdose, a prioridade é acionar serviços de urgência. Em situações complexas, a avaliação especializada indica quais medidas são possíveis e adequadas dentro da lei.</p>
<p>Convencer alguém a aceitar tratamento é processo, não disputa. Com informação, firmeza e apoio certo, a família aumenta as chances de iniciar uma mudança real.</p>`,
  },
  {
    title: 'Internação voluntária, involuntária e compulsória: quais são as diferenças?',
    slug: 'internacao-voluntaria-involuntaria-compulsoria-diferencas',
    image: '04-internacao-voluntaria-involuntaria-compulsoria.png',
    excerpt: 'Entenda as diferenças entre internação voluntária, involuntária e compulsória no contexto da dependência química.',
    tags: ['internação', 'clínica de recuperação', 'dependência química', 'família'],
    content: `
<p>Quando a dependência química coloca a pessoa em risco, muitas famílias começam a pesquisar sobre internação. Nesse momento surgem dúvidas importantes: o que é internação voluntária? Quando existe internação involuntária? E o que significa internação compulsória?</p>
<h2>Internação voluntária</h2>
<p>A internação voluntária acontece quando a própria pessoa aceita o tratamento e assina a autorização de entrada. Esse costuma ser o caminho mais desejável, porque há maior abertura para participar das atividades terapêuticas e reconhecer a necessidade de mudança. Mesmo assim, o tratamento exige acompanhamento, rotina e participação ativa.</p>
<h2>Internação involuntária</h2>
<p>A internação involuntária ocorre sem o consentimento da pessoa, a pedido de familiar ou responsável legal, quando há indicação médica e risco associado ao uso de substâncias. Ela deve seguir critérios legais e clínicos, com avaliação profissional. Não deve ser confundida com punição: o objetivo é proteção e cuidado quando a pessoa perdeu capacidade de avaliar o próprio risco.</p>
<h2>Internação compulsória</h2>
<p>A internação compulsória é determinada pela Justiça. Normalmente envolve situações em que há decisão judicial baseada em documentos, laudos e análise do caso. Por isso, não depende apenas da vontade da família ou da clínica.</p>
<h2>Internação não é a única resposta</h2>
<p>Embora seja importante em alguns casos, a internação não substitui todo o processo de recuperação. Dependência química é condição complexa e pode exigir acompanhamento psicológico, médico, familiar, social e estratégias de prevenção de recaída. A escolha do tipo de cuidado deve considerar gravidade, histórico, saúde mental, rede de apoio e riscos imediatos.</p>
<h2>Como saber qual caminho seguir?</h2>
<p>O primeiro passo é buscar avaliação especializada. Profissionais capacitados podem orientar a família sobre possibilidades, documentação, riscos, direitos da pessoa e continuidade do cuidado após a alta. Decisões tomadas no desespero podem gerar conflitos e não resolver o problema de fundo.</p>
<p>Se sua família está diante de uma situação urgente ou perigosa, procure orientação imediatamente. Entender as diferenças entre os tipos de internação ajuda a agir com responsabilidade, proteção e respeito.</p>`,
  },
  {
    title: 'Quanto tempo dura o tratamento para dependência química?',
    slug: 'quanto-tempo-dura-tratamento-dependencia-quimica',
    image: '05-tempo-tratamento-dependencia-quimica.png',
    excerpt: 'Conheça os fatores que influenciam a duração do tratamento para dependência química e por que cada caso precisa de avaliação individual.',
    tags: ['tratamento', 'tempo de internação', 'dependência química', 'recuperação'],
    content: `
<p>Uma das primeiras perguntas feitas por familiares é: quanto tempo dura o tratamento para dependência química? A resposta honesta é que não existe prazo único para todos. O tempo depende da substância utilizada, da gravidade do quadro, da saúde física e mental, do histórico de recaídas e do apoio familiar.</p>
<h2>Tratamento começa com avaliação</h2>
<p>Antes de definir duração, é preciso entender o caso. Há pessoas que precisam de desintoxicação supervisionada, outras necessitam de acompanhamento psicológico intensivo, e algumas apresentam comorbidades como depressão, ansiedade ou transtornos psiquiátricos que também precisam de cuidado.</p>
<h2>Por que não existe solução imediata?</h2>
<p>A dependência química envolve mudanças de comportamento, rotina, vínculos, gatilhos emocionais e, muitas vezes, alterações físicas relacionadas ao uso. Interromper o consumo é uma etapa importante, mas não é o fim do processo. A recuperação exige construção de novas estratégias para lidar com fissura, conflitos, frustrações e ambientes de risco.</p>
<h2>Internação pode variar conforme o caso</h2>
<p>Quando indicada, a internação pode ter duração diferente para cada pessoa. Alguns casos exigem período mais curto de estabilização; outros precisam de tempo maior para reorganizar hábitos, fortalecer adesão ao tratamento e preparar retorno à família e à sociedade. O importante é que a decisão seja técnica e acompanhada por equipe responsável.</p>
<h2>Depois da alta, o cuidado continua</h2>
<p>Um erro comum é imaginar que a alta significa “fim do tratamento”. Na prática, a continuidade é essencial. Acompanhamento ambulatorial, psicoterapia, grupos de apoio, rotina estruturada, participação familiar e prevenção de recaídas fazem parte do cuidado de longo prazo.</p>
<h2>O papel da família no tempo de recuperação</h2>
<p>Famílias orientadas ajudam a manter limites, incentivar consultas, identificar sinais de risco e apoiar mudanças saudáveis. Quando a família participa, a pessoa não retorna para o mesmo ambiente desorganizado que favorecia o uso.</p>
<p>Mais importante do que buscar um número exato de dias é procurar um plano adequado. O tratamento precisa ser suficiente para proteger, estabilizar e preparar a pessoa para seguir em recuperação com apoio.</p>`,
  },
  {
    title: 'O papel da família na recuperação do dependente químico',
    slug: 'papel-da-familia-na-recuperacao-do-dependente-quimico',
    image: '06-papel-familia-recuperacao.png',
    excerpt: 'Entenda como a família pode apoiar a recuperação do dependente químico sem reforçar comportamentos de risco.',
    tags: ['família', 'recuperação', 'dependência química', 'recaída'],
    content: `
<p>A família tem papel central na recuperação do dependente químico. Ela pode ser fonte de acolhimento, limite, motivação e continuidade do cuidado. Ao mesmo tempo, quando age sem orientação, pode acabar reforçando o ciclo de uso sem perceber.</p>
<h2>Apoiar não é encobrir</h2>
<p>Muitas famílias tentam proteger a pessoa pagando dívidas, justificando faltas, escondendo problemas ou cedendo a manipulações. A intenção costuma ser boa, mas essas atitudes podem diminuir a percepção das consequências e dificultar a busca por tratamento. Apoio saudável envolve cuidado com limites claros.</p>
<h2>Informação reduz culpa e conflito</h2>
<p>Dependência química é uma condição multifatorial. Isso significa que envolve fatores biológicos, emocionais, sociais e ambientais. Entender essa complexidade ajuda a família a sair de extremos: nem culpar totalmente a pessoa, nem tratar tudo como algo fora de controle. Existe responsabilidade, mas também existe necessidade de tratamento.</p>
<h2>Participação no plano terapêutico</h2>
<p>Quando possível, familiares devem participar de orientações, reuniões e atendimentos indicados pela equipe. Isso ajuda a compreender gatilhos, sinais de recaída, formas adequadas de comunicação e ajustes necessários na rotina familiar.</p>
<h2>Comunicação que ajuda</h2>
<p>Falas agressivas, humilhações e ameaças constantes tendem a aumentar resistência. Uma comunicação mais efetiva combina firmeza e respeito. É possível dizer “não aceitamos comportamentos que colocam a família em risco” e, ao mesmo tempo, “estamos dispostos a apoiar seu tratamento”.</p>
<h2>Família também precisa de cuidado</h2>
<p>Conviver com a dependência pode gerar ansiedade, medo, raiva, tristeza e exaustão. Buscar apoio para os familiares não é egoísmo; é parte do processo. Uma família mais fortalecida toma decisões melhores e sustenta limites com mais consistência.</p>
<p>Na recuperação, ninguém caminha sozinho. A presença da família, quando orientada, pode ajudar a transformar o tratamento em uma mudança real de vida.</p>`,
  },
  {
    title: 'Dependência de cocaína: sintomas, riscos e tratamento',
    slug: 'dependencia-de-cocaina-sintomas-riscos-tratamento',
    image: '07-dependencia-cocaina-sintomas-riscos-tratamento.png',
    excerpt: 'Veja sintomas comuns da dependência de cocaína, riscos para a saúde e possibilidades de tratamento especializado.',
    tags: ['cocaína', 'dependência química', 'tratamento', 'sintomas'],
    content: `
<p>A dependência de cocaína é um problema sério que pode evoluir rapidamente e trazer prejuízos físicos, emocionais, financeiros e familiares. Por atuar no sistema de recompensa do cérebro, a droga pode gerar forte desejo de repetição do uso, perda de controle e dificuldade de interromper o consumo sem ajuda.</p>
<h2>Sintomas e sinais de alerta</h2>
<p>Entre os sinais possíveis estão euforia seguida de irritabilidade, agitação, insônia, perda de apetite, gastos inexplicáveis, mentiras, isolamento e mudanças bruscas de humor. Também podem ocorrer ansiedade intensa, paranoia, impulsividade e comportamento de risco.</p>
<h2>Riscos para a saúde</h2>
<p>O uso de cocaína pode aumentar a pressão arterial e a frequência cardíaca, favorecer arritmias, dor no peito, crises de ansiedade, convulsões e outros problemas graves. Quando há mistura com álcool ou outras substâncias, os riscos podem aumentar. Em situações de dor no peito, confusão, desmaio, convulsão ou falta de ar, a orientação é procurar atendimento de urgência.</p>
<h2>Impactos na vida familiar</h2>
<p>A dependência costuma afetar confiança, finanças, rotina e segurança emocional da família. Promessas de parar podem se repetir sem resultado, especialmente quando a pessoa continua exposta aos mesmos gatilhos e ambientes.</p>
<h2>Como funciona o tratamento</h2>
<p>O tratamento deve ser individualizado. Pode envolver avaliação médica, psicoterapia, grupos terapêuticos, participação familiar, mudança de rotina e estratégias de prevenção de recaída. Em casos com risco elevado, uso compulsivo ou grave desorganização da vida, pode ser indicada uma modalidade mais intensiva de cuidado.</p>
<h2>Recuperação exige continuidade</h2>
<p>Parar de usar é um passo fundamental, mas manter-se em recuperação exige acompanhamento. Identificar gatilhos, reconstruir vínculos, criar rotina saudável e tratar possíveis comorbidades são partes importantes do processo.</p>
<p>Se você percebe sinais de dependência de cocaína em alguém próximo, buscar orientação especializada pode evitar agravamento e abrir um caminho mais seguro de cuidado.</p>`,
  },
  {
    title: 'Dependência de crack: impactos na saúde e possibilidades de recuperação',
    slug: 'dependencia-de-crack-impactos-na-saude-e-possibilidades-de-recuperacao',
    image: '08-dependencia-crack-impactos-recuperacao.png',
    excerpt: 'Entenda impactos da dependência de crack, sinais de agravamento e como o tratamento pode apoiar a recuperação.',
    tags: ['crack', 'dependência química', 'recuperação', 'tratamento'],
    content: `
<p>A dependência de crack é uma das formas mais graves de transtorno por uso de substâncias. O padrão de consumo pode se tornar intenso, com forte fissura, perda de controle e rápida desorganização da rotina. Apesar da gravidade, a recuperação é possível com tratamento adequado, rede de apoio e continuidade do cuidado.</p>
<h2>Impactos físicos e emocionais</h2>
<p>O uso pode estar associado a emagrecimento, insônia, exaustão, irritabilidade, ansiedade, sintomas paranoides, descuido com higiene e maior exposição a situações de risco. Também podem ocorrer problemas respiratórios, cardiovasculares e agravamento de condições de saúde mental.</p>
<h2>Consequências familiares e sociais</h2>
<p>A família frequentemente percebe sumiços, venda de objetos, conflitos, dívidas, rompimento de vínculos e dificuldade de manter trabalho ou estudos. Esses sinais não devem ser tratados apenas como “rebeldia” ou “falta de vontade”. A dependência altera prioridades e exige abordagem estruturada.</p>
<h2>Quando buscar ajuda urgente</h2>
<p>Confusão mental, agressividade intensa, risco de autoagressão, ameaça a terceiros, falta de ar, dor no peito, convulsões ou intoxicação grave exigem atendimento imediato. A proteção da vida vem antes de qualquer outra decisão.</p>
<h2>Tratamento precisa ser amplo</h2>
<p>O cuidado pode envolver estabilização clínica, acompanhamento psicológico, atividades terapêuticas, suporte familiar, reinserção social e prevenção de recaídas. Como muitos casos envolvem vulnerabilidade social, o plano precisa olhar para moradia, vínculos, rotina, trabalho e rede de apoio.</p>
<h2>A família pode ajudar sem se destruir</h2>
<p>Acolher não significa aceitar violência, mentiras ou riscos constantes. Orientação profissional ajuda a família a estabelecer limites, avaliar possibilidades de tratamento e agir com menos culpa e mais segurança.</p>
<p>Mesmo em quadros graves, cada passo de cuidado importa. Com equipe adequada e participação familiar, é possível construir um caminho de recuperação e reconstrução.</p>`,
  },
  {
    title: 'Dependência de medicamentos controlados: sinais de alerta',
    slug: 'dependencia-de-medicamentos-controlados-sinais-de-alerta',
    image: '09-dependencia-medicamentos-controlados.png',
    excerpt: 'Medicamentos controlados podem causar dependência quando usados sem acompanhamento. Conheça sinais de alerta e quando buscar ajuda.',
    tags: ['medicamentos controlados', 'dependência química', 'sinais de alerta', 'tratamento'],
    content: `
<p>Medicamentos controlados são importantes no tratamento de diversas condições de saúde, mas precisam ser usados com acompanhamento profissional. Quando há uso sem prescrição, aumento de dose por conta própria ou dificuldade de interromper, pode existir risco de dependência.</p>
<h2>Quais sinais merecem atenção?</h2>
<p>Alguns sinais incluem tomar doses maiores do que as recomendadas, antecipar horários, procurar receitas com vários profissionais, usar medicamento para lidar com qualquer desconforto, sentir medo intenso de ficar sem, esconder o uso da família ou misturar com álcool e outras substâncias.</p>
<h2>Dependência pode acontecer mesmo com remédio prescrito?</h2>
<p>Sim. Em alguns casos, a pessoa começa usando corretamente, mas desenvolve tolerância, aumenta a dose e passa a sentir dificuldade de reduzir. Por isso, medicamentos como ansiolíticos, sedativos, estimulantes e analgésicos de maior controle exigem acompanhamento cuidadoso.</p>
<h2>Riscos do uso inadequado</h2>
<p>O uso sem supervisão pode causar sonolência excessiva, alterações de memória, quedas, confusão, piora de ansiedade, abstinência e risco aumentado quando combinado com álcool. Nunca é recomendado interromper abruptamente certos medicamentos sem orientação, pois isso pode trazer sintomas importantes.</p>
<h2>Como conversar com a pessoa</h2>
<p>A família deve evitar acusações e focar em fatos: aumento de dose, mudanças de comportamento, sonolência, compras suspeitas ou mistura com bebidas. O objetivo é incentivar avaliação profissional, não gerar vergonha.</p>
<h2>Tratamento e acompanhamento</h2>
<p>O cuidado pode incluir revisão médica, plano gradual de redução quando indicado, psicoterapia, apoio familiar e tratamento de condições associadas, como ansiedade, depressão ou dor crônica. O caminho deve ser individualizado e seguro.</p>
<p>Se há suspeita de dependência de medicamentos controlados, procure orientação. O uso correto pode tratar; o uso descontrolado pode adoecer.</p>`,
  },
  {
    title: 'Como prevenir recaídas após o tratamento',
    slug: 'como-prevenir-recaidas-apos-o-tratamento',
    image: '10-prevenir-recaidas-apos-tratamento.png',
    excerpt: 'Prevenir recaídas exige rotina, apoio familiar e acompanhamento contínuo. Veja estratégias importantes após o tratamento.',
    tags: ['prevenção de recaídas', 'recuperação', 'dependência química', 'família'],
    content: `
<p>A recaída é uma preocupação comum após o tratamento para dependência química. Ela não significa que todo o processo foi perdido, mas indica que o plano de cuidado precisa ser revisto. A prevenção começa antes da alta e continua no dia a dia.</p>
<h2>Entenda os gatilhos</h2>
<p>Gatilhos são situações, emoções, pessoas ou lugares que aumentam a vontade de usar. Podem incluir antigos ambientes de consumo, conflitos familiares, solidão, estresse, dinheiro disponível sem planejamento, festas, frustrações e excesso de confiança. Identificar esses fatores ajuda a criar respostas antes da crise.</p>
<h2>Rotina protege</h2>
<p>Uma rotina estruturada reduz espaços para impulsividade. Sono regular, alimentação, atividade física, trabalho, estudo, terapia e momentos saudáveis de lazer ajudam a organizar a vida. A recuperação precisa ocupar o lugar que antes era dominado pelo uso.</p>
<h2>Acompanhamento não deve parar</h2>
<p>Após a alta, é importante manter consultas, grupos, psicoterapia ou outras orientações indicadas. Muitas recaídas acontecem quando a pessoa melhora, abandona o acompanhamento e volta aos mesmos hábitos de antes.</p>
<h2>Família com limites claros</h2>
<p>A família deve apoiar, mas também manter combinados. É importante observar mudanças de comportamento, evitar proteção excessiva e conversar cedo quando surgirem sinais de risco. Regras claras sobre dinheiro, horários, convivência e responsabilidades podem ajudar.</p>
<h2>Plano para momentos de fissura</h2>
<p>A pessoa em recuperação precisa saber o que fazer quando a vontade de usar aparecer: ligar para alguém de confiança, sair de um ambiente de risco, comparecer a uma reunião, praticar uma técnica de respiração, procurar atendimento ou acionar a equipe de referência.</p>
<p>Prevenir recaídas é construir continuidade. Com acompanhamento, rede de apoio e estratégias práticas, a recuperação se torna mais estável e possível.</p>`,
  },
];

const credentials = readCredentials();
const published = [];

for (const post of posts) {
  const existing = await postExists(post.slug);
  if (existing) {
    console.log(`skip ${post.slug} ${existing.link}`);
    published.push(existing.link);
    continue;
  }

  const mediaId = await uploadImage(credentials, post);
  if (!mediaId) throw new Error(`Falha ao enviar imagem de ${post.slug}`);
  const postId = await createPost(credentials, post, mediaId);
  const link = `${siteUrl}/${post.slug}/`;
  console.log(`published ${postId} ${link}`);
  published.push(link);
}

console.log(JSON.stringify(published, null, 2));
