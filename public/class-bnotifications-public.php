<?php
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BNotifications
 * @subpackage BNotifications/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    BNotifications
 * @subpackage BNotifications/public
 * @author     Emerson Broga <emerson@emersonbroga.com>
 */
class BNotifications_Public {

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
	 * @param      string    $bNotifications       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $bNotifications, $version ) {

		$this->bNotifications = $bNotifications;
		$this->version = $version;
 	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->bNotifications, plugin_dir_url( __FILE__ ) . 'css/bnotifications-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_enqueue_script( $this->bNotifications, plugin_dir_url( __FILE__ ) . 'js/bnotifications-public.js', array( 'jquery',  ), $this->version, false );
	}

	public function add_firebase_configs() {
 		$serviceWorker =  '/bnotification-sw.js';
		$publicVapidKey= BNotifications::VAPIDKEY_PUBLIC;

		echo '
<script type="module">
		const bnotifications = new BNotifications("'. $serviceWorker .'", "'.$publicVapidKey.'");
		bnotifications.init();
</script>
';
	}

	public function save_subscription(){
		global $wpdb;

		$table_name = $wpdb->prefix . BNotifications::TABLE_NAME;
	
		$endpoint = $_POST['endpoint'];
		$p256dh = $_POST['p256dh'];
		$auth = $_POST['auth'];

		if(!$endpoint || !$p256dh || !$auth){
			wp_send_json_error();
			die();
		}

		$data = [
			'endpoint' => $endpoint,
			'p256dh' => $p256dh,
			'auth' =>$auth,
		];
		
		$format = [
			'endpoint' => '%s',
			'p256dh' => '%s',
			'auth' => '%s',
		]; 
			
		$results = $wpdb->get_row("SELECT id FROM $table_name WHERE auth = '$auth'");
		if (!$results) {
			$wpdb->insert($table_name, $data,	$format);
		}

		wp_send_json_success();
		die();
	}
}
