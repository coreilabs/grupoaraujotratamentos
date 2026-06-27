<?php
/**
 * Template Name: Modelo Pagina Inicial
 * Template Post Type: page
 *
 * Página inicial convertida do site estático.
 *
 * @package Grupo_Araujo
 */

get_header();

function gat_replace_first_home_text( $html, $search, $replace ) {
	$position = strpos( $html, $search );

	if ( false === $position ) {
		return $html;
	}

	return substr_replace( $html, $replace, $position, strlen( $search ) );
}

$source_file = get_template_directory() . '/template-parts/home-source.html';
$source_html = is_readable( $source_file ) ? file_get_contents( $source_file ) : '';
$main_html   = '';

if ( preg_match( '#<main>(.*?)</main>#s', $source_html, $matches ) ) {
	$main_html = '<main>' . $matches[1] . '</main>';
}

$main_html = str_replace(
	array(
		'/site/assets/img/backgrounds/',
		'<p class="eyebrow">Atendimento humanizado e acompanhamento especializado</p>',
		'<h1>Grupo Araújo Tratamentos</h1>',
		'<p class="hero-lead">Transformando vidas, restaurando sonhos. Oferecemos acolhimento, orientação e acompanhamento individualizado para pessoas e famílias que buscam apoio em momentos desafiadores.</p>',
		'Telefone / WhatsApp: 0800 575 7714',
		'Site: www.grupoaraujotratamentos.com.br',
		'CNPJ: 67.252.579/0001-50',
	),
	array(
		esc_url( get_template_directory_uri() . '/assets/img/backgrounds/' ),
		'<p class="eyebrow">' . esc_html( get_theme_mod( 'gat_hero_eyebrow', 'Atendimento humanizado e acompanhamento especializado' ) ) . '</p>',
		'<h1>' . esc_html( get_theme_mod( 'gat_hero_title', 'Grupo Araújo Tratamentos' ) ) . '</h1>',
		'<p class="hero-lead">' . esc_html( get_theme_mod( 'gat_hero_text', 'Transformando vidas, restaurando sonhos. Oferecemos acolhimento, orientação e acompanhamento individualizado para pessoas e famílias que buscam apoio em momentos desafiadores.' ) ) . '</p>',
		'Telefone / WhatsApp: ' . esc_html( get_theme_mod( 'gat_phone', '0800 575 7714' ) ),
		'Site: ' . esc_html( get_theme_mod( 'gat_site', 'www.grupoaraujotratamentos.com.br' ) ),
		'CNPJ: ' . esc_html( get_theme_mod( 'gat_cnpj', '67.252.579/0001-50' ) ),
	),
	$main_html
);

foreach ( gat_home_text_fields() as $id => $field ) {
	$main_html = gat_replace_first_home_text(
		$main_html,
		$field[2],
		esc_html( get_theme_mod( $id, $field[2] ) )
	);
}

$main_html = str_replace(
	'<section id="servicos" class="section section-white">',
	gat_render_health_plans_section() . '<section id="servicos" class="section section-white">',
	$main_html
);

$main_html = str_replace(
	'<section class="cta-strip visual-bg"',
	gat_render_latest_posts_section() . '<section class="cta-strip visual-bg"',
	$main_html
);

echo $main_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

get_footer();
