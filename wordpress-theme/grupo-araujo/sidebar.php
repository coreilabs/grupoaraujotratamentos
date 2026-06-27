<?php
/**
 * Sidebar principal do tema.
 *
 * @package Grupo_Araujo
 */

if ( ! gat_has_sidebar() ) {
	return;
}
?>
<aside class="site-sidebar" aria-label="<?php esc_attr_e( 'Barra lateral', 'grupo-araujo' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
