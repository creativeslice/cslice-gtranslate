# Google Translate - Plugin
Creative Slice GTranslate script to enable simple Google translation using free API.

- https://gtranslate.io/website-translator-widget

## Usage:

To enable, use `.cslice-gtranslate-wrapper` class on any block:

1) *Paragraph block* - text entered will be replaced with translate widget.
2) *Icon block* - two letter language abbreviation will show next to icon (e.g. "EN" or "ES").
3) Use classname `globe` to show a globe icon next to language name.
4) Use classname `globe-white` to show a globe icon in white next to language name.
5) Use classname `globe` and `simple` to show a globe icon with arrow.
```
<!-- wp:paragraph {"className":"cslice-gtranslate-wrapper"} -->
<p class="cslice-gtranslate-wrapper">Translate</p>
<!-- /wp:paragraph -->
```

## Settings:

**Settings > General**, in the "Google Translate" section. There is also a Settings link on the plugin's row on the Plugins screen.

### Switcher style
- **Simple** (default) - the plugin's own dropdown of language names. This is the style the block classnames above (`globe`, `globe-white`, `simple`, icon block) apply to.
- **Flags** - GTranslate's flag dropdown (`float.js`), loaded from `cdn.gtranslate.net`. It renders itself **fixed in the bottom-left corner** of the page, so put the wrapper class on a group block; it does not sit inline where the block is. The paragraph and icon block layouts are for the Simple style only.

- **None** - hides the switcher and loads no CSS or JS at all. Use this to turn translation off without deactivating the plugin.

To place the Flags widget inline instead of floating, use the widget settings filter below:
```
add_filter('cslice_gtranslate_widget_settings', function($settings) {
	$settings['switcher_horizontal_position'] = 'inline';

	return $settings;
});
```

### Languages
One language per line as `code|Label`:

```
en|English
es|Spanish
ar|Arabic
zh-CN|Chinese (Simplified)
```

- The **first line is the language the site is written in** - the language Google translates from.
- Labels are only used by the Simple style. The Flags style supplies its own language names.
- Lines without a valid language code are ignored. Saving an empty box restores the defaults.
- Codes are Google Translate codes (`zh-CN`, `zh-TW`, `iw` for Hebrew, `jw` for Javanese).

Defaults: English, Spanish, French, German, Arabic, Chinese, Japanese.

## Filters:

All filters run after the saved settings, so a theme filter overrides the admin screen.

### Customize Languages
To update language support, add filter to theme functions.php:
```
function cslice_gtranslate_theme_languages($languages) {
	$languages['en'] = 'English';
	$languages['es'] = 'Spanish';
	$languages['ar'] = 'Arabic';
	$languages['zh-CN'] = 'Chinese (Simplified)';
	$languages['fr'] = 'French';
	$languages['de'] = 'German';
	$languages['it'] = 'Italian';
	$languages['ja'] = 'Japanese';
	$languages['ko'] = 'Korean';
	$languages['ne'] = 'Nepali';
	$languages['pt'] = 'Portuguese';

	return $languages;
}
add_filter('cslice_gtranslate_languages', 'cslice_gtranslate_theme_languages');
```

### Switcher Style
```
add_filter('cslice_gtranslate_style', fn() => 'simple');
```

### GTranslate Widget Settings
Flags style only. Passed to `window.gtranslateSettings`; see the widget options at the link above.
```
add_filter('cslice_gtranslate_widget_settings', function($settings) {
	$settings['switcher_horizontal_position'] = 'right'; // left (default), right, inline
	$settings['switcher_vertical_position'] = 'top';     // bottom (default), top
	$settings['float_switcher_open_direction'] = 'bottom';
	$settings['native_language_names'] = true;
	$settings['flag_style'] = '3d';

	return $settings;
});
```

### Disable Plugin Styles
To disable plugin styles, add filter to theme functions.php:
```
add_filter('cslice_gtranslate_load_styles', '__return_false');
```

And add your own theme styles like this:
```
/* Reset Google Translate defaults */
html.translated-ltr body {
	top: 0 !important;
}
.skiptranslate {
	display: none;
}
.cslice-gtranslate-wrapper {
	position: fixed;
	bottom: 1rem;
	left: 1rem;
	z-index: 9999;
}
```

## Updates:
- No auto-updater. Install new releases manually (upload/replace plugin files).
- Build a release zip with `npm run plugin-zip` (requires `npm install` first), or `npm run release` to format + build + zip in one step.

## CHANGELOG

### 2026-08-03
- Removed the GitHub-releases auto-updater; installs are manual now.
- Added `@wordpress/scripts` build tooling: `src/` is now the JS/CSS source, `npm run build` outputs to `build/`, `npm run plugin-zip` produces a version-stamped release zip.

### 2026-07-24
- Added Settings > General options for switcher style (Simple / Flags / None) and the language list.
- Added Flags style, using the GTranslate.io float widget.
- Default language now comes from the first language in the list instead of being hardcoded to English.
- Added `cslice_gtranslate_style` and `cslice_gtranslate_widget_settings` filters.
