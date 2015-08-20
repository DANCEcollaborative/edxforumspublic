<?php
/**
 * Functions Install
 *
 * Functions for installation & activation
 *
 * @package        Responsive
 * @license        license.txt
 * @copyright      2014 CyberChimps
 * @since          1.9.5.0
 *
 * Please do not edit this file. This file is part of the Responsive and all modifications
 * should be made in a child theme.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Customize theme activation message.
 *
 * @since    1.9.5.0
 */
function responsive_activation_notice() {
	if ( isset( $_GET['activated'] ) ) {
		$return = '<div class="updated activation"><p><strong>';
					$my_theme = wp_get_theme();
		if ( isset( $_GET['previewed'] ) ) {
			$return .= sprintf( __( 'Settings saved and %s activated successfully.', 'responsive' ), $my_theme->get( 'Name' ) );
		} else {
			$return .= sprintf( __( '%s activated successfully.', 'responsive' ), $my_theme->get( 'Name' ) );
		}
		$return .= '</strong> <a href="' . home_url( '/' ) . '">' . __( 'Visit site', 'responsive' ) . '</a></p>';
		//$return .= '<p><a class="button button-primary customize load-customize" href="' . admin_url( 'customize.php?theme=' . get_stylesheet() ) . '">' . __( 'Customize', 'responsive' ) . '</a>';
		$return .= ' <a class="button button-primary theme-options" href="' . admin_url( 'themes.php?page=theme_options' ) . '">' . __( 'Theme Options', 'responsive' ) . '</a>';
		$return .= ' <a class="button button-primary help" href="https://cyberchimps.com/forum/free/responsive/">' . __( 'Help', 'responsive' ) . '</a>';
		$return .= '</p></div>';
		echo $return;
	}
}
add_action( 'admin_notices', 'responsive_activation_notice' );

/*
 * Hide core theme activation message.
 *
 * @since    1.9.5.0
 */
function responsive_admin_css() { ?>
	<style>
	.themes-php #message2 {
		display: none;
	}
	.themes-php div.activation a {
		text-decoration: none;
	}
	</style>
<?php }
add_action( 'admin_head', 'responsive_admin_css' );