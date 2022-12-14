<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WCPBC_User_Info')){
	class WCPBC_User_Info{
		/*
		* Holds Country Code
		*/
		private $countrycode;
		/*
		* Holds Instance of Object
		*/
		private static $instance;
		public function __construct(){
		}
		public function get_code(){
			global $post, $woocommerce;
			$type=get_option('pbc_price_based_on');
			$countrycode='';
			if(!empty($type)){
				switch ($type) {
					case 'ip_based':
						$location = WC_Geolocation::geolocate_ip();
						$countrycode=$location['country'];
						break;
					case 'billing':
						if(is_user_logged_in() && empty($woocommerce->customer->get_billing_country())){
							$countrycode=get_user_meta(get_current_user_id(),'billing_country',true);
						}else{
							$countrycode=$woocommerce->customer->get_billing_country();
						}
					break;
					case 'shipping':
						if(is_user_logged_in() && empty($woocommerce->customer->get_shipping_country())){
							$countrycode=get_user_meta(get_current_user_id(),'shipping_country',true);
						}else{
							$countrycode=$woocommerce->customer->get_shipping_country();
						}
					break;
					default:
						$location = WC_Geolocation::geolocate_ip();
						$countrycode=$location['country'];
					break;
				}
			}else{
				$location = WC_Geolocation::geolocate_ip();
				$countrycode=$location['country'];
			}
			$this->countrycode=$countrycode;
			return $countrycode;
		}
		public static function instance()
	    {
	        if (is_null(self::$instance)) {
	            self::$instance = new self();
	        } //is_null(self::$instance)
	        return self::$instance;
	    }
	}
	new WCPBC_User_Info();
}