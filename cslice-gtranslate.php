<?php
/**
 * Plugin Name:       Creative Slice - Google Translate Widget
 * Plugin URI:        https://github.com/creativeslice/cslice-gtranslate
 * Description:       Add GTranslate language translation widget to .cslice-gtranslate-wrapper div, paragraph or icon block. Style and languages are configured under Settings > General.
 * Version:           26.08.18
 * Requires at least: 6.6
 * Tested up to:      7.1
 * Requires PHP:      8.0
 * Author:            Creative Slice
 * Author URI:        https://creativeslice.com
 * License:           GPL-2.0-or-later
 * Text Domain:       cslice-gtranslate
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if (!defined('CSLICE_GTRANSLATE_VERSION')) {
	$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
    define('CSLICE_GTRANSLATE_VERSION', $plugin_data['Version']);
}

if (!defined('CSLICE_GTRANSLATE_FILE')) {
    define('CSLICE_GTRANSLATE_FILE', __FILE__);
}

// Front end needs the getters too, so this loads on every request.
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

class CSliceGTranslate {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
    }

    public function enqueue_scripts() {
        if (is_admin()) return;

        $style = cslice_gtranslate_get_style();

        // Nothing loads at all when the switcher is turned off.
        if ($style === 'none') return;

        $asset = require plugin_dir_path(__FILE__) . 'build/index.asset.php';

        /**
         * Allow themes to disable default styles
         *
         * @use: add_filter('cslice_gtranslate_load_styles', '__return_false');
         */
        if (apply_filters('cslice_gtranslate_load_styles', true)) {
            wp_enqueue_style(
                'cslice-gtranslate-style',
                plugin_dir_url(__FILE__) . 'build/style-index.css',
                [],
                $asset['version']
            );
            wp_style_add_data('cslice-gtranslate-style', 'rtl', 'replace');
        }

        $languages = cslice_gtranslate_get_languages();

        // First language in the list is the one the site is written in.
        $default_lang = array_key_first($languages) ?: 'en';

        if ($style === 'flags') {
            $this->enqueue_flags_widget($languages, $default_lang);
            return;
        }

        wp_enqueue_script(
            'cslice-gtranslate-script',
            plugin_dir_url(__FILE__) . 'build/index.js',
            $asset['dependencies'],
            $asset['version'],
            ['strategy' => 'defer', 'in_footer' => true]
        );

        wp_localize_script('cslice-gtranslate-script', 'csliceGTranslate', [
            'languages' => $languages,
            'apiUrl' => 'https://translate.google.com/translate_a/element.js',
            'defaultLang' => $default_lang,
            'includedLanguages' => implode(',', array_keys($languages))
        ]);
    }

    /**
     * Flags style: GTranslate.io's float widget (flag dropdown), which loads
     * Google Translate itself. The plugin's own switcher script is not enqueued
     * in this mode.
     */
    private function enqueue_flags_widget($languages, $default_lang) {
        wp_enqueue_script(
            'cslice-gtranslate-flags',
            'https://cdn.gtranslate.net/widgets/latest/float.js',
            [],
            null,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        // GTranslate's own defaults handle the rest (fixed bottom-left, 2d flags).
        $settings = apply_filters('cslice_gtranslate_widget_settings', [
            'default_language' => $default_lang,
            'languages' => array_keys($languages),
            'wrapper_selector' => '.cslice-gtranslate-wrapper',
        ]);

        // Paragraph wrappers hold placeholder text the widget would append to.
        wp_add_inline_script(
            'cslice-gtranslate-flags',
            'window.gtranslateSettings = ' . wp_json_encode($settings) . ';'
            . "document.querySelectorAll('p.cslice-gtranslate-wrapper').forEach(function(el){el.textContent='';});",
            'before'
        );
    }
}

new CSliceGTranslate();
