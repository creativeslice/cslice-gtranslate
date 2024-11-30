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

// Uses
class CSliceGTranslate {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
    }

    public function enqueue_scripts() {
        if (is_admin()) return;

        wp_enqueue_style(
            'cslice-gtranslate-styles',
            CSLICE_GTRANSLATE_URL . 'style.css',
            [],
            CSLICE_GTRANSLATE_VERSION
        );

        // Plugin scripts
        wp_enqueue_script(
            'cslice-gtranslate-script',
            CSLICE_GTRANSLATE_URL . 'cslice-gtranslate.js',
            [],
            CSLICE_GTRANSLATE_VERSION,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        // Pass ALL necessary data to JavaScript
        wp_localize_script('cslice-gtranslate-script', 'csliceGTranslate', [
            'languages' => $this->get_languages(),
            'apiUrl' => 'https://translate.google.com/translate_a/element.js',
            'defaultLang' => 'en',
            'includedLanguages' => implode(',', array_keys($this->get_languages()))
        ]);
    }

    /**
     * To update languages from theme:
     * add_filter('cslice_gtranslate_languages', 'cslice_gtranslate_theme_languages');
     * function cslice_gtranslate_theme_languages($languages) {
     *    $languages['en'] = 'English';
     *    $languages['es'] = 'Spanish';
     *   return $languages;
     * }
     */

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
