<?php
/**
 * Inicializacao do tema.
 *
 * @package Grupo_Araujo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GAT_THEME_VERSION', '3.0.8' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/unidades.php';
require_once get_template_directory() . '/inc/contratos.php';

function gat_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'gat-og-image', 1200, 630, true );
	add_image_size( 'gat-featured-wide', 1280, 720, true );
	add_theme_support( 'responsive-embeds' );
	add_post_type_support( 'page', 'excerpt' );
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

function gat_widgets_init() {
	$shared_args = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Sidebar principal', 'grupo-araujo' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Widgets exibidos em posts, paginas internas, arquivos e busca.', 'grupo-araujo' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Antes da publicacao', 'grupo-araujo' ),
				'id'          => 'single-before',
				'description' => __( 'Area para widgets ou plugins antes do conteudo do post individual.', 'grupo-araujo' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Depois da publicacao', 'grupo-araujo' ),
				'id'          => 'single-after',
				'description' => __( 'Area para widgets ou plugins depois do conteudo do post individual.', 'grupo-araujo' ),
			)
		)
	);
}
add_action( 'widgets_init', 'gat_widgets_init' );

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

function gat_meta_description_legacy() {
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

function gat_get_meta_description() {
	if ( is_singular() ) {
		$description = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 32 );

		if ( $description ) {
			return $description;
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term_description = term_description();

		if ( $term_description ) {
			return wp_trim_words( wp_strip_all_tags( $term_description ), 32 );
		}
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );

		if ( $posts_page_id ) {
			$description = get_post_field( 'post_excerpt', $posts_page_id );

			if ( ! $description ) {
				$description = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $posts_page_id ) ), 32 );
			}

			if ( $description ) {
				return $description;
			}
		}
	}

	return get_theme_mod( 'gat_meta_description', 'Acolhimento, orientacao e acompanhamento especializado para pessoas e familias, com atendimento humanizado 24 horas.' );
}

function gat_get_og_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$image = gat_generate_post_og_image( get_the_ID() );

		if ( $image ) {
			return $image;
		}
	}

	return array(
		'url'    => get_template_directory_uri() . '/og.png',
		'width'  => 1200,
		'height' => 630,
	);
}

function gat_generate_post_og_image( $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$source_path  = $thumbnail_id ? get_attached_file( $thumbnail_id ) : '';

	if ( ! $source_path || ! is_readable( $source_path ) || ! function_exists( 'imagecreatefromstring' ) ) {
		return false;
	}

	$uploads = wp_upload_dir();

	if ( ! empty( $uploads['error'] ) ) {
		return false;
	}

	$directory = trailingslashit( $uploads['basedir'] ) . 'gat-og';
	$url_base  = trailingslashit( $uploads['baseurl'] ) . 'gat-og';

	if ( ! wp_mkdir_p( $directory ) ) {
		return false;
	}

	$modified    = (int) get_post_modified_time( 'U', true, $post_id );
	$target_name = sprintf( 'post-%d-%d.jpg', $post_id, $modified );
	$target_path = trailingslashit( $directory ) . $target_name;
	$target_url  = trailingslashit( $url_base ) . $target_name;

	if ( file_exists( $target_path ) ) {
		return array(
			'url'    => $target_url,
			'width'  => 1200,
			'height' => 630,
		);
	}

	$source_data = file_get_contents( $source_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$source      = $source_data ? imagecreatefromstring( $source_data ) : false;

	if ( ! $source ) {
		return false;
	}

	$source_width  = imagesx( $source );
	$source_height = imagesy( $source );
	$target_width  = 1200;
	$target_height = 630;
	$source_ratio  = $source_width / $source_height;
	$target_ratio  = $target_width / $target_height;

	if ( $source_ratio > $target_ratio ) {
		$crop_height = $source_height;
		$crop_width  = (int) round( $source_height * $target_ratio );
		$src_x       = (int) round( ( $source_width - $crop_width ) / 2 );
		$src_y       = 0;
	} else {
		$crop_width  = $source_width;
		$crop_height = (int) round( $source_width / $target_ratio );
		$src_x       = 0;
		$src_y       = (int) round( ( $source_height - $crop_height ) / 2 );
	}

	$target = imagecreatetruecolor( $target_width, $target_height );
	$white  = imagecolorallocate( $target, 255, 255, 255 );
	imagefill( $target, 0, 0, $white );
	imagecopyresampled( $target, $source, 0, 0, $src_x, $src_y, $target_width, $target_height, $crop_width, $crop_height );

	$saved = imagejpeg( $target, $target_path, 88 );
	imagedestroy( $source );
	imagedestroy( $target );

	if ( ! $saved ) {
		return false;
	}

	return array(
		'url'    => $target_url,
		'width'  => 1200,
		'height' => 630,
	);
}

function gat_get_og_url() {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		return $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
	}

	if ( is_archive() || is_search() ) {
		return get_pagenum_link();
	}

	return home_url( add_query_arg( null, null ) );
}

function gat_meta_description() {
	$description = gat_get_meta_description();
	$image       = gat_get_og_image();
	$type        = is_singular( 'post' ) ? 'article' : 'website';
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="theme-color" content="#038291">
	<meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:url" content="<?php echo esc_url( gat_get_og_url() ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image['url'] ); ?>">
	<meta property="og:image:width" content="<?php echo esc_attr( $image['width'] ); ?>">
	<meta property="og:image:height" content="<?php echo esc_attr( $image['height'] ); ?>">
	<meta property="og:image:alt" content="<?php echo esc_attr( is_singular() ? get_the_title() : get_bloginfo( 'name' ) ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $image['url'] ); ?>">
	<?php if ( is_singular( 'post' ) ) : ?>
		<meta property="article:published_time" content="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
		<meta property="article:author" content="<?php echo esc_attr( get_the_author() ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'gat_meta_description', 3 );

function gat_render_custom_code( $setting_id ) {
	$custom_code = get_theme_mod( $setting_id, '' );

	if ( ! $custom_code ) {
		return;
	}

	echo "\n" . $custom_code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function gat_custom_head_code() {
	gat_render_custom_code( 'gat_custom_head_code' );
}
add_action( 'wp_head', 'gat_custom_head_code', 99 );

function gat_custom_body_code() {
	gat_render_custom_code( 'gat_custom_body_code' );
}
add_action( 'wp_body_open', 'gat_custom_body_code', 5 );

function gat_custom_footer_code() {
	gat_render_custom_code( 'gat_custom_footer_code' );
}
add_action( 'wp_footer', 'gat_custom_footer_code', 99 );

function gat_has_sidebar() {
	return is_active_sidebar( 'sidebar-1' );
}

function gat_posted_on() {
	printf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

function gat_get_whatsapp_number() {
	return preg_replace( '/\D+/', '', get_theme_mod( 'gat_whatsapp', '5508005757714' ) );
}

function gat_get_whatsapp_url( $message = '' ) {
	$url = 'https://wa.me/' . gat_get_whatsapp_number();

	if ( $message ) {
		$url = add_query_arg( 'text', $message, $url );
	}

	return $url;
}

function gat_share_icon( $network ) {
	$icons = array(
		'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2a9.86 9.86 0 0 0-8.43 14.96L2.4 21.6l4.76-1.18A9.87 9.87 0 1 0 12.04 2Zm.01 1.8a8.06 8.06 0 0 1 6.83 12.36 8.05 8.05 0 0 1-10.8 2.54l-.34-.2-2.82.7.72-2.74-.22-.36a8.06 8.06 0 0 1 6.63-12.3Zm-3.4 4.22c-.17 0-.45.06-.69.33-.24.26-.9.88-.9 2.14 0 1.25.92 2.47 1.04 2.64.13.17 1.78 2.84 4.39 3.86 2.17.86 2.62.69 3.09.64.47-.04 1.52-.62 1.73-1.22.21-.6.21-1.12.15-1.22-.06-.11-.24-.17-.5-.3-.26-.13-1.53-.76-1.77-.84-.24-.09-.41-.13-.58.13-.17.25-.67.83-.82 1-.15.17-.3.19-.56.06-.26-.13-1.09-.4-2.08-1.28-.77-.69-1.29-1.54-1.44-1.8-.15-.26-.02-.4.11-.53.12-.12.26-.3.39-.45.13-.15.17-.26.26-.43.09-.17.04-.32-.02-.45-.07-.13-.58-1.4-.8-1.92-.2-.5-.41-.43-.58-.44h-.5Z"/></svg>',
		'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.2 8.6V6.9c0-.8.55-1 1.02-1h1.96V2.62A26.8 26.8 0 0 0 14.3 2c-2.85 0-4.8 1.74-4.8 4.94V8.6H6.28v3.67H9.5V22h3.96v-9.73h3.1l.5-3.67H14.2Z"/></svg>',
		'x'        => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.8 10.47 21.07 2h-1.72l-6.31 7.35L8 2H2.2l7.62 11.08L2.2 22h1.72l6.66-7.75L15.9 22h5.8l-7.9-11.53Zm-2.36 2.75-.77-1.1L4.52 3.3h2.65l4.96 7.1.77 1.1 6.46 9.26h-2.65l-5.27-7.54Z"/></svg>',
		'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.2 8.8H1.9V22h3.3V8.8ZM3.55 2.4A1.93 1.93 0 1 0 3.5 6.25h.03A1.93 1.93 0 1 0 3.55 2.4ZM22.1 14.43c0-3.56-1.9-5.22-4.44-5.22a3.82 3.82 0 0 0-3.47 1.92V8.8h-3.3c.04 1.24 0 13.2 0 13.2h3.3v-7.37c0-.4.03-.79.15-1.07.32-.79 1.05-1.6 2.28-1.6 1.6 0 2.25 1.22 2.25 3.02V22h3.3v-7.57Z"/></svg>',
		'telegram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.74 3.68c.25-1.05-.38-1.46-1.12-1.16L2.86 9.37c-1.22.48-1.2 1.16-.22 1.46l4.56 1.42 10.56-6.66c.5-.3.95-.14.58.2l-8.55 7.72-.32 4.74c.47 0 .68-.22.94-.48l2.26-2.2 4.7 3.47c.87.48 1.49.23 1.7-.8l2.67-14.56Z"/></svg>',
		'native'   => '<i data-lucide="share-2"></i>',
		'copy'     => '<i data-lucide="copy"></i>',
	);

	return $icons[ $network ] ?? '';
}

function gat_render_article_share() {
	$url           = get_permalink();
	$title         = get_the_title();
	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( $title );
	$whatsapp_text = sprintf( 'Confira este artigo do Grupo Araújo Tratamentos: %s %s', $title, $url );
	$share_links   = array(
		array(
			'network' => 'whatsapp',
			'label'   => 'WhatsApp',
			'url'     => gat_get_whatsapp_url( $whatsapp_text ),
		),
		array(
			'network' => 'facebook',
			'label'   => 'Facebook',
			'url'     => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
		),
		array(
			'network' => 'x',
			'label'   => 'X',
			'url'     => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
		),
		array(
			'network' => 'linkedin',
			'label'   => 'LinkedIn',
			'url'     => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
		),
		array(
			'network' => 'telegram',
			'label'   => 'Telegram',
			'url'     => 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title,
		),
	);
	?>
	<section class="article-share" aria-labelledby="article-share-title" data-share-section>
		<div class="article-share-heading">
			<p class="eyebrow"><?php esc_html_e( 'Compartilhamento', 'grupo-araujo' ); ?></p>
			<h2 id="article-share-title"><?php esc_html_e( 'Compartilhe este artigo', 'grupo-araujo' ); ?></h2>
		</div>
		<div class="article-share-actions">
			<?php foreach ( $share_links as $link ) : ?>
				<a class="share-button share-<?php echo esc_attr( $link['network'] ); ?>" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo gat_share_icon( $link['network'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( $link['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
			<button class="share-button share-native" type="button" data-native-share data-share-title="<?php echo esc_attr( $title ); ?>" data-share-url="<?php echo esc_url( $url ); ?>">
				<?php echo gat_share_icon( 'native' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Compartilhar', 'grupo-araujo' ); ?></span>
			</button>
		</div>
		<div class="article-share-copy">
			<label for="article-share-url"><?php esc_html_e( 'Link do artigo', 'grupo-araujo' ); ?></label>
			<div class="share-copy-control">
				<input id="article-share-url" type="url" value="<?php echo esc_url( $url ); ?>" readonly data-share-url-field>
				<button type="button" data-copy-share-url>
					<?php echo gat_share_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'Copiar', 'grupo-araujo' ); ?></span>
				</button>
			</div>
			<p class="share-copy-feedback" data-copy-feedback hidden><?php esc_html_e( 'Link copiado.', 'grupo-araujo' ); ?></p>
		</div>
	</section>
	<?php
}

function gat_render_article_institutional() {
	$message = 'Olá, equipe do Grupo Araújo Tratamentos. Li uma publicação no site e gostaria de conversar sobre atendimento e orientação.';
	?>
	<section class="article-institutional" aria-labelledby="article-institutional-title">
		<div class="article-institutional-logo">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/logotipo-vertical.png' ); ?>" alt="<?php esc_attr_e( 'Grupo Araújo Tratamentos', 'grupo-araujo' ); ?>" loading="lazy">
		</div>
		<div class="article-institutional-content">
			<p class="eyebrow"><?php esc_html_e( 'Grupo Araújo Tratamentos', 'grupo-araujo' ); ?></p>
			<h2 id="article-institutional-title"><?php esc_html_e( 'Acolhimento e orientação para famílias', 'grupo-araujo' ); ?></h2>
			<p><?php esc_html_e( 'Atuamos com atendimento humanizado, escuta responsável e encaminhamento para unidades parceiras conforme o perfil de cada caso. Nossa equipe está disponível para orientar famílias com sigilo, respeito e cuidado.', 'grupo-araujo' ); ?></p>
			<a class="btn btn-primary" href="<?php echo esc_url( gat_get_whatsapp_url( $message ) ); ?>" target="_blank" rel="noopener">
				<i data-lucide="message-circle"></i>
				<span><?php esc_html_e( 'Entrar em contato pelo WhatsApp', 'grupo-araujo' ); ?></span>
			</a>
		</div>
	</section>
	<?php
}

function gat_render_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => __( 'Anteriores', 'grupo-araujo' ),
			'next_text'          => __( 'Proximas', 'grupo-araujo' ),
			'screen_reader_text' => __( 'Navegacao de publicacoes', 'grupo-araujo' ),
		)
	);
}

function gat_ensure_latest_posts_page() {
	$page = get_page_by_path( 'publicacoes' );

	if ( $page ) {
		if ( 'template-latest-posts.php' !== get_page_template_slug( $page->ID ) ) {
			update_post_meta( $page->ID, '_wp_page_template', 'template-latest-posts.php' );
		}

		return (int) $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => 'publicacoes',
			'post_title'   => 'Publicações',
			'post_excerpt' => 'Conteúdos informativos do Grupo Araújo Tratamentos sobre dependência química, alcoolismo, família e recuperação.',
			'post_content' => '<p>Conteúdos informativos para famílias e pessoas que buscam orientação sobre dependência química, alcoolismo, tratamento e recuperação.</p>',
		)
	);

	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( $page_id, '_wp_page_template', 'template-latest-posts.php' );
		return (int) $page_id;
	}

	return 0;
}

function gat_get_latest_posts_url() {
	$page_id = gat_ensure_latest_posts_page();

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		return get_permalink( $posts_page_id );
	}

	return home_url( '/' );
}

add_action( 'after_switch_theme', 'gat_ensure_latest_posts_page' );
add_action( 'admin_init', 'gat_ensure_latest_posts_page' );
add_action( 'init', 'gat_ensure_latest_posts_page', 21 );

function gat_get_single_content_without_duplicate_title() {
	$content = apply_filters( 'the_content', get_the_content() );

	if ( ! $content ) {
		return $content;
	}

	return preg_replace( '/^\s*<h1\b[^>]*>.*?<\/h1>\s*/is', '', $content, 1 );
}

function gat_default_menu() {
	$links = array(
		'inicio'       => 'Início',
		'sobre'        => 'Sobre',
		'servicos'     => 'Serviços',
		'unidades'     => 'Unidades',
		'diferenciais' => 'Diferenciais',
		'contato'      => 'Contato',
	);
	echo '<ul class="gat-menu">';
	foreach ( $links as $anchor => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/#' . $anchor ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

function gat_default_nav_links() {
	return array(
		array(
			'title' => 'Inicio',
			'url'   => home_url( '/#inicio' ),
		),
		array(
			'title' => 'Sobre',
			'url'   => home_url( '/#sobre' ),
		),
		array(
			'title' => 'Servicos',
			'url'   => home_url( '/#servicos' ),
		),
		array(
			'title' => 'Unidades',
			'url'   => home_url( '/#unidades' ),
		),
		array(
			'title' => 'Diferenciais',
			'url'   => home_url( '/#diferenciais' ),
		),
		array(
			'title' => 'Contato',
			'url'   => home_url( '/#contato' ),
		),
	);
}

function gat_get_or_create_menu( $name, $links ) {
	$menu = wp_get_nav_menu_object( $name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $name );

		if ( is_wp_error( $menu_id ) ) {
			return 0;
		}

		foreach ( $links as $link ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $link['title'],
					'menu-item-url'    => $link['url'],
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);
		}

		return (int) $menu_id;
	}

	return (int) $menu->term_id;
}

function gat_default_privacy_content() {
	return '
<p class="privacy-notice"><strong>O Grupo Araujo Tratamentos respeita a privacidade dos usuarios e esta comprometido com a protecao dos dados pessoais, em conformidade com a Lei Geral de Protecao de Dados (LGPD - Lei no 13.709/2018).</strong></p>

<h2>1. Coleta de Informacoes</h2>
<p>Podemos coletar informacoes fornecidas voluntariamente pelo usuario, como:</p>
<ul>
<li>Nome</li>
<li>Telefone</li>
<li>Endereco de e-mail</li>
<li>Informacoes enviadas por meio dos formularios do site</li>
</ul>

<h2>2. Utilizacao das Informacoes</h2>
<p>As informacoes coletadas sao utilizadas para responder solicitacoes de contato, fornecer informacoes sobre nossos servicos, melhorar a experiencia do usuario no site e cumprir obrigacoes legais.</p>

<h2>3. Compartilhamento de Dados</h2>
<p>O Grupo Araujo Tratamentos nao comercializa dados pessoais e podera compartilha-los apenas quando necessario para cumprimento de obrigacoes legais.</p>

<h2>4. Seguranca das Informacoes</h2>
<p>Adotamos medidas de seguranca para proteger os dados pessoais contra acessos nao autorizados, perdas ou alteracoes indevidas.</p>

<h2>5. Direitos do Usuario</h2>
<p>O usuario podera solicitar acesso aos seus dados, correcao de informacoes, exclusao dos dados quando permitido pela legislacao e informacoes sobre o tratamento realizado.</p>

<h2>6. Termos de Uso</h2>
<p>Ao acessar e utilizar este site, o usuario concorda com as condicoes descritas nestes Termos de Uso.</p>
<ul>
<li>O conteudo do site tem finalidade informativa e institucional.</li>
<li>As informacoes disponibilizadas nao substituem avaliacao, orientacao ou atendimento profissional especializado.</li>
<li>O usuario compromete-se a utilizar o site de forma etica, licita e respeitosa.</li>
<li>E proibido utilizar o site para fins ilegais, fraudulentos ou que possam prejudicar o funcionamento da pagina.</li>
<li>O Grupo Araujo Tratamentos podera atualizar conteudos, servicos e informacoes do site sempre que necessario.</li>
</ul>

<h2>7. Politica de Cookies</h2>
<p>Este site pode utilizar cookies e tecnologias semelhantes para melhorar a navegacao, compreender o uso da pagina e aperfeicoar a experiencia do usuario.</p>
<p>O usuario pode configurar seu navegador para bloquear ou excluir cookies. No entanto, algumas funcionalidades do site podem nao funcionar corretamente caso os cookies sejam desativados.</p>

<h2>8. Contato</h2>
<p>Em caso de duvidas sobre esta Politica de Privacidade, entre em contato com o Grupo Araujo Tratamentos.</p>';
}

function gat_ensure_privacy_page() {
	$page = get_page_by_path( 'politica-de-privacidade' );

	if ( $page ) {
		if ( 'template-politica-privacidade.php' !== get_page_template_slug( $page->ID ) ) {
			update_post_meta( $page->ID, '_wp_page_template', 'template-politica-privacidade.php' );
		}

		return (int) $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => 'politica-de-privacidade',
			'post_title'   => 'Politica de Privacidade, Termos de Uso e Cookies',
			'post_excerpt' => 'O Grupo Araujo Tratamentos respeita a privacidade dos usuarios e esta comprometido com a protecao dos dados pessoais.',
			'post_content' => gat_default_privacy_content(),
		)
	);

	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( $page_id, '_wp_page_template', 'template-politica-privacidade.php' );
		return (int) $page_id;
	}

	return 0;
}

function gat_ensure_default_navigation() {
	if ( get_option( 'gat_default_navigation_seeded' ) ) {
		return;
	}

	$privacy_id    = gat_ensure_privacy_page();
	$primary_links = gat_default_nav_links();
	$footer_links  = $primary_links;

	if ( $privacy_id ) {
		$footer_links[] = array(
			'title' => 'Politica de Privacidade',
			'url'   => get_permalink( $privacy_id ),
		);
	}

	$locations       = (array) get_theme_mod( 'nav_menu_locations', array() );
	$primary_menu_id = gat_get_or_create_menu( 'Menu Principal Grupo Araujo', $primary_links );
	$footer_menu_id  = gat_get_or_create_menu( 'Menu Rodape Grupo Araujo', $footer_links );

	if ( empty( $locations['primary'] ) && $primary_menu_id ) {
		$locations['primary'] = $primary_menu_id;
	}

	if ( empty( $locations['footer'] ) && $footer_menu_id ) {
		$locations['footer'] = $footer_menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( 'gat_default_navigation_seeded', 1 );
}
add_action( 'after_switch_theme', 'gat_ensure_default_navigation' );
add_action( 'admin_init', 'gat_ensure_default_navigation' );
add_action( 'init', 'gat_ensure_default_navigation', 20 );

function gat_health_plan_legacy_items() {
	$items = array();
	for ( $index = 1; $index <= 12; $index++ ) {
		$image = get_theme_mod( 'gat_health_plan_image_' . $index );
		if ( $image ) {
			$items[] = array(
				'name'  => sprintf( 'Plano de saude %d', $index ),
				'image' => $image,
			);
		}
	}
	return $items;
}

function gat_default_health_plan_items() {
	$items      = gat_health_plan_legacy_items();
	$unimed_url = get_template_directory_uri() . '/assets/img/health-plans/unimed.webp';

	foreach ( $items as $item ) {
		if ( ! empty( $item['image'] ) && $unimed_url === $item['image'] ) {
			return $items;
		}
	}

	$items[] = array(
		'name'  => 'Unimed',
		'image' => $unimed_url,
	);

	return $items;
}

function gat_health_plan_items_to_text( $items ) {
	$lines = array();

	foreach ( $items as $item ) {
		if ( empty( $item['image'] ) ) {
			continue;
		}

		$name    = ! empty( $item['name'] ) ? $item['name'] : 'Plano de saude';
		$lines[] = $name . ' | ' . $item['image'];
	}

	return implode( "\n", $lines );
}

function gat_parse_health_plan_items( $value ) {
	$items = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( ! $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$name  = count( $parts ) > 1 ? sanitize_text_field( $parts[0] ) : '';
		$image = count( $parts ) > 1 ? $parts[1] : $parts[0];
		$image = esc_url_raw( $image );

		if ( ! $image ) {
			continue;
		}

		if ( ! $name ) {
			$name = ucfirst( preg_replace( '/[-_]+/', ' ', pathinfo( wp_parse_url( $image, PHP_URL_PATH ), PATHINFO_FILENAME ) ) );
		}

		$items[] = array(
			'name'  => $name,
			'image' => $image,
		);
	}

	return $items;
}

function gat_sanitize_health_plan_items( $value ) {
	return gat_health_plan_items_to_text( gat_parse_health_plan_items( $value ) );
}

function gat_health_plan_items_default_text() {
	return gat_health_plan_items_to_text( gat_default_health_plan_items() );
}

function gat_health_plan_items() {
	$saved_items = get_theme_mod( 'gat_health_plans_items', null );

	if ( null === $saved_items || '' === trim( (string) $saved_items ) ) {
		return gat_default_health_plan_items();
	}

	return gat_parse_health_plan_items( $saved_items );
}

function gat_render_health_plans_section() {
	$items = gat_health_plan_items();
	ob_start();
	?>
	<section id="planos-saude" class="section health-plans-section">
		<div class="container">
			<div class="section-heading health-plans-heading" data-aos="fade-up">
				<p class="eyebrow"><?php esc_html_e( 'Convênios', 'grupo-araujo' ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'gat_health_plans_title', 'Planos de Saúde' ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'gat_health_plans_text', 'Consulte nossa equipe para verificar cobertura, disponibilidade e condições de atendimento.' ) ); ?></p>
			</div>
			<?php if ( $items ) : ?>
				<div class="health-plans-carousel swiper" data-health-plans-carousel data-aos="fade-up">
					<div class="swiper-wrapper">
						<?php foreach ( $items as $index => $item ) : ?>
							<div class="swiper-slide">
								<div class="health-plan-logo">
									<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy">
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p class="health-plans-empty"><?php esc_html_e( 'Adicione os planos em Aparência > Personalizar > Carrossel - Planos de Saúde.', 'grupo-araujo' ); ?></p>
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
				<div class="latest-posts-action" data-aos="fade-up">
					<a class="btn btn-primary latest-posts-archive-button" href="<?php echo esc_url( gat_get_latest_posts_url() ); ?>">
						<i data-lucide="newspaper"></i>
						<span><?php esc_html_e( 'Ver todas as publicações', 'grupo-araujo' ); ?></span>
					</a>
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
