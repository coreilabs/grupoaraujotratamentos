<?php
/**
 * Arquivos de publicacoes, categorias e tags.
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

$archive_description = get_the_archive_description();
?>
<main class="section section-white blog-archive-page">
	<div class="container content-layout">
		<section class="posts-listing">
			<header class="archive-header blog-archive-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Blog', 'grupo-araujo' ); ?></p>
					<h1><?php echo esc_html( $archive_title ); ?></h1>
					<div class="archive-description">
						<?php if ( $archive_description ) : ?>
							<?php echo wp_kses_post( wpautop( $archive_description ) ); ?>
						<?php elseif ( is_tag() ) : ?>
							<p><?php esc_html_e( 'Conteúdos relacionados a este tema, com informações para famílias e pessoas que buscam orientação sobre tratamento e recuperação.', 'grupo-araujo' ); ?></p>
						<?php else : ?>
							<p><?php esc_html_e( 'Conteúdos informativos do Grupo Araújo Tratamentos sobre dependência química, alcoolismo, família e recuperação.', 'grupo-araujo' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<form class="archive-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="archive-search-field"><?php esc_html_e( 'Buscar no blog', 'grupo-araujo' ); ?></label>
					<input id="archive-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar publicações...', 'grupo-araujo' ); ?>">
					<button type="submit">
						<i data-lucide="search"></i>
						<span><?php esc_html_e( 'Buscar', 'grupo-araujo' ); ?></span>
					</button>
				</form>
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
				<div class="blog-empty-state">
					<h2><?php esc_html_e( 'Nenhuma publicação encontrada', 'grupo-araujo' ); ?></h2>
					<p><?php esc_html_e( 'Tente buscar outro termo ou acesse o arquivo completo de publicações.', 'grupo-araujo' ); ?></p>
					<a class="btn btn-primary" href="<?php echo esc_url( gat_get_latest_posts_url() ); ?>">
						<i data-lucide="newspaper"></i>
						<span><?php esc_html_e( 'Ver todas as publicações', 'grupo-araujo' ); ?></span>
					</a>
				</div>
			<?php endif; ?>
		</section>

		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
