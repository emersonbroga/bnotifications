<?php
require_once(dirname(plugin_dir_path(__FILE__)) . '/vendor/autoload.php');

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BNotifications
 * @subpackage BNotifications/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    BNotifications
 * @subpackage BNotifications/admin
 * @author     Emerson Broga <emerson@emersonbroga.com>
 */
class BNotifications_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $bNotifications    The ID of this plugin.
	 */
	private $bNotifications;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $bNotifications       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $bNotifications, $version ) {

		$this->bNotifications = $bNotifications;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in BNotifications_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BNotifications_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->bNotifications, plugin_dir_url( __FILE__ ) . 'css/bnotifications-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in BNotifications_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BNotifications_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->bNotifications, plugin_dir_url( __FILE__ ) . 'js/bnotifications-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function notify_subscribers($new_status, $old_status, $post ){

		global $wpdb;
		
		// if ($new_status != 'publish' || $old_status == 'publish') {
		if ($new_status != 'publish' ) {
			return;
		}

		$id = get_the_ID( $post );
		$title = get_the_title( $post );
		$url = get_the_permalink( $post );
		$site_logo  = BNotifications::SITE_LOGO_URL;
		$site_name = get_bloginfo('name'); 

		$table_name = $wpdb->prefix . BNotifications::TABLE_NAME;
		$subscriptions = $wpdb->get_results("SELECT * FROM $table_name");


		$auth = ['VAPID'=>[
			'subject' => BNotifications::VAPIDKEY_SUBJECT,
			'publicKey' => BNotifications::VAPIDKEY_PUBLIC,
			'privateKey' => BNotifications::VAPIDKEY_PRIVATE,
		]];

		$webPush = new WebPush($auth);

		$payload = json_encode([
			"title" => $site_name,
			"body" => $title,
			"data" => [ "url" => $url ],
			"icon" => $site_logo,
			"badge" => $site_logo,
			"tag" => 'new-post-' . $id,
		]);

		write_log($payload);

		$detinations = [];
		foreach ($subscriptions as $subscription) {
			 $detinations[] = Subscription::create([
				"endpoint" => $subscription->endpoint,
				"keys" => [
					"p256dh" => $subscription->p256dh,
					"auth" => $subscription->auth
				],
				
			 ]);
		}

		 // send multiple notifications with payload
		foreach ($detinations as $detination) {
			write_log($detination ); 
			write_log($payload ); 

			$webPush->queueNotification($detination, $payload);
		}

 
		foreach ($webPush->flush() as $report) {
			$endpoint = $report->getRequest()->getUri()->__toString();

			if ($report->isSuccess()) {
				write_log("[v] Message sent successfully for subscription {$endpoint}.");
			} else {
					write_log("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
			}
		}
	}
}

if (!function_exists('write_log')) {
	function write_log($log) {
			if (true === WP_DEBUG) {
					if (is_array($log) || is_object($log)) {
							error_log(print_r($log, true));
					} else {
							error_log($log);
					}
			}
	}
}
