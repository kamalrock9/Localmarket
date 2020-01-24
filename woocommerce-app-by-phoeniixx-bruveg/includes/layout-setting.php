<?php if(! defined('ABSPATH')) exit; // Exit if accessed directly

		$taxonomy = 'product_cat';
		$orderby ='name';
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

		$taxonomy = 'brands';
		$orderby ='name';
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
		$all_brands = get_categories( $args );
		$phoen_main_brands=array();
		foreach ($all_brands as $cat) {	
			$brand_id=$cat->term_id;	
			$phoen_main_brands[$brand_id]=$cat->name;  	
		}

	$brand_and_category = $phoen_main_catlist + $phoen_main_brands;


if (isset($_POST['create_layout']) && check_admin_referer('phoen_app_layout_create_form_action', 'phoen_app_layout_create_form_action_form_nonce_field')){
	
	$bran_cate=isset($_POST['bran_cate'])? array_values(array_filter($_POST['bran_cate'])):array();

	$top_seller=isset($_POST['top_seller'])? sanitize_text_field($_POST['top_seller']):'';
	
	$brand_seller=isset($_POST['brand_seller'])? $_POST['brand_seller']:null;

	$top_brand_seller=isset($_POST['top_brand_seller'])?$_POST['top_brand_seller']:null;

	$toolbartextcolor=isset($_POST['toolbartextcolor'])? sanitize_text_field($_POST['toolbartextcolor']):'';
	
	$tspnumber=isset($_POST['tspnumber'])? absint($_POST['tspnumber']):'';
	
	$featured_products=isset($_POST['featured_products'])? sanitize_text_field($_POST['featured_products']):'';
	
	$fpnumber=isset($_POST['fpnumber'])? absint($_POST['fpnumber']):'';
	
	$sale_products=isset($_POST['sale_products'])? sanitize_text_field($_POST['sale_products']):'';
	
	$salepnumber=isset($_POST['salepnumber'])? absint($_POST['salepnumber']):'';
	
	$top_rated_products=isset($_POST['top_rated_products'])? sanitize_text_field($_POST['top_rated_products']):'';
	
	$top_rated_pnumber=isset($_POST['top_rated_pnumber'])? absint($_POST['top_rated_pnumber']):'';
	
	$banner_url=isset($_POST['banner_url'])? array_values(array_filter($_POST['banner_url'])):null;

	$banner_category=isset($_POST['banner_category'])? array_values(array_filter($_POST['banner_category'])):null;

	$top_banner_brands=isset($_POST['top_banner_brands'])? array_values(array_filter($_POST['top_banner_brands'])):null;

	$brand_category_banner=isset($_POST['brand_category_banner'])? array_values(array_filter($_POST['brand_category_banner'])):array();

	$front_page_category=isset($_POST['front_page_category'])? array_values(array_filter($_POST['front_page_category'])):null;

	$center_banner_url=isset($_POST['center_banner_url'])? array_values(array_filter($_POST['center_banner_url'])):null;

	$center_banner_category=isset($_POST['center_banner_category'])? array_values(array_filter($_POST['center_banner_category'])):null;

	$center_banner_brands=isset($_POST['center_banner_brands'])? array_values(array_filter($_POST['center_banner_brands'])):null;
	
	$banner=array();
	if($banner_url && $banner_category || $top_banner_brands ){
		for($i=0;$i<count($banner_url);$i++){
			array_push($banner,array(
				"banner_url"=>$banner_url[$i],
				"banner_category"=>$banner_category[$i],
				"top_banner_brands"=>$top_banner_brands[$i],
				"type" => (isset($top_brand_seller[$i])?'category':'brand')
			));
		}

	}

	$bran_and_category=array();
	if($brand_category_banner && $bran_cate && is_array($brand_category_banner)){
		$i=0;
		foreach($brand_category_banner as $key=>$values){
			array_push($bran_and_category,array(
				"bran_cate" =>@$bran_cate[$i],
				"brand_category_banner"=>$values
			));
			$i++;
		}

	}

	//$top_brand_seller_data=array();
	if($banner_category){
		for($i=0;$i<count($banner_category);$i++){
			$top_brand_seller_data[]= (isset($top_brand_seller[$i])?1:0);

		}
	}

	$center_banner=array();
	if($center_banner_url && $center_banner_category || $center_banner_brands){
		for($i=0;$i<count($center_banner_url);$i++){
			array_push($center_banner,array(
				"center_banner_url"=>$center_banner_url[$i],
				"center_banner_category"=>$center_banner_category[$i],
				"center_banner_brands"=>$center_banner_brands[$i],
				"type" => (isset($brand_seller[$i])?'category':'brand')

			));
		}
	}
	//$brand_seller_data=array();
	if($center_banner_category){
		for($i=0;$i<count($center_banner_category);$i++){
			$brand_seller_data[]= (isset($brand_seller[$i])?1:0);
		}
	}

	
    $final_array=array(
		"top_seller"=>($top_seller=="true")?true:false,

		"top_brand_seller"=> $top_brand_seller_data,

		"brand_seller"=>$brand_seller_data,

		"bran_and_category" =>$bran_and_category,

		"tspnumber"=>$tspnumber,

		"featured_products"=>($featured_products=="true")?true:false,

		"fpnumber"=>$fpnumber,

		"sale_products"=>($sale_products=="true")?true:false,

		"salepnumber"=>$salepnumber,

		"top_rated_products"=>($top_rated_products=="true")?true:false,

		"top_rated_pnumber"=>$top_rated_pnumber,

		"banner"=>$banner,

		"front_page_category"=>$front_page_category,

		"center_banner"=>$center_banner,
		
    );

    update_option("phoen_app_layout_setting",$final_array);
}

$phoen_app_layout_setting=get_option("phoen_app_layout_setting");


$top_seller=isset($phoen_app_layout_setting['top_seller'])? $phoen_app_layout_setting['top_seller']:false;

$brand_seller=isset($phoen_app_layout_setting['brand_seller'])? $phoen_app_layout_setting['brand_seller']:false;

$top_brand_seller=isset($phoen_app_layout_setting['top_brand_seller'])? $phoen_app_layout_setting['top_brand_seller']:false;

$tspnumber=isset($phoen_app_layout_setting['tspnumber'])? $phoen_app_layout_setting['tspnumber']:'6';

$featured_products=isset($phoen_app_layout_setting['featured_products'])? $phoen_app_layout_setting['featured_products']:false;

$fpnumber=isset($phoen_app_layout_setting['fpnumber'])? $phoen_app_layout_setting['fpnumber']:'6';

$sale_products=isset($phoen_app_layout_setting['sale_products'])? $phoen_app_layout_setting['sale_products']:false;

$salepnumber=isset($phoen_app_layout_setting['salepnumber'])? $phoen_app_layout_setting['salepnumber']:'6';

$top_rated_products=isset($phoen_app_layout_setting['top_rated_products'])? $phoen_app_layout_setting['top_rated_products']:false;

$top_rated_pnumber=isset($phoen_app_layout_setting['top_rated_pnumber'])? $phoen_app_layout_setting['top_rated_pnumber']:'6';

$toolbartextcolor=isset($phoen_app_layout_setting['toolbartextcolor'])? $phoen_app_layout_setting['toolbartextcolor']:false;

$banner=isset($phoen_app_layout_setting['banner'])? $phoen_app_layout_setting['banner']:array();

$bran_and_category=isset($phoen_app_layout_setting['bran_and_category'])? $phoen_app_layout_setting['bran_and_category']:array();


$bran_cate=isset($phoen_app_layout_setting['bran_cate'])? $phoen_app_layout_setting['bran_cate']:false;

$front_page_category=isset($phoen_app_layout_setting['front_page_category'])? $phoen_app_layout_setting['front_page_category']:array();

$center_banner=isset($phoen_app_layout_setting['center_banner'])? $phoen_app_layout_setting['center_banner']:array();


?>
<style>
.switch-wrapper {
  display: inline-block;
  position: relative;
  top: 3px;

}
</style>

<script>


	jQuery(document).on('change','.brand_and_category_check',function(){
		
		var select_id =jQuery(this).attr('id');
		
		if(jQuery(this).prop('checked')==true){
			
			jQuery('.center_category_brand_'+select_id).hide();
			jQuery('.center_category_'+select_id).show();
		
		}else{
			
			jQuery('.center_category_brand_'+select_id).hide();
			jQuery('.center_brand_'+select_id).show();
			
		}
		
	});

		jQuery(document).on('change','.top_brand_and_category_check',function(){
		
		var top_select_id =jQuery(this).attr('id');
		
		if(jQuery(this).prop('checked')==true){
			
			jQuery('.top_category_brand_'+top_select_id).hide();
			jQuery('.top_category_'+top_select_id).show();
		
		}else{
			
			jQuery('.top_category_brand_'+top_select_id).hide();
			jQuery('.top_brand_'+top_select_id).show();
			
		}
		
	});

	var bran_and_category_index=<?php echo count($bran_and_category);?>;

		jQuery(document).on('click','.brand_category_banner_add',function(){

			
			var brand_category_banner_length = jQuery("#brand_and_category_banner").find("tbody .brand_category_banner_length").length;

			if(brand_category_banner_length<10){
				jQuery("#brand_and_category_banner").find("tbody").append("<tr valign ='top' class='brand_category_banner_length'> <td></td> <td> <input type='text'  class='form-control bran_cate' name='bran_cate[]' value=''/></br><select class='brand_category_banner' name='brand_category_banner[bran_and_category_index][]' multiple > <?php foreach($brand_and_category as $key=>$val){ ?> <option value='<?php echo $key;?>'><?php echo $val;?></option><?php } ?> </select><input type='button' class='brand_category_banner_delete' value='Delete' /> </td> </tr>");
				bran_and_category_index++;						
			}
			else{
				alert("Max banner limit is 10.");
			}
			jQuery('.brand_category_banner').select2();
	
	});

		jQuery(document).on('click',".brand_category_banner_delete",function(){
		
		
		var brand_category_banner_length= jQuery("#brand_and_category_banner").find("tbody .brand_category_banner_length").length;

		if(brand_category_banner_length > 1){
			jQuery(this).closest("tr").remove();
		}else{
			alert("Min limit is 1.");
		}
		
	});




	jQuery(document).on('click','.phoen_add_center_banner',function(){
			
			var center_btn_length= jQuery("#phoen_center_banner").find("tbody .center_btn_length").length;

			if(center_btn_length<10){
				jQuery("#phoen_center_banner").find("tbody").append("<tr valign='top' class='center_btn_length'><td><input type='checkbox' id='"+center_btn_length+"' class='brands-switch-wrapper brand_and_category_check' name='brand_seller["+center_btn_length+"]' value='true'/></td><td ><select class='center_category_brand_"+center_btn_length+" center_banner_category center_category_"+center_btn_length+"' name='center_banner_category[]' style='display:block;'><?php foreach($phoen_main_catlist as $keyq=>$val){ ?><option value='<?php echo $keyq;?>'><?php echo $val;?></option><?php } ?></select><select class='center_category_brand_"+center_btn_length+" center_banner_brands center_brand_"+center_btn_length+"' name='center_banner_brands[]' style='display:none;'><?php foreach($phoen_main_brands as $keyq=>$val){ ?><option value='<?php echo $keyq;?>'><?php echo $val;?></option><?php } ?></select></td><td><input type='text' class='center_banner' required  name='center_banner_url[]' /><input type='button' class='phoen_upload_center_banner'  value='Upload Image' /><input type='button' class='phoen_remove_center_banner' value='-' /></td></tr>");
			}
			else{
				alert("Max banner limit is 10.");
			}
			jQuery(".brands-switch-wrapper").switchButton(option);
	
	});




	jQuery("body").on('click',".phoen_remove_banner",function(){
		
		
		var bannerlength= jQuery("#phoen_banner").find("tbody .bannerlength").length;
		
		if(bannerlength > 3){
			jQuery(this).closest("tr").remove();
		}else{
			alert("Min banner limit is 3.");
		}
	});

	jQuery(document).on('click','.phoen_add_banner',function(){
		
		var bannerlength= jQuery("#phoen_banner").find("tbody .bannerlength").length;

		if(bannerlength<15){
			jQuery("#phoen_banner").find("tbody").append("<tr valign='top' class='bannerlength'><td><input type='checkbox' id='"+bannerlength+"' class='top-brands-switch-wrapper top_brand_and_category_check' name='top_brand_seller["+bannerlength+"]' value='true'/></td><td ><select class='top_category_brand_"+bannerlength+" top_banner_category top_category_"+bannerlength+"' name='banner_category[]' style='display:block;'><?php foreach($phoen_main_catlist as $keyq=>$val){ ?><option value='<?php echo $keyq;?>'><?php echo $val;?></option><?php } ?></select><select class='top_category_brand_"+bannerlength+" top_banner_brands top_brand_"+bannerlength+"' name='top_banner_brands[]' style='display:none;'><?php foreach($phoen_main_brands as $keyq=>$val){ ?><option value='<?php echo $keyq;?>'><?php echo $val;?></option><?php } ?></select></td><td><input type='text' class='banner' required  name='banner_url[]' /><input type='button' class='phoen_upload_banner'  value='Upload Image' /><input type='button' class='phoen_remove_banner' value='-' /></td></tr>");
		}else{									
			alert("Max banner limit is 15.");
		}
		jQuery(".top-brands-switch-wrapper").switchButton(options);
		
	});

	
	jQuery("document").ready(function($){

	jQuery('.front_page_category').select2();

	jQuery('.brand_category_banner').select2();

	jQuery(".switch-wrapper").switchButton();


	option = { on_label: 'Category',
 			off_label: 'Brands',
			}

	jQuery(".brands-switch-wrapper").switchButton(option)
	
	options = { on_label: 'Category',
 			off_label: 'Brands',
			 }
		jQuery(".top-brands-switch-wrapper").switchButton(options)
	
	
	var attach_url_center;
	var custom_uploader_center;

	
		jQuery(document).on("click",".phoen_upload_banner",function(e) {

		input = jQuery(this);
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
			
			if(attachment.filesizeInBytes!==null && attachment.filesizeInBytes > 102400){
				
				alert("File size could not be grater then 100kb.");
				
			}else{
				
				attach_url=attachment.url;
				
				input.closest("td").find(".banner").val(attach_url);
				
			}
			
		});
		
		custom_uploader.open();

	});


		jQuery("body").on('click',".phoen_remove_center_banner",function(){
		
		
		var center_btn_length= jQuery("#phoen_center_banner").find("tbody .center_btn_length").length;
		
		if(center_btn_length > 3){
			jQuery(this).closest("tr").remove();
		}else{
			alert("Min banner limit is 3.");
		}
	});
	


	var attach_center_url;
	var custom_center_uploader;

	
		jQuery(document).on("click",".phoen_upload_center_banner",function(e) {
	
		input = $(this);
		e.preventDefault();

		custom_center_uploader = wp.media.frames.file_frame = wp.media({
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

		custom_center_uploader.on('select', function() {
			
			attachment_center = custom_center_uploader.state().get('selection').first().toJSON();
			
			if(attachment_center.filesizeInBytes!==null && attachment_center.filesizeInBytes > 102400){
				
				alert("File size could not be grater then 100kb.");
				
			}else{
				
				attach_center_url=attachment_center.url;
				
				input.closest("td").find(".center_banner").val(attach_center_url);
				
			}
			
		});
		
		custom_center_uploader.open();

	});

	
});
</script>

<form method="post">

<?php wp_nonce_field('phoen_app_layout_create_form_action', 'phoen_app_layout_create_form_action_form_nonce_field'); ?>

<table class="form-table" id="phoen_banner">
  <tbody>
	<tr valign="top">

		<td colspan="2">
			<label for="top_banner"><?php _e( 'Category/Brands', 'phoen-woo-app' ); ?></label>
		</td>

		<td>
			<label for="banner_image"><?php _e( 'Banner Image', 'phoen-woo-app' ); ?></label>
		</td>	
	</tr>
		<?php 
			if(!empty($banner)){
				
				foreach($banner as $key=>$values){
		
		?>
		<tr valign="top" class="bannerlength">
			<td >
				<input type="checkbox" class="top-brands-switch-wrapper top_brand_and_category_check" id="<?php echo $key;?>" <?php echo (isset($top_brand_seller[$key]) && $top_brand_seller[$key]==1)?'checked':''; ?>  name="top_brand_seller[<?php echo $key;?>]" value="1" />

				<td> 
					<select class="top_category_brand_<?php echo $key;?> top_banner_category top_category_<?php echo $key;?>" name="banner_category[]" style="display:<?php echo (isset($top_brand_seller[$key]) && $top_brand_seller[$key]==1)?'block':'none';?>">
					<?php 
						foreach($phoen_main_catlist as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['banner_category'] && $values['banner_category']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>

					<select class="top_category_brand_<?php echo $key;?> top_banner_brands top_brand_<?php echo $key;?>" name="top_banner_brands[]" style="display:<?php echo (isset($top_brand_seller[$key]) && $top_brand_seller[$key]==0)?'block':'none';?>">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){


					?>
						<option value="<?php echo $keyq;?>" <?php if($values['top_banner_brands'] && $values['top_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>
			</td> 
				<td>
					<input type="text" class="banner"  name="banner_url[]" required value="<?php echo $values['banner_url']?$values['banner_url']:'';?>"/>
					<input type="button" class="phoen_upload_banner" value="Upload Image" />		
					<input type="button" class="phoen_remove_banner" value="-" />
				</td>	
			</tr>
				<?php
				}
					
			}else{
				?>
				<tr valign="top" class="bannerlength">
					<th scope="row">
							
					</th>
					<td> 
						<select class="banner_category" name="banner_category[]">
						<?php 
							foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
						</select>


					<select class="top_banner_brands" name="top_banner_brands[]">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['top_banner_brands'] && $values['top_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>
					<td> 
						<input type="text" class="banner" required  name="banner_url[]" />
						<input type="button" class="phoen_upload_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_banner" value="-" />
					</td>	
				</tr>
				<tr valign="top" class="bannerlength">
					<th scope="row">
					
					</th>
				<td> 
						<select class="banner_category" name="banner_category[]">
						<?php 
							foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
						</select>


					<select class="top_banner_brands" name="top_banner_brands[]">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['top_banner_brands'] && $values['top_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>	
					<td> 
						<input type="text" class="banner"  required name="banner_url[]" />
						<input type="button" class="phoen_upload_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_banner" value="-" />
					</td>	
				</tr>

				<tr valign="top" class="bannerlength">
					<th scope="row">
						
					</th>
					<td> 
						<select class="banner_category" name="banner_category[]">
						<?php 
							foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
					?>
					</select>
					<select class="top_banner_brands" name="top_banner_brands[]">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['top_banner_brands'] && $values['top_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>	
					<td> 
						<input type="text" class="banner" required  name="banner_url[]" />
						<input type="button" class="phoen_upload_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_banner" value="-" />
					</td>	
				</tr>
			
				<?php
				}
			?>
			
		</tbody>
		<tfoot>
			<tr valign="top">
				<td>
					<input type="button" class="phoen_add_banner" value="+" />
				</td>
			</tr>
		</tfoot>

	</table>

<table class="form-table">
    <tbody>

		<tr valign="top">
            <th scope="row">

                <label for="front_page_category"><?php _e( 'Front Page Category', 'phoen-woo-app' ); ?></label>
			</th>
				<td> 
					<select class="front_page_category" name="front_page_category[]" multiple>
					<?php 
						foreach($phoen_main_catlist as $key=>$val){
					?>
						<option value="<?php echo $key;?>" <?php if(is_array($front_page_category) && in_array($key,$front_page_category)){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>
			
		</tr>	

	</tbody>
	
</table>	


<table class="form-table" id="phoen_center_banner"> 

	<tbody>
		<tr valign="top">

			<td colspan="2">
				<label for="center_banner"><?php _e( 'Category/Brands', 'phoen-woo-app' ); ?></label>
			</td>

			<td>
				<label for="banner_image"><?php _e( 'Banner Image', 'phoen-woo-app' ); ?></label>
			</td>	
		</tr>
		<?php 
			if(!empty($center_banner)){
				
				foreach($center_banner as $key=>$values){
		?>
		<tr valign="top" class="center_btn_length">

			<td>
				<input type="checkbox" class="brands-switch-wrapper brand_and_category_check" id="<?php echo $key;?>" <?php echo (isset($brand_seller[$key]) && $brand_seller[$key]==1)?'checked':''; ?>  name="brand_seller[<?php echo $key;?>]" value="1" />
				
				<td> 
					<select class="center_category_brand_<?php echo $key;?> center_banner_category center_category_<?php echo $key;?>"  name="center_banner_category[]"  id="" style="display:<?php echo (isset($brand_seller[$key]) && $brand_seller[$key]==1)?'block':'none';?>">
					<?php 
						foreach($phoen_main_catlist as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['center_banner_category'] && $values['center_banner_category']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				
					<select class="center_category_brand_<?php echo $key;?> center_banner_brands center_brand_<?php echo $key;?>"   name="center_banner_brands[]" id=""  style="display:<?php echo (isset($brand_seller[$key]) && $brand_seller[$key]==0)?'block':'none';?>">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['center_banner_brands'] && $values['center_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>
			</td> 

				<td>
					<input type="text" class="center_banner"  name="center_banner_url[]" required value="<?php echo $values['center_banner_url']?$values['center_banner_url']:'';?>"/>
					<input type="button" class="phoen_upload_center_banner" value="Upload Image" />		
					<input type="button" class="phoen_remove_center_banner" value="-" />
				</td>	
		</tr>
				<?php
				}
					
			}else{
				?>
				<tr valign="top" class="center_btn_length">
					<th scope="row">
							
					</th>
					<td> 
						<select class="center_banner_category" name="center_banner_category[]" id="">
						<?php 
							foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
						</select>


					<select class="center_banner_brands" name="center_banner_brands[]" id="">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['center_banner_brands'] && $values['center_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>	
					<td> 
						<input type="text" class="center_banner" required  name="center_banner_url[]" />
						<input type="button" class="phoen_upload_center_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_center_banner" value="-" />
					</td>	
				</tr>
				<tr valign="top" class="center_btn_length">
					<th scope="row">
					
					</th>
					<td> 
						<select class="center_banner_category" name="center_banner_category[]" id="">
						<?php 
  								foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
						</select>


					<select class="center_banner_brands" name="center_banner_brands[]" id="">
					<?php 
						foreach($phoen_main_brands as $keyq=>$val){
					?>
						<option value="<?php echo $keyq;?>" <?php if($values['center_banner_brands'] && $values['center_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
						<?php
						}
						?>
					</select>
				</td>
					<td> 
						<input type="text" class="center_banner"  required name="center_banner_url[]" />
						<input type="button" class="phoen_upload_center_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_center_banner" value="-" />
					</td>	
				</tr>				
				

				<tr valign="top" class="center_btn_length">
					<th scope="row">
						
					</th>
					<td> 
						<select class="center_banner_category" name="center_banner_category[]" id="">
						<?php 
							foreach($phoen_main_catlist as $keyq=>$val){
						?>
							<option value="<?php echo $keyq;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
						</select>


					    <select class="center_banner_brands" name="center_banner_brands[]" id="">
					    <?php 
					    	foreach($phoen_main_brands as $keyq=>$val){
					    ?>
					    	<option value="<?php echo $keyq;?>" <?php if($values['center_banner_brands'] && $values['center_banner_brands']==$keyq){ echo 'selected';} ?>> <?php echo $val;?></option>
					    	<?php
					    	}
					    	?>
					    </select>
					</td>
				
					<td> 
						<input type="text" class="center_banner" required  name="center_banner_url[]" />
						<input type="button" class="phoen_upload_center_banner" value="Upload Image" />
						<input type="button" class="phoen_remove_center_banner" value="-" />
					</td>
					
			</tr>
					
				<?php
				}
			?>

			<tfoot>
			    <tr valign="top">
			       	<td>
			       		<input type="button" class="phoen_add_center_banner" value="+" />
			       	</td>
				 </tr>
					   
				
		    </tfoot>
			
		</tbody>

	</table> 

<table class="form-table" id="brand_and_category_banner">
    <tbody>

	<?php 	
	

	if(!empty($bran_and_category)){
			$bran_and_category_index=0;
			foreach($bran_and_category as $key=>$values){
		
			?>
	<tr valign ="top" class="brand_category_banner_length">			
            <th scope="row">

                <label for="brand_category_banner"><?php _e( 'Brand Category Banner', 'phoen-woo-app' ); ?></label>
			</th>

			<td> 
				<input type="text"  class="form-control bran_cate" name="bran_cate[]" value="<?php echo $values['bran_cate'];?>"/>
	
			<br/>
				<select class="brand_category_banner" name="brand_category_banner[<?php echo $bran_and_category_index;?>][];" multiple>
					<?php 
						foreach($brand_and_category as $key=>$val){
							
					?>
						<option value="<?php echo $key;?>" <?php if(is_array($values['brand_category_banner'] ) && in_array($key,$values['brand_category_banner'])){ echo 'selected';} ?> > <?php echo $val;?></option>
						<?php
						}
						?>
				</select>
			<input type="button" class="brand_category_banner_delete" value="<?php _e( 'Delete', 'phoen-woo-app' ); ?>" />

			</td>

					
	</tr>
	<?php
		$bran_and_category_index++;
			} 
		}				
			else{
		?>

			<tr valign ="top" class="brand_category_banner_length">
					
				<th scope="row">
	
					<label for="brand_category_banner"><?php _e( 'Brand Category Banner', 'phoen-woo-app' ); ?></label>
				</th>
	
				<td> 
					<input type="text"  class="form-control bran_cate" name="bran_cate[]" value="<?php echo $values['bran_cate'];?>"/>
		
				<br/>
					<select class="brand_category_banner" name="brand_category_banner[0][]" multiple>
						<?php 
							foreach($brand_and_category as $key=>$val){
								
						?>
							<option value="<?php echo $key;?>"> <?php echo $val;?></option>
							<?php
							}
							?>
					</select>
				<!-- <input type="button" class="brand_category_banner_delete" value="<?php _e( 'Delete', 'phoen-woo-app' ); ?>" /> -->
	
				</td>
	
						
		</tr>
		<?php
			}
			?>
		 <tfoot>
			    <tr valign="top">
					<td>
					</td>	
			       	<td>
			       		<input type="button" class="brand_category_banner_add" value="<?php _e( 'Add More', 'phoen-woo-app' ); ?>" />
			    	</td>
				</tr>
					   
				
		</tfoot> 
			

	</tbody>
	
</table>

	<table class="form-table" id=""> 

        <tr valign="top">

            <th scope="row">

                <label for="top_seller"><?php _e( 'Top seller', 'phoen-woo-app' ); ?></label>
				</th>
				
				<td >
				  <input type="checkbox" class="switch-wrapper"  <?php echo (isset($top_seller) && $top_seller==true)?'checked':''; ?>  name="top_seller" value="true" />
				</td>
				
		</tr>
		
		<tr valign="top">
			<th scope="row">
		
				<label for="tspnumber"><?php _e( 'Top seller product number', 'phoen-woo-app' ); ?></label>
			</th>
				
			<td> 
				<input type="number" min="5" max="20" id="tspnumber"  name="tspnumber" value="<?php echo $tspnumber;?>"/>
			</td>	
		</tr>
			
		<tr valign="top">
			<th scope="row">
		
				<label for="features"><?php _e( 'Featured Products', 'phoen-woo-app' ); ?></label>
			</th>
		
			<td> 
				<input type="checkbox" class="switch-wrapper" id="top_seller"  <?php echo (isset($featured_products) && $featured_products==true) ?'checked':''; ?> name="featured_products" value="true"/>
																				
			</td>	
		</tr>
			
		<tr valign="top">
			<th scope="row">
		
				<label for="fpnumber"><?php _e( 'Featured product number', 'phoen-woo-app' ); ?></label>
			</th>
			<td> 
				<input type="number" min="5" max="20" id="fpnumber"  name="fpnumber" value="<?php echo $fpnumber;?>"/>
			</td>	
		</tr>
			
		<tr valign="top">
			<th scope="row">
		
				<label for="sale_products"><?php _e( 'Sale Products', 'phoen-woo-app' ); ?></label>
			</th>
			<td> 
				<input type="checkbox" class="switch-wrapper" id="sale_products" <?php echo (isset($sale_products) && $sale_products==true) ? 'checked':''; ?>  name="sale_products" value="true"/>
																				 
			</td>	
		</tr>
			
		<tr valign="top">
			<th scope="row">
		
				<label for="salepnumber"><?php _e( 'Sale product show count', 'phoen-woo-app' ); ?></label>
			</th>
			<td> 
				<input type="number" min="5" max="20" id="salepnumber"  name="salepnumber" value="<?php echo $salepnumber;?>"/>
			</td>	
		</tr>
		
		<tr valign="top">
			<th scope="row">
		
				<label for="sale_products"><?php _e( 'Top Rated Product', 'phoen-woo-app' ); ?></label>
			</th>
			<td> 
				<input type="checkbox" class="switch-wrapper" class="top_rated_products" <?php echo (isset($top_rated_products) && $top_rated_products==true) ? 'checked':''; ?> name="top_rated_products" value="true"/>
																						 											
			</td>			
		</tr>
		
		<tr valign="top">
			<th scope="row">
		
				<label for="top_rated_pnumber"><?php _e( 'Top Rated number', 'phoen-woo-app' ); ?></label>
			</th>
			<td> 
				<input type="number" min="5" max="20" id="top_rated_pnumber"  name="top_rated_pnumber" value="<?php echo $top_rated_pnumber;?>"/>
			</td>	
		</tr>
			
		</tbody>
	
	</table>

    <input type="submit" name="create_layout" class="button-primary" value="<?php _e( 'Save', 'phoen-woo-app' ); ?>" />
	
    </form>
<style>
	.form-table th{ padding: 20px 10px 20px 20px;}
	.form-table {background: #fff none repeat scroll 0 0;}
	.form-table td {  padding: 15px 100px;}
	.button-primary{margin-top: 15px !important;}
</style>