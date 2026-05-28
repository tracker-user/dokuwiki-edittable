# EditTable plugin for DokuWiki — local fork

## Changes (2026-05-28)

### PHP

- Added `DOKU_INC` guards to `action/editor.php`, `action/jsinfo.php`, `action/sectionjump.php`
- Added explicit `public`/`protected`/`private` visibility to all methods in
  `renderer/inverse.php`, `renderer/json.php`, `action/jsinfo.php`, `action/sectionjump.php`
- Removed deprecated `require_once` from `renderer/json.php` and `renderer/inverse.php`
  (DokuWiki autoloader handles class loading)
- Replaced deprecated `utf8_strlen()` with `mb_strlen()` in `renderer/inverse.php`
- Simplified `strWidth()` in `action/editor.php`: removed `utf8_strlen` fallback
  (PHP 8.3 always provides mbstring)
- Modernized `array()` literals to `[]` throughout `renderer/inverse.php` and `renderer/json.php`
- Fixed null-unsafe string offset access `$text[0]` in `renderer/inverse.php`
- Replaced `join()` with `implode()` in `renderer/inverse.php`
- Removed stale FIXME comments in `renderer/json.php` and `renderer/inverse.php`
- Added docblock to `fill_jsinfo()` in `action/jsinfo.php`
- Fixed typo "containg" → "containing" in `action/sectionjump.php`

### JS

- Fixed `merges === []` comparison (always `false`) in `script/editor.js`;
  replaced with `Array.isArray()` + length check
- Replaced deprecated `.substr()` with `.substring()` in `script/newtable.js`

### CSS

- Removed legacy `-moz-border-radius-*` and `-webkit-border-*-radius` vendor prefixes
  from `less/editbutton.less` (redundant for Firefox 78+ floor)

### Handsontable

- Slimmed `lib/handsontable.full.js` from ~1.5 MB to ~1.1 MB by removing unused
  modules from the [cosmocode fork](https://github.com/tracker-user/cosmocode-handsontable) (`dokuwiki2017` branch) before bundling
- Fixed Ctrl+Z undo/redo regression
