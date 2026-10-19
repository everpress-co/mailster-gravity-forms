<?php
/**
 * Mailster settings panel for a Gravity Form.
 *
 * @package Mailster_Gravity_Forms
 */

if ( ! function_exists( 'mailster' ) ) {

	echo '<h3>Please enable the <a href="https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=Gravity+Forms">Mailster Newsletter Plugin</a></h3>';

	return;
}

$mailster_form_id = absint( filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT ) );
$mailster_form    = RGFormsModel::get_form_meta( $mailster_form_id );
$mailster         = isset( $mailster_form['mailster'] ) ? $mailster_form['mailster'] : array(
	'lists'             => array(),
	'map'               => array(),
	'conditional_field' => null,
);

?>
<form action="" method="post" id="gform_form_settings">

	<fieldset id="gform-settings-section-general-settings" class="gform-settings-panel gform-settings-panel--full gform-settings-panel--with-title">
		<legend class="gform-settings-panel__title gform-settings-panel__title--header"><?php esc_html_e( 'Mailster Settings', 'mailster-gravityforms' ); ?></legend>

	<div class="gform-settings-panel__content">

		<div id="gform_setting_preventIP" class="gsttings-field">
			<table class="gforms_form_settings" cellspacing="0" cellpadding="0">
			<tr>
				<th></th>
				<td><label><input type="checkbox" name="mailster[active]" value="1" <?php checked( isset( $mailster['active'] ) ); ?>> <?php esc_html_e( 'Enable Mailster for this Form', 'mailster-gravityforms' ); ?></label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Map Fields', 'mailster-gravityforms' ); ?></th>
				<td>
				<p class="description">
					<?php esc_html_e( 'Define which field represents which value from your Mailster settings.', 'mailster-gravityforms' ); ?><br>
					<?php esc_html_e( 'Mailster can create lists if certain fields are checked or not empty.', 'mailster-gravityforms' ); ?>
				</p>
				<?php
				$mailster_fields = array(
					'email'     => mailster_text( 'email' ),
					'firstname' => mailster_text( 'firstname' ),
					'lastname'  => mailster_text( 'lastname' ),
				);

				$mailster_custom_fields = mailster()->get_custom_fields();
				if ( $mailster_custom_fields ) {
					foreach ( $mailster_custom_fields as $mailster_field_key => $mailster_field_data ) {
						$mailster_fields[ $mailster_field_key ] = $mailster_field_data['name'];
					}
				}

				if ( is_array( $mailster_form['fields'] ) && ! empty( $mailster_form['fields'] ) ) {
					echo '<ul id="mailster-map">';
					foreach ( $mailster_form['fields'] as $mailster_field ) {
						if ( isset( $mailster_field['inputs'] ) && is_array( $mailster_field['inputs'] ) ) {
							$mailster_group_label = ! empty( $mailster_field['label'] ) ? $mailster_field['label'] : __( 'Untitled', 'mailster-gravityforms' );
							echo '<li><strong>' . esc_html( $mailster_group_label ) . ':</strong><ul>';

							foreach ( $mailster_field['inputs'] as $mailster_input ) {
								$mailster_mapped_input = isset( $mailster['map'][ $mailster_input['id'] . '' ] ) ? $mailster['map'][ $mailster_input['id'] . '' ] : '';
								echo '<li><label>' . esc_html( $mailster_input['label'] ) . '</label> ➨ <select name="mailster[map][' . esc_attr( $mailster_input['id'] ) . ']" >';
								echo '<option value="-1">' . esc_html__( 'not mapped', 'mailster-gravityforms' ) . '</option>';
								echo '<optgroup label="' . esc_attr__( 'Fields', 'mailster-gravityforms' ) . '">';
								foreach ( $mailster_fields as $mailster_field_key => $mailster_field_name ) {
									echo '<option value="' . esc_attr( $mailster_field_key ) . '" ' . selected( $mailster_field_key, $mailster_mapped_input, false ) . '>' . esc_html( $mailster_field_name ) . '</option>';
								}
								echo '</optgroup>';
								echo '<optgroup label="' . esc_attr__( 'List specific', 'mailster-gravityforms' ) . '">';
								echo '<option value="_list" ' . selected( '_list', $mailster_mapped_input, false ) . '>' . esc_html__( 'Add to List if checked or not empty', 'mailster-gravityforms' ) . '</option>';
								echo '</optgroup>';
								echo '</select></li>';
							}

							echo '</ul></li>';

						} else {
							$mailster_mapped_field = isset( $mailster['map'][ $mailster_field['id'] . '' ] ) ? $mailster['map'][ $mailster_field['id'] . '' ] : '';
							echo '<li> <label><strong>' . esc_html( $mailster_field['label'] ) . '</strong></label> ➨ <select name="mailster[map][' . esc_attr( $mailster_field['id'] ) . ']">';
							echo '<option value="-1">' . esc_html__( 'not mapped', 'mailster-gravityforms' ) . '</option>';
							echo '<optgroup label="' . esc_attr__( 'Fields', 'mailster-gravityforms' ) . '">';
							foreach ( $mailster_fields as $mailster_field_key => $mailster_field_name ) {
								echo '<option value="' . esc_attr( $mailster_field_key ) . '" ' . selected( $mailster_field_key, $mailster_mapped_field, false ) . '>' . esc_html( $mailster_field_name ) . '</option>';
							}
							echo '</optgroup>';
							echo '<optgroup label="' . esc_attr__( 'List specific', 'mailster-gravityforms' ) . '">';
								echo '<option value="_list" ' . selected( '_list', $mailster_mapped_field, false ) . '>' . esc_html__( 'Add to List if checked or not empty', 'mailster-gravityforms' ) . '</option>';
							echo '</optgroup>';
							echo '</select></li>';
						}
					}
					echo '</ul>';
				} else {
					esc_html_e( 'no fields defined!', 'mailster-gravityforms' );
				}

				?>

				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Subscribe new users to', 'mailster-gravityforms' ); ?></th>
				<td>
				<?php
				$mailster_selected_lists = isset( $mailster['lists'] ) ? $mailster['lists'] : array();
				mailster( 'lists' )->print_it( null, null, 'mailster[lists]', false, $mailster_selected_lists );
				?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Double Opt In', 'mailster-gravityforms' ); ?></th>
				<td><label><input type="checkbox" name="mailster[double-opt-in]" value="1" <?php checked( isset( $mailster['double-opt-in'] ) ); ?>> <?php esc_html_e( 'Users have to confirm their subscription', 'mailster-gravityforms' ); ?></label><br>

				</td>
			</tr>

			<tr>
				<th><?php esc_html_e( 'Conditional check', 'mailster-gravityforms' ); ?></th>
				<td><label><input type="checkbox" name="mailster[conditional]" value="1" <?php checked( isset( $mailster['conditional'] ) ); ?>> <?php esc_html_e( 'Enable Conditional check', 'mailster-gravityforms' ); ?></label>
				<p><?php esc_html_e( 'subscribe user only if', 'mailster-gravityforms' ); ?>
				<?php
				if ( is_array( $mailster_form['fields'] ) ) {
					echo '<select name="mailster[conditional_field]"><option value="-1">-</option>';
					foreach ( $mailster_form['fields'] as $mailster_field ) {
						if ( ! in_array( $mailster_field['type'], array( 'checkbox', 'radio' ), true ) ) {
							continue;
						}

						if ( isset( $mailster_field['inputs'] ) && is_array( $mailster_field['inputs'] ) ) {
							$mailster_group_label = $mailster_field['label'] ? $mailster_field['label'] : __( 'Checkbox', 'mailster-gravityforms' );
							echo '<optgroup label="' . esc_attr( $mailster_group_label ) . '">';
							foreach ( $mailster_field['inputs'] as $mailster_input ) {
								echo '<option value="' . esc_attr( $mailster_input['id'] ) . '" ' . selected( $mailster_input['id'], $mailster['conditional_field'], false ) . '>' . esc_html( $mailster_input['label'] ) . '</option>';
							}
							echo '</optgroup>';

						} elseif ( isset( $mailster_field['choices'] ) && is_array( $mailster_field['choices'] ) ) {
							echo '<optgroup label="' . esc_attr( $mailster_field['label'] ) . '">';
							foreach ( $mailster_field['choices'] as $mailster_input ) {
								echo '<option value="' . esc_attr( $mailster_field['id'] . '|' . $mailster_input['value'] ) . '" ' . selected( $mailster_input['value'], $mailster['conditional_field'], false ) . '>' . esc_html( $mailster_input['text'] ) . '</option>';
							}
							echo '</optgroup>';

						} else {
							echo '<option value="' . esc_attr( $mailster_field['id'] ) . '" ' . selected( $mailster_field['id'], $mailster['conditional_field'], false ) . '>' . esc_html( $mailster_field['label'] ) . '</option>';
						}
					}
					echo '</select>';
				}
				?>
				<?php esc_html_e( 'is checked', 'mailster-gravityforms' ); ?></p>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'mailster_gf_save_form', 'gform_save_form_settings' ); ?>
		<input type="hidden" id="gform_meta" name="gform_meta">
		</div>
	</div>

	</fieldset>

	<div class="gform-settings-save-container">
		<button type="submit" id="gform-settings-save" name="gform_save_settings" value="save" form="gform_form_settings" class="primary button large"><?php esc_html_e( 'Update Form Settings', 'mailster-gravityforms' ); ?> &nbsp;→</button>
	</div>

</form>
