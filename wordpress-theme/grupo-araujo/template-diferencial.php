<?php
/**
 * Template Name: Diferencial
 *
 * @package Grupo_Araujo
 */

get_header();

while ( have_posts() ) :
	the_post();

	$hero_image = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : get_template_directory_uri() . '/assets/img/backgrounds/chacara-piscina-03.webp';
	$lead       = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
	$message    = sprintf( 'Olá, equipe do Grupo Araújo Tratamentos. Gostaria de saber mais sobre: %s.', get_the_title() );
	?>
	<main class="differential-page">
		<section class="differential-hero image-band" style="--section-image: url('<?php echo esc_url( $hero_image ); ?>');">
			<div class="container differential-hero-inner">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Diferenciais', 'grupo-araujo' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<?php if ( $lead ) : ?>
						<p class="hero-lead"><?php echo esc_html( $lead ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section section-white differential-main">
			<div class="container differential-layout">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'differential-content entry-content-wrap' ); ?>>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php
						wp_link_pages(
							array(
								'before' => '<nav class="page-links">' . __( 'Paginas:', 'grupo-araujo' ),
								'after'  => '</nav>',
							)
						);
						?>
					</div>
				</article>

				<aside class="differential-contact" aria-label="<?php esc_attr_e( 'Atendimento do Grupo Araújo Tratamentos', 'grupo-araujo' ); ?>">
					<p class="eyebrow"><?php esc_html_e( 'Orientação', 'grupo-araujo' ); ?></p>
					<h2><?php esc_html_e( 'Converse com nossa equipe', 'grupo-araujo' ); ?></h2>
					<p><?php esc_html_e( 'Receba acolhimento, informações sobre unidades e orientação para entender o melhor encaminhamento para sua família.', 'grupo-araujo' ); ?></p>
					<a class="btn btn-primary differential-contact-button" href="<?php echo esc_url( gat_get_whatsapp_url( $message ) ); ?>" target="_blank" rel="noopener">
						<i data-lucide="message-circle"></i>
						<span><?php esc_html_e( 'Chamar no WhatsApp', 'grupo-araujo' ); ?></span>
					</a>
					<a class="btn btn-secondary differential-contact-button" href="<?php echo esc_url( home_url( '/#por-que-falar' ) ); ?>">
						<i data-lucide="arrow-left"></i>
						<span><?php esc_html_e( 'Voltar aos diferenciais', 'grupo-araujo' ); ?></span>
					</a>
				</aside>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
