<?php 

// Function to get the seller's name
function get_seller_name() {
    if (is_product()) { // Check if on a product page
        global $post;

        // Get the vendor ID from the product post
        $vendor_id = get_post_field('post_author', $post->ID);

        // Retrieve the vendor's display name
        $vendor_name = get_the_author_meta('display_name', $vendor_id);

        return $vendor_name ?: 'Unknown Seller'; // Return 'Unknown Seller' if name is not found
    }

    return ''; // Return an empty string if not on a product page
}

// Create a shortcode for the seller's name
add_shortcode('seller_name', 'get_seller_name');
