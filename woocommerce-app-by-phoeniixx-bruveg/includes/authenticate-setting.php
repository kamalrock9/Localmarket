<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( isset( $_POST['create_setting'] ) && check_admin_referer( 'phoe_app_create_form_action', 'phoe_app_create_form_action_form_nonce_field' ) ) {
	
	$consumer_key=isset($_POST['consumer_key'])? sanitize_text_field($_POST['consumer_key']):'';
	
	$consumer_secret=isset($_POST['consumer_secret'])? sanitize_text_field($_POST['consumer_secret']):'';

	$contact_email=isset($_POST['contact_email'])? sanitize_text_field($_POST['contact_email']):'';

	$contact_phone=isset($_POST['contact_phone'])? sanitize_text_field($_POST['contact_phone']):'';
	
	$direct_tawk_id=isset($_POST['direct_tawk_id'])? sanitize_text_field($_POST['direct_tawk_id']):'';
	
	$google_tracker_id=isset($_POST['google_tracker_id'])? sanitize_text_field($_POST['google_tracker_id']):'';

	$one_signal_app_id=isset($_POST['one_signal_app_id'])? sanitize_text_field($_POST['one_signal_app_id']):'';

	$google_project_number=isset($_POST['google_project_number'])? sanitize_text_field($_POST['google_project_number']):'';
	
	$final_array=array(
		
		"consumer_key"=>trim($consumer_key),
		"consumer_secret"=>trim($consumer_secret),
		"contact_email"=>trim($contact_email),
		"contact_phone"=>trim($contact_phone),
		"google_analytics_tracker_id"=>trim($google_tracker_id),
		"direct_tawk_id"=>trim($direct_tawk_id),
		"one_signal_app_id"=>trim($one_signal_app_id),
		"google_project_number"=>trim($google_project_number),
		
	);
	
	update_option("phoen_authenticate_setting",$final_array);
}

$getoption=get_option("phoen_authenticate_setting");

$consumer_key=isset($getoption['consumer_key'])? $getoption['consumer_key']:'';

$consumer_secret=isset($getoption['consumer_secret'])?$getoption['consumer_secret']:'';

$contact_email=isset($getoption['contact_email'])?$getoption['contact_email']:'';

$contact_phone=isset($getoption['contact_phone'])?$getoption['contact_phone']:'';

$direct_tawk_id=isset($getoption['direct_tawk_id'])?$getoption['direct_tawk_id']:'';

$google_tracker_id=isset($getoption['google_analytics_tracker_id'])?$getoption['google_analytics_tracker_id']:'';

$one_signal_app_id=isset($getoption['one_signal_app_id'])?$getoption['one_signal_app_id']:'';

$google_project_number=isset($getoption['google_project_number'])?$getoption['google_project_number']:'';

?>
	<form method="post">
		<?php wp_nonce_field( 'phoe_app_create_form_action', 'phoe_app_create_form_action_form_nonce_field' ); ?>
		
		<table class="form-table">
			<tbody>
				
				<tr valign="top">
					<th scope="row">
						<label for="consumer_key"><?php _e( 'Woocommerce API Consumer Key', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="consumer_key" value="<?php echo $consumer_key;?>" name="consumer_key" required />
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
								<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a new Woocommerce API Consumer Key.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>
			
				<tr valign="top">
					<th scope="row">
						<label for="consumer_secret"><?php _e( 'Woocommerce API Consumer Secret Key', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="consumer_secret" value="<?php echo $consumer_secret;?>" name="consumer_secret" required/>
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a new Woocommerce API Consumer Secret.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="contact_email"><?php _e( 'Contact Email', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="contact_email" value="<?php echo $contact_email;?>" name="contact_email">
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a new Contact Email.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="contact_phone"><?php _e( 'Contact Phone', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="contact_phone" value="<?php echo $contact_phone;?>" name="contact_phone"> 
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext"><span class="tooltiptext">Create a new Contact Phone.</span> </i>&nbsp;</label></span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="direct_tawk_id"><?php _e( 'Direct Chat Tawk To URL (Optional)', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="direct_tawk_id" value="<?php echo $direct_tawk_id;?>" name="direct_tawk_id" />
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a Tawk To Direct Chat url from tawk.to admin.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="google_tracker_id"><?php _e( 'GOOGLE ANALYTICS TRACKER ID (Optional)', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="google_tracker_id" value="<?php echo $google_tracker_id;?>" name="google_tracker_id" />
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a Google ANALYTICS Tracker ID.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="one_signal_app_id"><?php _e( 'One Signal App Id (Optional)', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="one_signal_app_id" value="<?php echo $one_signal_app_id;?>" name="one_signal_app_id" />
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a One Signal App Id.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="google_project_number"><?php _e( 'Google Project Number (Optional)', 'phoen-woo-app' ); ?></label>
					</th>
					<td> 
							<input type="text" id="google_project_number" value="<?php echo $google_project_number;?>" name="google_project_number" />
							<p class="phoen_select_inn   akkaak" style="display:inline-block;">
							<label class="tm-epo-field-label"><i class="dashicons dashicons-editor-help tooltip" aria-hidden="true" style="font-size:20px;"><span class="tooltiptext">Create a One Signal App Id.</span> </i>&nbsp;</label>
							</p>
					</td>
				</tr>

			</tbody>
		
		</table>
		<input type="submit" name="create_setting" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
	</form>

<style>

	.form-table th{ padding: 20px 10px 20px 20px;}
.form-table {background: #fff none repeat scroll 0 0;}
.form-table td {  padding: 15px 100px;}
.button-primary{margin-top: 15px !important;}

.tooltip {
	position: relative;
	display: inline-block;
}


.tooltip:hover .tooltiptext {
    visibility: visible;
}

.tooltiptext::after {
    border-color: transparent #444 transparent transparent;
    border-style: solid;
    border-width: 8px;
    content: "";
    position: absolute;
    right: 44%;
    top: -15px;
    opacity: 1;
    transform: rotate(90deg);
}

.tooltip .tooltiptext {

    visibility: hidden;
    padding: 5px 5px 5px 5px;
    color: #fff;
    bor position: absolute;
    z-index: 1;
    position: absolute;
    top: 100%;
    margin-top: 9px;
   left: -50px;
	height:auto;
	color: #fff;
	font-size: .8em;
	max-width: 150px;
	background: #333;
	text-align: center;
	border-radius: 3px;
	padding: .618em 1em;
	box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
</style>