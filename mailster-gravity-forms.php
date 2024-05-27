<?php
/*
Plugin Name: Mailster Gravity Forms
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=Gravity+Forms
Version: 2.0.0
License: GPLv2
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-gravityforms
Description: Integrates Mailster Newsletter Plugin with Gravity Forms to subscribe users with a Gravity Form.
Requires the Mailster Newsletter Plugin and the Gravity Forms plugin
*/

define( 'MAILSTER_GRAVITYFORMS_VERSION', '2.0.0' );
define( 'MAILSTER_GRAVITYFORMS_REQUIRED_VERSION', '4.0' );
define( 'MAILSTER_GRAVITYFORMS_FILE', __FILE__ );

require_once __DIR__ . '/classes/gravity.class.php';

new MailsterGravitiyForm();
