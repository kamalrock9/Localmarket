<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if (isset($_POST['refer_earn_setting']) && check_admin_referer('phoe_app_refer_earn_create_form_action', 'phoe_app_refer_earn_create_form_action_form_nonce_field')){
	
	$refer_earn_on=isset($_POST['refer_earn_on'])? sanitize_text_field($_POST['refer_earn_on']):'';
	
	$refer_earn_msg=isset($_POST['refer_earn_msg'])? sanitize_text_field($_POST['refer_earn_msg']):'';
	
	$refer_earn_uses=isset($_POST['refer_earn_uses'])? absint($_POST['refer_earn_uses']):'';
	
	$refer_earner_amt=isset($_POST['refer_earner_amt'])? absint($_POST['refer_earner_amt']):'';
	$refer_referrer_amt=isset($_POST['refer_referrer_amt'])? absint($_POST['refer_referrer_amt']):'';
	$refer_earn_banner_url=isset($_POST['refer_earn_banner_url'])? sanitize_text_field($_POST['refer_earn_banner_url']):'';
	
	$final_array=array(
        "refer_earn_on"=>($refer_earn_on=="true")?true:false,
		"refer_earn_msg"=>$refer_earn_msg,
		"refer_earner_amt"=>$refer_earner_amt,
		"refer_referrer_amt"=>$refer_referrer_amt,
		"refer_earn_uses"=>$refer_earn_uses,
		"refer_earn_banner_url"=>$refer_earn_banner_url,
		
    );
    update_option("phoen_app_refer_earn_layout_setting",$final_array);
}
$form_data = is_array(get_option("phoen_app_refer_earn_layout_setting"))?get_option("phoen_app_refer_earn_layout_setting"):array();
extract($form_data);
//$refer_earn_banner_url=isset($phoen_app_refer_earn_layout_setting['refer_earn_banner_url'])? $phoen_app_refer_earn_layout_setting['refer_earn_banner_url']:array();
?>

<style>
.switch-wrapper {
  display: inline-block;
  position: relative;
  top: 3px;
}
</style>

<script>

jQuery("document").ready(function($){


jQuery(".switch-wrapper").switchButton();



var attach_url;
var custom_uploader;


	jQuery(document).on("click",".phoen_upload_banner",function(e) {
	// alert(951);
	input = $(this);
	e.preventDefault();

	custom_uploader = wp.media.frames.file_frame = wp.media({
		title: 'Choose Collage Image',
		library: {
			type: 'image'
		},
		button: {
			text: 'Choose Collage Image'
		},
		multiple: false,

		displaySettings: true,

		displayUserSettings: false
	});

	custom_uploader.on('select', function() {
		
		attachment = custom_uploader.state().get('selection').first().toJSON();
		
		if(attachment.filesizeInBytes!==null && attachment.filesizeInBytes > 100000){
			
			alert("File size could not be grater then 100kb.");
			
		}else{
			
			attach_url=attachment.url;
			
			input.closest("td").find(".banner").val(attach_url);
			print_r(attach_url);
		}
		
	});
	
	custom_uploader.open();

});


});
</script>


<form method="post">
		<?php wp_nonce_field( 'phoe_app_refer_earn_create_form_action', 'phoe_app_refer_earn_create_form_action_form_nonce_field' ); ?>
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="refer_earn_on"><?php _e( 'Enable', 'phoen-woo-app' ); ?></label>
					</th>
					
					<td >
						<input type="checkbox" class="switch-wrapper"  <?php echo (isset($refer_earn_on) && $refer_earn_on==true)?'checked':''; ?>  name="refer_earn_on" value="true" />
					</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="banner"><?php _e( 'Banner', 'phoen-woo-app' ); ?></label>
				</th>
				
				<td>
					<input type="text" class="banner"  name="refer_earn_banner_url" value="<?php echo $refer_earn_banner_url ? $refer_earn_banner_url:'';?>"/>
					<input type="button" class="button phoen_upload_banner" value="Upload Image" />
								
				</td>	
				</tr>
			
					
				</tr>
						

				<tr valign="top">
					<th scope="row">
						<label for="refer_earn_msg"><?php _e( 'Refer And Earn Message', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<textarea name="refer_earn_msg" id="refer_earn_msg" cols="50" rows="5" ><?php echo $refer_earn_msg;?></textarea>
							<span>{referralcode} {referralamount} for referal code and referal amount Don't delete it.</span>
							
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="refer_referrer_amt"><?php _e( 'Referrer Amount', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="number" id="refer_referrer_amt" min="0" value="<?php echo $refer_referrer_amt;?>" name="refer_referrer_amt" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="refer_earner_amt"><?php _e( 'Earner Amount', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="number" id="refer_earner_amt" min="0" value="<?php echo $refer_earner_amt;?>" name="refer_earner_amt" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="refer_earn_uses"><?php _e( 'Refer And Earn Uses', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="number" id="refer_earn_uses" min="0" value="<?php echo $refer_earn_uses;?>" name="refer_earn_uses" />
					</td>
				</tr>
				

				
						
						
				
			</tbody>
		
		</table>
		<input type="submit" name="refer_earn_setting" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
	</form>
<style>
	.form-table th{ padding: 20px 10px 20px 20px;}
	.form-table {background: #fff none repeat scroll 0 0;}
	.form-table td {  padding: 15px 100px;}
	.button-primary{margin-top: 15px !important;}

</style>