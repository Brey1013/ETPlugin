<?php
add_action( 'wp_enqueue_scripts', 'classima_child_styles', 18 );
function classima_child_styles() {
	wp_enqueue_style( 'classipost-style', get_stylesheet_uri() );
}

add_action( 'after_setup_theme', 'classima_child_theme_setup' );
function classima_child_theme_setup() {
    load_child_theme_textdomain( 'classima', get_stylesheet_directory() . '/languages' );
}



// Disable block widgets (they are terrible)

function disable_hash_themes_support() {
    remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'disable_hash_themes_support' );

// Disable block editor

add_filter('use_block_editor_for_post', '__return_false');

// Add some JavaScript


function et_script() {
wp_enqueue_script( 'et', get_stylesheet_directory_uri() . '/js/et-scripts.js', array( 'jquery' ), false, true );
wp_enqueue_script( 'et_cycle', get_stylesheet_directory_uri() . '/js/jquery.cycle2.min.js', array( 'jquery' ), false, true );
}
add_action('wp_enqueue_scripts','et_script');

// Disable comments

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
    
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});


/**
* Add new predefined field "Profile Photo" in UM Form Builder.
*/
add_filter("um_predefined_fields_hook","um_predefined_fields_hook_profile_photo", 99999, 1 );
function um_predefined_fields_hook_profile_photo( $arr ){


    $arr['profile_photo'] = array(
        'title' => __('Profile Photo','ultimate-member'),
        'metakey' => 'profile_photo',
        'type' => 'image',
        'label' => __('Change your profile photo','ultimate-member'),
        'upload_text' => __('Upload your photo here','ultimate-member'),
        'icon' => 'um-faicon-camera',
        'crop' => 1,
        'max_size' => ( UM()->options()->get('profile_photo_max_size') ) ? UM()->options()->get('profile_photo_max_size') : 999999999,
        'min_width' => str_replace('px','',UM()->options()->get('profile_photosize')),
        'min_height' => str_replace('px','',UM()->options()->get('profile_photosize')),
    );

    return $arr;

}

/**
 *  Multiply Profile Photo with different sizes
*/
add_action( 'um_registration_set_extra_data', 'um_registration_set_profile_photo', 9999, 2 );
function um_registration_set_profile_photo( $user_id, $args ){

    if ( empty( $args['custom_fields'] ) ) return;
    
    if( ! isset( $args['form_id'] ) ) return;

    if( ! isset( $args['profile_photo'] ) || empty( $args['profile_photo'] ) ) return;

    // apply this to specific form
    //if( $args['form_id'] != 12345 ) return; 


    $files = array();

    $fields = unserialize( $args['custom_fields'] );

    $user_basedir = UM()->uploader()->get_upload_user_base_dir( $user_id, true );

    $profile_photo = get_user_meta( $user_id, 'profile_photo', true ); 

    $image_path = $user_basedir . DIRECTORY_SEPARATOR . $profile_photo;

    $image = wp_get_image_editor( $image_path );

    $file_info = wp_check_filetype_and_ext( $image_path, $profile_photo );
 
    $ext = $file_info['ext'];
    
    $new_image_name = str_replace( $profile_photo,  "profile_photo.".$ext, $image_path );

    $sizes = UM()->options()->get( 'photo_thumb_sizes' );

    $quality = UM()->options()->get( 'image_compression' );

    if ( ! is_wp_error( $image ) ) {
            
        $max_w = UM()->options()->get('image_max_width');
        if ( $src_w > $max_w ) {
            $image->resize( $max_w, $src_h );
        }

        $image->save( $new_image_name );

        $image->set_quality( $quality );

        $sizes_array = array();

        foreach( $sizes as $size ){
            $sizes_array[ ] = array ('width' => $size );
        }

        $image->multi_resize( $sizes_array );

        delete_user_meta( $user_id, 'synced_profile_photo' );
        update_user_meta( $user_id, 'profile_photo', "profile_photo.{$ext}" ); 
        @unlink( $image_path );

    } 

}

