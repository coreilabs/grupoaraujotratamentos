<?php
/**
 * Template Name: Politica de Privacidade
 *
 * @package Grupo_Araujo
 */

get_header();

while ( have_posts() ) :
	the_post();

	$hero_image = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : get_template_directory_uri() . '/assets/img/backgrounds/chacara-piscina-03.webp';
	$lead       = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
	?>
	<main class="privacy-page">
		<section class="privacy-hero image-band" style="--section-image: url('<?php echo esc_url( $hero_image ); ?>');">
			<div class="container privacy-hero-inner">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Protecao de dados e LGPD', 'grupo-araujo' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<?php if ( $lead ) : ?>
						<p class="hero-lead"><?php echo esc_html( $lead ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section section-white privacy-main">
			<div class="container privacy-layout">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'privacy-content entry-content-wrap' ); ?>>
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

				<aside class="privacy-contact" aria-label="<?php esc_attr_e( 'Canais de contato', 'grupo-araujo' ); ?>">
					<h2><?php esc_html_e( 'Canais de contato', 'grupo-araujo' ); ?></h2>
					<p><?php esc_html_e( 'Em caso de duvidas sobre esta Politica de Privacidade, entre em contato com o Grupo Araujo Tratamentos.', 'grupo-araujo' ); ?></p>
					<div class="privacy-contact-details">
						<p><strong>WhatsApp:</strong> <?php echo esc_html( get_theme_mod( 'gat_phone_display', '0800 575 7714' ) ); ?></p>
						<p><strong>Site:</strong> <?php echo esc_html( get_theme_mod( 'gat_site', 'www.grupoaraujotratamentos.com.br' ) ); ?></p>
						<p><strong>CNPJ:</strong> <?php echo esc_html( get_theme_mod( 'gat_cnpj', '67.252.579/0001-50' ) ); ?></p>
					</div>
					<div class="privacy-contact-actions">
						<a class="btn btn-primary full" href="#" data-whatsapp-default target="_blank" rel="noopener">
							<i data-lucide="message-circle"></i><span><?php esc_html_e( 'Conversar no WhatsApp', 'grupo-araujo' ); ?></span>
						</a>
						<a class="btn btn-secondary full" href="<?php echo esc_url( 'tel:' . preg_replace( '/\D+/', '', get_theme_mod( 'gat_phone_display', '08005757714' ) ) ); ?>">
							<i data-lucide="phone"></i><span><?php esc_html_e( 'Ligar agora', 'grupo-araujo' ); ?></span>
						</a>
					</div>
				</aside>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
