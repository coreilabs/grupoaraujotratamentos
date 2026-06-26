<?php
/**
 * Páginas internas.
 *
 * @package Grupo_Araujo
 */

get_header();
?>
<main class="section section-white">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
