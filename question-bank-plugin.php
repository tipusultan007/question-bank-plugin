<?php
/*
Plugin Name: Question Bank Plugin
Description: A plugin to manage and display a question bank.
Version: 1.0
Author: Solution Clime
*/

// Ensure this file is included in the WordPress environment
if (!defined('ABSPATH')) {
	exit;
}

// Include the main class file
include_once plugin_dir_path(__FILE__) . 'includes/class-question-bank-plugin.php';

// Initialize the plugin
function question_bank_plugin_init() {
	$plugin = new Question_Bank_Plugin();
	$plugin->run();
}
add_action('plugins_loaded', 'question_bank_plugin_init');

 function enqueue_scripts() {
	$plugin_version = '1.0.0'; 
	wp_enqueue_script('jquery');
	wp_enqueue_style('question-bank-style', plugins_url('css/style.css', __FILE__), array(), $plugin_version);
	wp_enqueue_script('question-bank-script', plugins_url('js/script.js', __FILE__), array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

function enqueue_custom_styles() {
    $plugin_version = '1.0.0'; 
    wp_enqueue_style('custom-meta-box-styles', plugins_url('css/custom-meta-box-styles.css', __FILE__), array(), $plugin_version);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_styles');

