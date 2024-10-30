<?php
/*
	Plugin Name: BS User Products
	Version: 1.0
	Description: Provides you a field in Woocommerce product add/edit page to assign users. And only those users will be able to see the product if logged in.
	Author: CMS Expert
	Author URI: https://profiles.wordpress.org/cmsexpert
	Plugin URI: 
*/

/**
 *  Make sure the plugin is accessed through the appropriate channels
 */
defined( 'ABSPATH' ) || die;

/**
 * The current version of the Plugin.
 */
define( 'BSUP', '1.0.1' );

// Plugin URL
define( 'BSUP_URL', plugin_dir_url( __FILE__ ) );

/**
 * Including files
 */
require_once( plugin_dir_path( __FILE__ ) . 'bs-user-products-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/woo-hooks.php' );

function bsup_load_wp_admin_style() {
    wp_register_style('bsup_admin_css', plugins_url('css/bsup-admin-css.css', __FILE__));
    wp_enqueue_style( 'bsup_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'bsup_load_wp_admin_style' );

function add_BSUP_product_metaboxes() {
    add_meta_box( 'wpt_BSUP_product_metas', 'Assign Users', 'wpt_BSUP_product_metas', 'product', 'side', 'default' );
}

function wpt_BSUP_product_metas() {
    global $post;

    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="BSUP_product_noncename" id="BSUP_product_noncename" value="' .
    wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

    $customers = get_users(array('role' => 'customer'));
    $selected_customers = explode(",", get_post_meta($post->ID, 'BSUP_user', true));
    $selected = "";
    if(empty($selected_customers)){
        $selected = "selected";
    } else {
        $selected = "";
    }
    $options = "<option ".$selected."></option>";
    foreach($customers as $customer){
        if(in_array($customer->data->ID, $selected_customers)){
            $selected = "selected";
        } else {
            $selected = "";
        }
        $options .= '<option '.$selected.' value="'.$customer->data->ID.'">'.$customer->data->display_name.'</option>';
    }
    echo '<select id="BSUP_user" name="BSUP_user[]" multiple>'.$options.'</select>';
}

function wpt_save_BSUP_product_meta( $post_id, $post ) {
    if ( ! wp_verify_nonce( $_POST['BSUP_product_noncename'], plugin_basename( __FILE__ ) ) ) {
        return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return $post->ID;
    }

    $user_array = array();
    foreach($_POST['BSUP_user'] as $ou){
        if($ou != ""){
            $user_array[] = $ou;
        }
    }
    $BSUP_user = implode(",", $user_array);
    $BSUP_user_data['BSUP_user'] = $BSUP_user;

    foreach ( $BSUP_user_data as $key => $value ) { // Cycle through the $tour_custom_meta array!
        if ( $post->post_type == 'revision' ) {
            return;
        } // Don't store custom data twice
        
        $value = implode( ',', (array) $value ); // If $value is an array, make it a CSV (unlikely)
        if ( get_post_meta( $post->ID, $key, false ) ) { // If the custom field already has a value
            update_post_meta( $post->ID, $key, $value );
        } else { // If the custom field doesn't have a value
            add_post_meta( $post->ID, $key, $value );
        }
        if ( ! $value ) {
            delete_post_meta( $post->ID, $key );
        } // Delete if blank
    }

}

add_action( 'add_meta_boxes', 'add_BSUP_product_metaboxes' );
add_action( 'save_post_product', 'wpt_save_BSUP_product_meta', 1, 2 ); // save the custom fields

function BSUP_admin_script(){
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                if(jQuery("#BSUP_user").length != 0) {                
                    jQuery('#BSUP_user').select2({
                        multiple: true,
                        allowClear: true,
                        placeholder: "Select a user",
                    });
                };
            });
        </script>
    <?php
}
add_action( 'admin_footer', 'BSUP_admin_script', 2500 );

?>