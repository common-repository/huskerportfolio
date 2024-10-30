<?php
/*
Plugin Name: Husker Portfolio
Plugin URI:http://www.huskerinfotech.com/index.php/wordpress-plugins
Description: Enables a portfolio post type and taxonomies.
Version: 0.3
Author: Husker infotech
Author URI: http://huskerinfotech.com/
License: GPLv2
*/

/**
 * Flushes rewrite rules on plugin activation to ensure portfolio posts don't 404
 * http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
 */
 error_reporting(0);
add_action("edit_user_profile","my_header_content");

add_action('admin_head', 'my_action_javascript');

function my_action_javascript() {
?>
<script type="text/javascript">
var intTextBox=0;
function addElement()
{      
intTextBox = intTextBox + 1;
var contentID = document.getElementById('content123');
var newTBDiv = document.createElement('div');
newTBDiv.setAttribute('id','strText'+intTextBox);
newTBDiv.innerHTML = "Image "+intTextBox+":  <input type='file' id='" + intTextBox + "' name='my_meta_box_text[]'/>";
contentID.appendChild(newTBDiv);
}
function removeElement()
{
if(intTextBox != 0)
{
var contentID = document.getElementById('content123');
contentID.removeChild(document.getElementById('strText'+intTextBox));
intTextBox = intTextBox-1;
}
}
   jQuery(document).ready(function(){
                         jQuery('#post').attr('enctype','multipart/form-data');
                         jQuery('#post').attr('encoding', 'multipart/form-data');                           
                 });
                
</script>
    
<script type="text/javascript">
function showHint()
{

var r=confirm("Are you sure want to delete?");
if (r != true)
  {
	return false;
  }
  else
  {
    alert("Sucess full Delete");
	<?php image_delete(); ?>
	return true;
  }
}
</script>
<?php
}


function huskerportfolioposttype_activation() {
	huskerportfolioposttype();
	flush_rewrite_rules();
	
	//$date = date("Y-m-d H:i:s");
//	global $wpdb;
//	$SQL = "INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt ,post_content ,post_title ,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count) VALUES (1, '$date', '$date' ,'[huskerPortfolio]' ,'Portfolio' , '' ,'publish' ,'open' ,'open' ,'', 'portfolio' , '' ,'' ,'$date' ,'$date','','0' ,'page=portfolio' ,'0' ,'page' ,'', '0')";
//	$results = $wpdb->query($SQL);
			
			
	
}

register_activation_hook( __FILE__, 'huskerportfolioposttype_activation' );
 


function huskerportfolioposttype() 
{

	/**
	 * Enable the Portfolio custom post type
	 * http://codex.wordpress.org/Function_Reference/register_post_type
	 */

	$labels = array(
		'name' => __( 'Portfolio', 'huskerportfolioposttype' ),
		'singular_name' => __( 'Portfolio Item', 'huskerportfolioposttype' ),
		'add_new' => __( 'Add New Item', 'huskerportfolioposttype' ),
		'add_new_item' => __( 'Add New Portfolio Item', 'huskerportfolioposttype' ),
		'edit_item' => __( 'Edit Portfolio Item', 'huskerportfolioposttype' ),
		'new_item' => __( 'Add New Portfolio Item', 'huskerportfolioposttype' ),
		'view_item' => __( 'View Item', 'huskerportfolioposttype' ),
		'search_items' => __( 'Search Portfolio', 'huskerportfolioposttype' ),
		'not_found' => __( 'No portfolio items found', 'huskerportfolioposttype' ),
		'not_found_in_trash' => __( 'No portfolio items found in trash', 'huskerportfolioposttype' )
	);

	$args = array(
    	'labels' => $labels,
    	'public' => true,
		//'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'supports' => array( 'title', 'editor', '', '' ),
		'capability_type' => 'post',
		'rewrite' => array("slug" => "portfolio"), // Permalinks format
		'menu_position' => 5,
		'has_archive' => true
	); 

	register_post_type('portfolio', $args );
		#add meta box	PortFolio Images
	add_action( 'add_meta_boxes', 'porfolio_images' );
	function porfolio_images()
		{
			add_meta_box( 'my-meta-box-id', 'PortFolio Images', 'add_images', 'portfolio', 'normal', 'high' );
		}

	function add_images( $post )
		{
			$values = get_post_custom( $post->ID );
			$text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';
			$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';
			$check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';
			wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
			?>
			<p>
            
			<input type="hidden" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo $text; ?>" />
			</p>
			<div id="content123"></div>
			<p><a href="javascript:addElement();">Add    ||</a><a href="javascript:removeElement();" >Remove</a></p>
		<?php	
		}
		
	function lastWord($sentence) 
		{
			
			$words = explode(',', $sentence);
			
			$result = trim($words[count($words) - 1], '.?![](){}*');
			
			return $result;
		}
			
	function image_delete()
		{
			if($_REQUEST['deleteIMg'] == "true")
			{
			global $wpdb;
			$mylink = $wpdb->get_row("SELECT meta_value FROM $wpdb->postmeta  WHERE  meta_key = 'my_meta_box_text' and post_id =".$_REQUEST['post']);
			$lastWrd = lastWord($mylink->meta_value);	
			
			if($lastWrd == $_REQUEST['image'])
				{
				
					$newphrase = str_replace(','.$_REQUEST['image'], "", $mylink->meta_value);
				}
			
			else
				{
					$newphrase = str_replace($_REQUEST['image'].',', "", $mylink->meta_value);
				}	
			
			$cnt =str_word_count($mylink->meta_value);
			if($cnt == 2 && $mylink->meta_value==$_REQUEST['image'])
				{
				
				update_post_meta( $_REQUEST['post'], 'my_meta_box_text', wp_kses( '', $allowed ) );
				}
			else 
				{
				
				update_post_meta( $_REQUEST['post'], 'my_meta_box_text', wp_kses( $newphrase, $allowed ) );
				}
				
			$file = $_REQUEST['image'];
			$path = realpath(dirname(__FILE__));
			@unlink($path."/upload/$file");
				
			}
		}	
		
		add_action( 'save_post', 'save' );
		
		function save( $post_id )
		{
						
					
			$folder = ABSPATH.'wp-content/plugins/huskerPortfolio/upload/';
			chmod($folder, 0777); 
					
			$url = plugins_url();
					
			$path = $url."/huskerPortfolio/upload";
			
			$cnt = count($_FILES["my_meta_box_text"]["tmp_name"]);
					
			$arr =$_FILES["my_meta_box_text"]["tmp_name"];
					
			$arrName = $_FILES["my_meta_box_text"]["name"];
		
			for($i=0;$i<$cnt;$i++)
			{
				
				move_uploaded_file($arr[$i],
				 $folder. $arrName[$i]);
			}
			global $wpdb;
			$mylink = $wpdb->get_row("SELECT meta_value FROM $wpdb->postmeta  WHERE  meta_key = 'my_meta_box_text' and post_id =".$_REQUEST['post_ID']);
					
			if($mylink->meta_value != "")
			{
				$comma= ",";
			}
			$comma_separated = implode(",", $arrName);
			$_POST['my_meta_box_text'] = $mylink->meta_value.$comma.$comma_separated;
					
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
						
			if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
					
			if( !current_user_can( 'edit_post' ) ) return;
					
			$allowed = array( 
					'a' => array( 
					'href' => array() 
						)
					);
					
			if( isset( $_POST['my_meta_box_text'] ) )
			update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
			$chk = ( isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_check'] ) ? 'on' : 'off';
			update_post_meta( $post_id, 'my_meta_box_check', $chk );
		}
		
		add_action( 'add_meta_boxes', 'image_meta_box_add' );		
		
		function image_meta_box_add()
		{
			add_meta_box( 'image_meta_box_add', 'PortFolio Image Gallary', 'image_meta_box_add_cb', 'portfolio', 'normal', 'high' );
		}	
		function image_meta_box_add_cb()
		{
			global $wpdb;
			$mylink = $wpdb->get_row("SELECT meta_value FROM $wpdb->postmeta  WHERE  meta_key = 'my_meta_box_text' and post_id =".$_REQUEST['post']);
			$arr = explode(",", $mylink->meta_value);
			for($i=0;$i<count($arr);$i++)
			{?>
			<?php if($arr[$i] != "") 
				{ ?>
	        <img src="<?php echo WP_PLUGIN_URL; ?>/huskerPortfolio/upload/<?php echo $arr[$i]; ?>" style="height:100px;width:100px;" />
            <a name='<?php echo $arr[$i]; ?>' href=' <?php echo get_site_url(). "/wp-admin/post.php?post=".$_REQUEST['post']."&action=edit"; ?>&deleteIMg=true&image=<?php echo $arr				[$i]; ?>' onclick=' return showHint()'>Delete</a>
		    <?php
				}
			}
			echo ' <span id="txtHint"></span>';
		}
		#end
	
	/**
	 * Register a taxonomy for Portfolio Categories
	 * http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */

   		$taxonomy_portfolio_category_labels = array(
		'name' => _x( 'Portfolio Categories', 'huskerportfolioposttype' ),
		'singular_name' => _x( 'Portfolio Category', 'huskerportfolioposttype' ),
		'search_items' => _x( 'Search Portfolio Categories', 'huskerportfolioposttype' ),
		'popular_items' => _x( 'Popular Portfolio Categories', 'huskerportfolioposttype' ),
		'all_items' => _x( 'All Portfolio Categories', 'huskerportfolioposttype' ),
		'parent_item' => _x( 'Parent Portfolio Category', 'huskerportfolioposttype' ),
		'parent_item_colon' => _x( 'Parent Portfolio Category:', 'huskerportfolioposttype' ),
		'edit_item' => _x( 'Edit Portfolio Category', 'huskerportfolioposttype' ),
		'update_item' => _x( 'Update Portfolio Category', 'huskerportfolioposttype' ),
		'add_new_item' => _x( 'Add New Portfolio Category', 'huskerportfolioposttype' ),
		'new_item_name' => _x( 'New Portfolio Category Name', 'huskerportfolioposttype' ),
		'separate_items_with_commas' => _x( 'Separate portfolio categories with commas', 'huskerportfolioposttype' ),
		'add_or_remove_items' => _x( 'Add or remove portfolio categories', 'huskerportfolioposttype' ),
		'choose_from_most_used' => _x( 'Choose from the most used portfolio categories', 'huskerportfolioposttype' ),
		'menu_name' => _x( 'Portfolio Categories', 'huskerportfolioposttype' ),
   		 );
	
		$taxonomy_portfolio_category_args = array(
			'labels' => $taxonomy_portfolio_category_labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'rewrite' => true,
			'query_var' => true
		);
	
   	   register_taxonomy( 'portfolio_category', array( 'portfolio' ), $taxonomy_portfolio_category_args );
	
}

	add_action( 'init', 'huskerportfolioposttype' );

// Allow thumbnails to be used on portfolio post type

	add_theme_support( 'post-thumbnails', array( 'portfolio' ) );


function huskerPortfolio_convertShortcodeToPortfolio()
{
?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
    
    
	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="<?php echo get_site_url(); ?>/wp-content/plugins/huskerPortfolio/js/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="<?php echo get_site_url(); ?>/wp-content/plugins/huskerPortfolio/js/jquery.fancybox.js"></script>
    
	<link rel="stylesheet" type="text/css" href="<?php echo get_site_url(); ?>/wp-content/plugins/huskerPortfolio/css/jquery.fancybox.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo get_site_url(); ?>/wp-content/plugins/huskerPortfolio/css/portfolio.css" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="<?php echo get_site_url(); ?>/wp-content/plugins/huskerPortfolio/css/jquery.fancybox-buttons.css?v=2.0.3" />
	<script type="text/javascript">
		$(document).ready(function() {
				$('.fancybox').fancybox();
				$(".fancybox-effects-a").fancybox({
				helpers: {
					title : {
						type : 'outside'
					},
					overlay : {
						speedIn : 500,
						opacity : 0.95
					}
				}
			});

			$(".fancybox-effects-b").fancybox({
				openEffect  : 'none',
				closeEffect	: 'none',

				helpers : {
					title : {
						type : 'over'
					}
				}
			});

			$(".fancybox-effects-c").fancybox({
				wrapCSS    : 'fancybox-custom',
				closeClick : true,

				helpers : {
					title : {
						type : 'inside'
					},
					overlay : {
						css : {
							'background-color' : '#eee'	
						}
					}
				}
			});

			$(".fancybox-effects-d").fancybox({
				padding: 0,

				openEffect : 'elastic',
				openSpeed  : 150,

				closeEffect : 'elastic',
				closeSpeed  : 150,

				closeClick : true,

				helpers : {
					overlay : null
				}
			});

			$('.fancybox-buttons').fancybox({
				openEffect  : 'none',
				closeEffect : 'none',

				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,

				helpers : {
					title : {
						type : 'inside'
					},
					buttons	: {}
				},

				afterLoad : function() {
					this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
				}
			});

			$('.fancybox-thumbs').fancybox({
				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,
				arrows    : false,
				nextClick : true,

				helpers : { 
					thumbs : {
						width  : 50,
						height : 50
					}
				}
			});

		});
	</script>
    
	<?php
	
	function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
	
	function count_up($val)
	 {
	 
		global $wpdb;
		$child='';
		$parents = $wpdb->get_results("SELECT * FROM $wpdb->term_taxonomy where	parent=".$val);
		
		foreach ( $parents as $parent ) 
		{		
		$child .= "'".$parent->term_taxonomy_id."'";		
						
		$child .= ','.count_up($parent->term_taxonomy_id);
		}
		return $child;
	
	 }
	
	$page= curPageURL();
	$queryString = $_SERVER['QUERY_STRING'];  
	$part = $queryString;
	global $wpdb;
	$path= "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
	$url = get_site_url().'?page_id='.$_REQUEST['page_id'];
	$categories = $wpdb->get_results("SELECT tax.term_taxonomy_id,tax.`description`,tax.`parent`,tax.`term_id`,tax.`count`,term.`name`,term.`slug`
										FROM $wpdb->term_taxonomy AS tax 
										LEFT JOIN $wpdb->terms AS term ON  term.term_id = tax.term_id 
										WHERE  tax.taxonomy= 'portfolio_category' AND tax.`parent`='0'
										");
		
	if(!isset($_REQUEST['id']))
	{
	echo "<div class='category'>";
			$url1 = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			 $url1 = strtok($url1,'&');
			
			if(isset($_REQUEST['page_id']))
				{
				echo"<a href='$url1'>All</a>";
				}
				else
				{
				$url11 = strtok($url1,'?');
				echo"<a href='$url11'>All</a>";
				}
	
	foreach ( $categories as $category ) 
			{
	
				if($part == "")
				{
				echo"<a href='$page?cat_id=$category->term_id'>$category->name</a>";
				}
				else
				{
				if(isset($_REQUEST['cat_id']))
				{
			
				if(!isset($_REQUEST['page_id']))
				{
				$url11 = strtok($url1,'?');
				echo"<a href='$url11?cat_id=$category->term_id'>$category->name</a>";
				}
				else
				{
				 $page1 = strtok($page,'&');
				echo"<a href='$page1&cat_id=$category->term_id'>$category->name</a>";
				
				}
				}
				else
				{
				
				echo"<a href='$page&cat_id=$category->term_id'>$category->name</a>";
				}
				}
			}
	echo "</div>";	
	}
		if(isset($_REQUEST['cat_id']))
	{
	$myresult = $wpdb->get_row("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id =".$_REQUEST['cat_id']); 
	$childer = count_up($_REQUEST['cat_id'])."'".$myresult->term_taxonomy_id."'";
	$rows = $wpdb->get_results("SELECT object_id
										FROM $wpdb->term_relationships 
										WHERE term_taxonomy_id  IN ($childer)
										");
	$rowID = "";
	
			foreach ( $rows as $row ) 
			{
			$rowID .= "'".$row->object_id."'".',';
			}
			 $rowID = substr($rowID,0,-1); 
			$portfolios = $wpdb->get_results("SELECT ID,post_content,post_title,post_name,meta_value 
										  FROM $wpdb->posts  
										  LEFT JOIN $wpdb->postmeta on  post_id = ID  
										  WHERE  post_status= 'publish' and post_type ='portfolio' 
										  AND meta_key = 'my_meta_box_text' 
										  AND ID IN ($rowID)
										  ");
								
	
	}
	else
	{
	$portfolios = $wpdb->get_results("SELECT ID,post_content,post_title,post_name,meta_value 
										  FROM $wpdb->posts  
										  LEFT JOIN $wpdb->postmeta on  post_id = ID  
										  WHERE  post_status= 'publish' and post_type ='portfolio' 
										  AND meta_key = 'my_meta_box_text' 
										  ");
										  }
        $i =1;
        if(!isset($_REQUEST['id']))
        {
		echo '<div class="cleared"></div>';
			foreach ( $portfolios as $portfolio ) 
			{
				$pieces = explode(",", $portfolio->meta_value);
				
				$token = strtok($portfolio->meta_value, ",");
				
				$dir = get_site_url().'/wp-content/plugins/huskerPortfolio/upload/'.$token;
				echo "<div class ='even'>";
				?>
				<div class="thumb">
                <a class="" href="<?php echo $dir; ?>" data-fancybox-group="<?php echo $portfolio->post_title;?>" title="">
                <img src="<?php echo $dir; ?>" alt="" />
                </a>
                <a href="<?php echo $dir; ?>" class="fancybox thumbnail_image"><img src="<?php echo $dir; ?>" alt="" /></a>
                </div>
				<?php
				for($j=1;$j<count($pieces);$j++)
					{
					?>
					<a class="fancybox" href="<?php echo get_site_url().'/wp-content/plugins/huskerPortfolio/upload/'.$pieces[$j]; ?>" data-fancybox-group="<?php echo $portfolio->post_title; ?>" title=""></a>
					<?php
					}
				if($part == "")
				{
				$qrystring = '?id='.$portfolio->ID;
				}
				else
				{
				$qrystring = '&id='.$portfolio->ID;
				}
			
				echo "<h3><a href='$page$qrystring'>".$portfolio->post_title."</a></h3>";
				echo '<div class="port-description">';
				echo substr($portfolio->post_content, 0,125)."..."; 
				echo '</div>';
				echo "<a href='$page$qrystring' class='view-project'>View Project</a>";
				echo "</div>";
				if($i%4){
						}
				else {
					echo '<div class="cleared"></div>';
					echo '<div class="port-separator"></div>';
					}
				
				$i++;
			}
        }
        else
        {
		
			global $wpdb;
			$data = $wpdb->get_row("SELECT post_content,post_title,post_name,meta_value 
									FROM $wpdb->posts  
									LEFT JOIN $wpdb->postmeta on  post_id = ID 
									WHERE  post_status= 'publish' and post_type ='portfolio' and meta_key = 'my_meta_box_text' and ID=".$_REQUEST['id']
									);
			$token = strtok($data->meta_value, ",");
			$path= "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
			$dir = get_site_url().'/wp-content/plugins/huskerPortfolio/upload/'.$token;
			$a= str_replace('&id='.$_REQUEST['id'], "", $path);
			echo '<a href="javascript:history.go(-1)">Go Back</a>';
			echo '<div class="cleared"></div>';
			echo "<div>";
			echo "<h3>".$data->post_title."</h3>";
			?>
            <p class="bigimage">
            <a class="fancybox crop" href="<?php echo $dir; ?>" data-fancybox-group="gallery" title="">
            <img src="<?php echo $dir; ?>" alt="Click here to see Full Image" title="Click here to see Full Image" />
            </a>
            </p>
            <h4 class="zoomimage">Click on image for Large View</h4>
            <div class="cleared"></div>
            <?php
			echo $data->post_content;
			$pieces = explode(",",$data->meta_value);
			for($j=1;$j<count($pieces);$j++)
			{
			?>
            <a class="fancybox" href="<?php echo get_site_url().'/wp-content/plugins/huskerPortfolio/upload/'.$pieces[$j]; ?>" data-fancybox-group="gallery" title=""></a>
			<?php
            }
			        
        }	

}

add_shortcode('huskerPortfolio', 'huskerPortfolio_convertShortcodeToPortfolio');
 
 
function huskerportfolioposttype_columns_display($portfolio_columns, $post_id)
{

	switch ( $portfolio_columns )
	
	{
		
		
		case "thumbnail":
			$width = (int) 35;
			$height = (int) 35;
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			
			if ($thumbnail_id) {
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			}
			if ( isset($thumb) ) {
				echo $thumb;
			} else {
				echo __('None', 'huskerportfolioposttype');
			}
			break;	
			
			case "portfolio_category":
			
			if ( $category_list = get_the_term_list( $post_id, 'portfolio_category', '', ', ', '' ) ) {
				echo $category_list;
			} else {
				echo __('None', 'huskerportfolioposttype');
			}
			break;	
			
			case "portfolio_tag":
			
			if ( $tag_list = get_the_term_list( $post_id, 'portfolio_tag', '', ', ', '' ) ) {
				echo $tag_list;
			} else {
				echo __('None', 'huskerportfolioposttype');
			}
			break;			
	}
}

add_action( 'manage_posts_custom_column',  'huskerportfolioposttype_columns_display', 10, 2 );

/**
 * Add Portfolio count to "Right Now" Dashboard Widget
 */

function add_portfolio_counts() 
{
        if ( ! post_type_exists( 'portfolio' ) ) {
             return;
        }

        $num_posts = wp_count_posts( 'portfolio' );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( 'Portfolio Item', 'Portfolio Items', intval($num_posts->publish) );
        if ( current_user_can( 'edit_posts' ) ) {
            $num = "<a href='edit.php?post_type=portfolio'>$num</a>";
            $text = "<a href='edit.php?post_type=portfolio'>$text</a>";
        }
        echo '<td class="first b b-portfolio">' . $num . '</td>';
        echo '<td class="t portfolio">' . $text . '</td>';
        echo '</tr>';

        if ($num_posts->pending > 0) {
            $num = number_format_i18n( $num_posts->pending );
            $text = _n( 'Portfolio Item Pending', 'Portfolio Items Pending', intval($num_posts->pending) );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = "<a href='edit.php?post_status=pending&post_type=portfolio'>$num</a>";
                $text = "<a href='edit.php?post_status=pending&post_type=portfolio'>$text</a>";
            }
            echo '<td class="first b b-portfolio">' . $num . '</td>';
            echo '<td class="t portfolio">' . $text . '</td>';

            echo '</tr>';
        }
}

add_action( 'right_now_content_table_end', 'add_portfolio_counts' );


 

/**
 * Displays the custom post type icon in the dashboard
 */

function huskerportfolioposttype_portfolio_icons() { ?>
    <style type="text/css" media="screen">
        #menu-posts-portfolio .wp-menu-image {
            background: url(<?php echo plugin_dir_url( __FILE__ ); ?>images/portfolio-icon.png) no-repeat 6px 6px !important;
        }
		#menu-posts-portfolio:hover .wp-menu-image, #menu-posts-portfolio.wp-has-current-submenu .wp-menu-image {
            background-position:6px -16px !important;
        }
		#icon-edit.icon32-posts-portfolio {background: url(<?php echo plugin_dir_url( __FILE__ ); ?>images/portfolio-32x32.png) no-repeat;}
    </style>
<?php }

add_action( 'admin_head', 'huskerportfolioposttype_portfolio_icons' );

?>
