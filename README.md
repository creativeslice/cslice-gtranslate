# Google Translate - Plugin
Creative Slice GTranslate script to enable simple Google translation using free API.

## Usage:

To enable, use `.cslice-gtranslate-wrapper` class on any block:

1) *Paragraph block* - text entered will be replaced with translate widget.
2) *Icon block* - two letter language abbreviation will show next to icon (e.g. "EN" or "ES").
3) Use classname `globe` to show a globe icon next to language name.
4) Use classname `globe-white` to show a globe icon in white next to language name.
5) Use classname `globe` and `simple` to show a globe icon with arrow.

## Customize Languages:
Default languages to select:

- 'en' => 'English', (default)
- 'es' => 'Spanish',
- 'fr' => 'French',
- 'de' => 'German',
- 'zh-CN' => 'Chinese',
- 'ar' => 'Arabic'

To update langauge support, add filter to theme functions.php file.
```
function cslice_gtranslate_theme_languages($languages) {
  $languages['en'] = 'English';
  $languages['es'] = 'Spanish';
  return $languages;
}
add_filter('cslice_gtranslate_languages', 'cslice_gtranslate_theme_languages');
```

## Updates:
- Update from WordPress admin from releases in public repo.
- TODO: Automate plugin zip creation for releases in GitHub
