<?php
/**
 * Settings, registered onto the core Settings > General screen.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const CSLICE_GTRANSLATE_OPTION = 'cslice_gtranslate_settings';
const CSLICE_GTRANSLATE_STYLES = [ 'simple', 'flags', 'none' ];

/**
 * Default language list. Order matters: the first entry is the site's own
 * language, which is what Google translates *from*.
 *
 * @return array<string, string>
 */
function cslice_gtranslate_default_languages() {
	return [
		'en'    => 'English',
		'es'    => 'Spanish',
		'fr'    => 'French',
		'de'    => 'German',
		'ar'    => 'Arabic',
		'zh-CN' => 'Chinese (Simplified)',
		'ja'    => 'Japanese',
		'it'    => 'Italian',
		'ko'    => 'Korean',
		'ne'    => 'Nepali',
		'pt'    => 'Portuguese',
	];
}

/**
 * The saved option, always as an array.
 *
 * @return array
 */
function cslice_gtranslate_get_option() {
	$option = get_option( CSLICE_GTRANSLATE_OPTION, [] );
	return is_array( $option ) ? $option : [];
}

/**
 * Switcher style: `simple` (built-in select) or `flags` (GTranslate widget).
 *
 * @return string
 */
function cslice_gtranslate_get_style() {
	$style = cslice_gtranslate_get_option()['style'] ?? 'simple';

	/**
	 * Allow themes to override the saved style.
	 *
	 * @use: add_filter('cslice_gtranslate_style', fn() => 'flags');
	 */
	$style = apply_filters( 'cslice_gtranslate_style', $style );

	return in_array( $style, CSLICE_GTRANSLATE_STYLES, true ) ? $style : 'simple';
}

/**
 * Active language list, saved settings first and the theme filter last.
 *
 * @return array<string, string>
 */
function cslice_gtranslate_get_languages() {
	$languages = cslice_gtranslate_get_option()['languages'] ?? [];
	if ( ! is_array( $languages ) || empty( $languages ) ) {
		$languages = cslice_gtranslate_default_languages();
	}

	// Languages can be overridden by theme (see README for example)
	return apply_filters( 'cslice_gtranslate_languages', $languages );
}

/**
 * Parse the `code|Label` textarea into an ordered code => label map.
 * Malformed lines are skipped rather than failing the whole save.
 *
 * @param array|string $text
 * @return array<string, string>
 */
function cslice_gtranslate_parse_languages( $text ) {
	if ( is_array( $text ) ) {
		return $text;
	}
	if ( ! is_string( $text ) ) {
		return [];
	}

	$languages = [];

	foreach ( preg_split( '/\r\n|\r|\n/', $text ) as $line ) {
		$parts = explode( '|', $line, 2 );
		$code  = trim( $parts[0] );

		if ( ! preg_match( '/^[a-zA-Z]{2,3}(-[a-zA-Z]{2,4})?$/', $code ) ) {
			continue;
		}

		$label = isset( $parts[1] ) ? sanitize_text_field( trim( $parts[1] ) ) : '';
		$languages[ $code ] = '' !== $label ? $label : $code;
	}

	return $languages;
}

/**
 * Render a code => label map back into textarea lines.
 *
 * @param array $languages
 * @return string
 */
function cslice_gtranslate_languages_to_text( array $languages ) {
	$lines = [];
	foreach ( $languages as $code => $label ) {
		$lines[] = $code . '|' . $label;
	}
	return implode( "\n", $lines );
}

add_action( 'admin_init', 'cslice_gtranslate_register_settings' );
function cslice_gtranslate_register_settings() {
	register_setting(
		'general',
		CSLICE_GTRANSLATE_OPTION,
		[
			'type'              => 'array',
			'sanitize_callback' => 'cslice_gtranslate_sanitize',
			'default'           => [
				'style'     => 'simple',
				'languages' => cslice_gtranslate_default_languages(),
			],
		]
	);

	add_settings_section(
		'cslice_gtranslate_section',
		__( 'Google Translate', 'cslice-gtranslate' ),
		'cslice_gtranslate_section_intro',
		'general'
	);

	add_settings_field(
		'cslice_gtranslate_style',
		__( 'Switcher style', 'cslice-gtranslate' ),
		'cslice_gtranslate_field_style',
		'general',
		'cslice_gtranslate_section'
	);

	add_settings_field(
		'cslice_gtranslate_languages',
		__( 'Languages', 'cslice-gtranslate' ),
		'cslice_gtranslate_field_languages',
		'general',
		'cslice_gtranslate_section'
	);
}

/**
 * Add a "Settings" link to the plugin's row on the Plugins list screen,
 * jumping to this plugin's section on Settings > General.
 */
add_filter( 'plugin_action_links_' . plugin_basename( CSLICE_GTRANSLATE_FILE ), 'cslice_gtranslate_action_links' );
function cslice_gtranslate_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php#cslice-gtranslate-settings' ) ),
		esc_html__( 'Settings', 'cslice-gtranslate' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}

/**
 * An empty language list would render a switcher with nothing in it, so an
 * empty save falls back to the defaults rather than storing nothing.
 *
 * @param mixed $input
 * @return array
 */
function cslice_gtranslate_sanitize( $input ) {
	$input = is_array( $input ) ? $input : [];

	$style = $input['style'] ?? 'simple';
	if ( ! in_array( $style, CSLICE_GTRANSLATE_STYLES, true ) ) {
		$style = 'simple';
	}

	$languages = cslice_gtranslate_parse_languages( $input['languages'] ?? '' );
	if ( empty( $languages ) ) {
		$languages = cslice_gtranslate_default_languages();
	}

	return [
		'style'     => $style,
		'languages' => $languages,
	];
}

/**
 * Anchor only. Usage guidance lives in the README rather than on a core
 * settings screen shared with WordPress's own sections.
 */
function cslice_gtranslate_section_intro() {
	echo '<span id="cslice-gtranslate-settings"></span>';
}

function cslice_gtranslate_field_style() {
	$current = cslice_gtranslate_get_option()['style'] ?? 'simple';
	$styles  = [
		'simple' => __( 'Simple - language names', 'cslice-gtranslate' ),
		'flags'  => __( 'Flags - GTranslate flag dropdown', 'cslice-gtranslate' ),
		'none'   => __( 'None - hide the switcher', 'cslice-gtranslate' ),
	];

	echo '<select name="' . esc_attr( CSLICE_GTRANSLATE_OPTION ) . '[style]">';
	foreach ( $styles as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
}

function cslice_gtranslate_field_languages() {
	$languages = cslice_gtranslate_get_option()['languages'] ?? cslice_gtranslate_default_languages();

	echo '<textarea name="' . esc_attr( CSLICE_GTRANSLATE_OPTION ) . '[languages]" rows="5" class="regular-text code">'
		. esc_textarea( cslice_gtranslate_languages_to_text( $languages ) )
		. '</textarea>';

	printf(
		'<p class="description">%s <a href="%s" target="_blank" rel="noopener">%s</a></p>',
		esc_html__( 'One per line as code|Label. The first is the default.', 'cslice-gtranslate' ),
		esc_url( 'https://gtranslate.io/website-translator-widget' ),
		esc_html__( 'Available languages and codes', 'cslice-gtranslate' )
	);
}
