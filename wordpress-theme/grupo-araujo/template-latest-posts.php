<?php
/**
 * Template Name: Arquivo de Publicações
 * Template Post Type: page
 *
 * @package Grupo_Araujo
 */

get_header();

$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$archive_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'paged'               => $paged,
		'ignore_sticky_posts' => false,
	)
);
?>
<main class="section section-white blog-archive-page">
	<div class="container content-layout">
		<section class="posts-listing">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<header class="archive-header blog-archive-header taxonomy-archive-header blog-index-header">
					<div class="taxonomy-archive-heading">
						<p class="eyebrow"><?php esc_html_e( 'Blog', 'grupo-araujo' ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php if ( get_the_content() ) : ?>
							<div class="archive-description"><?php the_content(); ?></div>
						<?php endif; ?>
					</div>
				</header>
			<?php endwhile; ?>

			<?php if ( $archive_query->have_posts() ) : ?>
				<div class="post-list">
					<?php while ( $archive_query->have_posts() ) : ?>
						<?php $archive_query->the_post(); ?>
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
				<nav class="navigation pagination" aria-label="<?php esc_attr_e( 'Navegacao de publicacoes', 'grupo-araujo' ); ?>">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'total'     => $archive_query->max_num_pages,
								'current'   => $paged,
								'mid_size'  => 1,
								'prev_text' => __( 'Anteriores', 'grupo-araujo' ),
								'next_text' => __( 'Proximas', 'grupo-araujo' ),
							)
						)
					);
					?>
				</nav>
			<?php else : ?>
				<p><?php esc_html_e( 'Nenhuma publicação encontrada.', 'grupo-araujo' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</section>

		<?php get_sidebar(); ?>
	</div>
</main>
<?php get_footer(); ?>
