<?php
/**
 * Arquivo de publicacoes, categorias e tags.
 *
 * @package Grupo_Araujo
 */

get_header();

$archive_title = get_the_archive_title();

if ( is_tag() ) {
	$archive_title = sprintf(
		/* translators: %s: tag name. */
		__( 'Publicações sobre %s', 'grupo-araujo' ),
		single_tag_title( '', false )
	);
} elseif ( is_category() ) {
	$archive_title = sprintf(
		/* translators: %s: category name. */
		__( 'Publicações em %s', 'grupo-araujo' ),
		single_cat_title( '', false )
	);
}
?>
<main class="section section-white blog-archive-page">
	<div class="container content-layout">
		<section class="posts-listing">
			<header class="archive-header blog-archive-header taxonomy-archive-header">
				<div class="taxonomy-archive-heading">
					<p class="eyebrow"><?php esc_html_e( 'Blog', 'grupo-araujo' ); ?></p>
					<h1><?php echo esc_html( $archive_title ); ?></h1>
					<div class="archive-description">
						<p><?php esc_html_e( 'Conteúdos informativos para famílias e pessoas que buscam orientação sobre dependência química, alcoolismo, tratamento e recuperação.', 'grupo-araujo' ); ?></p>
					</div>
				</div>
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
									<?php esc_html_e( 'Ler publicação', 'grupo-araujo' ); ?> <i data-lucide="arrow-right"></i>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php gat_render_pagination(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nenhuma publicação encontrada.', 'grupo-araujo' ); ?></p>
			<?php endif; ?>
		</section>

		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
