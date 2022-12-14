<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WCPBC_Price_by_country_Admin_cat')){
	class WCPBC_Price_by_country_Admin_cat{
		public function __construct(){ 
			add_action('product_cat_add_form_fields',array($this, 'pbc_add_new_field'), 10);
			add_action('product_cat_edit_form_fields', array($this,'pbc_edit_new_field'), 10,1);
			add_action('edited_product_cat', array($this,'pbc_save_new_field'), 10);
			add_action('create_product_cat', array($this,'pbc_save_new_field'), 10);
		}
		public function pbc_add_new_field(){  
		    ?>
      <div class="form-field term-price-by-country-wrap">
        <label for="wc_ubp_price_by_country"><?php _e("Price By Country","wc-pbc"); ?></label>
          <select class="postform" name="wc_ubp_price_by_country" id="wc_ubp_price_by_country">
            <option value="disable"><?php _e("Disable","wc-pbc"); ?></option>
            <option value="inherit"><?php _e("Inherit","wc-pbc"); ?></option>
            <option value="unique"><?php _e("Enable Unique","wc-pbc"); ?></option>
          </select>
      </div>
      <?php $countries_obj   = new WC_Countries();
            $countries   = $countries_obj->__get('countries'); ?>
      <div class="form-field term-price-by-country-wrap">
        <label for="wc_pbc_countries"><?php _e("Countries","wc-pbc"); ?></label>
          <select name="wc_pbc_countries[]" class="wc-enhanced-select" id="wc_pbc_countries" multiple>
            <?php foreach ($countries as $key => $value) {
              echo '<option value="'.$key.'">'.$value.'</option>';
            } ?>
          </select>
      </div>
      <div class="form-field term-price-type-wrap">
        <label for="wc_pbc_price_type"><?php _e("Price Type","wc-pbc"); ?></label>
          <select class="postform" name="wc_pbc_price_type" id="wc_pbc_price_type">
            <option value="increase_fixed"><?php _e("Increase by Fixed Price","wc-pbc"); ?></option>
            <option value="increase_percentage"><?php _e("Increase by Percentage Price","wc-pbc"); ?></option>
            <option value="decrease_fixed"><?php _e("Decrease by Fixed Price","wc-pbc"); ?></option>
            <option value="decrease_percentage"><?php _e("Decrease by Percentage Price","wc-pbc"); ?></option>
          </select>
      </div>
      <div class="form-field term-amount-wrap">
        <label for="wc_pbc_input_amount"><?php _e("Amount","wc-pbc"); ?></label>
          <input type="text" name="wc_pbc_input_amount" id="wc_pbc_input_amount">
      </div>
        <?php
		}  
		public function pbc_edit_new_field($term){
		  $term_id = $term->term_id; 
      $pbc=get_term_meta($term_id,'wc_ubp_price_by_country',true);
      $rulesets=get_term_meta($term_id,'wc_ubp_price_rulesets',true);
      ?>
      <tr class="form-field term-price-by-country-wrap">
        <th><?php _e("Price By Country","wc-pbc"); ?></th>
        <td scope="row">
          <select class="postform" name="wc_ubp_price_by_country">
            <option value="disable" <?php echo ($pbc=='disable') ? 'selected' : ''; ?>><?php _e("Disable","wc-pbc"); ?></option>
            <option value="inherit" <?php echo ($pbc=='inherit') ? 'selected' : ''; ?>><?php _e("Inherit","wc-pbc"); ?></option>
            <option value="unique" <?php echo ($pbc=='unique') ? 'selected' : ''; ?>><?php _e("Enable Unique","wc-pbc"); ?></option>
          </select>
        </td>
      </tr>
      <?php $countries_obj   = new WC_Countries();
            $countries   = $countries_obj->__get('countries'); ?>
      <tr class="form-field term-price-by-country-wrap">
        <th><?php _e("Countries","wc-pbc"); ?></th>
        <td scope="row">
          <select name="wc_pbc_countries[]" class="wc-enhanced-select" multiple>
            <?php foreach ($countries as $key => $value) {
               $selected="";
              if(isset($rulesets['countries']) && in_array($key, $rulesets['countries']))
                $selected="selected";
              echo '<option value="'.esc_attr($key).'" '.$selected.'>'.esc_html($value).'</option>';
            } ?>
          </select>
        </td>
      </tr>
      <tr class="form-field term-price-type-wrap">
        <th><?php _e("Price Type","wc-pbc"); ?></th>
        <td scope="row">
          <select class="postform" name="wc_pbc_price_type">
            <option value="increase_fixed" <?php echo (isset($rulesets['price_type']) && $rulesets['price_type']=='increase_fixed') ? 'selected' : ''; ?>><?php _e("Increase by Fixed Price","wc-pbc"); ?></option>
            <option value="increase_percentage" <?php echo (isset($rulesets['price_type']) && $rulesets['price_type']=='increase_percentage') ? 'selected' : ''; ?>><?php _e("Increase by Percentage Price","wc-pbc"); ?></option>
            <option value="decrease_fixed" <?php echo (isset($rulesets['price_type']) && $rulesets['price_type']=='decrease_fixed') ? 'selected' : ''; ?>><?php _e("Decrease by Fixed Price","wc-pbc"); ?></option>
            <option value="decrease_percentage" <?php echo (isset($rulesets['price_type']) && $rulesets['price_type']=='decrease_percentage') ? 'selected' : ''; ?>><?php _e("Decrease by Percentage Price","wc-pbc"); ?></option>
          </select>
        </td>
      </tr>
      <tr class="form-field term-amount-wrap">
        <th><?php _e("Amount","wc-pbc"); ?></th>
        <td scope="row">
          <input type="text" name="wc_pbc_input_amount" value="<?php echo isset($rulesets['amount']) ? esc_attr($rulesets['amount']) : ''; ?>">
        </td>
      </tr>
      <?php
  	}
		public function pbc_save_new_field( $term_id, $term ) {
			if ( isset( $_POST['wc_ubp_price_by_country']))		
				update_term_meta($term_id,'wc_ubp_price_by_country',wc_clean($_POST['wc_ubp_price_by_country']));

      $ruleset=array();
      if ( isset( $_POST['wc_pbc_countries']))  
        $ruleset['countries']=wc_clean($_POST['wc_pbc_countries']); 

      if ( isset( $_POST['wc_pbc_price_type']))
        $ruleset['price_type']=wc_clean($_POST['wc_pbc_price_type']);

      if ( isset( $_POST['wc_pbc_input_amount']))
        $ruleset['amount']=sanitize_text_field($_POST['wc_pbc_input_amount']);

        update_term_meta($term_id,'wc_ubp_price_rulesets',$ruleset);
		}
  }
	new WCPBC_Price_by_country_Admin_cat();
} 
	
	