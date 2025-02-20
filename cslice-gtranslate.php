<?php
/**
 * Plugin Name:       Creative Slice - Google Translate
 * Description:       Add GTranslate language translation widget to .cslice-gtranslate-wrapper div, paragraph or icon block.
 * Version:           2025.02.03
 * Requires at least: 6.6
 * Tested up to:      6.7.1
 * Requires PHP:      8.0
 * Author:            Creative Slice
 * Author URI:        https://creativeslice.com
 * License:           GPL-2.0-or-later
 * Text Domain:       cslice-gtranslate
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Plugin updater - PUBLIC REPO
 */
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'cslice-plugin-updater-public.php';
    new CSlice\GTranslate\Plugin_Updater(
        __FILE__,
        'creativeslice/cslice-gtranslate',
        'main'
    );
}

if (!defined('CSLICE_GTRANSLATE_VERSION')) {
	$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
    define('CSLICE_GTRANSLATE_VERSION', $plugin_data['Version']);
}

class CSliceGTranslate {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
    }

    public function enqueue_scripts() {
        if (is_admin()) return;

        wp_enqueue_style(
            'cslice-gtranslate-style',
            plugin_dir_url(__FILE__) . 'style.css',
            [],
            CSLICE_GTRANSLATE_VERSION
        );

        // TODO: Add build process for script.min.js
        wp_enqueue_script(
            'cslice-gtranslate-script',
            plugin_dir_url(__FILE__) . 'script.min.js', // manually compressed
            [],
            CSLICE_GTRANSLATE_VERSION,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        wp_localize_script('cslice-gtranslate-script', 'csliceGTranslate', [
            'languages' => $this->get_languages(),
            'apiUrl' => 'https://translate.google.com/translate_a/element.js',
            'defaultLang' => 'en',
            'includedLanguages' => implode(',', array_keys($this->get_languages()))
        ]);
    }

    // Languages can be overridden by theme.
    private function get_languages() {
        return apply_filters('cslice_gtranslate_languages', [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'zh-CN' => 'Chinese',
            'ar' => 'Arabic'
        ]);
    }
}

new CSliceGTranslate();
