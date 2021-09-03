<?php
/**
 * Comments template replacement.
 * This will replace the default comments.php when calling comments_template()
 * However, it is more performant to use coral_talk_comments_template()
 *
 * @package Talk_Plugin
 * @since 0.0.3 Added support for talk container class and removed id
 */

$talk_url = get_option( 'coral_talk_base_url' );
$static_url = get_option( 'coral_talk_static_url', $talk_url );
$talk_container_classes = get_option( 'coral_talk_container_classes' );
$talk_version = get_option( 'coral_talk_version' );

$div_id = 'coral_talk_' . absint( rand() );

if ( empty( $talk_url ) || is_attachment() ):
	exit();
endif;

if ( $talk_version == "5" ) : ?>
	<div class="<?php echo esc_attr( $talk_container_classes ); ?>" id="coral_thread"></div>
	<script type="text/javascript">
	var embed;
	(function() {
		var d = document, s = d.createElement('script');
		var coralConfig = {
			id: "coral_thread",
			autoRender: true,
			rootURL: "<?php echo esc_url( $talk_url ); ?>"
		}

		<?php if ($jwt_token = coral_talk_generate_jwt_token()): ?>
			coralConfig.accessToken = "<?php echo $jwt_token; ?>";
		<?php endif ?>

		s.src = "<?php echo esc_url( $talk_url . '/assets/js/embed.js' ); ?>"

		s.onload = function() {
			embed = Coral.createStreamEmbed(coralConfig);
		};
		(d.head || d.body).appendChild(s);
	})();
	</script>
<?php
else : ?>
	<div class="<?php echo esc_attr( $talk_container_classes ); ?>" id="coral_thread"></div>
	<script src="<?php echo esc_url( $static_url . '/static/embed.js' ); ?>" async onload="
		Coral.talkStream = Coral.Talk.render(document.getElementById('coral_thread'), {
			talk: '<?php echo esc_url( $talk_url ); ?>'
		});
	"></script>
<?php endif;
