<?php
/**
 * Template Name: Contrato de Internacao
 *
 * @package Grupo_Araujo
 */

$gat_contrato_result = gat_handle_contrato_submission();

get_header();
?>
<main class="contract-page">
	<section class="section section-white contract-hero">
		<div class="container contract-layout">
			<div class="section-heading" data-aos="fade-up">
				<p class="eyebrow"><?php esc_html_e( 'Contrato de internacao', 'grupo-araujo' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'Preencha os dados para confeccao do contrato e envie as imagens dos documentos solicitados.', 'grupo-araujo' ); ?></p>
			</div>

			<form class="contract-form" method="post" enctype="multipart/form-data" data-aos="fade-up">
				<?php if ( $gat_contrato_result ) : ?>
					<div class="contract-alert <?php echo $gat_contrato_result['success'] ? 'is-success' : 'is-error'; ?>">
						<p><?php echo esc_html( $gat_contrato_result['message'] ); ?></p>
						<?php if ( ! empty( $gat_contrato_result['errors'] ) ) : ?>
							<ul>
								<?php foreach ( $gat_contrato_result['errors'] as $error ) : ?>
									<li><?php echo esc_html( $error ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<input type="hidden" name="gat_contrato_action" value="send">
				<input class="contract-hp" type="text" name="gat_empresa" value="" tabindex="-1" autocomplete="off">
				<?php wp_nonce_field( 'gat_send_contrato', 'gat_contrato_nonce' ); ?>

				<fieldset>
					<legend><?php esc_html_e( 'Dados do paciente', 'grupo-araujo' ); ?></legend>
					<p class="contract-note"><?php esc_html_e( 'Enviar a foto do documento e a foto da carteirinha do plano de saude em arquivos separados.', 'grupo-araujo' ); ?></p>
					<div class="contract-grid">
						<label class="form-row full">
							<span><?php esc_html_e( 'Nome completo do paciente', 'grupo-araujo' ); ?></span>
							<input type="text" name="paciente_nome" value="<?php echo esc_attr( gat_contrato_old_value( 'paciente_nome', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Data de nascimento', 'grupo-araujo' ); ?></span>
							<input type="date" name="paciente_data_nascimento" value="<?php echo esc_attr( gat_contrato_old_value( 'paciente_data_nascimento', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'RG', 'grupo-araujo' ); ?></span>
							<input type="text" name="paciente_rg" value="<?php echo esc_attr( gat_contrato_old_value( 'paciente_rg', $gat_contrato_result ) ); ?>">
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Orgao expedidor', 'grupo-araujo' ); ?></span>
							<input type="text" name="paciente_orgao_expedidor" value="<?php echo esc_attr( gat_contrato_old_value( 'paciente_orgao_expedidor', $gat_contrato_result ) ); ?>">
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'CPF', 'grupo-araujo' ); ?></span>
							<input type="text" name="paciente_cpf" value="<?php echo esc_attr( gat_contrato_old_value( 'paciente_cpf', $gat_contrato_result ) ); ?>" inputmode="numeric" required>
						</label>
						<label class="form-row full contract-file">
							<span><?php esc_html_e( 'Foto do documento do paciente', 'grupo-araujo' ); ?></span>
							<input type="file" name="paciente_documento" accept="image/*,.pdf" capture="environment" required>
						</label>
						<label class="form-row full contract-file">
							<span><?php esc_html_e( 'Foto da carteirinha do plano de saude', 'grupo-araujo' ); ?></span>
							<input type="file" name="paciente_carteirinha" accept="image/*,.pdf" capture="environment" required>
						</label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php esc_html_e( 'Dados do responsavel pela internacao', 'grupo-araujo' ); ?></legend>
					<p class="contract-note"><?php esc_html_e( 'Enviar foto do documento do responsavel.', 'grupo-araujo' ); ?></p>
					<div class="contract-grid">
						<label class="form-row full">
							<span><?php esc_html_e( 'Nome completo', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_nome" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_nome', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'RG', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_rg" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_rg', $gat_contrato_result ) ); ?>">
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'CPF', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_cpf" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_cpf', $gat_contrato_result ) ); ?>" inputmode="numeric" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Orgao expedidor', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_orgao_expedidor" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_orgao_expedidor', $gat_contrato_result ) ); ?>">
						</label>
						<label class="form-row full">
							<span><?php esc_html_e( 'Endereco', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_endereco" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_endereco', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Telefone', 'grupo-araujo' ); ?></span>
							<input type="tel" name="responsavel_telefone" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_telefone', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Cidade', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_cidade" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_cidade', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'CEP', 'grupo-araujo' ); ?></span>
							<input type="text" name="responsavel_cep" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_cep', $gat_contrato_result ) ); ?>" inputmode="numeric" required>
						</label>
						<label class="form-row">
							<span><?php esc_html_e( 'Email', 'grupo-araujo' ); ?></span>
							<input type="email" name="responsavel_email" value="<?php echo esc_attr( gat_contrato_old_value( 'responsavel_email', $gat_contrato_result ) ); ?>" required>
						</label>
						<label class="form-row full contract-file">
							<span><?php esc_html_e( 'Documento do responsavel', 'grupo-araujo' ); ?></span>
							<input type="file" name="responsavel_documento" accept="image/*,.pdf" capture="environment" required>
						</label>
					</div>
				</fieldset>

				<button class="btn btn-primary" type="submit">
					<i data-lucide="send"></i>
					<span><?php esc_html_e( 'Enviar dados para contrato', 'grupo-araujo' ); ?></span>
				</button>
			</form>
		</div>
	</section>
</main>
<?php get_footer(); ?>
