<?php if(! defined('ABSPATH')) exit; // Exit if accessed directly

if (isset($_POST['save']) && check_admin_referer('phoen_app_term_condition_create_form_action', 'phoen_app_term_condition_create_form_action_form_nonce_field')){
  
	$term_condition=isset($_POST['term_condition'])? stripslashes($_POST['term_condition']):'';
	
	$enable_term_condition=isset($_POST['enable_term_condition'])? $_POST['enable_term_condition']:false;

    $final_array=array(
		
		"term_condition"=>trim($term_condition),
		"enable_term_condition"=>trim($enable_term_condition),
	
	);
	
	update_option("phoen_term_condition_setting",$final_array);
}

$getoption=get_option("phoen_term_condition_setting");

$term_condition=isset($getoption['term_condition'])? $getoption['term_condition']:'';

$enable_term_condition=isset($getoption['enable_term_condition'])? $getoption['enable_term_condition']:'';
//wp_editor( $term_condition, "term_condition",array( 'textarea_name' => 'term_condition' ) );
?>

<script>

jQuery("document").ready(function($){

	jQuery(".switch-wrapper").switchButton();
});
</script>

<form method="post">
	<?php wp_nonce_field( 'phoen_app_term_condition_create_form_action', 'phoen_app_term_condition_create_form_action_form_nonce_field' ); ?>

    <table class="form-table">
	    <tbody>

			<tr valign="top">
            <th scope="row">

                <label for="enable_term_condition"><?php _e( 'Enable', 'phoen-woo-app' ); ?></label>
				</th>
				
				<td >
				<input type="checkbox" class="switch-wrapper"  <?php echo (isset($enable_term_condition) && $enable_term_condition==true)?'checked':''; ?>  name="enable_term_condition" value="true" />
				</td>
				
			</tr>

			 <tr valign="top">
		    	<th  scope="row">
		    		<label for="term_condition"><?php _e( 'Terms and Conditions', 'phoen-woo-app' ); ?></label>
		    	</th>		    	
		    </tr>

			

			<tr valign="top">
		    	<td colspan="2"> 
				<?php wp_editor( $term_condition, "term_condition",array( 'textarea_name' => 'term_condition','teeny'=>true,'media_buttons'=>false ) ); ?>
		    		<!-- <textarea rows="5" cols="40" id="term_condition" name="term_condition" ><?php echo $term_condition;?></textarea> -->
		    		<p class="phoen_select_inn   akkaak" style="display:inline-block;">
		    		    <label class="tm-epo-field-label"><i class="fa fa-question-circle tooltip" aria-hidden="true" style="font-size:17px;"></label>
		    		</p>
		    	</td>
		    </tr>
        </tbody>
    </table>
	
        <input type="submit" name="save" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
</form>

<style>
.switch-wrapper {
  display: inline-block;
  position: relative;
  top: 3px;
}
</style>