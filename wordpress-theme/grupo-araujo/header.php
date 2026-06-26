<?php
/**
 * Cabeçalho.
 *
 * @package Grupo_Araujo
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" data-header>
	<div class="container header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/#inicio' ) ); ?>" aria-label="<?php esc_attr_e( 'Grupo Araújo Tratamentos - início', 'grupo-araujo' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( get_template_directory_uri() . '/logotipo-horizontal.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php endif; ?>
		</a>
		<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Abrir menu', 'grupo-araujo' ); ?>" aria-expanded="false" data-nav-toggle>
			<i data-lucide="menu"></i>
		</button>
		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Menu principal', 'grupo-araujo' ); ?>" data-nav-menu>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '<ul class="gat-menu">%3$s</ul>',
					)
				);
			} else {
				gat_default_menu();
			}
			?>
		</nav>
		<a class="header-cta" href="#" data-whatsapp-default target="_blank" rel="noopener">
			<i data-lucide="message-circle"></i><span><?php echo esc_html( get_theme_mod( 'gat_header_cta_text', 'Fale Conosco' ) ); ?></span>
		</a>
	</div>
</header>
