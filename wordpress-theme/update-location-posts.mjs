import { readFileSync } from 'node:fs';
import { join } from 'node:path';

const site = 'https://grupoaraujotratamentos.com.br';
const lines = readFileSync(join(process.cwd(), '..', 'credenciais de acesso.md'), 'utf8').split(/\r?\n/);
const start = lines.findIndex((line) => line.startsWith('Acesso wordpress:'));
const end = lines.findIndex((line, index) => index > start && line.startsWith('ftp:'));
const section = lines.slice(start + 1, end);
const username = section.find((line) => /^usu/i.test(line))?.split(/:(.+)/)[1]?.trim();
const password = section.find((line) => /^senha:/i.test(line))?.split(/:(.+)/)[1]?.trim();
if (!username || !password) throw new Error('Credenciais WordPress não encontradas.');

const escapeXml = (value) => String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
function xmlValue(value) {
  if (typeof value === 'number') return `<value><int>${value}</int></value>`;
  if (Array.isArray(value)) return `<value><array><data>${value.map(xmlValue).join('')}</data></array></value>`;
  if (value && typeof value === 'object') return `<value><struct>${Object.entries(value).map(([key, val]) => `<member><name>${escapeXml(key)}</name>${xmlValue(val)}</member>`).join('')}</struct></value>`;
  return `<value><string>${escapeXml(value ?? '')}</string></value>`;
}
async function rpc(method, params) {
  const body = `<?xml version="1.0"?><methodCall><methodName>${method}</methodName><params>${params.map((value) => `<param>${xmlValue(value)}</param>`).join('')}</params></methodCall>`;
  const response = await fetch(`${site}/xmlrpc.php`, { method: 'POST', headers: { 'Content-Type': 'text/xml' }, body });
  const text = await response.text();
  if (!response.ok || text.includes('<fault>')) throw new Error(`${method}: ${text.slice(0, 700)}`);
}

const posts = [
  {
    id: 154,
    slug: 'clinica-de-recuperacao-em-goiania-goias',
    title: 'Tratamento para dependência química em Goiânia: como buscar ajuda',
    excerpt: 'Orientação para famílias que procuram clínica de recuperação em Goiânia, com critérios de escolha, modalidades de cuidado e apoio após o tratamento.',
    faq: [
      ['Como começar a buscar tratamento em Goiânia?', 'Organize informações sobre padrão de consumo, riscos atuais, tratamentos anteriores e condições de saúde. Com esses dados, uma equipe pode orientar a avaliação inicial.'],
      ['A proximidade da família deve influenciar a escolha?', 'Sim. Visitas, reuniões e orientação familiar podem favorecer a continuidade, desde que a instituição ofereça a modalidade adequada ao caso.'],
      ['O atendimento pode receber famílias da região metropolitana?', 'A orientação inicial pode contemplar Goiânia, Aparecida de Goiânia, Senador Canedo, Trindade e outros municípios próximos.'],
    ],
    content: `<p>Para uma família de Goiânia, procurar tratamento costuma começar depois de um período de tentativas frustradas de controlar o uso de álcool ou outras drogas dentro de casa. A decisão não deve ser tomada apenas pela proximidade: uma <strong>clínica de recuperação em Goiânia</strong> precisa oferecer avaliação responsável, rotina compatível com o caso e participação familiar bem definida.</p>
<h2>O que observar no primeiro contato</h2>
<p>Antes de discutir internação, a equipe deve ouvir o histórico da pessoa. Frequência e substâncias utilizadas, episódios de abstinência, condições clínicas, uso de medicamentos, comportamento recente e tratamentos anteriores mudam a indicação. Promessas de resultado garantido ou propostas iguais para todos são sinais de alerta.</p>
<p>Também vale perguntar quem acompanha o paciente, como a família recebe informações, quais são as regras de visita e como é planejada a saída. Essas respostas mostram se existe um processo terapêutico ou apenas afastamento temporário do ambiente de consumo.</p>
<h2>Goiânia e a participação da rede familiar</h2>
<p>Para moradores da capital e da região metropolitana, a distância pode facilitar reuniões familiares e a preparação do retorno para casa. Isso é útil porque a recuperação continua fora da instituição. Conflitos, acesso a dinheiro, antigos contatos e rotinas desorganizadas precisam ser trabalhados antes da alta.</p>
<p>A família pode apoiar sem encobrir dívidas, faltas ou comportamentos perigosos. O artigo sobre <a href="${site}/papel-da-familia-na-recuperacao-do-dependente-quimico/">o papel da família na recuperação</a> explica como combinar acolhimento e limites.</p>
<h2>Quando considerar um cuidado mais intensivo</h2>
<p>Perda persistente de controle, recaídas seguidas, risco físico, abandono de responsabilidades e impossibilidade de manter acompanhamento fora de ambiente protegido podem levar à avaliação de uma modalidade intensiva. A decisão precisa respeitar critérios clínicos e legais. Entenda as diferenças entre <a href="${site}/internacao-voluntaria-involuntaria-compulsoria-diferencas/">internação voluntária, involuntária e compulsória</a>.</p>
<h2>Continuidade depois da alta</h2>
<p>Retornar a Goiânia significa reencontrar lugares, relações e situações associadas ao consumo. Por isso, um bom plano identifica gatilhos, organiza acompanhamento, define contatos de apoio e estabelece respostas para momentos de fissura. Leia também como <a href="${site}/como-prevenir-recaidas-apos-o-tratamento/">prevenir recaídas após o tratamento</a>.</p>
<h2>Perguntas frequentes</h2><h3>Como começar a buscar tratamento em Goiânia?</h3><p>Organize informações sobre padrão de consumo, riscos atuais, tratamentos anteriores e condições de saúde. Com esses dados, uma equipe pode orientar a avaliação inicial.</p><h3>A proximidade da família deve influenciar a escolha?</h3><p>Sim. Visitas, reuniões e orientação familiar podem favorecer a continuidade, desde que a instituição ofereça a modalidade adequada ao caso.</p><h3>O atendimento pode receber famílias da região metropolitana?</h3><p>A orientação inicial pode contemplar Goiânia, Aparecida de Goiânia, Senador Canedo, Trindade e outros municípios próximos.</p>`,
  },
  {
    id: 157,
    slug: 'clinica-de-recuperacao-em-anapolis-goias',
    title: 'Recuperação da dependência em Anápolis: caminhos para a família',
    excerpt: 'Saiba como avaliar uma clínica de recuperação em Anápolis, preparar a conversa familiar e organizar a continuidade do tratamento.',
    faq: [
      ['É preciso esperar a pessoa pedir ajuda?', 'Não é necessário esperar a situação se agravar para buscar orientação familiar. A adesão, porém, deve ser trabalhada com abordagem adequada e respeito à legislação.'],
      ['Como comparar clínicas próximas de Anápolis?', 'Compare equipe, avaliação, rotina, segurança, comunicação com a família e plano de continuidade, não apenas preço e distância.'],
      ['Quanto tempo dura o tratamento?', 'Não existe prazo único. A duração depende da gravidade, evolução, condições associadas e objetivos definidos na avaliação.'],
    ],
    content: `<p>Em Anápolis, muitas famílias chegam à busca por tratamento depois de ciclos de promessa, interrupção breve do consumo e recaída. Nessa fase, agir com pressa pode levar a decisões baseadas apenas em disponibilidade. Escolher uma <strong>clínica de recuperação em Anápolis</strong> exige entender primeiro o tipo de cuidado necessário.</p>
<h2>Prepare a família antes de apresentar uma proposta</h2>
<p>Uma conversa improvisada durante intoxicação ou conflito tende a aumentar a resistência. O melhor é reunir fatos concretos: faltas, mudanças de comportamento, problemas de saúde, riscos, dívidas e tentativas anteriores. A família deve combinar previamente quais limites consegue sustentar e quem conduzirá a conversa.</p>
<p>Em vez de rótulos, apresente observações e uma ação possível: avaliação, conversa com a equipe ou visita. Se essa etapa é difícil, veja estratégias para <a href="${site}/como-convencer-familiar-aceitar-tratamento/">conversar com um familiar sobre tratamento</a>.</p>
<h2>Critérios para comparar instituições</h2>
<p>Anápolis está conectada a diferentes municípios goianos, o que amplia as alternativas, mas também exige comparação cuidadosa. Confirme quem realiza a avaliação, como são tratadas condições clínicas e emocionais, quais atividades compõem a rotina e como ocorrem visitas e contatos.</p>
<p>Peça explicações sobre regras, custos, documentação, critérios de alta e conduta em urgências. Uma instituição responsável reconhece os próprios limites e encaminha situações que demandam atendimento hospitalar.</p>
<h2>Tratamento não é isolamento</h2>
<p>O afastamento do uso pode criar estabilidade inicial, mas a recuperação precisa desenvolver habilidades para a vida cotidiana. Responsabilidade, comunicação, reconhecimento de gatilhos e reconstrução de vínculos são parte do processo. O tempo necessário varia; confira os fatores que influenciam <a href="${site}/quanto-tempo-dura-tratamento-dependencia-quimica/">a duração do tratamento</a>.</p>
<h2>Planeje o retorno a Anápolis</h2>
<p>Antes da alta, a família precisa saber como agir diante de isolamento, abandono de acompanhamento, contato com antigos ambientes de uso ou mudanças bruscas de rotina. Consultas, grupos de apoio, atividades regulares e acordos familiares tornam o retorno menos vulnerável.</p>
<h2>Perguntas frequentes</h2><h3>É preciso esperar a pessoa pedir ajuda?</h3><p>Não é necessário esperar a situação se agravar para buscar orientação familiar. A adesão, porém, deve ser trabalhada com abordagem adequada e respeito à legislação.</p><h3>Como comparar clínicas próximas de Anápolis?</h3><p>Compare equipe, avaliação, rotina, segurança, comunicação com a família e plano de continuidade, não apenas preço e distância.</p><h3>Quanto tempo dura o tratamento?</h3><p>Não existe prazo único. A duração depende da gravidade, evolução, condições associadas e objetivos definidos na avaliação.</p>`,
  },
  {
    id: 160,
    slug: 'clinica-de-recuperacao-em-brasilia-df',
    title: 'Onde buscar tratamento para dependência química em Brasília',
    excerpt: 'Guia para procurar clínica de recuperação em Brasília e no Distrito Federal, com avaliação, logística familiar e continuidade do cuidado.',
    faq: [
      ['A localização dentro do Distrito Federal faz diferença?', 'A logística de deslocamento pode facilitar a participação familiar, mas a adequação clínica e a qualidade do programa devem vir primeiro.'],
      ['Uma avaliação pode indicar tratamento sem internação?', 'Sim. Conforme riscos e capacidade de manter a rotina, podem existir alternativas ambulatoriais ou outras modalidades de cuidado.'],
      ['O que levar para a conversa inicial?', 'Tenha informações sobre substâncias, frequência, medicamentos, crises recentes, condições de saúde e tratamentos já realizados.'],
    ],
    content: `<p>Quem procura uma <strong>clínica de recuperação em Brasília</strong> precisa conciliar duas decisões: qual cuidado é adequado e como a família participará em uma região formada por diferentes áreas administrativas. A facilidade de deslocamento importa, mas não substitui avaliação clínica, segurança e um plano terapêutico claro.</p>
<h2>A avaliação vem antes da escolha da modalidade</h2>
<p>Nem toda pessoa que apresenta uso problemático necessita da mesma intervenção. A equipe deve considerar riscos de abstinência, padrão de consumo, saúde física e mental, ambiente doméstico e possibilidade de manter acompanhamento. Em algumas situações, cuidado ambulatorial pode ser considerado; em outras, um ambiente protegido se torna necessário.</p>
<p>Se houver convulsão, perda de consciência, falta de ar, dor no peito, surto ou risco imediato de violência, a prioridade é a rede de urgência, e não a admissão direta em uma clínica.</p>
<h2>Logística familiar no Distrito Federal</h2>
<p>Famílias de Brasília, Taguatinga, Ceilândia, Águas Claras, Gama e outras regiões devem perguntar como funcionam visitas, reuniões e contatos com a equipe. Um local mais próximo pode facilitar a participação, mas só é uma vantagem quando o programa corresponde às necessidades identificadas.</p>
<p>Também é importante decidir quem será a referência da família. Informações desencontradas e limites diferentes entre parentes podem dificultar a adesão. Orientação conjunta ajuda a alinhar decisões e preparar o retorno.</p>
<h2>O que um plano terapêutico deve esclarecer</h2>
<p>O plano precisa explicar objetivos, atividades, acompanhamento profissional, critérios de reavaliação e preparação para a alta. Pergunte como são tratadas ansiedade, depressão e outras condições associadas e o que acontece quando há necessidade de suporte externo.</p>
<p>A modalidade de internação, quando indicada, deve seguir critérios específicos. Consulte o conteúdo sobre <a href="${site}/internacao-voluntaria-involuntaria-compulsoria-diferencas/">as diferenças legais entre os tipos de internação</a>.</p>
<h2>Voltar à rotina sem repetir o ciclo</h2>
<p>A alta exige um plano compatível com a vida no Distrito Federal: deslocamentos, trabalho, estudo, consultas, grupos de apoio e afastamento de ambientes de risco. O acompanhamento não deve ser interrompido apenas porque houve melhora inicial. Veja medidas práticas para <a href="${site}/como-prevenir-recaidas-apos-o-tratamento/">reduzir o risco de recaída</a>.</p>
<h2>Perguntas frequentes</h2><h3>A localização dentro do Distrito Federal faz diferença?</h3><p>A logística pode facilitar a participação familiar, mas a adequação clínica e a qualidade do programa devem vir primeiro.</p><h3>Uma avaliação pode indicar tratamento sem internação?</h3><p>Sim. Conforme os riscos e a capacidade de manter a rotina, podem existir alternativas ambulatoriais ou outras modalidades.</p><h3>O que levar para a conversa inicial?</h3><p>Tenha informações sobre substâncias, frequência, medicamentos, crises recentes, condições de saúde e tratamentos já realizados.</p>`,
  },
  {
    id: 163,
    slug: 'clinica-de-recuperacao-em-cuiaba-mato-grosso',
    title: 'Dependência química em Cuiabá: avaliação, acolhimento e recuperação',
    excerpt: 'Entenda como buscar clínica de recuperação em Cuiabá, avaliar segurança, organizar a família e preparar a continuidade após a alta.',
    faq: [
      ['Como verificar se a instituição é adequada?', 'Solicite informações sobre regularidade, equipe, estrutura, rotina, manejo de urgências e contrato antes da admissão.'],
      ['A família deve visitar antes da internação?', 'Quando possível, conhecer a estrutura e esclarecer regras ajuda a tomar uma decisão mais consciente.'],
      ['Como preparar a alta em Cuiabá?', 'Organize acompanhamento, rotina, limites familiares e respostas para gatilhos antes do retorno.'],
    ],
    content: `<p>Buscar uma <strong>clínica de recuperação em Cuiabá</strong> geralmente acontece quando o consumo já alterou a convivência, o trabalho e a segurança da pessoa. Nesse momento, a família precisa transformar a urgência emocional em perguntas objetivas sobre avaliação, estrutura e continuidade do cuidado.</p>
<h2>Segurança e transparência na admissão</h2>
<p>Antes de autorizar a entrada, solicite informações sobre documentação, profissionais responsáveis, rotina, medicamentos, comunicação com familiares e conduta diante de intercorrências. A instituição deve explicar o que consegue atender e quando é necessário recorrer a hospital ou serviço especializado.</p>
<p>Também é importante informar corretamente condições médicas, uso simultâneo de substâncias e histórico de abstinência. Omissões podem aumentar riscos e prejudicar o planejamento inicial.</p>
<h2>Tratamento adequado ao caso, não um pacote padrão</h2>
<p>A dependência pode estar acompanhada de ansiedade, depressão, impulsividade, perdas sociais ou conflitos intensos. Um plano individual deve estabelecer objetivos possíveis e ser revisto conforme a evolução. Interromper o consumo é essencial, mas reconstruir rotina, responsabilidade e vínculos é o que sustenta mudanças fora do ambiente protegido.</p>
<p>O prazo não deve ser vendido como fórmula fixa. Saiba quais fatores influenciam <a href="${site}/quanto-tempo-dura-tratamento-dependencia-quimica/">o tempo de tratamento da dependência química</a>.</p>
<h2>A participação da família em Cuiabá</h2>
<p>A proximidade entre Cuiabá e Várzea Grande pode facilitar visitas e reuniões, mas a família precisa usar esses momentos para aprender, e não apenas fiscalizar. Comunicação, limites financeiros, responsabilidades e sinais de recaída devem ser discutidos durante o processo.</p>
<p>Parentes também podem precisar de apoio para lidar com culpa e exaustão. Veja como participar de maneira saudável no artigo sobre <a href="${site}/papel-da-familia-na-recuperacao-do-dependente-quimico/">família e recuperação</a>.</p>
<h2>O retorno precisa começar antes da alta</h2>
<p>Um plano de continuidade deve definir onde haverá acompanhamento, quais atividades ocuparão a rotina e o que fazer diante de fissura ou contato com ambientes associados ao consumo. A alta sem referências claras pode devolver a pessoa às mesmas condições que contribuíam para o ciclo.</p>
<h2>Perguntas frequentes</h2><h3>Como verificar se a instituição é adequada?</h3><p>Solicite informações sobre regularidade, equipe, estrutura, rotina, manejo de urgências e contrato antes da admissão.</p><h3>A família deve visitar antes da internação?</h3><p>Quando possível, conhecer a estrutura e esclarecer regras ajuda a tomar uma decisão mais consciente.</p><h3>Como preparar a alta em Cuiabá?</h3><p>Organize acompanhamento, rotina, limites familiares e respostas para gatilhos antes do retorno.</p>`,
  },
  {
    id: 166,
    slug: 'clinica-de-recuperacao-em-varzea-grande-mato-grosso',
    title: 'Ajuda para dependência química em Várzea Grande: primeiros passos',
    excerpt: 'Critérios para escolher clínica de recuperação em Várzea Grande, conduzir a busca familiar e construir um plano seguro para depois da alta.',
    faq: [
      ['Preço e proximidade são suficientes para escolher?', 'Não. Eles devem ser analisados junto com equipe, estrutura, avaliação, segurança e proposta terapêutica.'],
      ['Como agir quando a pessoa recusa ajuda?', 'Evite confrontos durante intoxicação, apresente fatos concretos e procure orientação para organizar uma abordagem familiar.'],
      ['O tratamento termina na alta?', 'Não. A continuidade com acompanhamento, rotina e rede de apoio é parte fundamental da recuperação.'],
    ],
    content: `<p>Em Várzea Grande, a busca por tratamento pode envolver opções locais e instituições na área conurbada com Cuiabá. Essa proximidade amplia escolhas, mas torna ainda mais importante comparar propostas. Uma <strong>clínica de recuperação em Várzea Grande</strong> deve ser selecionada por critérios de cuidado, e não apenas pelo endereço ou pela primeira vaga disponível.</p>
<h2>Comece definindo a necessidade da pessoa</h2>
<p>O padrão de consumo, as condições de saúde, o risco atual e as tentativas anteriores ajudam a determinar o nível de suporte. Leve essas informações para a avaliação e pergunte por que determinada modalidade está sendo sugerida. Uma recomendação responsável deve ser explicável à família.</p>
<p>Quando a pessoa recusa qualquer conversa, acusações costumam aumentar a defesa. Relate consequências observáveis e apresente um primeiro passo viável. O conteúdo sobre <a href="${site}/como-convencer-familiar-aceitar-tratamento/">como abordar um familiar que recusa tratamento</a> pode ajudar na preparação.</p>
<h2>Compare o que acontece durante o tratamento</h2>
<p>Solicite a programação da rotina e esclareça quem acompanha o paciente. Atividades devem estar ligadas a objetivos terapêuticos, como reconhecimento de gatilhos, regulação emocional, responsabilidade, convivência e prevenção de recaídas. Pergunte ainda como são registrados avanços e dificuldades.</p>
<p>Contrato, custos adicionais, visitas, objetos permitidos, uso de medicamentos e critérios de alta precisam ser informados antes da admissão. Transparência protege a família e a pessoa atendida.</p>
<h2>Use a proximidade com Cuiabá de forma útil</h2>
<p>A integração urbana entre Várzea Grande e Cuiabá pode favorecer reuniões e acesso a serviços de continuidade. Ainda assim, deslocamento fácil não compensa uma proposta inadequada. Priorize o cuidado indicado e planeje como consultas, grupos, trabalho e convivência familiar funcionarão depois da alta.</p>
<h2>Construa um plano contra recaídas</h2>
<p>O retorno para os mesmos contatos e hábitos exige preparação. A família deve reconhecer sinais precoces, saber quem acionar e evitar tanto vigilância permanente quanto permissividade. Conheça estratégias de <a href="${site}/como-prevenir-recaidas-apos-o-tratamento/">prevenção de recaídas</a>.</p>
<h2>Perguntas frequentes</h2><h3>Preço e proximidade são suficientes para escolher?</h3><p>Não. Eles devem ser analisados junto com equipe, estrutura, avaliação, segurança e proposta terapêutica.</p><h3>Como agir quando a pessoa recusa ajuda?</h3><p>Evite confrontos durante intoxicação, apresente fatos concretos e procure orientação para organizar uma abordagem familiar.</p><h3>O tratamento termina na alta?</h3><p>Não. A continuidade com acompanhamento, rotina e rede de apoio é parte fundamental da recuperação.</p>`,
  },
];

for (const post of posts) {
  await rpc('wp.editPost', [1, username, password, post.id, {
    post_title: post.title,
    post_excerpt: post.excerpt,
    post_content: post.content,
    custom_fields: [
      { key: '_yoast_wpseo_title', value: post.title },
      { key: '_yoast_wpseo_metadesc', value: post.excerpt },
      { key: '_gat_faq_json', value: JSON.stringify(post.faq.map(([question, answer]) => ({ question, answer }))) },
    ],
  }]);
  console.log(`updated ${post.id} ${post.slug}`);
}
