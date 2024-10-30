<?php
    /* Settings page options */

    require_once( plugin_dir_path( __FILE__ ) . 'hd-wp-settings-api/class-hd-wp-settings-api.php' ); // Settings API

    $bsup_options = array(
        'page_title'  => __( 'BS User Products Settings', 'ipe' ),
        'menu_title'  => 'BS User Products',
        'menu_slug'   => 'bsup_options',
        'capability'  => 'manage_options',
        //'parent_slug'  => 'options-general.php',
    );

    $bsup_fields = array(
        'hd_tab_1'      => array(
            'title' => 'General',
            'type'  => 'tab',
        ),
        'bsup_activate' => array(
            'title'   => 'Enable/Disable',
            'type'    => 'radio',
            'default' => 'enable',
            'choices' => array(
                'enable'   => 'Enable',
                'disable'   => 'Disable',
            ),
            'desc'    => 'Enable/Disable user products.',
            'sanit'   => 'nohtml',
        ),
    );

    $bsup_settings = new HD_WP_Settings_API( $bsup_options, $bsup_fields );

?>