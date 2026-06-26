<?php
/**
 * Template obrigatorio do tema.
 *
 * @package Grupo_Araujo
 */

get_header();
?>
<main class="section section-white">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nenhum conteúdo encontrado.', 'grupo-araujo' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
