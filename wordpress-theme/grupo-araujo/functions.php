<?php
/**
 * Inicializacao do tema.
 *
 * @package Grupo_Araujo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GAT_THEME_VERSION', '2.9.6' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/unidades.php';
require_once get_template_directory() . '/inc/contratos.php';

function gat_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'style', 'script', 'gallery', 'caption' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 108,
			'width'       => 480,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'grupo-araujo' ),
			'footer'  => __( 'Menu do rodapé', 'grupo-araujo' ),
		)
	);
}
add_action( 'after_setup_theme', 'gat_theme_setup' );

function gat_enqueue_assets() {
	wp_enqueue_style( 'gat-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11' );
	wp_enqueue_style( 'gat-glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), '3.3.1' );
	wp_enqueue_style( 'gat-aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css', array(), '2.3.4' );
	wp_enqueue_style( 'gat-theme-header', get_stylesheet_uri(), array(), GAT_THEME_VERSION );
	wp_enqueue_style( 'gat-site', get_template_directory_uri() . '/assets/css-original.css', array( 'gat-theme-header', 'gat-swiper', 'gat-glightbox', 'gat-aos' ), GAT_THEME_VERSION );

	wp_enqueue_script( 'gat-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true );
	wp_enqueue_script( 'gat-glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', array(), '3.3.1', true );
	wp_enqueue_script( 'gat-aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), '2.3.4', true );
	wp_enqueue_script( 'gat-lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js', array(), null, true );
	wp_enqueue_script( 'gat-main', get_template_directory_uri() . '/assets/js/main.js', array( 'gat-swiper', 'gat-glightbox', 'gat-aos', 'gat-lucide' ), GAT_THEME_VERSION, true );

	wp_localize_script(
		'gat-main',
		'GAT_CONFIG',
		array(
			'whatsapp' => preg_replace( '/\D+/', '', get_theme_mod( 'gat_whatsapp', '5508005757714' ) ),
			'atendente' => get_theme_mod( 'gat_atendente', 'equipe' ),
			'unidades' => gat_get_unidades(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gat_enqueue_assets' );

function gat_favicon() {
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s">' . "\n", esc_url( get_template_directory_uri() . '/favicon.png' ) );
	}
}
add_action( 'wp_head', 'gat_favicon', 2 );

function gat_meta_description() {
	$description = get_theme_mod( 'gat_meta_description', 'Acolhimento, orientação e acompanhamento especializado para pessoas e famílias, com atendimento humanizado 24 horas.' );
	$image       = get_template_directory_uri() . '/og.png';
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="theme-color" content="#038291">
	<meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<?php
}
add_action( 'wp_head', 'gat_meta_description', 3 );

function gat_default_menu() {
	$links = array(
		'inicio'       => 'Início',
		'sobre'        => 'Sobre',
		'servicos'     => 'Serviços',
		'unidades'     => 'Unidades',
		'diferenciais' => 'Diferenciais',
		'contato'      => 'Contato',
	);
	foreach ( $links as $anchor => $label ) {
		printf( '<a href="%s">%s</a>', esc_url( home_url( '/#' . $anchor ) ), esc_html( $label ) );
	}
}

function gat_health_plan_images() {
	$images = array();
	for ( $index = 1; $index <= 12; $index++ ) {
		$image = get_theme_mod( 'gat_health_plan_image_' . $index );
		if ( $image ) {
			$images[] = $image;
		}
	}
	return $images;
}

function gat_render_health_plans_section() {
	$images = gat_health_plan_images();
	ob_start();
	?>
	<section id="planos-saude" class="section health-plans-section">
		<div class="container">
			<div class="section-heading health-plans-heading" data-aos="fade-up">
				<p class="eyebrow"><?php esc_html_e( 'Convênios', 'grupo-araujo' ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'gat_health_plans_title', 'Planos de Saúde' ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'gat_health_plans_text', 'Consulte nossa equipe para verificar cobertura, disponibilidade e condições de atendimento.' ) ); ?></p>
			</div>
			<?php if ( $images ) : ?>
				<div class="health-plans-carousel swiper" data-health-plans-carousel data-aos="fade-up">
					<div class="swiper-wrapper">
						<?php foreach ( $images as $index => $image ) : ?>
							<div class="swiper-slide">
								<div class="health-plan-logo">
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( sprintf( 'Plano de saúde %d', $index + 1 ) ); ?>" loading="lazy">
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p class="health-plans-empty"><?php esc_html_e( 'Adicione imagens em Aparência > Personalizar > Carrossel - Planos de Saúde.', 'grupo-araujo' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function gat_render_latest_posts_section() {
	$count = max( 1, min( 6, absint( get_theme_mod( 'gat_latest_posts_count', 3 ) ) ) );
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	ob_start();
	?>
	<section id="publicacoes" class="section section-white latest-posts-section">
		<div class="container">
			<div class="section-heading" data-aos="fade-up">
				<p class="eyebrow"><?php esc_html_e( 'Conteúdos', 'grupo-araujo' ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'gat_latest_posts_title', 'Últimas publicações' ) ); ?></h2>
			</div>
			<?php if ( $query->have_posts() ) : ?>
				<div class="latest-posts-grid">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<article class="latest-post-card" data-aos="fade-up">
							<a class="latest-post-image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<span><i data-lucide="newspaper"></i></span>
								<?php endif; ?>
							</a>
							<div class="latest-post-body">
								<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
								<a class="latest-post-link" href="<?php the_permalink(); ?>">
									<?php esc_html_e( 'Ler publicação', 'grupo-araujo' ); ?> <i data-lucide="arrow-right"></i>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<p class="latest-posts-empty"><?php esc_html_e( 'As publicações mais recentes aparecerão aqui.', 'grupo-araujo' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
