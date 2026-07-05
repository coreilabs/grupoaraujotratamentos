<?php
/**
 * Sidebar editorial do tema.
 *
 * @package Grupo_Araujo
 */

$recent_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$categories = get_categories(
	array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
		'number'     => 8,
	)
);
?>
<aside class="site-sidebar" aria-label="<?php esc_attr_e( 'Barra lateral', 'grupo-araujo' ); ?>">
	<section class="blog-sidebar-card sidebar-search-card">
		<div class="sidebar-card-heading">
			<p class="eyebrow"><?php esc_html_e( 'Pesquisar', 'grupo-araujo' ); ?></p>
			<h2><?php esc_html_e( 'Encontre uma publicação', 'grupo-araujo' ); ?></h2>
		</div>
		<form class="sidebar-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="sidebar-search-field"><?php esc_html_e( 'Buscar por publicações', 'grupo-araujo' ); ?></label>
			<input id="sidebar-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar no blog...', 'grupo-araujo' ); ?>">
			<button type="submit" aria-label="<?php esc_attr_e( 'Pesquisar', 'grupo-araujo' ); ?>">
				<i data-lucide="search"></i>
			</button>
		</form>
	</section>

	<?php if ( $categories ) : ?>
		<section class="blog-sidebar-card sidebar-categories-card">
			<div class="sidebar-card-heading">
				<p class="eyebrow"><?php esc_html_e( 'Temas', 'grupo-araujo' ); ?></p>
				<h2><?php esc_html_e( 'Categorias', 'grupo-araujo' ); ?></h2>
			</div>
			<ul class="sidebar-category-list">
				<?php foreach ( $categories as $category ) : ?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $category ) ); ?>">
							<span><?php echo esc_html( $category->name ); ?></span>
							<strong><?php echo esc_html( number_format_i18n( $category->count ) ); ?></strong>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php if ( $recent_posts->have_posts() ) : ?>
		<section class="blog-sidebar-card sidebar-recent-card">
			<div class="sidebar-card-heading">
				<p class="eyebrow"><?php esc_html_e( 'Leitura recente', 'grupo-araujo' ); ?></p>
				<h2><?php esc_html_e( 'Últimas publicações', 'grupo-araujo' ); ?></h2>
			</div>
			<div class="sidebar-recent-list">
				<?php while ( $recent_posts->have_posts() ) : ?>
					<?php $recent_posts->the_post(); ?>
					<a class="sidebar-recent-post" href="<?php the_permalink(); ?>">
						<span class="sidebar-recent-thumb">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<i data-lucide="newspaper"></i>
							<?php endif; ?>
						</span>
						<span class="sidebar-recent-content">
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<strong><?php the_title(); ?></strong>
						</span>
					</a>
				<?php endwhile; ?>
			</div>
			<a class="sidebar-all-posts-link" href="<?php echo esc_url( gat_get_latest_posts_url() ); ?>">
				<?php esc_html_e( 'Ver arquivo completo', 'grupo-araujo' ); ?> <i data-lucide="arrow-right"></i>
			</a>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<section class="blog-sidebar-card sidebar-help-card">
		<div class="sidebar-card-heading">
			<p class="eyebrow"><?php esc_html_e( 'Atendimento', 'grupo-araujo' ); ?></p>
			<h2><?php esc_html_e( 'Precisa de orientação?', 'grupo-araujo' ); ?></h2>
		</div>
		<p><?php esc_html_e( 'Nossa equipe pode orientar sua família com sigilo, acolhimento e responsabilidade.', 'grupo-araujo' ); ?></p>
		<a class="btn btn-primary full" href="<?php echo esc_url( gat_get_whatsapp_url( 'Olá, equipe do Grupo Araújo Tratamentos. Vim pelo blog e gostaria de orientação.' ) ); ?>" target="_blank" rel="noopener">
			<i data-lucide="message-circle"></i>
			<span><?php esc_html_e( 'Falar pelo WhatsApp', 'grupo-araujo' ); ?></span>
		</a>
	</section>

	<?php if ( gat_has_sidebar() ) : ?>
		<div class="sidebar-extra-widgets">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	<?php endif; ?>
</aside>
