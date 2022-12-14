<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WCPBC_Price_by_Country_Frontend')){
	class WCPBC_Price_by_Country_Frontend{
		public function __construct(){
			// Simple, grouped and external products
			add_filter('woocommerce_product_get_price', array($this, 'custom_product_price'), 99, 2);
			add_filter('woocommerce_product_get_regular_price', array($this,'custom_product_price'), 99, 2 );
			// Product variations (of a variable product)
			add_filter('woocommerce_product_variation_get_price', array($this,'custom_product_price') , 99, 2 );
			add_filter('woocommerce_product_variation_get_regular_price',array($this, 'custom_product_price'), 99, 2 );
			// Variable product price range
			add_filter('woocommerce_variation_prices_price', array($this,'custom_variation_price'), 99, 3 );
			add_filter('woocommerce_variation_prices_regular_price', array($this,'custom_variation_price'), 99, 3 );
		}	
		function custom_product_price($price, $product) {
			wc_delete_product_transients($product->get_id());
			$price_by_country=get_post_meta($product->get_id(),'wc_ubp_price_by_country',true);
			if($price_by_country==='inherit'){
				$price=$this->get_price_by_category($product->get_id(), $price);
			}elseif($price_by_country==='unique'){
				$price=$this->get_price($product->get_id(), $price);
			}else{
				return $price;
			}
			return $price;
		}
		public function custom_variation_price($price, $variation, $product){
			// Delete product cached price
		    wc_delete_product_transients($variation->get_id());
		    $price_by_country=get_post_meta($variation->get_id(),'wc_ubp_price_by_country',true);
			if($price_by_country==='inherit'){
				$price=$this->get_price_by_category($variation->get_id(), $price);
			}elseif($price_by_country==='unique'){
				$price=$this->get_price($variation->get_id(), $price);
			}else{
				return $price;
			}
		    return $price;
		}
		public function get_price($id, $price){
			$rulesets=get_post_meta($id,'wp_ubp_price_rule_sets',true);
			$info=WCPBC_User_Info::instance();
			$countrycode=$info->get_code();
			if(!empty($countrycode) && !empty($rulesets)){
				$price=$this->get_price_by_country($countrycode,$rulesets,$price);
			}
			return $price;
		}
		public function get_price_by_category($product_id,$price){
			$term_list = wp_get_post_terms($product_id, 'product_cat', array("fields" => "ids"));
			if(!is_wp_error($term_list) && !empty($term_list)){
				foreach ($term_list as $key => $term_id) {
					$price_by_country=get_term_meta($term_id,'wc_ubp_price_by_country',true);
					if($price_by_country==='inherit'){
						$price=$this->get_price_global($price);
						break;
					}elseif($price_by_country==='unique'){
						$info=WCPBC_User_Info::instance();
						$countrycode=$info->get_code();
						$rulesets=get_term_meta($term_id,'wc_ubp_price_rulesets');
						if(!empty($countrycode) && !empty($rulesets)){
							$price=$this->get_price_by_country($countrycode,$rulesets,$price);
							break;
						}
					}
				}
			}else{
				$price=$this->get_price_global($price);
			}
			return $price;
		}
		public function get_price_global($price){
			if(get_option('pbc_enable_global')=='yes'){
				$rulesets=array();
				$info=WCPBC_User_Info::instance();
				$countrycode=$info->get_code();
				$set['countries']=get_option('pbc_multi_select_countries');
				$set['price_type']=get_option('pbc_price_type');
				$set['amount']=get_option('pbc_specific_amount');
				$rulesets[]=$set;
				$price=$this->get_price_by_country($countrycode,$rulesets,$price);
			}
			return $price;
		}
		public function get_price_by_country($countrycode,$rulesets,$price){
			foreach ($rulesets as $key => $set) {
				if(isset($set['countries']) && in_array($countrycode, $set['countries']) 
					&& in_array($set['price_type'], $this->price_types()) && !empty($set['amount'])){
					switch ($set['price_type']) {
						case 'increase_fixed':
							$price+=$set['amount'];
							break;
						case 'decrease_fixed':
							$price-=$set['amount'];
							break;
						case 'increase_percentage':
							$price+=($price/100)*$set['amount'];
							break;
						case 'decrease_percentage':
							$price-=($price/100)*$set['amount'];
							break;		
						default:
							$price;
							break;
					}
					break;
				}
			}
			return $price;
		}
		public function price_types(){
			return array('increase_fixed','decrease_fixed','increase_percentage','decrease_percentage');
		}		
	}		
	new WCPBC_Price_by_Country_Frontend();
}