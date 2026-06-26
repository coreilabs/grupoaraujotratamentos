<?php
/**
 * Unidades administráveis.
 *
 * @package Grupo_Araujo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gat_register_unidades() {
	register_post_type(
		'gat_unidade',
		array(
			'labels' => array(
				'name'          => __( 'Unidades', 'grupo-araujo' ),
				'singular_name' => __( 'Unidade', 'grupo-araujo' ),
				'add_new_item'  => __( 'Adicionar unidade', 'grupo-araujo' ),
				'edit_item'     => __( 'Editar unidade', 'grupo-araujo' ),
			),
			'public'             => false,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-building',
			'supports'           => array( 'title', 'editor', 'page-attributes' ),
			'publicly_queryable' => false,
		)
	);
}
add_action( 'init', 'gat_register_unidades' );

function gat_unidade_metabox() {
	add_meta_box( 'gat_unidade_data', __( 'Dados da unidade', 'grupo-araujo' ), 'gat_unidade_metabox_html', 'gat_unidade', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'gat_unidade_metabox' );

function gat_unidade_metabox_html( $post ) {
	wp_nonce_field( 'gat_save_unidade', 'gat_unidade_nonce' );
	$cidade     = get_post_meta( $post->ID, '_gat_cidade', true );
	$estado     = get_post_meta( $post->ID, '_gat_estado', true ) ?: 'GO';
	$modalidade = get_post_meta( $post->ID, '_gat_modalidade', true );
	$recursos   = get_post_meta( $post->ID, '_gat_recursos', true );
	$midias     = get_post_meta( $post->ID, '_gat_midias', true );
	?>
	<p><label><strong>Cidade</strong><br><input class="widefat" type="text" name="gat_cidade" value="<?php echo esc_attr( $cidade ); ?>"></label></p>
	<p><label><strong>Estado</strong><br><input type="text" maxlength="2" name="gat_estado" value="<?php echo esc_attr( $estado ); ?>"></label></p>
	<p>
		<label><strong>Modalidade</strong><br>
			<select name="gat_modalidade">
				<option value="Masculina" <?php selected( $modalidade, 'Masculina' ); ?>>Masculina</option>
				<option value="Feminina" <?php selected( $modalidade, 'Feminina' ); ?>>Feminina</option>
			</select>
		</label>
	</p>
	<p><label><strong>Recursos, um por linha</strong><br><textarea class="widefat" rows="5" name="gat_recursos"><?php echo esc_textarea( $recursos ); ?></textarea></label></p>
	<div class="gat-media-field">
		<p><strong>Galeria de imagens e vídeos</strong></p>
		<p>
			<button class="button button-primary" type="button" id="gat-select-media">
				<?php esc_html_e( 'Selecionar ou enviar mídias', 'grupo-araujo' ); ?>
			</button>
			<button class="button" type="button" id="gat-clear-media">
				<?php esc_html_e( 'Limpar galeria', 'grupo-araujo' ); ?>
			</button>
		</p>
		<div id="gat-media-preview" class="gat-media-preview"></div>
		<label for="gat-midias"><strong><?php esc_html_e( 'URLs da galeria', 'grupo-araujo' ); ?></strong></label>
		<textarea class="widefat" id="gat-midias" rows="8" name="gat_midias"><?php echo esc_textarea( $midias ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Use o botão acima para enviar ou escolher vários arquivos da Biblioteca de Mídia. Também é possível informar uma URL por linha.', 'grupo-araujo' ); ?>
		</p>
	</div>
	<?php
}

function gat_unidade_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || 'gat_unidade' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'gat-unidade-admin',
		get_template_directory_uri() . '/assets/js/unidade-admin.js',
		array( 'jquery' ),
		GAT_THEME_VERSION,
		true
	);
	wp_enqueue_style(
		'gat-unidade-admin',
		get_template_directory_uri() . '/assets/unidade-admin.css',
		array(),
		GAT_THEME_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'gat_unidade_admin_assets' );

function gat_save_unidade( $post_id ) {
	if (
		! isset( $_POST['gat_unidade_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gat_unidade_nonce'] ) ), 'gat_save_unidade' ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	foreach ( array( 'gat_cidade' => '_gat_cidade', 'gat_estado' => '_gat_estado', 'gat_modalidade' => '_gat_modalidade' ) as $input => $meta ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta, sanitize_text_field( wp_unslash( $_POST[ $input ] ) ) );
		}
	}
	foreach ( array( 'gat_recursos' => '_gat_recursos', 'gat_midias' => '_gat_midias' ) as $input => $meta ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta, sanitize_textarea_field( wp_unslash( $_POST[ $input ] ) ) );
		}
	}
}
add_action( 'save_post_gat_unidade', 'gat_save_unidade' );

function gat_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

function gat_media_path( $path ) {
	if ( preg_match( '#^https?://#i', $path ) ) {
		return esc_url_raw( $path );
	}
	return get_template_directory_uri() . '/' . ltrim( $path, '/' );
}

function gat_get_unidades() {
	$posts = get_posts(
		array(
			'post_type'      => 'gat_unidade',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	$data = array();
	foreach ( $posts as $post ) {
		$data[] = array(
			'id'         => sanitize_title( $post->post_title ) . '-' . $post->ID,
			'cidade'     => get_post_meta( $post->ID, '_gat_cidade', true ),
			'estado'     => get_post_meta( $post->ID, '_gat_estado', true ) ?: 'GO',
			'modalidade' => get_post_meta( $post->ID, '_gat_modalidade', true ),
			'descricao'  => wp_strip_all_tags( $post->post_content ),
			'recursos'   => gat_lines( get_post_meta( $post->ID, '_gat_recursos', true ) ),
			'fotos'      => array_map( 'gat_media_path', gat_lines( get_post_meta( $post->ID, '_gat_midias', true ) ) ),
		);
	}
	return $data;
}

function gat_seed_unidades() {
	gat_register_unidades();
	if ( get_posts( array( 'post_type' => 'gat_unidade', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}

	$units = array(
		array( 'Aparecida de Goiânia - Masculina 1', 'Aparecida de Goiânia', 'Masculina', 'Unidade parceira em Aparecida de Goiânia com áreas de convivência, lazer, terapias e apoio para o processo de encaminhamento.', "Área de lazer\nPiscina\nAcomodações\nEspaços terapêuticos", "unidades/aparecida-de-goiania-goias/masculina/1/area-lazer.webp\nunidades/aparecida-de-goiania-goias/masculina/1/acomodacoes.webp\nunidades/aparecida-de-goiania-goias/masculina/1/cozinha.webp\nunidades/aparecida-de-goiania-goias/masculina/1/enfermaria.webp" ),
		array( 'Aparecida de Goiânia - Masculina 2', 'Aparecida de Goiânia', 'Masculina', 'Unidade parceira com estrutura para acolhimento conforme avaliação inicial, perfil do caso e disponibilidade.', "Estrutura ampla\nAmbiente reservado\nRotina terapêutica\nApoio no encaminhamento", "unidades/aparecida-de-goiania-goias/masculina/2/img-20260310-wa0214.jpg\nunidades/aparecida-de-goiania-goias/masculina/2/img-20260310-wa0216.jpg\nunidades/aparecida-de-goiania-goias/masculina/2/img-20260310-wa0218.jpg\nunidades/aparecida-de-goiania-goias/masculina/2/img-20260310-wa0219.jpg" ),
		array( 'Caturaí - Feminina', 'Caturaí', 'Feminina', 'Unidade parceira feminina com ambiente acolhedor e encaminhamento conforme disponibilidade e perfil do caso.', "Atendimento feminino\nAmbiente reservado\nApoio à família\nEncaminhamento responsável", "unidades/caturai-goias/feminina/1/img-20251226-wa0161.jpg\nunidades/caturai-goias/feminina/1/img-20251226-wa0163.jpg\nunidades/caturai-goias/feminina/1/img-20251226-wa0165.jpg\nunidades/caturai-goias/feminina/1/img-20251226-wa0171.jpg" ),
		array( 'Goiânia - Feminina', 'Goiânia', 'Feminina', 'Unidade parceira direcionada ao público feminino, com acolhimento humanizado e ambiente reservado.', "Atendimento feminino\nAmbiente acolhedor\nEquipe preparada\nApoio à família", "unidades/goiania-goias/feminina/1/img-20251204-wa0044.jpg\nunidades/goiania-goias/feminina/1/img-20251204-wa0045.jpg\nunidades/goiania-goias/feminina/1/vid-20251204-wa0052.mp4\nunidades/goiania-goias/feminina/1/vid-20251204-wa0056.mp4" ),
		array( 'Goiânia - Masculina', 'Goiânia', 'Masculina', 'Unidade parceira em Goiânia com estrutura para acolhimento conforme avaliação inicial e disponibilidade.', "Ambiente reservado\nRotina terapêutica\nApoio à família\nEncaminhamento responsável", "unidades/goiania-goias/masculina/1/img-20251130-wa0054-1.jpg\nunidades/goiania-goias/masculina/1/img-20251130-wa0055-1.jpg\nunidades/goiania-goias/masculina/1/img-20251130-wa0057-1.jpg\nunidades/goiania-goias/masculina/1/img-20251130-wa0059-1.jpg" ),
		array( 'Senador Canedo - Feminina', 'Senador Canedo', 'Feminina', 'Unidade parceira feminina em Senador Canedo, com acolhimento e orientação para encaminhamento responsável.', "Atendimento feminino\nAmbiente acolhedor\nEquipe preparada\nApoio no encaminhamento", "unidades/senador-canedo-goias/feminina/1/img-20260604-wa0169.jpg\nunidades/senador-canedo-goias/feminina/1/img-20260604-wa0171.jpg\nunidades/senador-canedo-goias/feminina/1/img-20260604-wa0174.jpg\nunidades/senador-canedo-goias/feminina/1/img-20260604-wa0175.jpg" ),
	);

	foreach ( $units as $order => $unit ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'gat_unidade',
				'post_status'  => 'publish',
				'post_title'   => $unit[0],
				'post_content' => $unit[3],
				'menu_order'   => $order,
			)
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_gat_cidade', $unit[1] );
		update_post_meta( $id, '_gat_estado', 'GO' );
		update_post_meta( $id, '_gat_modalidade', $unit[2] );
		update_post_meta( $id, '_gat_recursos', $unit[4] );
		update_post_meta( $id, '_gat_midias', $unit[5] );
	}
}
add_action( 'after_switch_theme', 'gat_seed_unidades' );
