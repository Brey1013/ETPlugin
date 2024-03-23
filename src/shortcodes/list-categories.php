<?php



if (!defined('ABSPATH')) {

    exit; // Exit if accessed directly.

}



require $PLUGIN_ABSPATH . '/constants/index.php';



function et_list_categories()

{


$taxonomy = 'ad_category';
$terms = get_terms($taxonomy); // Get all terms of a taxonomy

if ( $terms && !is_wp_error( $terms ) ) :
?>
    <ul>
        <?php foreach ( $terms as $term ) { ?>
            <li><a href="<?php echo get_term_link($term->slug, $taxonomy); ?>"><?php echo $term->name; ?></a></li>
        <?php } ?>
    </ul>
<?php endif;?>




    <?php

}



add_shortcode('et-list-categorieset-list-categories', 'et_list_categories');

