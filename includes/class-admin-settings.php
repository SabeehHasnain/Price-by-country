<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WCPBC_Price_by_country_Admin')){
	class WCPBC_Price_by_country_Admin{
		public function __construct(){
	       	add_filter( 'woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50 );
	        add_action( 'woocommerce_settings_tabs_settings_tab', array($this,'settings_tab'));
	        add_action( 'woocommerce_update_options_settings_tab', array($this, 'update_settings'));
	        add_action('admin_enqueue_scripts',array($this,"enqueue_scripts"));
   		}
	    public static function add_settings_tab( $settings_tabs ){
        	$settings_tabs['settings_tab'] = __( 'Price by Country', 'wc-pbc' );
        	return $settings_tabs;
   		}
   		public static function settings_tab(){
        	woocommerce_admin_fields( self::get_settings() );
    	}
    	public static function update_settings(){
        	woocommerce_update_options( self::get_settings() );
    	}
    	public static function get_settings(){
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Price by Country Settings', 'wc-pbc' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_section_title'
            ),
            'pbc_price_by_country' =>array(
						'title'   => __( 'Price by Country', 'wc-pbc' ),
						'desc'    => __( 'Enable Price by Country Settings.','wc-pbc' ),
						'id'      => 'pbc_price_by_country',
						'default' => 'no',
						//'desc_tip' => true,
						'type'    => 'checkbox',
					),
            'pbc_price_based_on' =>  array(
					'title'    => __( 'Price Based On', 'wc-pbc' ),
					'desc'     => __( 'This controls which address is used to refresh products prices on checkout.','wc-pbc' ),
					'class'    => __('price_based_price'),
					'id'       => 'pbc_price_based_on',
					'default'  => __('billing','wc-pbc'),
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'ip_based' => __('IP Detect Country','wc-pbc'),
						'billing'  => __( 'Customer Billing Country', 'wc-pbc' ),
						'shipping' => __( 'Customer Shipping Country', 'wc-pbc' ),
					),
				),
            'pbc_logged_in_users' =>  array(
					'title'    => __( 'Logged in Users', 'wc-pbc' ),
					'desc'     => __( 'Enable Price by Country for logged in users only.', 'wc-pbc' ),
					'id'       => 'pbc_logged_in_users',
					'default'  => __('no'),
					'type'     => 'checkbox',
					//'desc_tip' => true,
				),
            'pbc_enable_global' =>  array(
					'title'    => __( 'Global', 'wc-pbc' ),
					'desc'     => __( 'Global Price by Country Rule Sets.', 'wc-pbc' ),
					'id'       => 'pbc_enable_global',
					'default'  => __('no'),
					'type'     => 'checkbox',
					//'desc_tip' => true,
				),
            'pbc_multi_select_countries' => array(
					'title'   => __( 'Select Countries', 'wc-pbc' ),
					'desc'    => __('This controls which countries you want to select for price.','wc-pbc'),
					'id'      => 'pbc_multi_select_countries',
					'default' => '',
					'desc_tip' => true,
					'type'    => 'multi_select_countries',
				),
            'pbc_price_type' =>  array(
					'title'    => __( 'Price Type', 'wc-pbc' ),
					'desc'     => __( '' ,'wc-pbc'),
					'id'       => 'pbc_price_type',
					'default'  => __(''),
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'increase_fixed'  => __( 'Increase by Fixed Price', 'wc-pbc' ),
						'increase_percentage' => __( 'Increase by Percentage Price', 'wc-pbc' ),
						'decrease_fixed'  => __( 'Decrease by Fixed Price', 'wc-pbc' ),
						'decrease_percentage' => __( 'Decrease by Percentage Price', 'wc-pbc' ),
					),
				),
            'pbc_specific_amount' =>  array(
					'title'    => __( 'Amount ', 'wc-pbc' ),
					'desc'     => __( '' , 'wc-pbc'),
					'id'       => 'pbc_specific_amount',
					'default'  => __(''),
					'type'     => 'number',
					'desc_tip' => true,
					'options'  => array(),
				),
            '</div>',
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_section_end'
            )
	        );
	        return apply_filters( 'wc_settings_tab_settings', $settings );
	    }
	    public function enqueue_scripts(){
			global $post_type;
		    if( 'product' == $post_type ){
				wp_enqueue_style( 'wc-pbc-price', WOO_WCPBC_URL . 'assets/css/backend_style.css', '');
				wp_enqueue_script('pbc_script',WOO_WCPBC_URL.'assets/js/backend_script.js',array('jquery'));
			}
		}
	}
 	new WCPBC_Price_by_country_Admin();
}