<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.3.4
 */

namespace radiustheme\Classima;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
	<!-- Google tag (gtag.js) -->
	<script async src=https://www.googletagmanager.com/gtag/js?id=G-1Z3TEPTDVJ>
	</script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'G-1Z3TEPTDVJ');
	</script>
</head>

<body <?php body_class(); ?>>
	<?php do_action('wp_body_open'); ?>
	<div id="et-post-count">
		<?php
		$count_posts = wp_count_posts('listing_ad');
		$total_posts = $count_posts->publish;
		echo 'Featuring <span>' . $total_posts . '</span> classified listings, and counting! ';
		?>
	</div>
	<a href="/my-account/add-listings/" class="et-mobile-header-btn"><i class="fas fa-plus"></i> Your Ad</a>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content">
			<?php esc_html_e('Skip to content', 'classima'); ?></a>
		<div id="et-header-button">

		</div>
		<?php get_template_part('template-parts/content', 'menu'); ?>
		<div id="content" class="site-content">
			<?php get_template_part('template-parts/content', 'banner'); ?>