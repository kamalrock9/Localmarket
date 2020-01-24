<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( isset( $_POST['create_faq'] ) && check_admin_referer( 'phoe_app_faq_create_form_action', 'phoe_app_faq_create_form_action_form_nonce_field' ) ) {

	$enable_faq=isset($_POST['enable_faq'])? $_POST['enable_faq']:false;
	
	$main_array=array();
		
	$category_list=$_POST['category'];
	
	$question_list=$_POST['question'];
	
	$answer_list=$_POST['answer'];
	
	for($i=0;$i<count($category_list);$i++){
		
		for($j=0;$j<count($question_list[$i]);$j++){
			
			$main_array[stripslashes($category_list[$i])][]=array(
				'question'=>stripslashes($question_list[$i][$j]),
				'answer'=>stripslashes($answer_list[$i][$j]),
				);
			
		}
		
	}
	
	update_option("pheon_woo_app_faq_setting",$main_array);
	update_option("pheon_woo_app_enable_faq",$enable_faq);
	
}

$faq_setting=get_option("pheon_woo_app_faq_setting");

$enable_faq=get_option("pheon_woo_app_enable_faq",false);
//echo $enable_faq; die();

?>


<script>

jQuery("document").ready(function($){

	jQuery(".switch-wrapper").switchButton();
});
</script>

<form method="post">
<div id="phoen_main_id">
<br />
<?php wp_nonce_field( 'phoe_app_faq_create_form_action', 'phoe_app_faq_create_form_action_form_nonce_field' ); 
?>
<table class="form-table">
		<tbody>
			<tr valign="top">
            <th scope="row">

                <label for="enable_faq"><?php _e( 'Enable', 'phoen-woo-app' ); ?></label>
				</th>
				
				<td >
				<input type="checkbox" class="switch-wrapper"  <?php echo ($enable_faq)?'checked':''; ?>  name="enable_faq" value="true" />
				</td>
				
			</tr>
			</tbody>
		</table>
<?php
$m=0;
if(is_array($faq_setting) && !empty($faq_setting)){
	
	foreach($faq_setting as $key=>$values){
		?>
			<div class="accordion phoen_number_count">
				<h3>
						<strong class="spjjeoou">Section Name &mdash; </strong>
						<input type="text" class="phoen_section_name" name="category[]"  value="<?php echo $key; ?>" />
						<input type="button" class="phoen_remove_section" value="Remove" />
				</h3>
				<div class="phoen_upper_accordian">
					<?php
						for($i=0;$i<count($values);$i++){
						?>
						<div class="inner_accordion">
							<h3>
								Section
								<input type="button" class="phoen_remove_inner_section" value="Remove" />
							</h3>
							<div class="inner_content">
								<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="banner_urls">Question</label>
										</th>
										<td> 
											<input type="text" value="<?php echo $values[$i]["question"]; ?>" name="question[<?php echo $m;?>][]"/>
										</td>	
									</tr>
									
									<tr valign="top">
										<th scope="row">
											<label for="banner_urls">Answer</label>
										</th>
										<td> 
											<textarea name="answer[<?php echo $m;?>][]"><?php echo $values[$i]["answer"]; ?></textarea>
										</td>	
									</tr>
								</tbody>
							</table>
							</div>
						</div>
						<?php
						}
						
					?>
					<div class="before_new"></div>
					<br />
				<input type="button"  class="phoen_add_new_faq button-primary" data-mum="<?php echo $m;?>" value="<?php _e( 'Add More', 'phoen-woo-app' ); ?>" />

				</div>
			</div>
		<?php
		$m++;
	}
	
}


?>
<div id="before_new_main"></div>

<br />
<input type="button" id="add_new_section" class="button-primary" value="<?php _e( 'Add Section', 'phoen-woo-app' ); ?>" />
<input type="submit" name="create_faq" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
</div>
</form>
  <script>
  jQuery( function($) {
    jQuery( ".accordion,.inner_accordion" ).accordion({
		collapsible: true,
		active: false
	});
	jQuery(".phoen_section_name").click(function(e){
		return false;
	});
	
  });
  
  jQuery("body").on("click",'.phoen_remove_section',function(){
	  jQuery(this).closest('.phoen_number_count').remove();
  });

  jQuery("body").on("click",'.phoen_remove_inner_section',function(){
	  jQuery(this).closest('.inner_accordion').remove();
  });

  jQuery("body").on("click",'.phoen_add_new_faq',function(){
	  
	   var loop = jQuery(this).attr("data-mum");
	   
	    var html=jQuery( "#phoen_main_section_inner" ).html();
		
		html = html.replace( /{loop}/g, loop );
		
		 jQuery(this).closest(".phoen_number_count").find(".before_new").before( html );
		 
		  jQuery( ".inner_accordion1" ).accordion({
			collapsible: true,
			active: false
		});
		 jQuery(".inner_accordion1").accordion("refresh");
  });
  jQuery("body").on("click",'#add_new_section',function(){
	  
	  var loop = jQuery('#phoen_main_id .phoen_section_name').size();
	  
	  var html=jQuery( "#phoen_main_section" ).html();
	  
	  html = html.replace( /{loop}/g, loop );
	  
	  jQuery('#before_new_main').before( html );
  
	  jQuery( ".accordion1,.inner_accordion1" ).accordion({
			collapsible: true,
			active: false
		});
		jQuery(".phoen_section_name").click(function(e){
			return false;
		});
	  
	  jQuery(".accordion1").accordion("refresh");
	  
  });
  </script>	

 <style>
.inner_content,.phoen_upper_accordian{
	height:auto !important;
}

.switch-wrapper {
  display: inline-block;
  position: relative;
  top: 3px;
}
</style>	

<div id="phoen_main_section_inner" style="display:none;">
<div class="inner_accordion1">
	<h3>
		Section
	</h3>
	<div class="inner_content">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="banner_urls">Question</label>
				</th>
				<td> 
					<input type="text" value="" name="question[{loop}][]"/>
				</td>	
			</tr>
			
			<tr valign="top">
				<th scope="row">
					<label for="banner_urls">Answer</label>
				</th>
				<td> 
					<textarea name="answer[{loop}][]"></textarea>
				</td>	
			</tr>
		</tbody>
	</table>
	</div>
</div>
</div>
		

<div id="phoen_main_section" style="display:none;">
<div class="accordion1 phoen_number_count">
	<h3>
			<strong class="spjjeoou">Section Name &mdash; </strong>
			<input type="text" class="phoen_section_name" name="category[]" />
	</h3>
	<div class="phoen_upper_accordian">
		<div class="inner_accordion1">
			<h3>
				Section
			</h3>
			<div class="inner_content">
				<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="banner_urls">Question</label>
						</th>
						<td> 
							<input type="text" value="" name="question[{loop}][]"/>
						</td>	
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<label for="banner_urls">Answer</label>
						</th>
						<td> 
							<textarea name="answer[{loop}][]"></textarea>
						</td>	
					</tr>
				</tbody>
			</table>
			</div>
		</div>
		<div class="before_new"></div>
		<br />
	<input type="button"  class="phoen_add_new_faq button-primary" data-mum="{loop}" value="<?php _e( 'Add More', 'phoen-woo-app' ); ?>" />

	</div>
</div>
</div>