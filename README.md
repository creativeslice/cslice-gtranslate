# Google Translate - Plugin
Creative Slice GTranslate script to enable simple Google translation using free API.

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

## Customize Languages:
Default languages to select:

- 'en' => 'English', (default)
- 'es' => 'Spanish',
- 'fr' => 'French',
- 'de' => 'German',
- 'zh-CN' => 'Chinese',
- 'ar' => 'Arabic'

To update langauge support, add filter to theme functions.php:
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
- Update from WordPress admin from releases in public repo.
- TODO: Automate plugin zip creation for releases in GitHub
