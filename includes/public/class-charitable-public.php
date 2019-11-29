<?php
/**
 * Charitable Public class.
 *
 * @package   Charitable/Classes/Charitable_Public
 * @author    Eric Daams
 * @copyright Copyright (c) 2019, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Public' ) ) :

	/**
	 * Charitable Public class.
	 *
	 * @since 1.0.0
	 */
	final class Charitable_Public {

		/**
		 * The single instance of this class.
		 *
		 * @since 1.2.0
		 *
		 * @var   Charitable_Public|null
		 */
		private static $instance = null;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since  1.2.0
		 *
		 * @return Charitable_Public
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Set up the class.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_donation_form_scripts' ), 11 );
			add_action( 'charitable_campaign_loop_before', array( $this, 'maybe_enqueue_donation_form_scripts' ) );
			add_filter( 'post_class', array( $this, 'campaign_post_class' ) );
			add_filter( 'comments_open', array( $this, 'disable_comments_on_application_pages' ) );

			do_action( 'charitable_public_start', $this );
		}

		/**
		 * Conditionally load the donation form scripts if we're viewing the donation form.
		 *
		 * @since   1.4.0
		 *
		 * @return  boolean True if scripts were loaded. False otherwise.
		 */
		public function maybe_enqueue_donation_form_scripts() {
			$load = charitable_is_page( 'campaign_donation_page' );

			if ( ! $load ) {
				$load = 'charitable_campaign_loop_before' == current_action() && 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' );
			}

			if ( $load ) {
				$this->enqueue_donation_form_scripts();
			}

			return $load;
		}

		/**
		 * Enqueues the donation form scripts.
		 *
		 * @since   1.4.6
		 *
		 * @return  void
		 */
		public function enqueue_donation_form_scripts() {
			wp_enqueue_script( 'charitable-script' );

			if ( Charitable_Gateways::get_instance()->any_gateway_supports( 'credit-card' ) ) {
				wp_enqueue_script( 'charitable-credit-card' );
			}
		}

		/**
		 * Adds custom post classes when viewing campaign.
		 *
		 * @since   1.0.0
		 *
		 * @param 	string[] $classes List of classes to be added with post_class().
		 * @return  string[]
		 */
		public function campaign_post_class( $classes ) {
			$campaign = charitable_get_current_campaign();

			if ( ! $campaign ) {
				return $classes;
			}

			$classes[] = $campaign->has_goal() ? 'campaign-has-goal' : 'campaign-has-no-goal';
			$classes[] = $campaign->is_endless() ? 'campaign-is-endless' : 'campaign-has-end-date';
			return $classes;
		}

		/**
		 * Disable comments on application pages like the donation page.
		 *
		 * @since   1.3.0
		 *
		 * @param   boolean $open Whether comments are open.
		 * @return  boolean
		 */
		public function disable_comments_on_application_pages( $open ) {
			/* If open is already false, just hit return. */
			if ( ! $open ) {
				return $open;
			}

			if ( charitable_is_page( 'campaign_donation_page', array( 'strict' => true ) )
			|| charitable_is_page( 'campaign_widget_page' )
			|| charitable_is_page( 'donation_receipt_page' )
			|| charitable_is_page( 'donation_processing_page' ) ) {
				$open = false;
			}

			return $open;
		}

		/**
		 * Load the template functions after theme is loaded.
		 *
		 * This gives themes time to override the functions.
		 *
		 * @deprecated 2.0.0
		 *
		 * @since  1.2.3
		 * @since  1.6.10 Deprecated
		 *
		 * @return void
		 */
		public function load_template_files() {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.6.10',
				'charitable()->load_template_files()'
			);

			charitable()->load_template_files();
		}
	}

endif;
