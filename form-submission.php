<?Php
/**
 * Plugin Name: Form Submission
 * Description: Awesome plugin.
 * Plugin URI:  #
 * Version:     1.0.0
 * Author:      Anisur Rahman
 * Author URI:  https:github.com/anisur2805
 * Text Domain: afs-form
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

use AFS\Form_Submission\Admin;
use AFS\Form_Submission\Ajax;
use AFS\Form_Submission\API;
use AFS\Form_Submission\Assets;
use AFS\Form_Submission\Frontend;
use AFS\Form_Submission\Installer;

defined( 'ABSPATH' ) or die( 'No Cheating!' );

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class AFS_Form_Submission {
	/**
	 * plugin version
	 */
	const VERSION = '1.0';

	/**
	 * class constructor
	 */
	private function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_action( 'init', array( $this, 'afs_gutenberg_scripts' ) );
	}

	/**
	 * Initialize a singleton instance
	 *
	 * @return \AFS_Form_Submission
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * define plugin require constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'AFS_VERSION', self::VERSION );
		define( 'AFS_FILE', __FILE__ );
		define( 'AFS_PATH', __DIR__ );
		define( 'AFS_URL', plugins_url( '', AFS_FILE ) );
		define( 'AFS_ASSETS', AFS_URL . '/assets' );
		define( 'AFS_INCLUDES', AFS_URL . '/includes' );
		define( 'AFS_INCLUDES_FILE', AFS_PATH . '/includes' );
	}

	/**
	 * Do staff upon plugin activation
	 *
	 * @return void
	 */
	public function activate() {
		$installer = new Installer();
		$installer->run();
	}

	public function init_plugin() {

		new Assets();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new Ajax();
		}

		if ( is_admin() ) {
			new Admin();
		} else {
			new Frontend();
		}

		new API();
	}

	public function afs_gutenberg_scripts() {
		register_block_type( __DIR__ . '/build/report-table' );
		register_block_type( __DIR__ . '/build/afs-form' );
	}
}

/**
 * Initialize the main plugin
 *
 * @return \AFS_Form_Submission
 */
function afs_call() {
	return AFS_Form_Submission::init();
}

afs_call();
