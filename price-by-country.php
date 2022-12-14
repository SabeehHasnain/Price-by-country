<?php
/*
* Plugin Name: Price By Country
* Plugin URI: http://developers-hub.com/
* Description: Woocommerce price by country.
* Version: 1.0.1
* Author: Developers Hub
* Author URI: https://developers-hub.com/
* License: GPL-2.0+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain: wc-pbc
* Domain Path: /languages/
*/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if (!defined('WOO_WCPBC_DIR'))
    define('WOO_WCPBC_DIR', plugin_dir_path(__FILE__));
if (!defined('WOO_WCPBC_URL'))
    define('WOO_WCPBC_URL', plugin_dir_url(__FILE__));
if(!class_exists('WCPBC_Price_by_Country')){
	class WCPBC_Price_by_country{
		public function __construct(){
			if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
				$this->init();
			}else{
				add_action('admin_notices', array($this, 'admin_notice'));
			}
		}
		public function init(){
			if(is_admin()){
				require_once WOO_WCPBC_DIR.'includes/class-admin-settings.php';
				require_once WOO_WCPBC_DIR.'includes/class-product-settings.php';
				require_once WOO_WCPBC_DIR.'includes/class-taxonomy-settings.php';
			}else{
				if(get_option('pbc_price_by_country')=='yes'){
					require_once WOO_WCPBC_DIR.'includes/class-frontend.php';
					require_once WOO_WCPBC_DIR.'includes/class-user-info.php';
				}
			}
		}
		public function admin_notice(){
			global $pagenow;
	        if($pagenow==='plugins.php'){
	            $class = 'notice notice-error is-dismissible';
	            $message = __('Price by Country needs Woocommerce to be installed and active.', 'wc-pbc');
	            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
		    }
		}
	}
	new WCPBC_Price_by_country();
}