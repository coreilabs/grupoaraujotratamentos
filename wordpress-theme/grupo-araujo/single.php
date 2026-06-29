<?php
/**
 * Publicacao individual.
 *
 * @package Grupo_Araujo
 */

get_header();
?>
<main class="section section-white">
	<div class="container content-layout">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content-wrap single-entry' ); ?>>
				<header class="entry-header">
					<div class="entry-meta"><?php gat_posted_on(); ?></div>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="entry-featured-image">
							<?php the_post_thumbnail( 'gat-featured-wide' ); ?>
						</figure>
					<?php endif; ?>
				</header>

				<?php do_action( 'gat_before_single_post' ); ?>
				<?php if ( is_active_sidebar( 'single-before' ) ) : ?>
					<div class="single-widget-area single-widget-area-before">
						<?php dynamic_sidebar( 'single-before' ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php echo gat_get_single_content_without_duplicate_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="page-links">' . __( 'Paginas:', 'grupo-araujo' ),
							'after'  => '</nav>',
						)
					);
					?>
				</div>

				<footer class="entry-footer">
					<?php the_tags( '<div class="entry-tags">', ' ', '</div>' ); ?>
				</footer>

				<?php do_action( 'gat_after_single_post' ); ?>
				<?php if ( is_active_sidebar( 'single-after' ) ) : ?>
					<div class="single-widget-area single-widget-area-after">
						<?php dynamic_sidebar( 'single-after' ); ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>

		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
