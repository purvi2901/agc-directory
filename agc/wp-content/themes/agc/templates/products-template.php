<?php
/**
 * Template Name: Products Template
 *
 */
get_header ();

$query_args = array(
    'post_type' => 'product',
    'posts_per_page' => 10, // Display 10 products
    'orderby' => 'date', // Default sorting by date
    'order' => 'DESC'
);


// Query the products
$products_query = new WP_Query($query_args);

?>

<!-- Display the custom AJAX filter dropdown -->
    <label for="product-filter">Filter by:</label>
    <select name="product_filter" id="product-filter">
        <option value="">Default</option>
        <option value="popular">Show only Popular products</option>
        <option value="featured">Show only Featured products</option>
    </select>
    <button class="filter-button">Filter</button>

<!-- Display the product list -->
<div class="product-list">
    <?php
    // Check if there are products to display
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            ?>
            <div class="product-item">
                <h2><?php the_title(); ?></h2>
                <a href="<?php echo get_the_permalink (); ?>">View</a>
                <!-- Display other product information as needed -->
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No products found.</p>';
    }
    ?>
</div>




<?php get_footer (); ?>