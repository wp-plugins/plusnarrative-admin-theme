<?php
/*
Plugin Name: PlusNarrative Admin Theme
Plugin URI: http://plusnarrative.com
Description: PlusNarrative Admin Theme
Author: PlusNarrative (Lliam Scholtz)
Version: 1.1
Author URI: http://plusnarrative.com
*/

// Apply PlusNarrative theme
function pn_admin_theme() {
    wp_enqueue_style('pn-admin-theme', plugins_url('wp-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'pn_admin_theme');
add_action('login_enqueue_scripts', 'pn_admin_theme');


// Remove admin bar items
function remove_wp_logo( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );


// Create PlusNarrative logo and link in admin bar
function pn_tweaked_admin_bar() {
	global $wp_admin_bar;
	$wp_admin_bar->add_node(array(
		'id'    => 'pn-link',
		'title' => 'PlusNarrative',
		'href'  => 'http://plusnarrative.com',
		'meta'  => array( 'id' => 'pn-mini-logo', 'target' => '_blank' )
	));
}
add_action( 'admin_bar_menu', 'pn_tweaked_admin_bar' , 1 ); 


// Clean the dashboard
function pn_clean_dashboard() {

	// Remove welcome panel
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	// Remove dashboard widgets
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );

	// Reorder remaining dashboard widgets
	global $wp_meta_boxes;
	$widget = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'];
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	$wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] = $widget;
}
add_action( 'wp_dashboard_setup', 'pn_clean_dashboard' );


// Add custom dashboard widgets
function pn_add_welcome_widget(){ ?>
    <p>This content management system lets you edit the pages and posts on your website.</p>
    <p>Your site consists of the following content, which you can access via the menu on the left:</p>
    <ul>
        <li><strong>Pages</strong> - static pages which you can edit.</li>
        <li><strong>Posts</strong> - news or blog articles - you can edit these and add more.</li>
        <li><strong>Media</strong> - images and documents which you can upload via the Media menu on the left or within each post or page.</li>
    </ul>
    <p>On each editing screen there are instructions to help you add and edit content.</p>
<?php }
 
function pn_add_links_widget() { ?>
    <p>If you are having any issues please:</p>
    <ul>
        <li><a href="mailto:developers@plusnarrative.com">Email Our Development Team</a></li>
        <li><a href="http://plusnarrative.com">Visit Our Website</a></li>
        <li><a href="tel:+27845890687">Call Us on +27 84 589 0687</a></li>
    </ul>
<?php }

function pn_add_feed_widget() { ?>

<?php // Get RSS Feed(s)
include_once( ABSPATH . WPINC . '/feed.php' );

// Get a SimplePie feed object from the specified feed source.
$rss = fetch_feed( 'http://plusnarrative.com/feed/' );

if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

    // Figure out how many total items there are, but limit it to 5. 
    $maxitems = $rss->get_item_quantity( 5 ); 

    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items( 0, $maxitems );

endif;
?>

<ul>
    <?php if ( $maxitems == 0 ) : ?>
        <li><?php _e( 'No items', 'my-text-domain' ); ?></li>
    <?php else : ?>
        <?php // Loop through each feed item and display each item as a hyperlink. ?>
        <?php foreach ( $rss_items as $item ) : 

        ?>
            <li>
                <p><a style="font-weight:bold;font-size:16px;" href="<?php echo esc_url( $item->get_permalink() ); ?>?uxs=admin-plugin"
                    title="<?php printf( __( 'Posted %s', 'my-text-domain' ), $item->get_date('j F Y | g:i a') ); ?>">
                    <?php echo esc_html( $item->get_title() ); ?>
                </a></p>
                <style type="text/css" media="screen">
                	.blog-post-feed p{
						font-size: 12px !important;
                	}
                </style>
                <div class='blog-post-feed'><?php echo $item->get_description(); ?></div>
            </li>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<?php }


// Add new dashboard widgets
function pn_add_dashboard_widgets() {
    wp_add_dashboard_widget( 'pn_dashboard_welcome', 'Welcome from PlusNarratve', 'pn_add_welcome_widget' );
    wp_add_dashboard_widget( 'pn_dashboard_links', 'Contact PlusNarrative', 'pn_add_links_widget' );
    add_meta_box('pn_dashboard_feed', 'PlusNarrative Blog Feed', 'pn_add_feed_widget', 'dashboard', 'side', 'high');
}
add_action( 'wp_dashboard_setup', 'pn_add_dashboard_widgets' );


// Change the footer
function pn_remove_footer_admin (){
    echo '<span id="footer-thankyou">Content Marketing & Development by <a href="http://plusnarrative.com" target="_blank">PlusNarrative</a></span>';
}
add_filter('admin_footer_text', 'pn_remove_footer_admin');