<?php
/**
 * Páginas internas.
 *
 * @package Grupo_Araujo
 */

get_header();
?>
<main class="section section-white">
	<div class="container content-layout">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content-wrap page-entry' ); ?>>
				<header class="entry-header">
					<h1><?php the_title(); ?></h1>
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="entry-featured-image">
							<?php the_post_thumbnail( 'gat-featured-wide' ); ?>
						</figure>
					<?php endif; ?>
				</header>
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
		<?php endwhile; ?>
		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
