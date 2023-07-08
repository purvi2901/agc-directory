<?php
add_action ( 'after_setup_theme', 'agc_setup' );

function agc_setup() {
    add_theme_support ( 'post-thumbnails' );
    set_post_thumbnail_size ( 1200, 9999 );
    add_theme_support ( 'woocommerce' );
    add_theme_support ( 'wc-product-gallery-zoom' );
    add_theme_support ( 'wc-product-gallery-lightbox' );
    add_theme_support ( 'wc-product-gallery-slider' );
}

function agc_admin_scripts() {
    global $wp_version;
    // Load our admin script.
    wp_enqueue_script ( 'agc-admin-script', get_template_directory_uri () . '/js/admin-script.js' );
    //localize admin script
    wp_localize_script ( 'agc-admin-script', 'AGCADMIN', array (
        'ajaxurl' => admin_url ( 'admin-ajax.php', ( is_ssl () ? 'https' : 'http' ) ),
    ) );
    wp_enqueue_media ();
}

add_action ( 'wp_enqueue_scripts', 'agc_public_scripts' );

function agc_public_scripts() {

    wp_enqueue_style ( 'agc-style', get_stylesheet_uri (), array (), NULL );
// Load main jquery
    wp_enqueue_script ( 'jquery', array (), NULL );
// Load public script
    wp_enqueue_script ( 'agc-public-script', get_template_directory_uri () . '/js/public-script.js', array (), NULL, true );
//localize public script
    wp_localize_script ( 'agc-public-script', 'AGCPUBLIC', array (
        'ajaxurl' => admin_url ( 'admin-ajax.php', ( is_ssl () ? 'https' : 'http' ) ),
    ) );
}

add_action ( 'woocommerce_product_options_general_product_data', 'add_product_popular_field' );

function add_product_popular_field() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    woocommerce_wp_checkbox (
            array (
                'id'          => '_product_popular',
                'label'       => __ ( 'Popular Product', 'text-domain' ),
                'description' => __ ( 'Mark as popular product', 'text-domain' )
            )
    );

    echo '</div>';
}

add_action ( 'woocommerce_process_product_meta', 'save_product_popular_field' );

function save_product_popular_field($post_id) {
    $product    = wc_get_product ( $post_id );
    $is_popular = isset ( $_POST[ '_product_popular' ] ) ? 'yes' : 'no';
    $product->update_meta_data ( '_product_popular', $is_popular );
    $product->save ();
}

add_action ( 'wp_ajax_filter_products', 'filter_products' );
add_action ( 'wp_ajax_nopriv_filter_products', 'filter_products' );

function filter_products() {
    $filter = isset ( $_POST[ 'filterValue' ] ) ? $_POST[ 'filterValue' ] : '';

    // Custom query arguments based on filter selection
    $query_args = array (
        'post_type'      => 'product',
        'posts_per_page' => 10, // Display 10 products
        'orderby'        => 'date', // Default sorting by date
        'order'          => 'DESC'
    );

    // Apply filter based on selection
    if( $filter === 'popular' ) {
        $query_args[ 'meta_key' ]   = '_product_popular';
        $query_args[ 'meta_value' ] = 'yes';
    } elseif( $filter === 'featured' ) {
        $query_args[ 'tax_query' ] = array (
            array (
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN'
            )
        );
    }

    // Query the products
    $products_query = new WP_Query ( $query_args );

    ob_start ();

    // Display the product list
    if( $products_query->have_posts () ) {
        while ( $products_query->have_posts () ) {
            $products_query->the_post ();
            ?>
            <div class="product-item">
                <h2><?php the_title (); ?></h2>
                <a href="<?php echo get_the_permalink (); ?>">View</a>
                <!-- Display other product information as needed -->
            </div>
            <?php
        }
        wp_reset_postdata ();
    } else {
        echo '<p>No products found.</p>';
    }

    $response = ob_get_clean ();
    echo $response;
    if( isset ( $_POST ) && ! empty ( $_POST ) ) {
        exit ();
    }
}

add_filter ( 'add_to_cart_redirect', 'cw_redirect_add_to_cart' );

function cw_redirect_add_to_cart() {
    global $woocommerce;
    $cw_redirect_url_checkout = $woocommerce->cart->get_checkout_url ();
    return $cw_redirect_url_checkout;
}

add_filter ( 'wc_add_to_cart_message', function ($string, $product_id = 0) {

    $start = strpos ( $string, '<a href=' ) ?: 0;
    $end   = strpos ( $string, '</a>', $start ) ?: 0;

    return substr ( $string, $end ) ?: $string;
} );

