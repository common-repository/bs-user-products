<?php

function BSUP_product_query( $query_args ){
	$bsup_activate = get_option('bsup_activate');
    if(is_user_logged_in() && current_user_can('customer') && $bsup_activate == "enable"){
        $product_array = array();
        $user_id = get_current_user_id();
        $product_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => 'BSUP_user',
                    'value'   => $user_id,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $products_query = get_posts($product_args);
        foreach($products_query as $pq){
            $product_array[] = $pq->ID;
        }
        $query_args['post__in'] = $product_array;
    }
    return $query_args;
}
add_filter( 'woocommerce_shortcode_products_query', 'BSUP_product_query');

function BSUP_product_query_shop( $query_args ){
	$bsup_activate = get_option('bsup_activate');
    if(is_user_logged_in() && current_user_can('customer') && $bsup_activate == "enable"){
        $product_array = array();
        $user_id = get_current_user_id();
        $product_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => 'BSUP_user',
                    'value'   => $user_id,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $products_query = get_posts($product_args);
        foreach($products_query as $pq){
            $product_array[] = $pq->ID;
        }
        $query_args->query_vars['post__in'] = $product_array;
    }
    return $query_args;
}
add_action( 'woocommerce_product_query', 'BSUP_product_query_shop' );