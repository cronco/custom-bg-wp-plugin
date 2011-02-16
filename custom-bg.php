<?php
/**
 * custom-bg.php
 *
 * Plugin Name:Custom Backgrounds
 * Plugin Description: Allows users to choose a custom background for every post,page, or category. or use the default background;
 * Author: Mihai Chereji
 * Version: 0.0.1
 * since Wed 29 Dec 2010 02:12:11 AM 
 */
 
 add_action('admin_menu','custom_bg_add_menu');
 add_action('add_meta_boxes','custom_bg_meta_box');
 add_action('save_post','custom_bg_save_data');
 add_action('admin_init','custom_bg_admin_scripts');
 
function custom_bg_admin_scripts() 
{
	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-effects-core", "http://jquery-ui.googlecode.com/svn/tags/1.8.1/ui/jquery.effects.core.js");
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style("thickbox");
	wp_enqueue_style('custom_bg',WP_PLUGIN_URL . '/custom-bg/custom-bg.css');
	//wp_register_script('custom-bg-default', WP_PLUGIN_URL.'/custom-bg/custom-bg.js');
	wp_enqueue_script('custom_bg-default',WP_PLUGIN_URL . '/custom-bg/custom-bg.js');
}
 
 function custom_bg_add_menu()
 {
 	
 	add_theme_page('Custom backgrounds','Custom bg','manage_options','custom-bg','custom_bg_admin_page');
 	
 }
 
 
 function custom_bg_meta_box()
 {
 	
 	add_meta_box('custom_bg_meta_box','Custom background','custom_bg_create_inner_box','post','side','high');
 	add_meta_box('custom_bg_meta_box','Custom background','custom_bg_create_inner_box','page','side','high');
 	
 }
 
 function custom_bg_create_inner_box()
 {
 	if(isset($_GET['post']))
	 	$post_id = $_GET['post'];
	 else
	 	$post_id = 0;
	 if($post_id)
	 	$post_bg = (int) get_post_meta($post_id,'custom_bg_id',true);
// 	if($post_bg == 0 )
// 		$post_bg = custom_bg_get_default();
 	?>
 	<div class="custom_bg_post_select">
 		<span class="custom_bg_preview"><?php custom_bg_thumb($post_id); ?></span>
 		<input type="hidden" id="custom_bg_old_post_bg" name="custom_bg_old_post_bg" value="<?php echo $post_bg ?>" />
 		<input type="hidden" id="custom_bg_post_bg" name="custom_bg_post_bg" />
 		<input type="text" id="custom_bg_post_bg_title" value="<?php echo (isset($post_bg) && ($post_bg) ) ? get_the_title($post_bg) : '' ?>" size="6" />
 		<a href="media-upload.php?custom_bg_post_bg=true&post_id=<?php echo $post_id?>&type=image&TB_iframe=true" class="button-secondary thickbox">Select background</a>
 	</div>
 	
 	
 	<?php
 	
 	
 }
 
 function custom_bg_get_default()
 {
 	
 	return get_option('custom_bg_attachment',0);
 	
 }
 
 function custom_bg_save_data($post_id = null,$post = null)
 {
 	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) { return; }
	if(isset($_POST['custom_bg_post_bg']))
	{
		$custom_bg_id = $_POST['custom_bg_post_bg'];
		if($custom_bg_id)
			update_post_meta($post_id,'custom_bg_id',$custom_bg_id);
	}
 }
 
 function custom_bg_thumb($post = false, $echo = true)
 {
	 $bg_thumb_html = '';
 	if($post !== false && get_post_meta($post,'custom_bg_id',true) != 0 )
 	{
 	
 		$custom_bg_id = (int) get_post_meta($post,'custom_bg_id',true);
 		 
	 }
	 
 	elseif(get_option('custom_bg_attachment','__null__') !== '__null__' )
 	{
	 	$custom_bg_id = (int) get_option('custom_bg_attachment','__null__');
	}
	
 	$bg_thumbnail = wp_get_attachment_image_src($custom_bg_id,'thumbnail');
 	$bg_thumbnail_src = $bg_thumbnail[0];
 	$bg_thumb_html = "<img src=\"{$bg_thumbnail_src}\" width=\"{$bg_thumbnail[1]}\"  />";
 	
	 if($echo)
 	 	echo $bg_thumb_html;

 	 	return $bg_thumb_html;
 }
 
 
 
 function custom_bg_admin_page()
 {
 	if(isset($_POST['save']) && isset($_POST['custom_bg_new_default_id']) && $_POST['custom_bg_new_default_id'] !== 0 )
 		update_option('custom_bg_attachment',(int) $_POST['custom_bg_new_default_id'] );
 		
 	$custom_bg_default_id = (int) get_option('custom_bg_attachment',0);
 	?>
 	<form action="" id="save-custom-bg" method="POST">
 		<div class="wrap">
 			<h2>Custom Backgrounds</h2>
 			<div class="custom_bg_menu">
 				<span class="custom-bg-thumb"><?php custom_bg_thumb(); ?></span>
 					<input type="hidden" name="custom_bg_default_id" id="custom_bg_default_id" value="<?php echo $custom_bg_default_id ? $custom_bg_default_id : '' ?>">
 					<input type="hidden" id="custom_bg_new_default_id" name="custom_bg_new_default_id" />
 					<input type="text" class="file" name="background-file" id="background-file" value="<?php echo $custom_bg_default_id ? get_the_title($custom_bg_default_id) : '' ?>" />
 					<a id="select-custom-bg" class="button-secondary thickbox" href="media-upload.php?custom_bg_default=true&type=image&post_id=0&TB_iframe=true" title="Set background" >Set background</a>
 					
 				</div>
 				<div class="submit">
 					<input type="submit" class="button-primary" name="save" value="Save changes" id="submitbutton" />
 				</div>
 		</div>
 	</form>
 	
 	
 	<?php
 	
 	
 	
 }
 
 
 add_filter('media_upload_form_url','custom_bg_upload_form_url');
 
 /**
  * custom_bg_upload_form_url
  *
  * 
  * credits go to Pär Thernström,author of simple fields plugin
  * @param string $url
  * @return string url
  */
 function custom_bg_upload_form_url($url)
 {

 	foreach($_GET as $key => $val)
 	{
 		if(strpos($key, "custom_bg_") === 0)
 		{
 			
 			$url = add_query_arg($key,$val,$url);

 		}
 		
 	}

 	return $url;	
 	
 }
 
 
	 add_filter( 'media_send_to_editor','custom_bg_default_send_to_editor',20,2);
 function custom_bg_default_send_to_editor($html,$send_id)
 {
 	parse_str(parse_url($_POST["_wp_http_referer"],PHP_URL_QUERY), $arr_postinfo);

 	
 	print_r($arr_postinfo);
 	if(isset($arr_postinfo['custom_bg_default']) && $arr_postinfo['custom_bg_default'] == "true" )
 	{
 	$attach_id = (int) $send_id;
 	$bg_thumbnail = wp_get_attachment_image_src($attach_id,'thumbnail');
 	$bg_thumbnail_src = $bg_thumbnail[0];
 	$bg_thumb_html = "<img src=\"{$bg_thumbnail_src}\" width=\"{$bg_thumbnail[1]}\"  />";
 	$file_title = get_the_title($attach_id);
 	?>
 	
 	<script type="text/javascript">
 		var win = window.dialogArguments || opener || parent || top;
 		win.jQuery("#background-file").val("<?php echo $file_title ?>");
 		win.jQuery('.custom-bg-thumb').html('<?php echo $bg_thumb_html ?>');
 		win.jQuery('#custom_bg_new_default_id').val("<?php echo $attach_id ?>");
 		win.tb_remove();

 	</script>
 	
 	<?php
 	}
 	if(isset($arr_postinfo['custom_bg_post_bg']))
 	{
 		$attach_id = (int) $send_id;
	 	$bg_thumbnail = wp_get_attachment_image_src($attach_id,'thumbnail');
	 	$bg_thumbnail_src = $bg_thumbnail[0];
	 	$bg_thumb_html = "<img src=\"{$bg_thumbnail_src}\" width=\"{$bg_thumbnail[1]}\"  />";
	 	$file_title = get_the_title($attach_id);
	 	?>
	 	
	 	<script type="text/javascript">

 			var win = window.dialogArguments || opener || parent || top;
 			win.jQuery(".custom_bg_preview").html('<?php echo $bg_thumb_html ?>');
 			win.jQuery("#custom_bg_post_bg").val('<?php echo $attach_id ?>');
 			win.jQuery("#custom_bg_post_bg_title").val("<?php echo $file_title ?>");
 			win.tb_remove();
	 	
 		</script>
 		
 		<?php
 	} 	
 }
 
 
 
 
 
 function custom_bg_print_bg($echo = true,$id = 'background-image')
 {
 	
 	if(is_singular())
 	{
 	global $post;
 	$bg_html = '';
	$attachment_id = (int) get_post_meta($post->ID,'custom_bg_id',true);
	
	if( $attachment_id !== 0 )
		{
			
			$bg = wp_get_attachment_image_src($attachment_id,'full');
			$bg_src = $bg[0];
			$bg_width = $bg[1];
			$bg_height = $bg[2];
			$bg_html = "<img src=\"{$bg_src}\" width=\"{$bg_width}\" height=\"{$bg_height}\" id=\"{$id}\" />";
		}
		else
		{
	$custom_bg_default_id = (int) get_option('custom_bg_attachment','__null__');
 	$bg = wp_get_attachment_image_src($custom_bg_default_id,'full');
 	$bg_src = $bg[0];
	$bg_width = $bg[1];
	$bg_height = $bg[2];
	$bg_html = "<img src=\"{$bg_src}\" width=\"{$bg_width}\" height=\"{$bg_height}\" id=\"{$id}\" />";

	
	
		}
		
 	}
 	else
 	{
 		$custom_bg_default_id = (int) get_option('custom_bg_attachment','__null__');
 	 	$bg = wp_get_attachment_image_src($custom_bg_default_id,'full');
	 	$bg_src = $bg[0];
		$bg_width = $bg[1];
		$bg_height = $bg[2];
		$bg_html = "<img src=\"{$bg_src}\" width=\"{$bg_width}\" height=\"{$bg_height}\" id=\"{$id}\" />";

 		
 		
 	}
 	
 if($echo)
 	echo $bg_html;
 else
 	return $bg_html;
 }
 
 
 
 
 
