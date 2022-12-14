<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WCPBC_Price_by_country_Admin_settings')){
	class WCPBC_Price_by_country_Admin_settings{
		public function __construct(){
			add_filter('woocommerce_product_data_tabs', array($this,'pbc_product_tabs') , 10, 1 );
      add_action('woocommerce_product_data_panels', array($this,'pbc_price_by_country_tab_content'), 10 );
      add_action('woocommerce_process_product_meta',array($this,'pbc_field_save'));
      // variation fields
      add_action( 'woocommerce_product_after_variable_attributes',array($this, 'field_to_variations'), 10, 3 ); 
      add_action( 'woocommerce_save_product_variation', array($this,'save_field_variations'), 10, 2 );
		}
		public function pbc_product_tabs( $tabs ){
       $tabs['pbc_price_by_country'] = array(
           'label' => __( 'Price By Country', 'wc-pbc' ),
           'target' => 'price_by_country_data',
           'priority' => 80,
       );
       return $tabs;
    }
    public function pbc_price_by_country_tab_content(){
      global $post;
      echo '<div id="price_by_country_data" class="panel woocommerce_options_panel price_by_country_data">';
      wp_nonce_field( 'wc_ubp_country_pricing_nonce', 'wc_ubp_country_pricing_nonce' );
        $price_by_country=get_post_meta($post->ID,'wc_ubp_price_by_country',true);
          woocommerce_wp_select(  array(
             'type'          => 'select',
             'id'            => 'wc_ubp_price_by_country',
             'label'         => __( 'Price By Country', 'wc-pbc' ),
             'value'        => !empty($price_by_country) ? $price_by_country : '',
             'description'   => __( '', 'wc-pbc' ),
              'default'       => '0',
              'description'   => '',
              'options' => array(
                  'disable' => __( 'Disable', 'wc-pbc'),
                  'inherit' => __( 'Inherit', 'wc-pbc' ), 
                  'unique' => __( 'Enable Unique', 'wc-pbc' ),
          )) );
          $countries_obj   = new WC_Countries();
          $countries   = $countries_obj->__get('countries');
          echo '<div class="pbc_rule_sets">';
          echo '<p>'.__("Please create countries based rule sets.","wc-pbc").'</p>';
          //echo '<pre>';
          $rulesets=get_post_meta($post->ID,'wp_ubp_price_rule_sets',true);
          //echo '</pre>';
          echo '<table>';
          echo '<tr><th>'.__("Countries","wc-pbc").'</th>
                <th>'.__("Price Type","wc-pbc").'</th>
                <th>'.__("Amount","wc-pbc").'</th>
                <th>'.__("Action","wc-pbc").'</th>
                </tr>';
          if(empty($rulesets)){
            echo '<tr><td>';
            woocommerce_wp_select( array(
                'id'           => 'wc_pbc_countries[0][]',
                'label'         => '',
                'options'      => $countries,
                'wrapper_class'=> 'temp',
                'class'        => 'wc-enhanced-select',
                'custom_attributes' => array('multiple' => 'multiple')
            ));
            echo '</td><td>';
            woocommerce_wp_select( array( 
                'id'            => 'wc_pbc_price_type[]', 
                'label'         => '',
                'default'       => '0',
                'wrapper_class'=> 'temp',
                  'options' => array(
                  'increase_fixed' => __( 'Increase by Fixed Price', 'wc-pbc' ), 
                  'increase_percentage' => __( 'Increase by Percentage Price', 'wc-pbc' ),
                  'decrease_fixed' => __('Decrease by Fixed Price', 'wc-pbc'),
                  'decrease_percentage' => __( 'Decrease by Percentage Price', 'wc-pbc' ),
             )
            ));
            echo '</td><td>';
            woocommerce_wp_text_input( array( 
                'id'           => 'wc_pbc_input_amount[]', 
                'label'         => '',
                'wrapper_class'=> 'temp',
            ));
           echo '</td><td><div class="pbc_flex"><span class="pbc_remove_rule_set" style="visibility:hidden">-</span><span class="pbc_add_rule_set">+</span></div></td></tr>';
         }else{
          $i=0;
          foreach ($rulesets as $key => $value) {
            echo '<tr><td>';
            woocommerce_wp_select( array(
                'id'           => 'wc_pbc_countries['.$i.'][]',
                'label'         => '',
                'options'      => $countries,
                'value'        => isset($value['countries']) ? $value['countries'] : '',
                'wrapper_class'=> 'temp',
                'class'        => 'wc-enhanced-select',
                'custom_attributes' => array('multiple' => 'multiple')
            ));
            echo '</td><td>';
            woocommerce_wp_select( array( 
                'id'            => 'wc_pbc_price_type[]', 
                'label'         => '',
                'default'       => '0',
                'wrapper_class'=> 'temp',
                'value'        => isset($value['price_type']) ? $value['price_type'] : '',
                'options' => array(
                 'increase_fixed' => __( 'Increase by Fixed Price', 'wc-pbc' ), 
                 'increase_percentage' => __( 'Increase by Percentage Price', 'wc-pbc' ),
                 'decrease_fixed' => __('Decrease by Fixed Price', 'wc-pbc'),
                 'decrease_percentage' => __( 'Decrease by Percentage Price', 'wc-pbc' ),
             )
            ));
            echo '</td><td>';
            woocommerce_wp_text_input( array( 
                'id'           => 'wc_pbc_input_amount[]', 
                'label'         => '',
                'value'        => isset($value['amount']) ? $value['amount'] : '',
                'wrapper_class'=> 'temp',
            ));
            $style='';
            if($i==0) $style='style="visibility:hidden"';
           echo '</td><td><div class="pbc_flex"><span class="pbc_remove_rule_set" '.$style.'>-</span><span class="pbc_add_rule_set">+</span></div></td></tr>';
           $i++;
          }
         }
         echo '</table>';
        echo '</div>';
        echo '</div>';
      }
      public function pbc_field_save( $post_id ){
          //if doing an auto save
          if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
          // if our nonce isn't there, or we can't verify it
          if( !isset( $_POST['wc_ubp_country_pricing_nonce'] ) || !wp_verify_nonce( $_POST['wc_ubp_country_pricing_nonce'], 'wc_ubp_country_pricing_nonce' ) ) return;
          // if current user can't edit this post
          if( !current_user_can( 'edit_post' ) ) return;
          if(!isset($_POST['product-type']) || @$_POST['product-type']=='variable') return;
          if ( isset($_POST['wc_ubp_price_by_country']))
             update_post_meta( $post_id, 'wc_ubp_price_by_country', sanitize_text_field($_POST['wc_ubp_price_by_country']));
          
          $rulesets=array();
          if ( isset($_POST['wc_pbc_countries']) && count($_POST['wc_pbc_countries'])>0 && isset($_POST['wc_pbc_price_type']) && isset($_POST['wc_pbc_input_amount'])){
            for($i=0; $i<count($_POST['wc_pbc_countries']); $i++){
              if(isset($_POST['wc_pbc_countries'][$i]) && !empty($_POST['wc_pbc_countries'][$i]) 
                && isset($_POST['wc_pbc_price_type'][$i]) && !empty($_POST['wc_pbc_price_type'][$i]) 
                && isset($_POST['wc_pbc_input_amount'][$i]) && !empty($_POST['wc_pbc_input_amount'][$i])){
                $rulesets[$i]['countries']=wc_clean($_POST['wc_pbc_countries'][$i]);
                $rulesets[$i]['price_type']=sanitize_text_field($_POST['wc_pbc_price_type'][$i]);
                $rulesets[$i]['amount']=sanitize_text_field($_POST['wc_pbc_input_amount'][$i]);
              }else{
                continue;
              }
            }
          }
          update_post_meta($post_id,'wp_ubp_price_rule_sets',$rulesets);
       }   
      public function field_to_variations($loop, $variation_data, $variation){
        echo '<div class="form-field price_by_country_data form-row variation" data-id="'.$loop.'">';
        woocommerce_wp_select(  array(
             'type'          => 'select',
             'id'            => 'wc_ubp_price_by_country[' . $loop . ']',
             'label'         => __( 'Price By Country', 'wc-pbc' ),
             'value'         => get_post_meta( $variation->ID, 'wc_ubp_price_by_country', true ),
             'description'   => __( '', 'wc-pbc' ),
              'default'       => '0',
              'description'   => '',
              'options' => array(
                  'disable' => __( 'Disable', 'wc-pbc'),
                  'inherit' => __( 'Inherit', 'wc-pbc' ), 
                  'unique' => __( 'Enable Unique', 'wc-pbc' ),
          )) );
          $countries_obj   = new WC_Countries();
          $countries   = $countries_obj->__get('countries');
          echo '<table>';
          echo '<thead>';
          echo '<tr><th>'.__("Counties","wc-pbc").'</th>
                <th>'.__("Price Type","wc-pbc").'</th>
                <th>'.__("Amount","wc-pbc").'</th>
                <th>'.__("Action","wc-pbc").'</th>
                </tr>';
          echo '</thead>';
          echo '<tbody>';
          $rulesets=get_post_meta($variation->ID,'wp_ubp_price_rule_sets',true);
          if(empty($rulesets)){
          echo '<tr><td>';
            woocommerce_wp_select( array(
                'id'           => 'wc_pbc_countries[' . $loop . '][0][]',
                'label'         => '',
                'options'      => $countries,
                'wrapper_class'=> 'temp',
                'class'        => 'wc-enhanced-select',
                'custom_attributes' => array('multiple' => 'multiple')
            ));
            echo '</td><td>';
            woocommerce_wp_select( array( 
                'id'            => 'wc_pbc_price_type[' . $loop . '][]', 
                'label'         => '',
                'default'       => '0',
                'wrapper_class'=> 'temp',
                  'options' => array(
                  'increase_fixed' => __( 'Increase by Fixed Price', 'wc-pbc' ), 
                  'increase_percentage' => __( 'Increase by Percentage Price', 'wc-pbc' ),
                  'decrease_fixed' => __('Decrease by Fixed Price', 'wc-pbc'),
                  'decrease_percentage' => __( 'Decrease by Percentage Price', 'wc-pbc' ),
             )
            ));
            echo '</td><td>';
            woocommerce_wp_text_input( array( 
                'id'           => 'wc_pbc_input_amount[' . $loop . '][]', 
                'label'         => '',
                'wrapper_class'=> 'temp',
                'type'  => 'text'
            ));
           echo '</td><td><div class="pbc_flex"><span class="pbc_remove_rule_set" style="visibility:hidden">-</span><span class="pbc_add_rule_set">+</span></div></td></tr>';
          }else{
            $j=0;
            foreach ($rulesets as $rkey => $rule) {
              echo '<tr><td>';
              woocommerce_wp_select( array(
                  'id'           => 'wc_pbc_countries[' . $loop . ']['.$j.'][]',
                  'label'         => '',
                  'options'      => $countries,
                  'wrapper_class'=> 'temp',
                  'value'        => isset($rule['countries']) ? $rule['countries'] : '',
                  'class'        => 'wc-enhanced-select',
                  'custom_attributes' => array('multiple' => 'multiple')
              ));
              echo '</td><td>';
              woocommerce_wp_select( array( 
                  'id'            => 'wc_pbc_price_type[' . $loop . '][]', 
                  'label'         => '',
                  'default'       => '0',
                  'wrapper_class'=> 'temp',
                  'value'        => isset($rule['price_type']) ? $rule['price_type'] : '',
                    'options' => array(
                    'increase_fixed' => __( 'Increase by Fixed Price', 'wc-pbc' ), 
                    'increase_percentage' => __( 'Increase by Percentage Price', 'wc-pbc' ),
                    'decrease_fixed' => __('Decrease by Fixed Price', 'wc-pbc'),
                    'decrease_percentage' => __( 'Decrease by Percentage Price', 'wc-pbc' ),
               )
              ));
              echo '</td><td>';
              woocommerce_wp_text_input( array( 
                  'id'           => 'wc_pbc_input_amount[' . $loop . '][]', 
                  'label'         => '',
                  'wrapper_class'=> 'temp',
                  'value'        => isset($rule['amount']) ? $rule['amount'] : '',
                  'type'  => 'text'
              ));
              $style='';
            if($j==0) $style='style="visibility:hidden"';
             echo '</td><td><div class="pbc_flex"><span class="pbc_remove_rule_set" '.$style.'>-</span><span class="pbc_add_rule_set">+</span></div></td></tr>';
            $j++;
            }
          }
          echo '</tbody>';
          echo '</table>';
          echo '</div>';
      }   
      public function save_field_variations( $variation_id, $i){

        if ( isset($_POST['wc_ubp_price_by_country'][$i]) ) {
            update_post_meta( $variation_id, 'wc_ubp_price_by_country', esc_attr( $_POST['wc_ubp_price_by_country'][$i]) );
        } 
        $rulesets=array();
          if ( isset($_POST['wc_pbc_countries'][$i]) && count($_POST['wc_pbc_countries'][$i])>0 && isset($_POST['wc_pbc_price_type'][$i]) && isset($_POST['wc_pbc_input_amount'][$i])){
            for($j=0; $j<count($_POST['wc_pbc_countries'][$i]); $j++){
              if(isset($_POST['wc_pbc_countries'][$i][$j]) && !empty($_POST['wc_pbc_countries'][$i][$j]) 
                && isset($_POST['wc_pbc_price_type'][$i][$j]) && !empty($_POST['wc_pbc_price_type'][$i][$j]) 
                && isset($_POST['wc_pbc_input_amount'][$i][$j]) && !empty($_POST['wc_pbc_input_amount'][$i][$j])){
                $rulesets[$j]['countries']=wc_clean($_POST['wc_pbc_countries'][$i][$j]);
                $rulesets[$j]['price_type']=sanitize_text_field($_POST['wc_pbc_price_type'][$i][$j]);
                $rulesets[$j]['amount']=sanitize_text_field($_POST['wc_pbc_input_amount'][$i][$j]);
              }else{
                continue;
              }
            }
          }
        update_post_meta($variation_id,'wp_ubp_price_rule_sets',$rulesets);
      }   
	}
	new WCPBC_Price_by_country_Admin_settings();
}