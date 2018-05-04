<?php

/**
 * The main file that handles the entire output, content filters, etc., for this plugin.
 *
 * @package Sticky Genesis Topbar
 * @author RainaStudio
 * @version 1.0.0
 */
 
// Check to make sure Genesis Framework is active.
register_activation_hook(__FILE__, 'sticky_genesis_topbar_require_genesis');
function sticky_genesis_topbar_require_genesis() {
		
    if( basename( get_template_directory() ) != 'genesis' ) {
	    deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Sorry, you can\'t use this plugin unless a <a href="http://my.studiopress.com/themes/" target="_blank" rel="nofollow">Genesis</a> theme is active. <a href="/wp-admin/plugins.php">Go Back</a>');

	}

}

// Hook function to 'genesis_setup'.
add_action( 'genesis_setup', 'sticky_genesis_topbar_setup', 15 );
function sticky_genesis_topbar_setup() {
	
	// Register metabox.
	add_action('genesis_theme_settings_metaboxes', 'sticky_genesis_topbar_settings');
	
	function sticky_genesis_topbar_settings( $_genesis_theme_settings_pagehook ) {
		
		add_meta_box( 'sticky_genesis_topbar-genesis-settings', __( 'Sticky Topbar', 'sticky_genesis_topbar' ), 'custom_topbar_box', $_genesis_theme_settings_pagehook, 'main', 'high' );
		
	}
	
	// Create topbar metabox.
	function custom_topbar_box() {
		?>
		<p><?php _e("<em>Enter your topbar text, button URL & text, including HTML if desired.</em>", 'sticky_genesis_topbar'); ?></p>
		<table class="form-table"><tbody><tr valign="top">
			<th scope="row"><label><strong>Topbar Text:</strong></label></th>
			<td>
				<p><textarea name="<?php echo GENESIS_SETTINGS_FIELD; ?>[sticky_genesis_topbar_text]" class="large-text" id="sticky_genesis_topbar_text" cols="40" rows="5" maxlength="80"><?php echo htmlspecialchars( genesis_get_option('sticky_genesis_topbar_text') ); ?></textarea></p>
				<p><span class="description">ie. Get 30% OFF on StudioPlayer Genesis WordPress theme</span><code>Limit 80 characters</code></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><strong>Topbar Button URL:</strong></label></th>
			<td>
				<p><input type="url" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[sticky_genesis_topbar_url]" class="large-text" id="sticky_genesis_topbar_url"placeholder="http://" size="40" value="<?php echo htmlspecialchars( genesis_get_option('sticky_genesis_topbar_url') ); ?>"/></p>
				<p><span class="description">ie.<code>https://rainastudio.com/themes/studioplayer</code></span></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><strong>Topbar Button Text:</strong></label></th>
			<td>
				<p><input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[sticky_genesis_topbar_btn_text]" class="regular-text" id="sticky_genesis_topbar_btn_text" value="<?php echo htmlspecialchars( genesis_get_option('sticky_genesis_topbar_btn_text') ); ?>"/></p>
				<p><span class="description">ie. Subscribe, Buy Now, Get Now, Check Out, 30% OFF, Best Deal, Learn More, Live Demo</span></p>
			</td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Remove Topbar', 'sticky_genesis_topbar' ); ?></th>
		<td>
			<fieldset>
			<legend class="screen-reader-text"><?php esc_html_e( 'Remove Topbar', 'sticky_genesis_topbar' ); ?></legend>
			
					<p><label for="sticky_genesis_topbar_on_remove"><input type="checkbox" name="genesis-settings[sticky_genesis_topbar_on_remove]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[sticky_genesis_topbar_on_remove]" value="1"<?php checked( 1, genesis_get_option( 'sticky_genesis_topbar_on_remove', GENESIS_SETTINGS_FIELD ) ); ?>/></p>

			</fieldset>
		</td>
	</tr>
		</tbody></table>
		<?php
	}
	
	// Register defaults.
	add_filter( 'genesis_theme_settings_defaults', 'sticky_genesis_topbar_settings_defaults' );
	function sticky_genesis_topbar_settings_defaults( $defaults ) {
		
		$defaults['sticky_genesis_topbar_text'] = 'Go to Genesis > Theme Settings to set topbar';
		
		$defaults['sticky_genesis_topbar_url'] = admin_url() . 'admin.php?page=genesis';
		
		$defaults['sticky_genesis_topbar_btn_text'] = 'Set Now';
	 
		return $defaults;
	}
	
	// Sanitization.
	add_action( 'genesis_settings_sanitizer_init', 'sticky_genesis_topbar_sanitization_filters' );
	
	function sticky_genesis_topbar_sanitization_filters() {
		
		genesis_add_option_filter( 'safe_html', GENESIS_SETTINGS_FIELD, array( 'sticky_genesis_topbar_text' ) );
		genesis_add_option_filter( 'safe_html', GENESIS_SETTINGS_FIELD, array( 'sticky_genesis_topbar_url' ) );
		genesis_add_option_filter( 'safe_html', GENESIS_SETTINGS_FIELD, array( 'sticky_genesis_topbar_btn_text' ) );
		genesis_add_option_filter( 'one_zero', GENESIS_SETTINGS_FIELD, array( 'sticky_genesis_topbar_on_remove' ) );
	}
	
	// Remove topbar if checkbox on
	$topbar = genesis_get_option( 'sticky_genesis_topbar_on_remove', GENESIS_SETTINGS_FIELD);
	if ( $topbar == 1 ) {
		remove_action( 'genesis_before_header', 'sticky_genesis_topbar_before_header' );
	} else {
		// Register 'topbar' before genesis_before_header
		add_action( 'genesis_before_header', 'sticky_genesis_topbar_before_header' );
		
		function sticky_genesis_topbar_before_header() {
			?><div class="topbar"><div class="wrap"><div class="promo-topbar">
				<p><?php echo genesis_get_option('sticky_genesis_topbar_text'); ?></p>
				<a class="button promo" href="<?php echo genesis_get_option('sticky_genesis_topbar_url'); ?>"><?php echo genesis_get_option('sticky_genesis_topbar_btn_text'); ?></a>
			</div></div></div><?php
		}
	}
	
	// Call plugin's scripts & stylesheet.
	add_action( 'wp_enqueue_scripts', 'sticky_genesis_topbar_scripts' );
	function sticky_genesis_topbar_scripts() {
		if ( !is_admin() ) {
		
			wp_enqueue_style( 'app-topbar-css', sticky_genesis_topbar_css . 'style.css' );
			wp_register_script( 'app-topbar-js', sticky_genesis_topbar_js . 'topbar.js', array( 'jquery' ), sticky_genesis_topbar_version, true );
			wp_enqueue_script( 'app-topbar-js' );
		
		}
	}
	
}