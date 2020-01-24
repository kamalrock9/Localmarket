<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$taxonomy     = 'product_cat';	
$orderby      = 'name';  
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no  	
$title        = '';  	
$empty        = 0;
$args = array(
		'taxonomy'     => $taxonomy,
		'orderby'      => $orderby,
		'show_count'   => $show_count,
		'pad_counts'   => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li'     => $title,
		'hide_empty'   => $empty
	);
$all_categories = get_categories( $args );
$phoen_main_catlist=array();
foreach ($all_categories as $cat) {	
	$term_id=$cat->term_id;	
	$phoen_main_catlist[$term_id]=$cat->name;  	
}

if ( isset( $_POST['create_setting'] ) && check_admin_referer( 'phoe_app_style_create_form_action', 'phoe_app_style_create_form_action_form_nonce_field' ) ) {
	
	$secondary_text_color=isset($_POST['secondary_text_color'])? sanitize_text_field($_POST['secondary_text_color']):'';
	
	$primary_text_color=isset($_POST['primary_text_color'])? sanitize_text_field($_POST['primary_text_color']):'';
	
	$accent_color=isset($_POST['accent_color'])? sanitize_text_field($_POST['accent_color']):'';
	
	$primary_color_text=isset($_POST['primary_color_text'])? sanitize_text_field($_POST['primary_color_text']):'';
	
	$toolbarbadgecolor=isset($_POST['toolbarbadgecolor'])? sanitize_text_field($_POST['toolbarbadgecolor']):'';
	
	$primary_color_light=isset($_POST['primary_color_light'])? sanitize_text_field($_POST['primary_color_light']):'';
	
	$primary_color_dark=isset($_POST['primary_color_dark'])? sanitize_text_field($_POST['primary_color_dark']):'';
	
	$primary_color=isset($_POST['primary_color'])? sanitize_text_field($_POST['primary_color']):'';
	

	$final_array=array(
		"secondary_text_color"=>$secondary_text_color,
		"primary_text_color"=>$primary_text_color,
		"accent_color"=>$accent_color,
		"primary_color_text"=>$primary_color_text,
		"toolbarbadgecolor"=>$toolbarbadgecolor,
		"primary_color_light"=>$primary_color_light,
		"primary_color_dark"=>$primary_color_dark,
		"primary_color"=>$primary_color
	);
	
	update_option("phoen_app_styling_setting",$final_array);
}
$phoen_app_styling_setting=get_option("phoen_app_styling_setting");

$secondary_text_color=isset($phoen_app_styling_setting['secondary_text_color'])? $phoen_app_styling_setting['secondary_text_color']:'';
	
$primary_text_color=isset($phoen_app_styling_setting['primary_text_color'])?$phoen_app_styling_setting['primary_text_color']:'';

$accent_color=isset($phoen_app_styling_setting['accent_color'])? $phoen_app_styling_setting['accent_color']:'';

$primary_color_text=isset($phoen_app_styling_setting['primary_color_text'])?$phoen_app_styling_setting['primary_color_text']:'';

$toolbarbadgecolor=isset($phoen_app_styling_setting['toolbarbadgecolor'])?$phoen_app_styling_setting['toolbarbadgecolor']:'';

$primary_color_light=isset($phoen_app_styling_setting['primary_color_light'])?$phoen_app_styling_setting['primary_color_light']:'';

$primary_color_dark=isset($phoen_app_styling_setting['primary_color_dark'])?$phoen_app_styling_setting['primary_color_dark']:'';

$primary_color=isset($phoen_app_styling_setting['primary_color'])?$phoen_app_styling_setting['primary_color']:'';


?>
<script> 
	jQuery("document").ready(function($){
	
		jQuery("#secondary_text_color,#primary_text_color,#toolbarbadgecolor,#accent_color,#primary_color_text,#primary_color_light,#primary_color_dark,#primary_color").wpColorPicker();
	
		jQuery("body").on('click',".phoen_remove_banner",function(){
			
			
			var bannerlength= jQuery("#phoen_banner").find("tbody .bannerlength").length;
			
			if(bannerlength > 3){
				jQuery(this).closest("tr").remove();
			}else{
				alert("Min banner limit is 3.");
			}
		});
		jQuery(".phoen_add_banner").click(function(){
			
			var bannerlength= jQuery("#phoen_banner").find("tbody .bannerlength").length;
			
			if(bannerlength<6){
				jQuery("#phoen_banner").find("tbody").append("<tr valign='top' class='bannerlength'><th scope='row'></th><td><input type='text' class='banner_urls' required  name='banner_urls[]' /><input type='button' class='button phoen_upload_banner'  value='Upload Image' /><input type='button' class='phoen_remove_banner' value='-' /></td></tr>");
			}else{
				alert("Max banner limit is 6.");
			}
			
		});
		
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
				
				if(attachment.filesizeInBytes!==null && attachment.filesizeInBytes > 35000){
					
					alert("File size could not be grater then 35kb.");
					
				}else{
					
					attach_url=attachment.url;
					
					input.closest("td").find(".banner_urls").val(attach_url);
					
				}
				
			});
			
			custom_uploader.open();

		});

		jQuery('.banner_category').select2();
		
	});
</script>
<form method="post">

	<?php wp_nonce_field( 'phoe_app_style_create_form_action', 'phoe_app_style_create_form_action_form_nonce_field' ); ?>
		
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="primary_color"><?php _e( 'Primary color', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
					<input type="text" id="primary_color"  name="primary_color" value="<?php echo $primary_color;?>"/>
				</td>	
			</tr>
			 
			<tr valign="top">
				<th scope="row">
					<label for="primary_color_dark"><?php _e( 'Primary Color Dark', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
					<input type="text" id="primary_color_dark"  name="primary_color_dark" value="<?php echo $primary_color_dark;?>"/>
				</td>	
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="toolbarbadgecolor"><?php _e( 'Toolbar Badge Color', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
					<input type="text" id="toolbarbadgecolor"  name="toolbarbadgecolor" value="<?php echo $toolbarbadgecolor;?>"/>
				</td>	
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="primary_color_text"><?php _e( 'Primary Color Text (Toolbar)', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
					<input type="text" id="primary_color_text"  name="primary_color_text" value="<?php echo $primary_color_text;?>"/>
				</td>	
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="accent_color"><?php _e( 'Accent Color', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
					<input type="text" id="accent_color"  name="accent_color" value="<?php echo $accent_color;?>"/>
				</td>	
			</tr>
			</tbody>
			
			</table>	
			
			
	<!-- <table class="form-table" id="phoen_banner">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="banner_urls"><?php //_e( 'Banner Image', 'phoen-woo-app' ); ?></label>
				</th>
				<td> 
				</td>	
			</tr>
			<?php 
				//if(!empty($banner_urls)){
					
					//foreach($banner_urls as $values){
						?>
						<tr valign="top" class="bannerlength">
							<th scope="row">
								
							</th>
							<td> 
								<input type="text" class="banner_urls"  name="banner_urls[]" required value="<?php echo $values;?>"/>
								<input type="button" class="button phoen_upload_banner" value="Upload Image" />
								
								<input type="button" class="phoen_remove_banner" value="-" />
							</td>	
						</tr>
						<?php
					//}
					
				//}else{
					?>
					<tr valign="top" class="bannerlength">
						<th scope="row">
							
						</th>
						<td> 
							<input type="text" class="banner_urls" required  name="banner_urls[]" />
							<input type="button" class="button phoen_upload_banner" value="Upload Image" />
							<input type="button" class="phoen_remove_banner" value="-" />
						</td>	
					</tr>
					<tr valign="top" class="bannerlength">
						<th scope="row">
							
						</th>
						<td> 
							<input type="text" class="banner_urls"  required name="banner_urls[]" />
							<input type="button" class="button phoen_upload_banner" value="Upload Image" />
							<input type="button" class="phoen_remove_banner" value="-" />
						</td>	
					</tr>
					<tr valign="top" class="bannerlength">
						<th scope="row">
							
						</th>
						<td> 
							<input type="text" class="banner_urls" required  name="banner_urls[]" />
							<input type="button" class="button phoen_upload_banner" value="Upload Image" />
							<input type="button" class="phoen_remove_banner" value="-" />
						</td>	
					</tr>
					
					<?php
				//}
			?>
			
		</tbody>
		<tfoot>
			<tr valign="top">
				<td>
					<input type="button" class="phoen_add_banner" value="+" />
					
				</td>
			</tr>
		</tfoot>

	</table> -->
		

	<input type="submit" name="create_setting" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
	
</form>
<style>
	.form-table th{ padding: 20px 10px 20px 20px;}
.form-table {background: #fff none repeat scroll 0 0;}
.form-table td {  padding: 15px 100px;}
.button-primary{margin-top: 15px !important;}

</style>