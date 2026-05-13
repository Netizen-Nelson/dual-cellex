# dual-cellex + DualCell.php

> A flexible, theme-driven grid component with per-cell menus, overlays, carousels,  
> collapsible groups, and progressive row reveal — paired with a PHP class for direct database rendering.

![No Dependencies](https://img.shields.io/badge/dependencies-Bootstrap_Icons_only-40c99a?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-C3A5E5?style=flat-square&logo=php)
![License](https://img.shields.io/badge/license-MIT-08a9d1?style=flat-square)
![Custom Elements](https://img.shields.io/badge/Web_Components-Custom_Elements-C8DD5A?style=flat-square)

---

## What is this?

**dual-cellex** is a data-grid Web Component (`<dual-cell>`) that turns structured HTML
into an interactive table — with no DOM manipulation needed from the outside world.

Key capabilities:

- **Per-cell action menus**: push, pull, copy, swap, clear, toggle, put, show-next
- **Overlays**: one or two click-to-reveal layers per cell
- **Vertical carousel**: auto-rotate multiple items inside a single cell
- **Collapsible groups**: themed group headers that fold/unfold their rows
- **Progressive reveal**: hidden rows that appear on demand (`show-next`) or on a timer (`auto-reveal-interval`)
- **Toggle slots**: expandable detail panels beneath any row
- **Hover preview**: mouseover a cell to populate a target element elsewhere on the page

The companion **DualCell.php** renders all markup from PHP variables or database
queries, handling attributes, escaping, and newline conversion automatically.

> Requires [Bootstrap Icons](https://icons.getbootstrap.com/) for menu button icons.  
> Optimised for desktop / widescreen layouts.

---

## Files

```
your-project/
├── dual-cellex.js    ← Web Component (CSS injected per-instance automatically)
├── DualCell.php      ← PHP render class
└── your-page.php     ← your page, require DualCell.php
```

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Initialisation Methods](#initialisation-methods)
3. [HTML Structure](#html-structure)
4. [PHP Class Usage](#php-class-usage)
5. [Menu Actions](#menu-actions)
6. [Overlay (Click-to-Reveal)](#overlay-click-to-reveal)
7. [Vertical Carousel](#vertical-carousel)
8. [Groups](#groups)
9. [Progressive Row Reveal](#progressive-row-reveal)
10. [Toggle Slot](#toggle-slot)
11. [Hover Preview](#hover-preview)
12. [Themes](#themes)
13. [Options Reference](#options-reference)
14. [Escape & Newline Handling](#escape--newline-handling)
15. [License](#license)

---

## Quick Start

### Plain HTML (Custom Element)

```html
<!-- Bootstrap Icons -->
<link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<dual-cell id="my-grid" theme="sky" cols="2"
  menu-button-icon-push="bi-box-arrow-right"
  target-id="output">

  <dual-col>欄位名稱</dual-col>
  <dual-col menu-action="push">欄位內容</dual-col>

</dual-cell>

<div id="output"></div>

<script src="dual-cellex.js"></script>
```

### PHP + Database

```php
<?php
require_once 'DualCell.php';

$drug = $pdo->query("SELECT * FROM drugs WHERE id = 1")->fetch();

// Render entire DB row as key/value pairs
echo DualCell::open('drug-table', [
    'theme'                  => 'warning',
    'menu_button_icon_push'  => 'bi-box-arrow-right',
    'target_id'              => 'push-area',
]);
echo DualCell::rowsFromArray(
    ['藥名' => $drug['name'], '劑量' => $drug['dosage'], '禁忌' => $drug['contraindication']],
    ['width' => '120px', 'show_menu' => false],  // key column
    ['menu_action' => 'push']                    // value column
);
echo DualCell::close();

echo '<div id="push-area"></div>';
echo DualCell::script('/assets/dual-cellex.js');
?>
```

---

## Initialisation Methods

Three ways to initialise — all equivalent in behaviour.

### 1. Custom Element `<dual-cell>`

Automatically initialises when the browser parses the element.

```html
<dual-cell id="grid-1" theme="sky" cols="3">
  ...
</dual-cell>
```

### 2. `data-dual-cell` attribute on a `<div>`

Initialised on `DOMContentLoaded`.

```html
<div id="grid-2" data-dual-cell data-theme="info" data-cols="2">
  ...
</div>
```

### 3. JavaScript constructor

```js
const grid = new DualCell('grid-3', {
  theme: 'safe',
  cols: 2,
  onCellClick: (row, col) => console.log(row, col),
});
```

---

## HTML Structure

```
<dual-cell>  or  <div data-dual-cell>       ← container
  <dual-group title="Section Name">         ← optional collapsible group
    <dual-row>                              ← one row
      <dual-col>cell content</dual-col>     ← one cell
      <dual-col>
        <dual-item>carousel item 1</dual-item>   ← carousel: 2+ items
        <dual-item>carousel item 2</dual-item>
      </dual-col>
      <dual-slot>toggle detail HTML</dual-slot>  ← optional expandable slot
    </dual-row>
  </dual-group>
  <dual-col>flat cell (no row wrapper)</dual-col>  ← auto-grouped into rows
</dual-cell>
```

Flat `<dual-col>` elements (without a `<dual-row>` wrapper) are automatically
grouped into rows based on the `cols` option.

---

## PHP Class Usage

### Container

```php
// div style
echo DualCell::open('grid-id', $options);
// ... rows ...
echo DualCell::close();

// custom element style
echo DualCell::element('grid-id', $options);
// ... rows ...
echo DualCell::elementClose();
```

### Rows

```php
echo DualCell::row(
    [DualCell::col('A'), DualCell::col('B')],
    ['hidden' => true, 'auto_reveal_delay' => 800]
);
```

### Columns

```php
// HTML content (escape = false by default)
DualCell::col('<strong>Title</strong>', ['menu_action' => 'push'])

// Plain text — htmlspecialchars + nl2br forced
DualCell::colText($db['note'], ['width' => '200px'])

// Carousel — 2+ items auto-activates rotation
DualCell::colCarousel(['Item A', 'Item B', 'Item C'], ['carousel_interval' => 3000])
```

### Batch rows from DB row

```php
// Single key/value pair → one row
echo DualCell::rowFromPair('藥名', $drug['name'], $keyOpts, $valOpts);

// Entire associative array → multiple rows
echo DualCell::rowsFromArray(
    ['藥名' => $drug['name'], '劑量' => $drug['dosage']],
    ['width' => '120px', 'show_menu' => false],  // key column options
    ['menu_action' => 'copy']                    // value column options
);
```

### Groups

```php
echo DualCell::groupOpen('Group Title', [
    'title_icon'    => 'bi-folder',
    'title_right'   => '3 items',
    'collapsed'     => false,
    'collapsed_icon'=> 'bi-chevron-right',
    'expanded_icon' => 'bi-chevron-down',
    'title_color'   => '#1C1C1E',
    'title_bg_color'=> '#08a9d1',
]);
// ... rows ...
echo DualCell::groupClose();
```

### Script tag

```php
// Place before </body>. Safe to call multiple times — renders only once.
echo DualCell::script('/assets/dual-cellex.js');
```

---

## Menu Actions

Set via `menu-action` attribute on `<dual-col>` or `'menu_action'` PHP option.  
Each action needs its corresponding icon option set on the container.

| Action | Container icon option | Description |
|---|---|---|
| `push` | `menu_button_icon_push` | Copy cell content → target element |
| `pull` | `menu_button_icon_pull` | Replace cell content ← target element |
| `copy` | `menu_button_icon_copy` | Copy cell text to clipboard |
| `swap` | `menu_button_icon_swap` | Swap content with adjacent column |
| `clear` | `menu_button_icon_clear` | Clear cell content (with confirm) |
| `toggle` | `menu_button_icon_toggle` | Expand / collapse the row's slot |
| `put` | `menu_button_icon_put` | Place content into a specific cell by id |
| `show-next` | `menu_button_icon_show_next` | Reveal the next hidden row |

The `target` attribute on `<dual-col>` (or `'target'` PHP option) overrides the
container-level `target-id` for `push`, `pull`, `toggle`, and `put` actions.

---

## Overlay (Click-to-Reveal)

Add up to two stacked click-to-reveal overlays on any cell.

```html
<dual-col
  overlay-1-text="⚠ 隱私資料"
  overlay-1-color="warning"
  overlay-2-text="點擊解鎖"
  overlay-2-color="sky"
>
  hidden content
</dual-col>
```

```php
DualCell::col($content, [
    'overlay_1_text'  => '⚠ 隱私資料',
    'overlay_1_color' => 'warning',
    'overlay_2_text'  => '點擊解鎖',
    'overlay_2_color' => 'sky',
])
```

Layer 1 sits on top; layer 2 is beneath. Each click removes one layer.  
Add `overlay-invert` / `'overlay_invert' => true` to flip to a light-on-colour style.

---

## Vertical Carousel

Place two or more `<dual-item>` children inside a `<dual-col>` to activate auto-rotation.

```html
<dual-col carousel-interval="3000">
  <dual-item>Content A</dual-item>
  <dual-item>Content B</dual-item>
  <dual-item>Content C</dual-item>
</dual-col>
```

```php
DualCell::colCarousel(['Content A', 'Content B', 'Content C'], [
    'carousel_interval'         => 3000,
    'carousel_indicator'        => true,   // progress bar
    'carousel_indicator_color'  => '#08a9d1',
    'carousel_indicator_height' => '3px',
])
```

Per-column settings override the container-level defaults.

---

## Groups

```html
<dual-group
  title="Section Name"
  title-icon="bi-folder"
  title-right="12 rows"
  title-right-icon="bi-info-circle"
  collapsed="false"
  collapsed-icon="bi-chevron-right"
  expanded-icon="bi-chevron-down"
  title-color="#1C1C1E"
  title-bg-color="#08a9d1"
>
  <dual-row>...</dual-row>
</dual-group>
```

Click the group header to toggle collapse/expand.  
Use `border-follow-theme` on the container to tint all group borders with the group's title colour.

---

## Progressive Row Reveal

Two independent patterns — they do not interfere with each other.

### Pattern A — Global timer (`auto-reveal-interval`)

The container reveals all initially-hidden rows one by one at a fixed interval.

```php
echo DualCell::open('grid', ['auto_reveal_interval' => 600]);
echo DualCell::row($cols);               // visible
echo DualCell::row($cols, ['hidden' => true]);  // revealed after 600ms
echo DualCell::row($cols, ['hidden' => true]);  // revealed after 1200ms
echo DualCell::close();
```

### Pattern B — User-triggered chain (`show-next` + `auto-reveal-delay`)

A `show-next` menu button reveals the next hidden row. If that row has
`auto-reveal-delay`, it automatically reveals the row after itself after the delay.

```php
echo DualCell::row([
    DualCell::col('Step 1', ['menu_action' => 'show-next']),
], []);

echo DualCell::row([
    DualCell::col('Step 2', ['menu_action' => 'show-next']),
], ['hidden' => true, 'auto_reveal_delay' => 1000]);

echo DualCell::row([
    DualCell::col('Step 3', ['show_menu' => false]),
], ['hidden' => true]);
```

---

## Toggle Slot

A collapsible detail panel beneath a row. Activated by `menu-action="toggle"`.

```php
echo DualCell::row(
    [DualCell::col('Row title', ['menu_action' => 'toggle'])],
    ['slot' => '<p>Detail HTML shown when expanded</p>']
);

// Two-column slot
echo DualCell::row(
    [DualCell::col('Row title', ['menu_action' => 'toggle'])],
    ['slot_cols' => ['Left column HTML', 'Right column HTML']]
);
```

---

## Hover Preview

Mouseover a cell to populate a target element elsewhere on the page.  
The target keeps the last hovered content when the mouse leaves.

```php
// Hidden data source
echo '<div id="emp-note-101" style="display:none">備註內容 HTML</div>';

// Cell with hover binding
echo DualCell::col($empName, [
    'hover_source' => 'emp-note-101',
    'hover_target' => 'preview-area',
]);

// Preview display area (anywhere on page)
echo '<div id="preview-area"></div>';
```

---

## Themes

Set via `theme` attribute / `'theme'` PHP option on the container.  
The theme sets `borderColor`, `cellBgColor`, `hoverBgColor`, `textColor`,
`menuButtonColor`, `groupTitleColor`, and `groupTitleBgColor` simultaneously.

| Theme | Accent colour |
|---|---|
| `lavender` | `#C3A5E5` |
| `special` | `#C8DD5A` |
| `warning` | `#F08080` |
| `sky` | `#08a9d1` |
| `safe` | `#40c99a` |
| `info` | `#5fafed` |
| `salmon` | `#E5C3B3` |
| `attention` | `#E5E5A6` |
| `pink` | `#FFB3D9` |
| `orange` | `#eda109` |
| `yellow` | `#DECA4B` |
| `stone` | `#7090A8` |
| `brown` | `#d9b375` |
| `default` | `#c6c7bd` |

Individual colour options (`cell-bg-color`, `border-color`, etc.) override the theme.  
Use colour name shortcuts (`sky`, `safe`, `warning`, …) or hex values.

---

## Options Reference

### Container options (PHP keys → HTML `data-*` attributes)

| PHP key | HTML attribute | Type | Default |
|---|---|---|---|
| `theme` | `data-theme` | string | — |
| `cols` | `data-cols` | int | `2` |
| `col_widths` | `data-col-widths` | string `"1:2:1"` | equal |
| `cell_min_height` | `data-cell-min-height` | CSS | `40px` |
| `cell_padding` | `data-cell-padding` | CSS | `8px 12px` |
| `cell_bg_color` | `data-cell-bg-color` | colour | theme |
| `hover_bg_color` | `data-hover-bg-color` | colour | theme |
| `text_color` | `data-text-color` | colour | theme |
| `font_size` | `data-font-size` | CSS | `1rem` |
| `cell_alignment` | `data-cell-alignment` | `left` `center` `right` | `left` |
| `vertical_alignment` | `data-vertical-alignment` | `top` `middle` `bottom` | `middle` |
| `border_width` | `data-border-width` | CSS | `1px` |
| `border_style` | `data-border-style` | CSS | `solid` |
| `border_color` | `data-border-color` | colour | theme |
| `border_follow_theme` | `data-border-follow-theme` | bool | `false` |
| `show_menu_button` | `data-show-menu-button` | bool | `true` |
| `menu_button_position` | `data-menu-button-position` | `left` `right` | `right` |
| `menu_button_color` | `data-menu-button-color` | colour | theme |
| `menu_button_size` | `data-menu-button-size` | CSS | `1.25rem` |
| `menu_button_icon_push` | `data-menu-button-icon-push` | Bootstrap Icon class | — |
| `menu_button_icon_pull` | `data-menu-button-icon-pull` | Bootstrap Icon class | — |
| `menu_button_icon_copy` | `data-menu-button-icon-copy` | Bootstrap Icon class | — |
| `menu_button_icon_swap` | `data-menu-button-icon-swap` | Bootstrap Icon class | — |
| `menu_button_icon_clear` | `data-menu-button-icon-clear` | Bootstrap Icon class | — |
| `menu_button_icon_toggle` | `data-menu-button-icon-toggle` | Bootstrap Icon class | — |
| `menu_button_icon_toggle_expanded` | `data-menu-button-icon-toggle-expanded` | Bootstrap Icon class | — |
| `menu_button_icon_put` | `data-menu-button-icon-put` | Bootstrap Icon class | — |
| `menu_button_icon_show_next` | `data-menu-button-icon-show-next` | Bootstrap Icon class | — |
| `target_id` | `data-target-id` | element id | — |
| `auto_reveal_interval` | `data-auto-reveal-interval` | ms | `0` (off) |
| `overlay_1_text` | `data-overlay-1-text` | string | — |
| `overlay_1_color` | `data-overlay-1-color` | colour | — |
| `overlay_2_text` | `data-overlay-2-text` | string | — |
| `overlay_2_color` | `data-overlay-2-color` | colour | — |
| `overlay_invert` | `data-overlay-invert` | bool | `false` |
| `carousel_interval` | `data-carousel-interval` | ms | `4000` |
| `carousel_indicator` | `data-carousel-indicator` | bool | `true` |
| `carousel_indicator_color` | `data-carousel-indicator-color` | colour | theme |
| `carousel_indicator_height` | `data-carousel-indicator-height` | CSS | `3px` |
| `group_title_font_size` | `data-group-title-font-size` | CSS | `1.125rem` |
| `group_title_color` | `data-group-title-color` | colour | theme |
| `group_title_bg_color` | `data-group-title-bg-color` | colour | theme |
| `group_title_padding` | `data-group-title-padding` | CSS | `10px 12px` |
| `group_icon_size` | `data-group-icon-size` | CSS | `1rem` |
| `group_collapsed_icon` | `data-group-collapsed-icon` | Bootstrap Icon class | — |
| `group_expanded_icon` | `data-group-expanded-icon` | Bootstrap Icon class | — |

### Column options (`<dual-col>` attributes / PHP col options)

| PHP key | HTML attribute | Description |
|---|---|---|
| `id` | `id` | Cell element id (for `put` target) |
| `width` | `width` | Fixed column width (CSS value) |
| `span` | `span` | `"all"` to span full row width |
| `padding` | `padding` | Override cell padding |
| `show_menu` | `show-menu` | Set to `false` to hide menu button |
| `menu_action` | `menu-action` | Action name (see Menu Actions) |
| `target` | `target` | Target element id for push/pull/toggle/put |
| `overlay_1_text` | `overlay-1-text` | Top overlay label |
| `overlay_1_color` | `overlay-1-color` | Top overlay label colour |
| `overlay_2_text` | `overlay-2-text` | Bottom overlay label |
| `overlay_2_color` | `overlay-2-color` | Bottom overlay label colour |
| `overlay_invert` | `overlay-invert` | Invert overlay style |
| `hover_source` | `hover-source` | Element id to read on hover |
| `hover_target` | `hover-target` | Element id to write on hover |
| `carousel_interval` | `carousel-interval` | Per-column interval override |
| `carousel_indicator` | `carousel-indicator` | Per-column indicator toggle |
| `carousel_indicator_color` | `carousel-indicator-color` | Per-column indicator colour |
| `carousel_indicator_height` | `carousel-indicator-height` | Per-column indicator height |

### Row options

| PHP key | HTML attribute | Description |
|---|---|---|
| `col_widths` | `col-widths` | Row-level column width ratios e.g. `"1:3"` |
| `hidden` | `hidden` | Initially hidden (for progressive reveal) |
| `auto_reveal_delay` | `auto-reveal-delay` | ms to auto-reveal next row after this one is shown |
| `slot` | — (PHP only) | HTML string for the toggle slot |
| `slot_cols` | — (PHP only) | Array of two HTML strings for two-column slot |

---

## Escape & Newline Handling

| Method | `escape` default | `\n` → `<br>` | Use when |
|---|---|---|---|
| `col()` | `false` | manual | You assemble the HTML |
| `colText()` | `true` (forced) | ✅ automatic | Plain-text DB field |
| `colCarousel()` | `false` | manual | Items are HTML strings you build |
| `rowFromPair()` | `true` (forced) | ✅ automatic | Single key/value pair from DB |
| `rowsFromArray()` | `true` (forced) | ✅ automatic | Entire associative array from DB |

> **Security note:**  
> When building HTML manually for `col()` or `colCarousel()`,  
> always escape untrusted values:
> ```php
> nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'))
> ```

---

## JavaScript Callbacks

When using the JS constructor directly:

```js
new DualCell('grid-id', {
  theme: 'sky',
  onCellClick:   (rowIdx, colIdx) => { /* cell clicked */ },
  onMenuClick:   (action, rowIdx, colIdx) => { /* menu button clicked */ },
  onContentPush: (rowIdx, colIdx, html) => { /* after push */ },
  onContentPull: (rowIdx, colIdx, html) => { /* after pull */ },
});
```

---

## License

MIT License — free to use, modify, and distribute in personal and commercial projects.
