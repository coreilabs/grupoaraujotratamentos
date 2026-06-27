<?php
/**
 * Lista de ultimas publicacoes.
 *
 * @package Grupo_Araujo
 */

get_header();
?>
<main class="section section-white">
	<div class="container content-layout">
		<section class="posts-listing">
			<header class="archive-header">
				<p class="eyebrow"><?php esc_html_e( 'Blog', 'grupo-araujo' ); ?></p>
				<h1><?php single_post_title(); ?></h1>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="post-list">
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-list-item' ); ?>>
							<a class="post-list-image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<span><i data-lucide="newspaper"></i></span>
								<?php endif; ?>
							</a>
							<div class="post-list-body">
								<div class="entry-meta"><?php gat_posted_on(); ?></div>
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
								<a class="latest-post-link" href="<?php the_permalink(); ?>">
									<?php esc_html_e( 'Ler publicacao', 'grupo-araujo' ); ?> <i data-lucide="arrow-right"></i>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php gat_render_pagination(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nenhuma publicacao encontrada.', 'grupo-araujo' ); ?></p>
			<?php endif; ?>
		</section>

		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
