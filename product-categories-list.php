<?php

function display_product_categories_a_to_z_full_hierarchy($atts) {
    $atts = shortcode_atts(
        [
            'taxonomy'   => 'product_cat', // Allows flexibility for other taxonomies
            'hide_empty' => true,          // Option to hide empty categories
        ],
        $atts
    );

    $categories = get_terms([
        'taxonomy'   => $atts['taxonomy'],
        'hide_empty' => $atts['hide_empty'],
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (empty($categories)) {
        return '<p>No categories available.</p>';
    }

    // Group categories by first letter
    $grouped_categories = [];
    foreach ($categories as $category) {
        $first_letter = strtoupper(mb_substr($category->name, 0, 1)); // Support for multibyte characters
        $grouped_categories[$first_letter][] = $category;
    }

    ksort($grouped_categories); // Sort alphabetically by letter

    // Recursive function to display the full hierarchy
    function display_category_hierarchy($category_id, $taxonomy, $hide_empty) {
        $child_categories = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => $hide_empty,
            'parent'     => $category_id,
        ]);

        if (!empty($child_categories)) {
            echo '<ul>';
            foreach ($child_categories as $child) {
                echo '<li>';
                echo '<a href="' . esc_url(get_term_link($child)) . '">' . esc_html($child->name) . '</a>';
                display_category_hierarchy($child->term_id, $taxonomy, $hide_empty); // Recursive call
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    ob_start();
    ?>
    <div class="product-category-a-to-z-list">
        <?php foreach ($grouped_categories as $letter => $categories): ?>
            <div class="category-letter-group">
                <h2 class="category-letter"><?php echo esc_html($letter); ?></h2>
                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item">
                            <div class="category-item-inner">
                                <div class="category-list-top">
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-main">
                                        <h4 class="category-title"><?php echo esc_html($category->name); ?></h4>
                                    </a>
                                </div>
                                <div class="category-list-bottom">
                                    <?php display_category_hierarchy($category->term_id, $atts['taxonomy'], $atts['hide_empty']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_categories_a_to_z_full_hierarchy', 'display_product_categories_a_to_z_full_hierarchy');
