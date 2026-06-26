<?php
/**
 * Opções do personalizador.
 *
 * @package Grupo_Araujo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gat_home_text_fields() {
	return array(
		'gat_home_hero_primary_button' => array( 'Home - Banner', 'Botao principal', 'Falar pelo WhatsApp', 'text' ),
		'gat_home_hero_secondary_button' => array( 'Home - Banner', 'Botao secundario', 'Conhecer serviços', 'text' ),
		'gat_home_trust_1' => array( 'Home - Banner', 'Item de confianca 1', 'Atendimento 24 horas', 'text' ),
		'gat_home_trust_2' => array( 'Home - Banner', 'Item de confianca 2', 'Equipe multidisciplinar', 'text' ),
		'gat_home_trust_3' => array( 'Home - Banner', 'Item de confianca 3', 'Ética e sigilo', 'text' ),
		'gat_home_trust_4' => array( 'Home - Banner', 'Item de confianca 4', 'Suporte às famílias', 'text' ),

		'gat_home_about_eyebrow' => array( 'Home - Sobre', 'Chamada', 'Sobre', 'text' ),
		'gat_home_about_title' => array( 'Home - Sobre', 'Titulo', 'Conheça o Grupo Araújo Tratamentos', 'text' ),
		'gat_home_about_text' => array( 'Home - Sobre', 'Texto principal', 'O Grupo Araújo Tratamentos nasceu com o propósito de oferecer acolhimento e apoio especializado às pessoas e famílias que enfrentam desafios relacionados ao bem-estar emocional e à qualidade de vida.', 'textarea' ),
		'gat_home_about_paragraph_1' => array( 'Home - Sobre', 'Paragrafo 1', 'Nossa atuação é baseada em princípios de ética, respeito, responsabilidade e humanização, sempre valorizando a individualidade de cada pessoa atendida.', 'textarea' ),
		'gat_home_about_paragraph_2' => array( 'Home - Sobre', 'Paragrafo 2', 'Trabalhamos em parceria com unidades especializadas e profissionais comprometidos com um atendimento responsável e seguro.', 'textarea' ),
		'gat_home_about_vision' => array( 'Home - Sobre', 'Texto de visao', 'Nossa visão é ser referência em atendimento humanizado, promovendo esperança e contribuindo para a transformação de vidas.', 'textarea' ),
		'gat_home_value_1' => array( 'Home - Sobre', 'Valor 1', 'Respeito à dignidade humana', 'text' ),
		'gat_home_value_2' => array( 'Home - Sobre', 'Valor 2', 'Ética e transparência', 'text' ),
		'gat_home_value_3' => array( 'Home - Sobre', 'Valor 3', 'Responsabilidade social', 'text' ),
		'gat_home_value_4' => array( 'Home - Sobre', 'Valor 4', 'Humanização do atendimento', 'text' ),
		'gat_home_value_5' => array( 'Home - Sobre', 'Valor 5', 'Compromisso com as famílias', 'text' ),
		'gat_home_value_6' => array( 'Home - Sobre', 'Valor 6', 'Excelência no acolhimento', 'text' ),

		'gat_home_services_eyebrow' => array( 'Home - Servicos', 'Chamada', 'Serviços', 'text' ),
		'gat_home_services_title' => array( 'Home - Servicos', 'Titulo', 'Atendimento e acompanhamento especializado', 'text' ),
		'gat_home_services_text' => array( 'Home - Servicos', 'Texto', 'O Grupo Araújo Tratamentos oferece suporte e acompanhamento individualizado, respeitando as necessidades de cada pessoa e de sua família.', 'textarea' ),
		'gat_home_service_1_title' => array( 'Home - Servicos', 'Servico 1 - titulo', 'Acolhimento especializado', 'text' ),
		'gat_home_service_1_text' => array( 'Home - Servicos', 'Servico 1 - texto', 'Atendimento realizado de forma humanizada, com foco no bem-estar e no suporte integral.', 'textarea' ),
		'gat_home_service_2_title' => array( 'Home - Servicos', 'Servico 2 - titulo', 'Orientação às famílias', 'text' ),
		'gat_home_service_2_text' => array( 'Home - Servicos', 'Servico 2 - texto', 'Acompanhamento e informações para familiares que necessitam de apoio e esclarecimentos.', 'textarea' ),
		'gat_home_service_3_title' => array( 'Home - Servicos', 'Servico 3 - titulo', 'Programas individualizados', 'text' ),
		'gat_home_service_3_text' => array( 'Home - Servicos', 'Servico 3 - texto', 'Planejamento personalizado, considerando as necessidades específicas de cada caso.', 'textarea' ),
		'gat_home_service_4_title' => array( 'Home - Servicos', 'Servico 4 - titulo', 'Suporte contínuo', 'text' ),
		'gat_home_service_4_text' => array( 'Home - Servicos', 'Servico 4 - texto', 'Acompanhamento responsável com foco na qualidade de vida e no fortalecimento dos vínculos familiares.', 'textarea' ),
		'gat_home_service_5_title' => array( 'Home - Servicos', 'Servico 5 - titulo', 'Atendimento 24 horas', 'text' ),
		'gat_home_service_5_text' => array( 'Home - Servicos', 'Servico 5 - texto', 'Equipe disponível para orientar e prestar informações sempre que necessário.', 'textarea' ),

		'gat_home_units_eyebrow' => array( 'Home - Unidades', 'Chamada', 'Rede de apoio', 'text' ),
		'gat_home_units_title' => array( 'Home - Unidades', 'Titulo', 'Unidades parceiras', 'text' ),
		'gat_home_units_text' => array( 'Home - Unidades', 'Texto', 'Trabalhamos em parceria com unidades especializadas e profissionais comprometidos com um atendimento responsável e seguro.', 'textarea' ),
		'gat_home_units_availability_eyebrow' => array( 'Home - Unidades', 'Chamada disponibilidade', 'Disponibilidade', 'text' ),
		'gat_home_units_availability_title' => array( 'Home - Unidades', 'Titulo disponibilidade', 'Procura outra cidade ou modalidade?', 'text' ),
		'gat_home_units_availability_text' => array( 'Home - Unidades', 'Texto disponibilidade', 'Além das unidades listadas, nossa equipe pode verificar opções conforme o perfil do caso, urgência e disponibilidade no momento do atendimento.', 'textarea' ),
		'gat_home_units_availability_button' => array( 'Home - Unidades', 'Botao disponibilidade', 'Verificar Disponibilidade de Outras Unidades', 'text' ),
		'gat_home_units_type_label' => array( 'Home - Unidades', 'Label tipo de unidade', 'Tipo de unidade', 'text' ),
		'gat_home_units_all_label' => array( 'Home - Unidades', 'Opcao todos', 'Masculina e feminina', 'text' ),
		'gat_home_units_city_label' => array( 'Home - Unidades', 'Label cidade', 'Cidade da unidade', 'text' ),
		'gat_home_units_clear_label' => array( 'Home - Unidades', 'Botao limpar filtros', 'Limpar filtros', 'text' ),
		'gat_home_units_empty' => array( 'Home - Unidades', 'Mensagem sem resultado', 'Nenhuma unidade encontrada com os filtros selecionados.', 'text' ),
		'gat_home_units_help_title' => array( 'Home - Unidades', 'Titulo ajuda', 'Não sabe qual unidade escolher?', 'text' ),
		'gat_home_units_help_text' => array( 'Home - Unidades', 'Texto ajuda', 'Converse com nossa equipe para organizar as informações do caso e entender qual encaminhamento faz mais sentido.', 'textarea' ),
		'gat_home_units_help_button' => array( 'Home - Unidades', 'Botao ajuda', 'Não sei qual unidade escolher', 'text' ),

		'gat_home_mission_eyebrow' => array( 'Home - Missao', 'Chamada', 'Compromisso', 'text' ),
		'gat_home_mission_title' => array( 'Home - Missao', 'Titulo', 'Nossa missão', 'text' ),
		'gat_home_mission_text' => array( 'Home - Missao', 'Texto', 'Promover cuidado, acolhimento e acompanhamento especializado, contribuindo para a reconstrução de vidas e para o fortalecimento da esperança.', 'textarea' ),
		'gat_home_mission_1_title' => array( 'Home - Missao', 'Card 1 - titulo', 'Cuidado humanizado', 'text' ),
		'gat_home_mission_1_text' => array( 'Home - Missao', 'Card 1 - texto', 'Acolhimento com respeito, escuta e atenção à individualidade de cada pessoa.', 'textarea' ),
		'gat_home_mission_2_title' => array( 'Home - Missao', 'Card 2 - titulo', 'Vínculos familiares', 'text' ),
		'gat_home_mission_2_text' => array( 'Home - Missao', 'Card 2 - texto', 'Suporte para fortalecer relações, orientar familiares e ampliar a rede de apoio.', 'textarea' ),
		'gat_home_mission_3_title' => array( 'Home - Missao', 'Card 3 - titulo', 'Reconstrução de histórias', 'text' ),
		'gat_home_mission_3_text' => array( 'Home - Missao', 'Card 3 - texto', 'Compromisso contínuo com qualidade de vida, esperança e novos caminhos.', 'textarea' ),

		'gat_home_differentials_eyebrow' => array( 'Home - Diferenciais', 'Chamada', 'Nossos diferenciais', 'text' ),
		'gat_home_differentials_title' => array( 'Home - Diferenciais', 'Titulo', 'Atendimento com responsabilidade, ética e respeito', 'text' ),
		'gat_home_differentials_text' => array( 'Home - Diferenciais', 'Texto', 'Proporcionamos um ambiente seguro e acolhedor, com suporte contínuo para pessoas e famílias que desejam reconstruir suas histórias.', 'textarea' ),
		'gat_home_differentials_button' => array( 'Home - Diferenciais', 'Botao', 'Entre em contato', 'text' ),
		'gat_home_diff_list_1' => array( 'Home - Diferenciais', 'Lista 1', 'Atendimento 24 horas', 'text' ),
		'gat_home_diff_list_2' => array( 'Home - Diferenciais', 'Lista 2', 'Equipe multidisciplinar', 'text' ),
		'gat_home_diff_list_3' => array( 'Home - Diferenciais', 'Lista 3', 'Ambiente seguro e acolhedor', 'text' ),
		'gat_home_diff_list_4' => array( 'Home - Diferenciais', 'Lista 4', 'Suporte às famílias', 'text' ),
		'gat_home_diff_list_5' => array( 'Home - Diferenciais', 'Lista 5', 'Programas individualizados', 'text' ),
		'gat_home_diff_list_6' => array( 'Home - Diferenciais', 'Lista 6', 'Atendimento com ética e sigilo', 'text' ),

		'gat_home_support_eyebrow' => array( 'Home - Acompanhamento', 'Chamada', 'Acompanhamento', 'text' ),
		'gat_home_support_title' => array( 'Home - Acompanhamento', 'Titulo', 'Suporte para momentos desafiadores', 'text' ),
		'gat_home_support_text' => array( 'Home - Acompanhamento', 'Texto', 'Nosso compromisso é promover qualidade de vida, fortalecimento dos vínculos familiares e suporte contínuo para aqueles que desejam reconstruir suas histórias.', 'textarea' ),
		'gat_home_support_highlight_1' => array( 'Home - Acompanhamento', 'Destaque 1', 'Atendimento 24 horas', 'text' ),
		'gat_home_support_highlight_2' => array( 'Home - Acompanhamento', 'Destaque 2', 'Ética e sigilo', 'text' ),
		'gat_home_support_highlight_3' => array( 'Home - Acompanhamento', 'Destaque 3', 'Acolhimento', 'text' ),
		'gat_home_support_emergency_title' => array( 'Home - Acompanhamento', 'Titulo emergencia', 'Emergência ou risco imediato', 'text' ),
		'gat_home_support_emergency_text' => array( 'Home - Acompanhamento', 'Texto emergencia', 'Em casos de risco à vida, surto grave ou violência, procure imediatamente os serviços de urgência da sua cidade.', 'textarea' ),

		'gat_home_reasons_eyebrow' => array( 'Home - Por que falar', 'Chamada', 'Diferenciais', 'text' ),
		'gat_home_reasons_title' => array( 'Home - Por que falar', 'Titulo', 'Por que falar com o Grupo Araújo Tratamentos?', 'text' ),
		'gat_home_reason_1_title' => array( 'Home - Por que falar', 'Motivo 1 - titulo', 'Atendimento humanizado', 'text' ),
		'gat_home_reason_1_text' => array( 'Home - Por que falar', 'Motivo 1 - texto', 'Escuta cuidadosa, acolhimento e orientação com respeito ao momento de cada família.', 'textarea' ),
		'gat_home_reason_2_title' => array( 'Home - Por que falar', 'Motivo 2 - titulo', 'Programas individualizados', 'text' ),
		'gat_home_reason_2_text' => array( 'Home - Por que falar', 'Motivo 2 - texto', 'Planejamento personalizado, considerando necessidades específicas de cada caso.', 'textarea' ),
		'gat_home_reason_3_title' => array( 'Home - Por que falar', 'Motivo 3 - titulo', 'Equipe multidisciplinar', 'text' ),
		'gat_home_reason_3_text' => array( 'Home - Por que falar', 'Motivo 3 - texto', 'Atuação com profissionais e unidades especializadas comprometidos com atendimento responsável.', 'textarea' ),
		'gat_home_reason_4_title' => array( 'Home - Por que falar', 'Motivo 4 - titulo', 'Ética e sigilo', 'text' ),
		'gat_home_reason_4_text' => array( 'Home - Por que falar', 'Motivo 4 - texto', 'Atendimento conduzido com discrição, transparência e responsabilidade.', 'textarea' ),
		'gat_home_reason_5_title' => array( 'Home - Por que falar', 'Motivo 5 - titulo', 'Suporte às famílias', 'text' ),
		'gat_home_reason_5_text' => array( 'Home - Por que falar', 'Motivo 5 - texto', 'Informação, acompanhamento e presença para fortalecer vínculos familiares.', 'textarea' ),

		'gat_home_cta_eyebrow' => array( 'Home - CTA', 'Chamada', 'Contato responsável', 'text' ),
		'gat_home_cta_title' => array( 'Home - CTA', 'Titulo', 'Precisa de orientação agora?', 'text' ),
		'gat_home_cta_text' => array( 'Home - CTA', 'Texto', 'Estamos prontos para oferecer orientação e esclarecer suas dúvidas.', 'textarea' ),
		'gat_home_cta_button' => array( 'Home - CTA', 'Botao', 'Chamar no WhatsApp', 'text' ),

		'gat_home_faq_eyebrow' => array( 'Home - FAQ', 'Chamada', 'Perguntas frequentes', 'text' ),
		'gat_home_faq_title' => array( 'Home - FAQ', 'Titulo', 'Dúvidas comuns', 'text' ),
		'gat_home_faq_1_question' => array( 'Home - FAQ', 'Pergunta 1', 'O atendimento é humanizado?', 'text' ),
		'gat_home_faq_1_answer' => array( 'Home - FAQ', 'Resposta 1', 'Sim. Nossa atuação é baseada em acolhimento, respeito, ética e atenção individualizada.', 'textarea' ),
		'gat_home_faq_2_question' => array( 'Home - FAQ', 'Pergunta 2', 'O atendimento funciona 24 horas?', 'text' ),
		'gat_home_faq_2_answer' => array( 'Home - FAQ', 'Resposta 2', 'Sim. A equipe fica disponível para orientar e prestar informações sempre que necessário.', 'textarea' ),
		'gat_home_faq_3_question' => array( 'Home - FAQ', 'Pergunta 3', 'Vocês oferecem orientação às famílias?', 'text' ),
		'gat_home_faq_3_answer' => array( 'Home - FAQ', 'Resposta 3', 'Sim. Oferecemos acompanhamento e informações para familiares que necessitam de apoio e esclarecimentos.', 'textarea' ),
		'gat_home_faq_4_question' => array( 'Home - FAQ', 'Pergunta 4', 'O acompanhamento é individualizado?', 'text' ),
		'gat_home_faq_4_answer' => array( 'Home - FAQ', 'Resposta 4', 'Sim. O planejamento considera as necessidades específicas de cada pessoa e família.', 'textarea' ),
		'gat_home_faq_5_question' => array( 'Home - FAQ', 'Pergunta 5', 'O contato é sigiloso?', 'text' ),
		'gat_home_faq_5_answer' => array( 'Home - FAQ', 'Resposta 5', 'Sim. O atendimento é conduzido com ética, sigilo e respeito à dignidade humana.', 'textarea' ),
		'gat_home_faq_6_question' => array( 'Home - FAQ', 'Pergunta 6', 'Vocês trabalham com unidades especializadas?', 'text' ),
		'gat_home_faq_6_answer' => array( 'Home - FAQ', 'Resposta 6', 'Sim. Trabalhamos em parceria com unidades especializadas e profissionais comprometidos com atendimento responsável e seguro.', 'textarea' ),
		'gat_home_faq_7_question' => array( 'Home - FAQ', 'Pergunta 7', 'O formulário substitui atendimento de urgência?', 'text' ),
		'gat_home_faq_7_answer' => array( 'Home - FAQ', 'Resposta 7', 'Não. Em casos de risco à vida, surto grave ou violência, procure imediatamente os serviços de urgência da sua cidade.', 'textarea' ),

		'gat_home_contact_eyebrow' => array( 'Home - Contato', 'Chamada', 'Contato', 'text' ),
		'gat_home_contact_title' => array( 'Home - Contato', 'Titulo', 'Fale conosco', 'text' ),
		'gat_home_contact_text' => array( 'Home - Contato', 'Texto', 'Estamos prontos para oferecer orientação e esclarecer suas dúvidas.', 'textarea' ),
		'gat_home_contact_name' => array( 'Home - Contato', 'Nome exibido', 'Grupo Araújo Tratamentos', 'text' ),
		'gat_home_contact_hours' => array( 'Home - Contato', 'Horario exibido', 'Horário de atendimento: 24 horas por dia', 'text' ),
		'gat_home_contact_direct' => array( 'Home - Contato', 'Aviso WhatsApp', 'O envio abre uma conversa no WhatsApp com os dados preenchidos.', 'textarea' ),
		'gat_home_contact_form_name' => array( 'Home - Contato', 'Formulario - nome', 'Nome', 'text' ),
		'gat_home_contact_form_phone' => array( 'Home - Contato', 'Formulario - telefone', 'Telefone', 'text' ),
		'gat_home_contact_form_email' => array( 'Home - Contato', 'Formulario - e-mail', 'E-mail', 'text' ),
		'gat_home_contact_form_message' => array( 'Home - Contato', 'Formulario - mensagem', 'Mensagem', 'text' ),
		'gat_home_contact_form_button' => array( 'Home - Contato', 'Formulario - botao', 'Enviar mensagem', 'text' ),
	);
}

function gat_customize_register( $customizer ) {
	$customizer->add_section(
		'gat_settings',
		array(
			'title'    => __( 'Grupo Araújo - Configurações', 'grupo-araujo' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'gat_whatsapp'        => array( 'WhatsApp com código do país', '5508005757714', 'text' ),
		'gat_atendente'       => array( 'Nome usado nas mensagens', 'equipe', 'text' ),
		'gat_phone'           => array( 'Telefone exibido', '0800 575 7714', 'text' ),
		'gat_cnpj'            => array( 'CNPJ', '67.252.579/0001-50', 'text' ),
		'gat_site'            => array( 'Endereço do site exibido', 'www.grupoaraujotratamentos.com.br', 'text' ),
		'gat_hero_eyebrow'    => array( 'Chamada do banner', 'Atendimento humanizado e acompanhamento especializado', 'text' ),
		'gat_hero_title'      => array( 'Título do banner', 'Grupo Araújo Tratamentos', 'text' ),
		'gat_hero_text'       => array( 'Texto do banner', 'Transformando vidas, restaurando sonhos. Oferecemos acolhimento, orientação e acompanhamento individualizado para pessoas e famílias que buscam apoio em momentos desafiadores.', 'textarea' ),
		'gat_meta_description'=> array( 'Descrição para buscadores', 'Acolhimento, orientação e acompanhamento especializado para pessoas e famílias, com atendimento humanizado 24 horas.', 'textarea' ),
	);

	$fields['gat_header_cta_text'] = array( 'Texto do botao do cabecalho', 'Fale Conosco', 'text' );
	$fields['gat_footer_text'] = array( 'Texto do rodape', 'Acolhimento, orientação e acompanhamento especializado para pessoas e famílias.', 'textarea' );
	$fields['gat_footer_note'] = array( 'Observacao do rodape', 'Atendimento 24 horas por dia, com ética, sigilo e respeito.', 'textarea' );
	$fields['gat_floating_whatsapp_text'] = array( 'Texto do WhatsApp flutuante', 'Falar pelo WhatsApp', 'text' );

	foreach ( $fields as $id => $field ) {
		$customizer->add_setting(
			$id,
			array(
				'default'           => $field[1],
				'sanitize_callback' => 'textarea' === $field[2] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$customizer->add_control(
			$id,
			array(
				'label'   => $field[0],
				'section' => 'gat_settings',
				'type'    => $field[2],
			)
		);
	}

	$home_sections = array();
	foreach ( gat_home_text_fields() as $id => $field ) {
		$section_title = $field[0];
		$section_id    = sanitize_key( 'gat_' . remove_accents( strtolower( str_replace( ' ', '_', $section_title ) ) ) );

		if ( ! isset( $home_sections[ $section_id ] ) ) {
			$customizer->add_section(
				$section_id,
				array(
					'title'    => $section_title,
					'priority' => 32,
				)
			);
			$home_sections[ $section_id ] = true;
		}

		$customizer->add_setting(
			$id,
			array(
				'default'           => $field[2],
				'sanitize_callback' => 'textarea' === $field[3] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$customizer->add_control(
			$id,
			array(
				'label'   => $field[1],
				'section' => $section_id,
				'type'    => $field[3],
			)
		);
	}

	$customizer->add_section(
		'gat_health_plans',
		array(
			'title'       => __( 'Carrossel - Planos de Saúde', 'grupo-araujo' ),
			'description' => __( 'Envie as imagens ou logotipos exibidos no carrossel da página inicial.', 'grupo-araujo' ),
			'priority'    => 31,
		)
	);

	$customizer->add_setting(
		'gat_health_plans_title',
		array(
			'default'           => 'Planos de Saúde',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$customizer->add_control(
		'gat_health_plans_title',
		array(
			'label'   => __( 'Título da seção', 'grupo-araujo' ),
			'section' => 'gat_health_plans',
			'type'    => 'text',
		)
	);

	$customizer->add_setting(
		'gat_health_plans_text',
		array(
			'default'           => 'Consulte nossa equipe para verificar cobertura, disponibilidade e condições de atendimento.',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$customizer->add_control(
		'gat_health_plans_text',
		array(
			'label'   => __( 'Texto da seção', 'grupo-araujo' ),
			'section' => 'gat_health_plans',
			'type'    => 'textarea',
		)
	);

	for ( $index = 1; $index <= 12; $index++ ) {
		$setting_id = 'gat_health_plan_image_' . $index;
		$customizer->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$customizer->add_control(
			new WP_Customize_Image_Control(
				$customizer,
				$setting_id,
				array(
					'label'   => sprintf( __( 'Imagem %d', 'grupo-araujo' ), $index ),
					'section' => 'gat_health_plans',
				)
			)
		);
	}

	$customizer->add_section(
		'gat_latest_posts',
		array(
			'title'    => __( 'Últimas publicações', 'grupo-araujo' ),
			'priority' => 33,
		)
	);
	$customizer->add_setting(
		'gat_latest_posts_title',
		array(
			'default'           => 'Últimas publicações',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$customizer->add_control(
		'gat_latest_posts_title',
		array(
			'label'   => __( 'Título da seção', 'grupo-araujo' ),
			'section' => 'gat_latest_posts',
			'type'    => 'text',
		)
	);
	$customizer->add_setting(
		'gat_latest_posts_count',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
		)
	);
	$customizer->add_control(
		'gat_latest_posts_count',
		array(
			'label'       => __( 'Quantidade de publicações', 'grupo-araujo' ),
			'section'     => 'gat_latest_posts',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 6 ),
		)
	);

}
add_action( 'customize_register', 'gat_customize_register' );
