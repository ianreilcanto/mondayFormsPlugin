<?php
/**
 * @package Akismet
 */
/*
Plugin Name: monday.com Forms
Plugin URI: 
Description: Used to Integrate monday.com Forms to Wordpress
Version: 1.0
Requires at least: 5.8
Requires PHP: 5.6.20
Author: MintConsulting - Ian
License: GPLv2 or later
Text Domain: monday.com Forms
*/


if(!class_exists('MintMondayForms')){

    class MintMondayForms {

        public function __construct(){
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'register_settings'));
        }

        // Add admin menu
        public function add_admin_menu() {
            add_options_page('MM Form Settings', 'Monday Form Settings', 'manage_options', 'mint-monday-settings', array($this, 'load_settings_page'));
        }

         // Register settings
         public function register_settings() {
            register_setting('mint-monday-setting-group', 'mint_monday_settings');
        }

        // Load settings page
        public function load_settings_page() {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            require_once plugin_dir_path(__FILE__) . 'views/mint-monday-settings-page.php';
        }


    }

    // Initialize the plugin
    if (class_exists('MintMondayForms')) {
        $mintForms = new MintMondayForms();
    }

}