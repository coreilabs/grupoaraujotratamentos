<?php
/**
 * Solicitacoes de contrato de internacao.
 *
 * @package Grupo_Araujo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gat_register_contratos() {
	register_post_type(
		'gat_contrato',
		array(
			'labels' => array(
				'name'          => __( 'Contratos de Internacao', 'grupo-araujo' ),
				'singular_name' => __( 'Solicitacao de contrato', 'grupo-araujo' ),
				'edit_item'     => __( 'Ver solicitacao', 'grupo-araujo' ),
				'view_item'     => __( 'Ver solicitacao', 'grupo-araujo' ),
				'search_items'  => __( 'Buscar solicitacoes', 'grupo-araujo' ),
			),
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => false,
			'menu_icon'          => 'dashicons-media-document',
			'capability_type'    => 'post',
			'supports'           => array( 'title' ),
			'publicly_queryable' => false,
		)
	);
}
add_action( 'init', 'gat_register_contratos' );

function gat_contrato_fields() {
	return array(
		'patient'     => array(
			'title'  => __( 'Dados do paciente', 'grupo-araujo' ),
			'fields' => array(
				'paciente_nome'             => __( 'Nome completo do paciente', 'grupo-araujo' ),
				'paciente_data_nascimento'  => __( 'Data de nascimento', 'grupo-araujo' ),
				'paciente_rg'               => __( 'RG', 'grupo-araujo' ),
				'paciente_orgao_expedidor'  => __( 'Orgao expedidor', 'grupo-araujo' ),
				'paciente_cpf'              => __( 'CPF', 'grupo-araujo' ),
			),
		),
		'responsavel' => array(
			'title'  => __( 'Dados do responsavel pela internacao', 'grupo-araujo' ),
			'fields' => array(
				'responsavel_nome'            => __( 'Nome completo', 'grupo-araujo' ),
				'responsavel_rg'              => __( 'RG', 'grupo-araujo' ),
				'responsavel_cpf'             => __( 'CPF', 'grupo-araujo' ),
				'responsavel_orgao_expedidor' => __( 'Orgao expedidor', 'grupo-araujo' ),
				'responsavel_endereco'        => __( 'Endereco', 'grupo-araujo' ),
				'responsavel_telefone'        => __( 'Telefone', 'grupo-araujo' ),
				'responsavel_cidade'          => __( 'Cidade', 'grupo-araujo' ),
				'responsavel_cep'             => __( 'CEP', 'grupo-araujo' ),
				'responsavel_email'           => __( 'Email', 'grupo-araujo' ),
			),
		),
	);
}

function gat_contrato_upload_fields() {
	return array(
		'paciente_documento'     => __( 'Foto do documento do paciente', 'grupo-araujo' ),
		'paciente_carteirinha'   => __( 'Foto da carteirinha do plano de saude', 'grupo-araujo' ),
		'responsavel_documento'  => __( 'Documento do responsavel', 'grupo-araujo' ),
	);
}

function gat_contrato_optional_fields() {
	return array(
		'paciente_rg',
		'paciente_orgao_expedidor',
		'responsavel_rg',
		'responsavel_orgao_expedidor',
	);
}

function gat_contrato_metaboxes() {
	add_meta_box( 'gat_contrato_data', __( 'Dados enviados', 'grupo-araujo' ), 'gat_contrato_metabox_html', 'gat_contrato', 'normal', 'high' );
	add_meta_box( 'gat_contrato_files', __( 'Documentos enviados', 'grupo-araujo' ), 'gat_contrato_files_metabox_html', 'gat_contrato', 'side', 'high' );
	add_meta_box( 'gat_contrato_whatsapp', __( 'Enviar por WhatsApp', 'grupo-araujo' ), 'gat_contrato_whatsapp_metabox_html', 'gat_contrato', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'gat_contrato_metaboxes' );

function gat_contrato_metabox_html( $post ) {
	echo '<p><a class="button button-primary" href="' . esc_url( gat_contrato_whatsapp_url( $post->ID ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Enviar dados por WhatsApp', 'grupo-araujo' ) . '</a></p>';

	foreach ( gat_contrato_fields() as $group ) :
		?>
		<h3><?php echo esc_html( $group['title'] ); ?></h3>
		<table class="widefat striped">
			<tbody>
				<?php foreach ( $group['fields'] as $key => $label ) : ?>
					<tr>
						<th scope="row" style="width: 32%;"><?php echo esc_html( $label ); ?></th>
						<td><?php echo esc_html( get_post_meta( $post->ID, '_gat_' . $key, true ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	endforeach;
}

function gat_contrato_files_metabox_html( $post ) {
	foreach ( gat_contrato_upload_fields() as $key => $label ) {
		$attachment_id = absint( get_post_meta( $post->ID, '_gat_' . $key, true ) );
		echo '<p><strong>' . esc_html( $label ) . '</strong><br>';
		if ( $attachment_id ) {
			echo '<a class="button" href="' . esc_url( wp_get_attachment_url( $attachment_id ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Abrir arquivo', 'grupo-araujo' ) . '</a>';
		} else {
			esc_html_e( 'Nenhum arquivo enviado.', 'grupo-araujo' );
		}
		echo '</p>';
	}
}

function gat_contrato_whatsapp_metabox_html( $post ) {
	?>
	<p><?php esc_html_e( 'Gera uma conversa com todos os dados em texto para o WhatsApp definido em Aparencia > Personalizar.', 'grupo-araujo' ); ?></p>
	<p>
		<a class="button button-primary" href="<?php echo esc_url( gat_contrato_whatsapp_url( $post->ID ) ); ?>" target="_blank" rel="noopener">
			<?php esc_html_e( 'Enviar dados por WhatsApp', 'grupo-araujo' ); ?>
		</a>
	</p>
	<?php
}

function gat_contrato_columns( $columns ) {
	$columns['responsavel'] = __( 'Responsavel', 'grupo-araujo' );
	$columns['telefone']    = __( 'Telefone', 'grupo-araujo' );
	$columns['whatsapp']    = __( 'WhatsApp', 'grupo-araujo' );
	return $columns;
}
add_filter( 'manage_gat_contrato_posts_columns', 'gat_contrato_columns' );

function gat_contrato_column_content( $column, $post_id ) {
	if ( 'responsavel' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_gat_responsavel_nome', true ) );
	}
	if ( 'telefone' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_gat_responsavel_telefone', true ) );
	}
	if ( 'whatsapp' === $column ) {
		echo '<a class="button" href="' . esc_url( gat_contrato_whatsapp_url( $post_id ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Enviar', 'grupo-araujo' ) . '</a>';
	}
}
add_action( 'manage_gat_contrato_posts_custom_column', 'gat_contrato_column_content', 10, 2 );

function gat_contrato_whatsapp_number() {
	$number = preg_replace( '/\D+/', '', get_theme_mod( 'gat_whatsapp', '5508005757714' ) );
	return $number ? $number : '5508005757714';
}

function gat_contrato_whatsapp_message( $post_id ) {
	$lines = array(
		'Solicitacao de contrato de internacao',
		'Data: ' . get_the_date( 'd/m/Y H:i', $post_id ),
		'',
	);

	foreach ( gat_contrato_fields() as $group ) {
		$lines[] = $group['title'];
		foreach ( $group['fields'] as $key => $label ) {
			$value = get_post_meta( $post_id, '_gat_' . $key, true );
			$lines[] = $label . ': ' . ( '' !== $value ? $value : '-' );
		}
		$lines[] = '';
	}

	$lines[] = __( 'Documentos enviados', 'grupo-araujo' );
	foreach ( gat_contrato_upload_fields() as $key => $label ) {
		$attachment_id = absint( get_post_meta( $post_id, '_gat_' . $key, true ) );
		$url           = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
		$lines[]       = $label . ': ' . ( $url ? $url : '-' );
	}

	return implode( "\n", $lines );
}

function gat_contrato_whatsapp_url( $post_id ) {
	return 'https://wa.me/' . gat_contrato_whatsapp_number() . '?text=' . rawurlencode( gat_contrato_whatsapp_message( $post_id ) );
}

function gat_handle_contrato_submission() {
	if ( empty( $_POST['gat_contrato_action'] ) ) {
		return null;
	}

	$result = array(
		'success' => false,
		'message' => __( 'Nao foi possivel enviar os dados. Revise os campos e tente novamente.', 'grupo-araujo' ),
		'errors'  => array(),
	);

	if (
		empty( $_POST['gat_contrato_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gat_contrato_nonce'] ) ), 'gat_send_contrato' )
	) {
		$result['errors'][] = __( 'Sessao expirada. Recarregue a pagina e tente novamente.', 'grupo-araujo' );
		return $result;
	}

	if ( ! empty( $_POST['gat_empresa'] ) ) {
		$result['errors'][] = __( 'Envio recusado.', 'grupo-araujo' );
		return $result;
	}

	$values = array();
	$optional_fields = gat_contrato_optional_fields();
	foreach ( gat_contrato_fields() as $group ) {
		foreach ( $group['fields'] as $key => $label ) {
			$value = isset( $_POST[ $key ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) ) : '';
			if ( '' === $value && ! in_array( $key, $optional_fields, true ) ) {
				$result['errors'][] = sprintf( __( 'Preencha o campo %s.', 'grupo-araujo' ), $label );
			}
			$values[ $key ] = $value;
		}
	}

	if ( ! is_email( $values['responsavel_email'] ) ) {
		$result['errors'][] = __( 'Informe um email valido.', 'grupo-araujo' );
	}

	foreach ( gat_contrato_upload_fields() as $key => $label ) {
		if ( empty( $_FILES[ $key ]['name'] ) ) {
			$result['errors'][] = sprintf( __( 'Envie o arquivo: %s.', 'grupo-araujo' ), $label );
		}
	}

	if ( $result['errors'] ) {
		return $result;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'gat_contrato',
			'post_status' => 'private',
			'post_title'  => sprintf(
				'Contrato - %s - %s',
				$values['paciente_nome'],
				current_time( 'd/m/Y H:i' )
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		$result['errors'][] = $post_id->get_error_message();
		return $result;
	}

	foreach ( $values as $key => $value ) {
		update_post_meta( $post_id, '_gat_' . $key, $value );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	foreach ( gat_contrato_upload_fields() as $key => $label ) {
		$attachment_id = media_handle_upload( $key, $post_id );
		if ( is_wp_error( $attachment_id ) ) {
			wp_delete_post( $post_id, true );
			$result['errors'][] = sprintf( __( 'Erro ao enviar %s: %s', 'grupo-araujo' ), $label, $attachment_id->get_error_message() );
			return $result;
		}
		update_post_meta( $post_id, '_gat_' . $key, absint( $attachment_id ) );
	}

	wp_mail(
		get_option( 'admin_email' ),
		__( 'Nova solicitacao de contrato de internacao', 'grupo-araujo' ),
		sprintf(
			"Uma nova solicitacao foi enviada pelo site.\n\nPaciente: %s\nResponsavel: %s\nTelefone: %s\n\nAcesse o painel do WordPress em Contratos de Internacao para conferir os dados e documentos.",
			$values['paciente_nome'],
			$values['responsavel_nome'],
			$values['responsavel_telefone']
		)
	);

	$result['success'] = true;
	$result['message'] = __( 'Dados enviados com sucesso. Nossa equipe ira conferir as informacoes e dar continuidade ao atendimento.', 'grupo-araujo' );
	$result['errors']  = array();
	return $result;
}

function gat_contrato_old_value( $key, $result = null ) {
	if ( $result && ! empty( $result['success'] ) ) {
		return '';
	}

	if ( ! isset( $_POST[ $key ] ) ) {
		return '';
	}

	return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
}

function gat_create_contrato_page() {
	gat_register_contratos();

	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-contrato-internacao.php',
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return absint( $existing[0] );
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Contrato de Internacao',
			'post_name'    => 'contrato-de-internacao',
			'post_content' => '',
		)
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'template-contrato-internacao.php' );
		return absint( $page_id );
	}

	return 0;
}

function gat_maybe_create_contrato_page() {
	if ( ! is_admin() || wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['gat_create_contrato_page'] ) ) {
		return;
	}

	check_admin_referer( 'gat_create_contrato_page' );
	gat_create_contrato_page();
	wp_safe_redirect( admin_url( 'edit.php?post_type=page&gat_contrato_page_created=1' ) );
	exit;
}
add_action( 'admin_init', 'gat_maybe_create_contrato_page' );

function gat_contrato_page_exists() {
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-contrato-internacao.php',
			'fields'         => 'ids',
		)
	);

	return ! empty( $existing );
}

function gat_contrato_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || gat_contrato_page_exists() ) {
		return;
	}

	$url = wp_nonce_url( admin_url( 'edit.php?post_type=page&gat_create_contrato_page=1' ), 'gat_create_contrato_page' );
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Grupo Araujo:', 'grupo-araujo' ); ?></strong>
			<?php esc_html_e( 'crie a pagina publica de Contrato de Internacao para receber dados e documentos pelo site.', 'grupo-araujo' ); ?>
			<a class="button button-primary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Criar pagina agora', 'grupo-araujo' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'gat_contrato_admin_notice' );
