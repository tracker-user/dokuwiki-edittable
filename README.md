# EditTable plugin for DokuWiki — local fork

## Changes (2026-06-01)

### PHP

- Added `DOKU_INC` guards to all non-namespaced action files (`action/newtable.php`,
  `action/preprocess.php`; previously missed in the 2026-05-28 pass)
- Added `public` visibility to `register()` in `action/newtable.php` (checklist #3)
- Fixed PHP 8.3 compile-time deprecation in `renderer/inverse.php`: `interwikilink()`
  had `$name = null` before required parameters; dropped the default
- Fixed `TypeError: count(): Argument #1 must be of type Countable|array, null given`
  in `action/editor.php::handle_table_post()`: `json_decode()` returns `null` on invalid
  JSON; added `is_array()` guard before calling `build_table()`
- Simplified `strWidth()` in `action/editor.php` to `mb_strwidth()` directly
  (PHP 8.3 always provides mbstring; removed the now-dead `UTF8_MBSTRING` conditional
  and the `use dokuwiki\Utf8` import)
- Corrected `getDataJSON()`/`getMetaJSON()` docblocks in `renderer/json.php`:
  `@return array` → `@return string`; added PHP 8 return type hints
- Flipped `private` → `protected` on all state properties in `renderer/inverse.php`
  and `renderer/json.php`, and on `_table_to_wikitext()`, `_tablefield_open/close()`
  (house style; allows subclass/plugin overrides)
- Modernized remaining `array()` literals to `[]` in `action/editor.php` and
  `action/newtable.php`; dropped unnecessary by-ref `&$Renderer` in `call_user_func_array`

### JS

- Restored resize-on-paste: `script/editor.js` wrapped `window.pasteText()` to trigger
  `AutoResizer.check()` after toolbar insertions, but the wrapper was immediately
  overwritten by the original reference on the very next line — deleted the overwrite

### CSS

- Removed dead parametric mixin `.a() when (@ini_site_width)` from `less/editor.less`;
  it was defined but never called so the `#edittable__editor` site-width override
  was silently no-op

### Performance: conditional JS loading

- `action/scripts.php` (new): registers `TPL_METAHEADER_OUTPUT` BEFORE to inject
  Handsontable + contextmenu/editor/newtable scripts only on `edit`/`preview` actions
- `script.js` stripped to `editbutton.js` only (the edit-button overlay needed on
  page-view); the ~1.1 MB Handsontable bundle is now absent from every non-edit page

## Changes (2026-05-28)

### PHP

- Added `DOKU_INC` guards to `action/editor.php`, `action/jsinfo.php`, `action/sectionjump.php`
- Added explicit `public`/`protected`/`private` visibility to all methods in
  `renderer/inverse.php`, `renderer/json.php`, `action/jsinfo.php`, `action/sectionjump.php`
- Removed deprecated `require_once` from `renderer/json.php` and `renderer/inverse.php`
  (DokuWiki autoloader handles class loading)
- Replaced deprecated `utf8_strlen()` with `mb_strlen()` in `renderer/inverse.php`
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
