<?php
/** 
    * Plugin Name: Bootstrap Carousel 2x Post Widget
    * Plugin URI: http://plugin.bdwebteam.com/bootstrap-carousel-2x-post-widget
    * Description: Adds a widget that shows the most recent posts of your site with excerpt, featured image by sorting & ordering feature
    * Author: Mahabub Hasan
    * Author URI: http://bdwebteam.com/
    * Version: 1.0.1
    * Text Domain: bdwebteam
    * Domain Path: /languages
    * License: MIT License
    * License URI: http://opensource.org/licenses/MIT
*/

/**
   *
   * @package   bootstrap-carousel-2x-post-widget
   * @author    Md. Mahabub Masan Manik <m.manik01@gmail.com>
   * @license   MIT License
   * @link      http://plugin.bdwebteam.com/bootstrap-carousel-2x-post-widget
   * @copyright 2015 Mahabub Hasan
   * 
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}
if (!defined('PLUGIN_ROOT')) {
	define('PLUGIN_ROOT', dirname(__FILE__) . '/');
	define('PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
}
if (! defined ( 'WP_CONTENT_URL' ))
	define ( 'WP_CONTENT_URL', get_option ( 'siteurl' ) . '/wp-content' );
if (! defined ( 'WP_CONTENT_DIR' ))
	define ( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (! defined ( 'WP_PLUGIN_URL' ))
	define ( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
if (! defined ( 'WP_PLUGIN_DIR' ))
	define ( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
    require_once(dirname(__FILE__).'/post_resizer.php'); 
    add_action('widgets_init', create_function('', 'return register_widget("bootstrap_carousel_2x_post");'));
class bootstrap_carousel_2x_post extends WP_Widget {
             
    //	@var string (The plugin version)		
	var $version = '1.0.1';
	//	@var string $localizationDomain (Domain used for localization)
	var $localizationDomain = 'bdwebteam';
	//	@var string $pluginurl (The url to this plugin)
	var $pluginurl = '';
	//	@var string $pluginpath (The path to this plugin)		
	var $pluginpath = '';	

	function bootstrap_carousel_2x_post() {
		$this->__construct();
	}
	
	function __construct() {
		$name = dirname ( plugin_basename ( __FILE__ ) );
		$this->pluginurl = WP_PLUGIN_URL . "/$name/";
		$this->pluginpath = WP_PLUGIN_DIR . "/$name/";
		add_action ( 'wp_print_styles', array (&$this, 'bdwebteam_carousel_2x_post_css' ) );
		
		$widget_ops = array ('classname' => 'bdwebteam-carousel-2x-post-widget', 'description' => __ ( 'Show recent posts from selected category. Includes advanced options.', $this->localizationDomain ) );
		$this->WP_Widget ( 'bdwebteam-carousel-2x-post-widget', __ ( 'bdwebteam-carousel-2x-post-widget', $this->localizationDomain ), $widget_ops );
	}	
	function bdwebteam_carousel_2x_post_css() {
		$name = "bdwebteam-carousel-2x-post-widget.css";
		if (false !== @file_exists ( TEMPLATEPATH . "/$name" )) {
			$css = get_template_directory_uri () . "/$name";
		} else {
			$css = $this->pluginurl . $name;
		}
		wp_enqueue_style ( 'bdwebteam-carousel-2x-post-widget', $css, false, $this->version, 'screen' );
	}   
	function widget($args, $instance) {
		extract( $args );
		$instance['title']?NULL:$instance['title']='';
         $widget_id = $this->id = $widget_id;        
       $post_type='post';    
           
        $cat_id=$instance['posts_cat_id'];
        $cat_name= get_cat_name( $cat_id );
        $category_link = get_category_link($cat_id);
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";   
    $output .= '<div class="panel  panel-primary carousel-2x-post top-border">';
    
    if($instance['x_header']=='1'):
        $output .= '<div class="panel-heading">'; 
		if($title) {
			$output .= $before_title.'<a href="'.$instance['title_url'].'" class="pull-right x-view-all"><i class="fa fa-dashcube"></i> View all</a> <h4> '. $title.'</h4>'.$after_title;
		} elseif($cat_id) {
			$output .= '<a  style="color:'.$borderTopColor.'" href="'.$category_link.'" class="pull-right x-view-all"><i class="fa fa-dashcube"></i> View all</a> <h4> '. $cat_name.'</h4>';
		}
        else{
            $output .= '<a style="color:'.$borderTopColor.'" href="#" class="pull-right x-view-all"><i class="fa fa-dashcube"></i> View all</a> <h4>  bdwebteam Carousel 2x Post Widget</h4>';
        }
      $output .='</div>';
      endif;
      
		ob_start();
		$posts = new WP_Query( array(
			'post_type'		=> array($post_type),
			'showposts'		=> $instance['posts_num'],
			'cat'			=> $instance['posts_cat_id'],
			'ignore_sticky_posts'	=> true,
			'orderby'		=> $instance['posts_orderby'],
			'order'			=> 'dsc',
			'date_query' => array(
				array(
					'after' => $instance['posts_time'],
				),
			),
		) );        
        $output .= '<div class="panel-body">';        
        $output .= '<div id="carousel-example-generic'.$widget_id.'" class="carousel slide carousel-fade" data-ride="carousel">';	       
         $count_data = 0;
         
         if($instance['pagination']=='1'):
         $output .= '<ol class="carousel-indicators">';
        while ($posts->have_posts()): $posts->the_post();                             
                $count_data++;
                $active = ($count_data == 1 ? 'active' : '');            
                $output .= '<li data-target="#carousel-example-generic'.$widget_id.'" data-slide-to="'.($count_data -1).'" class="'.$active.'"></li>';
            endwhile;             
            $output .= '</ol>';	
            endif;
        	$output .= '<div class="carousel-inner">';
            $count_data_info = 0;            
            while ($posts->have_posts()): $posts->the_post(); 
            $show_title_limit=$instance['word_posts_title'];
                $post_title = get_the_title(get_the_ID($post->ID));
                $trimmed_title = wp_trim_words( $post_title,$show_title_limit);
                $post_permalink=get_the_permalink(get_the_ID($post->ID));
                $author_posts_url=get_author_posts_url(get_the_author_meta('ID'));
                $the_author=get_the_author(get_the_ID($post->ID));               
                $show_content_limit=$instance['word_posts_content'];
                $content = get_the_content(get_the_ID($post->ID));
                $trimmed_content = wp_trim_words( $content,$show_content_limit);
            $count_data_info++;
            $active = ($count_data_info == 1 ? 'active' : '');  
			    $output .= '<div class="item ' .$active.'">';			                       
                   $trainer_thumb   = get_post_thumbnail_id($post->ID);
                    $trainer_img_url = wp_get_attachment_url( $trainer_thumb,'medium' ); 
                       
                        if ( has_post_thumbnail() ):
                            $output .= '<img src="'. $trainer_img_url. '" alt="' .$title .'" />';  
                       endif;	                   
                    $output .= '<div class="header-text hidden-xs">';
                         
                         $output .= '<div class="text-center">';                            
                            if($instance['posts_title']=='1'):
                            $output .= '<h2>';
                            	$output .= '<span>'.$trimmed_title.'</span>';
                            $output .= '</h2>';
                            endif;
                            $output .= '<br>';
                             if($instance['posts_content']=='1'):   
                            $output .= '<h3>';                             
                            	$output .= '<span>'.$trimmed_content.'</span>';
                            $output .= '</h3>';
                            endif;
                            $output .= '<br>';
                            if($instance['x-read-more']=='1'):                            
                            $output .= '<div class="">';
                               $output .= ' <a class="btn btn-theme btn-sm btn-min-block x-read-more" href="#">Read more</a></div>'; 
                               endif;
                        $output .= '</div>';
                       
                    $output .= '</div><!-- /header-text -->';
			    $output .= '</div>';			    
		
       	endwhile; 
        	$output .= '</div>';
                if($instance['navigation']=='1'):		
                    $output .= '<a class="left carousel-control" href="#carousel-example-generic'.$widget_id.'" data-slide="prev">';
                    $output .= '<i class="fa fa-caret-left"></i>';
                    $output .= '</a>';
                    $output .= '<a class="right carousel-control" href="#carousel-example-generic'.$widget_id.'" data-slide="next">';
                    $output .= '<i class="fa fa-caret-right"></i>';
                    $output .= '</a>';
                endif;            
		$output .= '</div><!-- /carousel --> ';
	$output .= '</div>';
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	}

/*  Widget update
/* ------------------------------------ */
	public function update($new,$old) {
        $instance = $old;
        $instance['title'] = strip_tags($new['title']);        
        $instance['title_url'] = strip_tags($new['title_url']);        
        $instance['posts_title'] = $new['posts_title']?1:0;
        $instance['word_posts_title'] = strip_tags($new['word_posts_title']);
        $instance['posts_content'] = $new['posts_content']?1:0;        
        $instance['word_posts_content'] = strip_tags($new['word_posts_content']);        
        $instance['posts_cat_id'] = strip_tags($new['posts_cat_id']);
        $instance['posts_orderby'] = strip_tags($new['posts_orderby']);
        $instance['navigation'] = $new['navigation']?1:0;
        $instance['pagination'] = $new['pagination']?1:0;
         $instance['x-read-more-txt'] = strip_tags($new['x-read-more-txt']); 
        $instance['x-read-more'] = $new['x-read-more']?1:0;
        $instance['x_header'] = $new['x_header']?1:0;
        $instance['posts_time'] = strip_tags($new['posts_time']);  
         $instance['posts_num'] = strip_tags($new['posts_num']);     
      
        return $instance;
	}

/*  Widget form
/* ------------------------------------ */
	public function form($instance) {
	   	$bordercolor         = esc_attr($instance['bordercolor']);
		// Default widget settings
		$defaults = array(
			'title' 			 => '',
            'title_url'    => '', 
             'posts_title' 	 => '1',
                'word_posts_title' => '20',
            'posts_content' 	 => '1',
                'word_posts_content' => '20',
			'posts_num' 		 => '5',            
			'posts_cat_id' 		 => '0',
			'posts_orderby' 	 => 'date',
            'show_date'		     => '1',
            'navigation'         => '1',  
            'pagination'         => '1',
            'x-read-more-txt'   =>  'Read More',
            'x-read-more'        => '1',
            'x_header'           => '1',
			'posts_time' 		 => '0',
            
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
	
	<div class="bdwebteam_carousel_2x_post-options-posts">
		<p style="padding-right: 0;">
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :', 'bdwebteam'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
		</p>           
        <p>
			<label for="<?php echo $this->get_field_id('title_url'); ?>"><?php _e('Title URL :', 'bdwebteam'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title_url'); ?>" name="<?php echo $this->get_field_name('title_url'); ?>" type="text" value="<?php echo esc_attr($instance["title_url"]); ?>"  placeholder="<?php echo esc_attr( 'http://test.com/cat' ); ?>" />
		</p>  
        <p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('posts_title'); ?>" name="<?php echo $this->get_field_name('posts_title'); ?>" <?php checked( (bool) $instance["posts_title"], true ); ?>>
			<label for="<?php echo $this->get_field_id('posts_title'); ?>"><?php _e('Show Posts Title:', 'bdwebteam'); ?></label>
		</p>
        <p style="padding-left: 20px;" class="content_show_box">
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("word_posts_title"); ?>"><?php _e('Words to show:', 'bdwebteam'); ?></label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("word_posts_title"); ?>" name="<?php echo $this->get_field_name("word_posts_title"); ?>" type="text" value="<?php echo absint($instance["word_posts_title"]); ?>" size='3' />
		</p>
        <hr />
		<p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('posts_content'); ?>" name="<?php echo $this->get_field_name('posts_content'); ?>" <?php checked( (bool) $instance["posts_content"], true ); ?>>
			<label for="<?php echo $this->get_field_id('posts_content'); ?>"><?php _e('Posts Content:', 'bdwebteam'); ?></label>
		</p>
        <p style="padding-left: 20px;" class="content_show_box">
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("word_posts_content"); ?>"><?php _e('Words to show:', 'bdwebteam'); ?></label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("word_posts_content"); ?>" name="<?php echo $this->get_field_name("word_posts_content"); ?>" type="text" value="<?php echo absint($instance["word_posts_content"]); ?>" size='3' />
		</p>
         <hr />	
        <p class="img_show_box">
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("posts_num"); ?>"><?php _e('Items to show:', 'bdwebteam'); ?></label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("posts_num"); ?>" name="<?php echo $this->get_field_name("posts_num"); ?>" type="text" value="<?php echo absint($instance["posts_num"]); ?>" size='3' />
		</p>         
       		 <hr />
		<p>
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("posts_cat_id"); ?>"><?php _e('Category:', 'bdwebteam'); ?></label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("posts_cat_id"), 'selected' => $instance["posts_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>		
		</p>
		<p style="padding-top: 0.3em;">
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("posts_orderby"); ?>"><?php _e('Order by:', 'bdwebteam'); ?></label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id("posts_orderby"); ?>" name="<?php echo $this->get_field_name("posts_orderby"); ?>">
			  <option value="date"<?php selected( $instance["posts_orderby"], "date" ); ?>><?php _e('Most recent', 'bdwebteam'); ?></option>
			  <option value="comment_count"<?php selected( $instance["posts_orderby"], "comment_count" ); ?>><?php _e('Most commented', 'bdwebteam'); ?></option>
			  <option value="rand"<?php selected( $instance["posts_orderby"], "rand" ); ?>><?php _e('Random', 'bdwebteam'); ?></option>
			</select>	
		</p>
		<p style="padding-top: 0.3em;">
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("posts_time"); ?>"><?php _e('Posts from:', 'bdwebteam'); ?></label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id("posts_time"); ?>" name="<?php echo $this->get_field_name("posts_time"); ?>">
			  <option value="0"<?php selected( $instance["posts_time"], "0" ); ?>><?php _e('All time', 'bdwebteam'); ?></option>
			  <option value="1 year ago"<?php selected( $instance["posts_time"], "1 year ago" ); ?>><?php _e('This year', 'bdwebteam'); ?></option>
			  <option value="1 month ago"<?php selected( $instance["posts_time"], "1 month ago" ); ?>><?php _e('This month', 'bdwebteam'); ?></option>
			  <option value="1 week ago"<?php selected( $instance["posts_time"], "1 week ago" ); ?>><?php _e('This week', 'bdwebteam'); ?></option>
			  <option value="1 day ago"<?php selected( $instance["posts_time"], "1 day ago" ); ?>><?php _e('Past 24 hours', 'bdwebteam'); ?></option>
			</select>	
		</p>		
        <p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('navigation'); ?>" name="<?php echo $this->get_field_name('navigation'); ?>" <?php checked( (bool) $instance["navigation"], true ); ?>>
			<label for="<?php echo $this->get_field_id('navigation'); ?>"><?php _e('Navigation On/Off:', 'bdwebteam'); ?></label>
		</p>
         <p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('pagination'); ?>" name="<?php echo $this->get_field_name('pagination'); ?>" <?php checked( (bool) $instance["pagination"], true ); ?>>
			<label for="<?php echo $this->get_field_id('pagination'); ?>"><?php _e('Pagination On/Off:', 'bdwebteam'); ?></label>
		</p>
        <p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('x-read-more'); ?>" name="<?php echo $this->get_field_name('x-read-more'); ?>" <?php checked( (bool) $instance["x-read-more"], true ); ?>>
			<label for="<?php echo $this->get_field_id('x-read-more'); ?>"><?php _e('Read More On/Off:', 'bdwebteam'); ?></label>
		</p>
         <p class="img_show_box">
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("x-read-more-txt"); ?>"><?php _e('Read More Text:', 'bdwebteam'); ?></label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("x-read-more-txt"); ?>" name="<?php echo $this->get_field_name("x-read-more-txt"); ?>" type="text" value="<?php echo absint($instance["x-read-more-txt"]); ?>" size='3' />
		</p>    
        <p>
			<input type="checkbox" class="checkbox checkboxcontent" id="<?php echo $this->get_field_id('x_header'); ?>" name="<?php echo $this->get_field_name('x_header'); ?>" <?php checked( (bool) $instance["x_header"], true ); ?>>
			<label for="<?php echo $this->get_field_id('x_header'); ?>"><?php _e('X Header On/Off:', 'bdwebteam'); ?></label>
		</p>
	</div>
<?php
    }
}
