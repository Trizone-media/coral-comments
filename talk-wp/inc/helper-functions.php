<?php
/**
 * Helper functions
 *
 * @package Talk_Plugin
 * @since 0.0.4
 */

define('INC_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

require INC_PATH . '/php-jwt/src/BeforeValidException.php';
require INC_PATH . '/php-jwt/src/ExpiredException.php';
require INC_PATH . '/php-jwt/src/SignatureInvalidException.php';
require INC_PATH . '/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;

/**
 * Prints an admin notice. If the message contains two %s placeholders,
 * the content between them will be wrapped in a link to the plugin settings page
 *
 * @param string $type 'error', 'warning', 'success'.
 * @param string $message Translated message text.
 */
function coral_talk_print_admin_notice( $type = 'error', $message = 'Coral Talk error' ) {
	$has_link = ( 2 === substr_count( $message, '%s' ) );

	?>
		<div class="notice notice-<?php echo esc_attr( $type ); ?>">
			<p><?php echo ! $has_link ?
				esc_html( $message ) :
				sprintf(
					esc_html( $message ),
					'<a href="' . esc_url( admin_url( 'options-general.php?page=talk-settings' ) ) . '">',
					'</a>'
				);
			?>
			</p>
		</div>
	<?php
}

function coral_talk_generate_jwt_token() {
	if (!is_user_logged_in()) {
		return false;
	}

	$current_user = wp_get_current_user();

	$payload = ['user' => []];
	$payload['user']['id'] = "$current_user->ID";
	$payload['user']['email'] = $current_user->user_email;
	$payload['user']['username'] = $current_user->user_login;
	$payload['user']['url'] = $current_user->user_url;

	$key = get_option( 'coral_talk_jwt_secret');

	$jwt = JWT::encode($payload, $key, 'HS256');

	return $jwt;
}
