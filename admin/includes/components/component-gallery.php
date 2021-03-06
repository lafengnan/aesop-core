<?php
/**
 	* Filters custom meta box class to add cusotm meta to galelry component
 	*
 	* @since    1.0.0
*/
class AesopGalleryComponentAdmin {

	public function __construct(){

       	add_action('init',array($this,'do_type'));
       	add_action('admin_init',array($this,'sc_helper'));
       	add_filter('manage_ai_galleries_posts_columns', array($this,'col_head'));
		add_action('manage_ai_galleries_posts_custom_column', array($this,'col_content'), 10, 2);
		add_filter( 'cmb_meta_boxes', array($this,'aesop_gallery_meta' ));
	}

	/**
	 	* Creates an Aesop Galleries custom post type to manage all psot galleries
	 	*
	 	* @since    1.0.0
	*/
	function do_type() {

		$labels = array(
			'name'                		=> _x( 'Galleries','aesop-core' ),
			'singular_name'       		=> _x( 'Gallery','aesop-core' ),
			'menu_name'           		=> __( 'Galleries', 'aesop-core' ),
			'parent_item_colon'   		=> __( 'Parent Gallery:', 'aesop-core' ),
			'all_items'           		=> __( 'All Galleries', 'aesop-core' ),
			'view_item'           		=> __( 'View Gallery', 'aesop-core' ),
			'add_new_item'        		=> __( 'Add New Gallery', 'aesop-core' ),
			'add_new'             		=> __( 'New Gallery', 'aesop-core' ),
			'edit_item'           		=> __( 'Edit Gallery', 'aesop-core' ),
			'update_item'         		=> __( 'Update Gallery', 'aesop-core' ),
			'search_items'        		=> __( 'Search Galleries', 'aesop-core' ),
			'not_found'           		=> __( 'No Galleries found', 'aesop-core' ),
			'not_found_in_trash'  		=> __( 'No Galleries found in Trash', 'aesop-core' ),
		);
		$args = array(
			'label'               		=> __( 'Galleries', 'aesop-core' ),
			'description'         		=> __( 'Create responsive boxes', 'aesop-core' ),
			'menu_icon' 		  		=> AI_CORE_URL.'/admin/assets/img/icon.png',  // Icon Path
			'menu_position'				=> 15,
			'labels'              		=> $labels,
			'supports'            		=> array( 'title', 'editor' ),
			'hierarchical'        		=> false,
			'public'              		=> false,
 			'show_ui' 					=> true,
			'exclude_from_search'		=> true,
			'query_var' 				=> true,
			'can_export' 				=> true,
			'capability_type' 			=> 'post'
		);
		register_post_type( 'ai_galleries', $args );

	}

	/**
	 	* Adds meta box to gallery post type in admin that displays the shortcode 
	 	*
	 	* @since    1.0.0
	*/
	function sc_helper(){
		add_meta_box('ai_gallery_sc',__('Gallery Instructions','aesop-core'),array($this,'sc_helper_cb'),'ai_galleries','side', 'low');
	}
	function sc_helper_cb(){
		_e('1. Click the Add Media button<br />2. Click Create Gallery to create a gallery<br />3. Insert gallery into editor, and publish.<br /><br /> Once you\'ve created the gallery, copy the code below, and paste it into your story where you want the gallery to be shown.<br />','aesop-core');
		printf('<pre>[aesop_gallery id="%s"]</pre>',get_the_ID());
	}

	/**
	 	* Adds columns to the Aesop Galleries custom post type
	 	* Adds the shortcode for easy copy and past
	 	* Adds the posts that the shortcode is used in
	 	*
	 	* @since    1.0.0
	*/
	function col_head($defaults) {
	    $defaults['aesop_gallery'] = __('Gallery Code','aesop-core');
	    $defaults['used_in'] = __('Used In','aesop-core');
	    return $defaults;
	}

	/**
	 	* Callback for col_head
	 	*
	 	* @since    1.0.0
	*/
	function col_content($column_name, $post_ID) {

	    if ('aesop_gallery' == $column_name) {
	        printf('[aesop_gallery id="%s"]',$post_ID);
	    }

	   	if ('used_in' == $column_name) {

			$pages = get_posts(array ('s' => '[aesop_gallery','post_type' => array ( 'page', 'post' ) ));

			foreach($pages as $page):
				$id = $page->ID;
				if(has_shortcode($page->post_content,'aesop_gallery')){
					echo ucfirst($this->the_slug($id));
				}

			endforeach;

	    }
	}

	/**
	 	* Return the post slug based on ID
	 	*
	 	* @since    1.0.0
	*/
	function the_slug($id) {
		$post_data = get_post($id, ARRAY_A);
		$slug = $post_data['post_name'];
		return $slug; 
	}

	/**
	 	* Adds custom gallery meta
	 	*
	 	* @since    1.0.0
	*/
	function aesop_gallery_meta( array $meta_boxes ) {

		$opts = array(
			array(
				'id'             => 'aesop_gallery_width',
				'name'           => 'Gallery Width',
				'type'           => 'text',
			),
		);

		$meta_boxes[] = array(
			'title' => __('Gallery Options', 'aesop-core'),
			'pages' => array('ai_galleries'),
			'context'	=> 'side',
			'fields' => $opts
		);

		return $meta_boxes;

	}

}
new AesopGalleryComponentAdmin;