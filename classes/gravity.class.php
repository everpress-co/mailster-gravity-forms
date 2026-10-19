<?php
/**
 * Gravity Forms integration for Mailster.
 *
 * @package Mailster_Gravity_Forms
 */

/**
 * Connects Gravity Forms submissions to Mailster subscribers.
 *
 * @since 1.0
 */
class MailsterGravitiyForm {

	/**
	 * Absolute filesystem path of this plugin.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Base URL of this plugin.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Registers lifecycle hooks and loads the text domain.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_GRAVITYFORMS_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_GRAVITYFORMS_FILE );

		register_activation_hook( MAILSTER_GRAVITYFORMS_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_GRAVITYFORMS_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-gravityforms' );

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
	}

	/**
	 * Fires when the plugin is activated.
	 *
	 * @since 1.0
	 *
	 * @param bool $network_wide Whether the plugin is being activated network-wide.
	 * @return void
	 */
	public function activate( $network_wide ) {}

	/**
	 * Fires when the plugin is deactivated.
	 *
	 * @since 1.0
	 *
	 * @param bool $network_wide Whether the plugin is being deactivated network-wide.
	 * @return void
	 */
	public function deactivate( $network_wide ) {}

	/**
	 * Registers Gravity Forms hooks for submissions and form settings.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init() {

		add_filter( 'gform_after_submission', array( &$this, 'after_submission' ), 10, 2 );

		if ( is_admin() ) {

			add_filter( 'gform_form_settings_menu', array( &$this, 'settings_menu' ) );
			add_action( 'gform_form_settings_page_mailster', array( &$this, 'settings_page' ) );

			$this->save();
		}
	}

	/**
	 * Creates or updates a Mailster subscriber from a Gravity Forms entry.
	 *
	 * Respects the form's list mapping, conditional field, and double opt-in settings.
	 *
	 * @since 1.0
	 *
	 * @param array $entry Submitted Gravity Forms entry.
	 * @param array $form  Gravity Forms form meta, including Mailster settings.
	 * @return void
	 */
	public function after_submission( $entry, $form ) {

		// Mailster doesn't exists.
		if ( ! function_exists( 'mailster' ) ) {
			return;
		}

		// Mailster options are not defined.
		if ( ! isset( $form['mailster'] ) ) {
			return;
		}

		// form not active.
		if ( ! isset( $form['mailster']['active'] ) ) {
			return;
		}

		// condition check matches.
		if ( isset( $form['mailster']['conditional'] ) ) {

			// radio button.
			if ( isset( $form['mailster']['conditional_id'] ) ) {

				$conditional_id = $form['mailster']['conditional_id'];
				if ( isset( $entry[ $conditional_id ] ) && (string) $entry[ $conditional_id ] !== (string) $form['mailster']['conditional_field'] ) {
					return;
				}
				if ( ! isset( $entry[ $conditional_id ] ) ) {
					return;
				}

				// checkbox.
			} else {

				if ( isset( $entry[ $form['mailster']['conditional_field'] ] ) && empty( $entry[ $form['mailster']['conditional_field'] ] ) ) {
					return;
				}
				if ( ! isset( $entry[ $form['mailster']['conditional_field'] ] ) ) {
					return;
				}
			}
		}

		$userdata = array();
		$list_ids = isset( $form['mailster']['lists'] ) ? (array) $form['mailster']['lists'] : array();

		foreach ( $form['mailster']['map'] as $field_id => $key ) {
			if ( '_list' === $key ) {
				$listname = $entry[ $field_id ];
				if ( empty( $listname ) ) {
					continue;
				}
				$list_id = mailster( 'lists' )->get_by_name( $listname, 'ID' );
				if ( ! $list_id ) {
					$list_ids[] = mailster( 'lists' )->add( $listname, true );
				} else {
					$list_ids[] = $list_id;
				}
			} elseif ( '-1' !== (string) $key ) {
				$userdata[ $key ] = $entry[ $field_id ];
			}
		}

		if ( ! isset( $userdata['email'] ) ) {
			return;
		}

		$subscriber = mailster( 'subscribers' )->get_by_mail( $userdata['email'] );
		if ( $subscriber ) {
			$userdata['ID']          = (int) $subscriber->ID;
			$userdata['hash']        = $subscriber->hash;
			$userdata['status']      = $subscriber->status;
			$subscriber_notification = 0 === (int) $subscriber->status; // send again if not confirmed already.

			// add() strips the ID on the front end. When the duplicate-key update
			// changes nothing, insert_id is 0 and send_confirmations() emails every
			// pending subscriber. Updating by ID keeps the resend on this subscriber.
			$subscriber_id = mailster( 'subscribers' )->update( $userdata, true, true, $subscriber_notification );
		} else {
			$userdata['status']      = isset( $form['mailster']['double-opt-in'] ) ? 0 : 1;
			$subscriber_notification = true;
			$subscriber_id           = mailster( 'subscribers' )->add( $userdata, true, true, $subscriber_notification );
		}

		if ( ! is_wp_error( $subscriber_id ) ) {
			mailster( 'subscribers' )->assign_lists( $subscriber_id, $list_ids, false, $userdata['status'] ? true : false );
		}
	}

	/**
	 * Prints the Mailster panel on the Gravity Forms settings screen.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function settings_page() {

		GFFormSettings::page_header();

		include $this->plugin_path . '/views/page.php';

		GFFormSettings::page_footer();
	}

	/**
	 * Adds the Mailster tab to a form's settings menu.
	 *
	 * @since 1.0
	 *
	 * @param array $settings_tabs Existing settings tabs.
	 * @return array Settings tabs including the Mailster tab.
	 */
	public function settings_menu( $settings_tabs ) {

		wp_enqueue_style( 'mailster-gravityforms-style', $this->plugin_url . 'assets/style.css', array(), MAILSTER_GRAVITYFORMS_VERSION );

		$settings_tabs[] = array(
			'name'  => 'mailster',
			'label' => 'Mailster',
			'icon'  => 'gform-icon--mailster',
		);
		return $settings_tabs;
	}

	/**
	 * Saves the posted Mailster settings onto the current Gravity Form.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function save() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce = isset( $_POST['gform_save_form_settings'] ) ? sanitize_text_field( wp_unslash( $_POST['gform_save_form_settings'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'mailster_gf_save_form' ) ) {
			return;
		}

		$form_id = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
		if ( ! $form_id ) {
			return;
		}

		$form = RGFormsModel::get_form_meta( $form_id );
		if ( ! $form || ! isset( $_POST['mailster'] ) || ! is_array( $_POST['mailster'] ) ) {
			return;
		}

		$form['mailster']  = map_deep( wp_unslash( $_POST['mailster'] ), 'sanitize_text_field' );
		$conditional_field = isset( $form['mailster']['conditional_field'] ) ? $form['mailster']['conditional_field'] : '';
		$conditional       = explode( '|', $conditional_field );

		if ( count( $conditional ) > 1 ) {
			$form['mailster']['conditional_id'] = array_shift( $conditional );
		}

		$form['mailster']['conditional_field'] = implode( '|', $conditional );

		RGFormsModel::update_form_meta( $form_id, $form );
	}
}
