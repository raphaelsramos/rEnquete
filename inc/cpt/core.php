<?php

	/***
	 *	2019-03-06
	 *	https://wordpress.stackexchange.com/questions/94817/add-category-base-to-url-in-custom-post-type-taxonomy/188834
	 *	https://wordpress.stackexchange.com/questions/108642/permalinks-custom-post-type-custom-taxonomy-post
	 */

	namespace r\enquete;
	
	
	class CPT {

		
		const SLUG = 'enquete';


		public static function init(){

			# register cpt
			\add_action( 'init', 'r\enquete\CPT::register', 0 );
			
			require_once( INC .'cpt/helpers.php' );
			CPT_Helpers::init();
			
			require_once( INC .'cpt/metabox.php' );
			CPT_Metabox::init();
			
			require_once( INC .'cpt/columns.php' );
			CPT_Columns::init();
			
			require_once( INC .'cpt/sc.php' );
			CPT_SC::init();
			
			require_once( INC .'cpt/admin-post.php' );
			CPT_AdminPost::init();
			
			require_once( INC .'cpt/ajax.php' );
			CPT_Ajax::init();
		
		}
		
		
		public static function get_slug(){
			return r\enquete\CPT::SLUG;
		}


		# Register Custom Post Type
		public static function register(){
		
			$labels = [
				'name'                  => \_x( 'Polls', 'Post Type General Name', 'r-enquete' ),
				'singular_name'         => \_x( 'Poll', 'Post Type Singular Name', 'r-enquete' ),
				'menu_name'             => \__( 'Polls', 'r-enquete' ),
				'name_admin_bar'        => \__( 'Poll', 'r-enquete' ),
				'archives'              => \__( 'Poll Archives', 'r-enquete' ),
				'attributes'            => \__( 'Poll Attributes', 'r-enquete' ),
				'parent_item_colon'     => \__( 'Parent Poll:', 'r-enquete' ),
				'all_items'             => \__( 'All Polls', 'r-enquete' ),
				'add_new_item'          => \__( 'Add New Poll', 'r-enquete' ),
				'add_new'               => \__( 'Add New Poll', 'r-enquete' ),
				'new_item'              => \__( 'New Poll', 'r-enquete' ),
				'edit_item'             => \__( 'Edit Poll', 'r-enquete' ),
				'update_item'           => \__( 'Update Poll', 'r-enquete' ),
				'view_item'             => \__( 'View Poll', 'r-enquete' ),
				'view_items'            => \__( 'View Polls', 'r-enquete' ),
				'search_items'          => \__( 'Search Poll', 'r-enquete' ),
				'not_found'             => \__( 'Not found', 'r-enquete' ),
				'not_found_in_trash'    => \__( 'Not found in Trash', 'r-enquete' ),
				'featured_image'        => \__( 'Featured Image', 'r-enquete' ),
				'set_featured_image'    => \__( 'Set featured image', 'r-enquete' ),
				'remove_featured_image' => \__( 'Remove featured image', 'r-enquete' ),
				'use_featured_image'    => \__( 'Use as featured image', 'r-enquete' ),
				'insert_into_item'      => \__( 'Insert into poll', 'r-enquete' ),
				'uploaded_to_this_item' => \__( 'Uploaded to this poll', 'r-enquete' ),
				'items_list'            => \__( 'Polls list', 'r-enquete' ),
				'items_list_navigation' => \__( 'Polls list navigation', 'r-enquete' ),
				'filter_items_list'     => \__( 'Filter polls list', 'r-enquete' ),
			];
			
			$rewrite = [
				'slug'			=> CPT::SLUG,
				'with_front'	=> false,
				'pages'			=> true,
				'feeds'			=> false,
			];
			
			$args = [
				'label'                 => $labels,
				'description'           => \__( 'List of surveys', 'r-enquete' ),
				'labels'                => $labels,
				'supports'              => [ 'title', 'revisions' ],
				'taxonomies'            => [],
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 7,
				'menu_icon'             => 'dashicons-forms',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'rewrite'               => $rewrite,
				'capability_type'       => 'page',
				'show_in_rest'          => true,
			];
			
			$args = \apply_filters( 'r/enquete/cpt/args', $args );
			
			\register_post_type( self::SLUG, $args );

		}

		/*
		public static function assoc_forum_tax( $cpts ){
			$cpts[] = self::get_slug();
			return $cpts;
		}
		
		
		public static function make_forum_tax_required(){
			global $typenow;
			if( $typenow == rForum_CPT::get_slug() ){
?>
<script type="text/javascript">
( function( $ ){
	$( function(){

		var $tx = '<?php echo rForum_Tax::get_slug() ?>',
			$scope = $( '#'+ $tx +'-all > ul' );
		
		if( $scope.length ){				
			$( '#publish' ).on( 'click', function(){
				if( !$scope.find( 'input:checked' ).length ){
					alert( '<?php _e( 'You need to select a Forum.', 'r-enquete' ) ?>' );
					return false;
				}
			} );
		}

	});
})( jQuery );
</script>
<?php
			}
		}


		public static function add_url_rule(){
			add_rewrite_rule(
				'^'. rForum_Tax::get_slug() .'/([^/]*)/([^/]*)/?',
				'index.php?post_type='. rForum_CPT::get_slug() .'&name=$matches[2]', 'top' );
		}


		public static function adjust_link( $post_link, $id = 0 ){
			$post = get_post( $id );
			if( is_object( $post ) ){
				$terms = wp_get_object_terms( $post->ID, rForum_Tax::get_slug() );
				if( $terms ){
					return str_replace( '%forum%', $terms[0]->slug, $post_link );
				}
			}
			return $post_link;
		}

		
		public static function pre_get_posts( $query ){
			if( $query->is_main_query() && !is_admin() && is_tax( 'forum' ) ){
				$query->set( 'post_status', [ 'publish', 'pending' ] );
			}
		}
		*/

	}
