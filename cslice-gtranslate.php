<?php
/**
 * Plugin Name:       Creative Slice - Google Translate
 * Description:       Add GTranslate widget to #google-translate-wrapper div
 * Version:           2024.11.29
 * Requires at least: 6.6
 * Tested up to:      6.7.1
 * Requires PHP:      8.0
 * Author:            Creative Slice
 * Author URI:        https://creativeslice.com
 * License:           GPL-2.0-or-later
 * Text Domain:       cslice-gtranslate
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

define('CSLICE_GTRANSLATE_VERSION', '2024.11.29');
define('CSLICE_GTRANSLATE_PATH', plugin_dir_path(__FILE__));
define('CSLICE_GTRANSLATE_URL', plugin_dir_url(__FILE__));

class CSliceGTranslate {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
    }

    public function enqueue_scripts() {
        if (is_admin()) return;

        wp_enqueue_style('cslice-gtranslate-style',
            CSLICE_GTRANSLATE_URL . 'style.css',
            [],
            CSLICE_GTRANSLATE_VERSION
        );

        // Plugin scripts (currently manually compressed)
        wp_enqueue_script('cslice-gtranslate-script',
            CSLICE_GTRANSLATE_URL . 'script.min.js',
            [],
            CSLICE_GTRANSLATE_VERSION,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        // Localize script
        wp_localize_script('cslice-gtranslate-script', 'csliceGTranslate', [
            'languages' => $this->get_languages(),
            'apiUrl' => 'https://translate.google.com/translate_a/element.js',
            'defaultLang' => 'en',
            'includedLanguages' => implode(',', array_keys($this->get_languages()))
        ]);
    }

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
