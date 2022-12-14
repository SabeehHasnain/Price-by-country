jQuery(document).ready(function(){
	jQuery('.pbc_price_by_country_tab').addClass('hide_if_variable');
	jQuery(document).on('click','.price_by_country_data .pbc_remove_rule_set',function(e){
		var me =jQuery(this);	
		me.closest('tr').remove();	
		var i=0;
	    me.closest('table').find('select.wc-enhanced-select').each(function(s){
	    	var id = jQuery(this).closest('.price_by_country_data').attr('data-id');
	    	if(jQuery(this).closest('.price_by_country_data').hasClass('variation')){
	    		jQuery(this).attr('name','wc_pbc_countries['+id+']['+i+'][]');
	    	}else{
			    jQuery(this).attr('name','wc_pbc_countries['+i+'][]');
		    }
		    i++;
		});
		
	});
	jQuery(document).on('click','.price_by_country_data .pbc_add_rule_set',function(e){
		var me =jQuery(this);
		me.closest("tr").find("select.wc-enhanced-select")
	    .selectWoo("destroy").closest("tr").clone().appendTo(me.closest("table"));
	    jQuery('.price_by_country_data table select.wc-enhanced-select').selectWoo();
	    var i=0;
	    me.closest("tbody").find('tr').last().find('.pbc_remove_rule_set').css('visibility','visible');
	    me.closest('table').find('select.wc-enhanced-select').each(function(s){
	    	var id = jQuery(this).closest('.price_by_country_data').attr('data-id');
	    	if(jQuery(this).closest('.price_by_country_data').hasClass('variation')){
	    		jQuery(this).attr('name','wc_pbc_countries['+id+']['+i+'][]');
	    	}else{
			    jQuery(this).attr('name','wc_pbc_countries['+i+'][]');
		    }
		    i++;
		});
	});

});

