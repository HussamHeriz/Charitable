<?php
/**
 * Donation Form shortcode class.
 *
 * @version     1.6.0
 * @package     Charitable/Shortcodes/Donation Form
 * @category    Class
 * @author      Eric Daams
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Donation_Form_Shortcode' ) ) :

	/**
	 * Charitable_Donation_Form_Shortcode class.
	 *
	 * @since   1.2.0
	 */
	class Charitable_Donation_Form_Shortcode {

		/**
		 * The callback method for the donation form shortcode.
		 *
		 * This receives the user-defined attributes and passes the logic off to the class.
		 *
		 * @since   1.6.0
		 *
		 * @param   array   $atts   User-defined shortcode attributes.
		 * @return  string
		 */
		public static function display( $atts ) {

			if ( ! array_key_exists( 'campaign_id', $atts ) ) {
		        return '';
		    }
		    if ( Charitable::CAMPAIGN_POST_TYPE !== get_post_type( $atts['campaign_id'] ) ) {
		        return '';
		    }
		    ob_start();
		    if ( ! wp_script_is( 'charitable-script', 'enqueued' ) ) {
		        Charitable_Public::get_instance()->enqueue_donation_form_scripts();
		    }
		    
		    $form = charitable_get_campaign( $atts['campaign_id'] )->get_donation_form();
		    do_action( 'charitable_donation_form_before', $form );
		    
		    $form->render();
		    
		    do_action( 'charitable_donation_form_after', $form );
		    return ob_get_clean();

		}
	}

endif;
