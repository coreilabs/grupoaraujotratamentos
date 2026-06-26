<?php
/**
 * Rodape.
 *
 * @package Grupo_Araujo
 */
?>
<footer class="site-footer">
	<div class="container footer-layout">
		<div>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/logotipo-vertical.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<p class="footer-cnpj">CNPJ: <?php echo esc_html( get_theme_mod( 'gat_cnpj', '67.252.579/0001-50' ) ); ?></p>
			<p><?php echo esc_html( get_theme_mod( 'gat_footer_text', 'Acolhimento, orientação e acompanhamento especializado para pessoas e famílias.' ) ); ?></p>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Links do rodape', 'grupo-araujo' ); ?>">
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'items_wrap'     => '<ul class="gat-menu">%3$s</ul>',
					)
				);
			} else {
				gat_default_menu();
			}
			?>
		</nav>
		<div>
			<a class="footer-whatsapp" href="#" data-whatsapp-default target="_blank" rel="noopener">
				<i data-lucide="message-circle"></i><span>WhatsApp</span>
			</a>
			<p class="footer-note">Site: <?php echo esc_html( get_theme_mod( 'gat_site', 'www.grupoaraujotratamentos.com.br' ) ); ?><br><?php echo esc_html( get_theme_mod( 'gat_footer_note', 'Atendimento 24 horas por dia, com ética, sigilo e respeito.' ) ); ?></p>
		</div>
	</div>
</footer>
<?php $gat_floating_whatsapp_text = get_theme_mod( 'gat_floating_whatsapp_text', 'Falar pelo WhatsApp' ); ?>
<a class="floating-whatsapp" href="#" data-whatsapp-default target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $gat_floating_whatsapp_text ); ?>">
	<i data-lucide="message-circle"></i><span><?php echo esc_html( $gat_floating_whatsapp_text ); ?></span>
</a>
<?php wp_footer(); ?>
</body>
</html>
