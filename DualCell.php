<?php
/**
 * DualCell.php
 * PHP 渲染類別，搭配 dual-cellex.js 使用
 * 負責從 PHP / DB 資料產生正確的 dual-cell HTML 標記
 *
 * 使用方式：
 *   require_once 'DualCell.php';
 *
 *   // 最簡單：div 方式初始化
 *   echo DualCell::open('my-table', ['theme' => 'sky', 'cols' => 2]);
 *   echo DualCell::row([DualCell::col('姓名'), DualCell::col('王大明')]);
 *   echo DualCell::close();
 *
 *   // Custom Element 方式
 *   echo DualCell::element('my-table2', ['theme' => 'info']);
 *   echo DualCell::col('欄位一');
 *   echo DualCell::col('欄位二');
 *   echo DualCell::elementClose();
 */
class DualCell
{
    /** 已輸出 script 旗標 */
    private static bool $scriptRendered = false;

    /** 已使用的 id 清單 */
    private static array $usedIds = [];

    /** 有效主題名稱 */
    private const VALID_THEMES = [
        'lavender','special','warning','sky','safe','info','salmon',
        'attention','pink','orange','yellow','stone','brown','default',
    ];

    /** 有效色彩名稱（resolveColor 對應表） */
    private const VALID_COLORS = [
        'shell','lavender','special','warning','salmon','attention',
        'sky','safe','brown','info','pink','orange','yellow','stone',
        'bg','area',
    ];

    // ──────────────────────────────────────────────
    //  主容器（div 初始化方式）
    // ──────────────────────────────────────────────

    /**
     * 輸出容器開始標籤（div + data-dual-cell）
     * 搭配 close() 使用，中間放 row() / group() 等
     *
     * @param string $id      容器 id（必填）
     * @param array  $options 設定選項
     */
    public static function open(string $id, array $options = []): string
    {
        $id = self::safeId($id);
        $attrs = self::buildContainerAttrs($options);
        return "<div id=\"{$id}\" data-dual-cell{$attrs}>\n";
    }

    /**
     * 輸出容器結束標籤
     */
    public static function close(): string
    {
        return "</div>\n";
    }

    // ──────────────────────────────────────────────
    //  Custom Element 方式（<dual-cell>）
    // ──────────────────────────────────────────────

    /**
     * 輸出 <dual-cell> 開始標籤
     * 需搭配 elementClose() 使用
     *
     * @param string $id      容器 id（必填）
     * @param array  $options 設定選項
     */
    public static function element(string $id, array $options = []): string
    {
        $id = self::safeId($id);
        $attrs = self::buildContainerAttrs($options);
        return "<dual-cell id=\"{$id}\"{$attrs}>\n";
    }

    /**
     * 輸出 <dual-cell> 結束標籤
     */
    public static function elementClose(): string
    {
        return "</dual-cell>\n";
    }

    // ──────────────────────────────────────────────
    //  群組
    // ──────────────────────────────────────────────

    /**
     * 群組開始：輸出 <dual-group> 開始標籤
     *
     * @param string $title   群組標題
     * @param array  $options 群組選項
     */
    public static function groupOpen(string $title, array $options = []): string
    {
        $t = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $attrs = " title=\"{$t}\"";

        if (!empty($options['title_icon'])) {
            $attrs .= ' title-icon="' . htmlspecialchars($options['title_icon'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['title_right'])) {
            $attrs .= ' title-right="' . htmlspecialchars($options['title_right'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['title_right_icon'])) {
            $attrs .= ' title-right-icon="' . htmlspecialchars($options['title_right_icon'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['collapsed'])) {
            $attrs .= ' collapsed="true"';
        }
        if (!empty($options['title_font_size'])) {
            $attrs .= ' title-font-size="' . htmlspecialchars($options['title_font_size'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['title_color'])) {
            $attrs .= ' title-color="' . htmlspecialchars($options['title_color'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['title_bg_color'])) {
            $attrs .= ' title-bg-color="' . htmlspecialchars($options['title_bg_color'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['title_padding'])) {
            $attrs .= ' title-padding="' . htmlspecialchars($options['title_padding'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['collapsed_icon'])) {
            $attrs .= ' collapsed-icon="' . htmlspecialchars($options['collapsed_icon'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['expanded_icon'])) {
            $attrs .= ' expanded-icon="' . htmlspecialchars($options['expanded_icon'], ENT_QUOTES, 'UTF-8') . '"';
        }

        return "<dual-group{$attrs}>\n";
    }

    /**
     * 群組結束
     */
    public static function groupClose(): string
    {
        return "</dual-group>\n";
    }

    // ──────────────────────────────────────────────
    //  列（Row）
    // ──────────────────────────────────────────────

    /**
     * 輸出一整列，傳入多個 col() 字串組成的陣列
     *
     * @param array $cols    由 col() 產生的欄字串陣列
     * @param array $options 列選項
     */
    public static function row(array $cols, array $options = []): string
    {
        $attrs = '';
        if (!empty($options['col_widths'])) {
            $attrs .= ' col-widths="' . htmlspecialchars($options['col_widths'], ENT_QUOTES, 'UTF-8') . '"';
        }
        if (!empty($options['hidden'])) {
            $attrs .= ' hidden';
        }
        if (!empty($options['auto_reveal_delay'])) {
            $attrs .= ' auto-reveal-delay="' . (int)$options['auto_reveal_delay'] . '"';
        }

        $slotHtml = '';
        if (!empty($options['slot'])) {
            $slotHtml = "\n  <dual-slot>" . $options['slot'] . "</dual-slot>";
        }
        if (!empty($options['slot_cols']) && is_array($options['slot_cols'])) {
            $inner = '';
            foreach ($options['slot_cols'] as $sc) {
                $inner .= "<dual-col>{$sc}</dual-col>";
            }
            $slotHtml = "\n  <dual-slot>{$inner}</dual-slot>";
        }

        $inner = implode('', $cols);
        return "<dual-row{$attrs}>\n{$inner}{$slotHtml}\n</dual-row>\n";
    }

    // ──────────────────────────────────────────────
    //  欄（Col）
    // ──────────────────────────────────────────────

    /**
     * 輸出單一欄
     *
     * @param string $content  欄內 HTML 內容
     * @param array  $options  欄選項
     */
    public static function col(string $content, array $options = []): string
    {
        $escape = $options['escape'] ?? false;
        $body   = $escape ? self::escapeText($content) : $content;

        $attrs = self::buildColAttrs($options);
        return "  <dual-col{$attrs}>{$body}</dual-col>\n";
    }

    /**
     * 純文字欄：escape 強制 true，\n 自動轉 <br>
     *
     * @param string $content 純文字內容
     * @param array  $options 欄選項
     */
    public static function colText(string $content, array $options = []): string
    {
        $options['escape'] = true;
        return self::col($content, $options);
    }

    /**
     * 輪播欄：傳入多個 HTML 字串陣列，自動產生 <dual-item> 結構
     * 超過一個 item 才會啟動輪播（JS 自動偵測）
     *
     * @param array $items   HTML 字串陣列，每個元素為一個輪播頁
     * @param array $options 欄選項（可含 carousel_interval / carousel_indicator 等）
     */
    public static function colCarousel(array $items, array $options = []): string
    {
        $escape = $options['escape'] ?? false;
        $inner  = '';
        foreach ($items as $item) {
            $body   = $escape ? self::escapeText((string)$item) : (string)$item;
            $inner .= "    <dual-item>{$body}</dual-item>\n";
        }
        $attrs = self::buildColAttrs($options);
        return "  <dual-col{$attrs}>\n{$inner}  </dual-col>\n";
    }

    /**
     * 從 DB 關聯陣列快速產生兩欄列（key 欄 + value 欄）
     * 自動處理跳脫與換行
     *
     * @param array  $row     關聯陣列
     * @param array  $keyOpts key 欄的欄選項（如 width, menu_action 等）
     * @param array  $valOpts value 欄的欄選項
     * @param array  $rowOpts row 選項
     */
    public static function rowFromPair(
        string $key,
        string $value,
        array  $keyOpts = [],
        array  $valOpts = [],
        array  $rowOpts = []
    ): string {
        $keyOpts['escape'] = true;
        $valOpts['escape'] = true;
        return self::row([
            self::col($key,   $keyOpts),
            self::col($value, $valOpts),
        ], $rowOpts);
    }

    /**
     * 從 DB 關聯陣列批次產生多列（每個 key/value 對 → 一列兩欄）
     * key 欄與 value 欄都自動跳脫，\n 自動轉 <br>
     *
     * @param array $dbRow   關聯陣列
     * @param array $keyOpts key 欄的欄選項
     * @param array $valOpts value 欄的欄選項
     */
    public static function rowsFromArray(
        array $dbRow,
        array $keyOpts = [],
        array $valOpts = []
    ): string {
        $html = '';
        foreach ($dbRow as $key => $value) {
            $html .= self::rowFromPair((string)$key, (string)$value, $keyOpts, $valOpts);
        }
        return $html;
    }

    // ──────────────────────────────────────────────
    //  Quiz 列
    // ──────────────────────────────────────────────

    /**
     * Quiz 列：輸入框欄 + 遮罩答案欄，自動配對
     * 大小寫敏感比對，ENTER 後一律揭開遮罩，輸入框立即鎖定
     *
     * @param string $prompt  輸入框的 placeholder 提示文字
     * @param string $answer  正確答案（同時也是遮罩欄顯示內容）
     * @param array  $options input_side(left|right), overlay_text, overlay_color,
     *                        col_widths, row_opts
     */
    public static function rowQuiz(string $prompt, string $answer, array $options = []): string
    {
        $inputSide    = $options['input_side']    ?? 'left';
        $overlayText  = $options['overlay_text']  ?? '？';
        $overlayColor = $options['overlay_color'] ?? 'sky';
        $rowOpts      = $options['row_opts']       ?? [];
        if (!empty($options['col_widths'])) {
            $rowOpts['col_widths'] = $options['col_widths'];
        }

        $safeAnswer = htmlspecialchars($answer, ENT_QUOTES, 'UTF-8');

        $inputCol = self::col('', [
            'quiz_input'       => true,
            'quiz_placeholder' => $prompt,
            'show_menu'        => false,
        ]);

        $answerCol = self::col($safeAnswer, [
            'quiz_answer'     => true,
            'overlay_1_text'  => $overlayText,
            'overlay_1_color' => $overlayColor,
            'show_menu'       => false,
        ]);

        $cols = $inputSide === 'left'
            ? [$inputCol, $answerCol]
            : [$answerCol, $inputCol];

        return self::row($cols, $rowOpts);
    }

    // ──────────────────────────────────────────────
    //  Script 引入
    // ──────────────────────────────────────────────

    /**
     * 輸出引入 script 標籤，安全呼叫多次只輸出一次
     *
     * @param string $jsPath dual-cellex.js 的路徑
     */
    public static function script(string $jsPath): string
    {
        if (self::$scriptRendered) return '';
        self::$scriptRendered = true;
        return '<script src="' . htmlspecialchars($jsPath, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    }

    /**
     * 重置旗標（測試用途）
     */
    public static function reset(): void
    {
        self::$scriptRendered = false;
        self::$usedIds        = [];
    }

    // ──────────────────────────────────────────────
    //  私有輔助方法
    // ──────────────────────────────────────────────

    /**
     * 產生容器層的 data-* 屬性字串（支援所有 DualCell options）
     */
    private static function buildContainerAttrs(array $o): string
    {
        $a = '';
        $s = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

        if (!empty($o['theme']))               $a .= " data-theme=\"{$s($o['theme'])}\"";
        if (!empty($o['cols']))                $a .= " data-cols=\"{$s($o['cols'])}\"";
        if (!empty($o['col_widths']))          $a .= " data-col-widths=\"{$s($o['col_widths'])}\"";
        if (!empty($o['cell_min_height']))     $a .= " data-cell-min-height=\"{$s($o['cell_min_height'])}\"";
        if (!empty($o['cell_padding']))        $a .= " data-cell-padding=\"{$s($o['cell_padding'])}\"";
        if (!empty($o['cell_bg_color']))       $a .= " data-cell-bg-color=\"{$s($o['cell_bg_color'])}\"";
        if (!empty($o['hover_bg_color']))      $a .= " data-hover-bg-color=\"{$s($o['hover_bg_color'])}\"";
        if (!empty($o['text_color']))          $a .= " data-text-color=\"{$s($o['text_color'])}\"";
        if (!empty($o['font_size']))           $a .= " data-font-size=\"{$s($o['font_size'])}\"";
        if (!empty($o['cell_alignment']))      $a .= " data-cell-alignment=\"{$s($o['cell_alignment'])}\"";
        if (!empty($o['vertical_alignment']))  $a .= " data-vertical-alignment=\"{$s($o['vertical_alignment'])}\"";
        if (!empty($o['border_width']))        $a .= " data-border-width=\"{$s($o['border_width'])}\"";
        if (!empty($o['border_style']))        $a .= " data-border-style=\"{$s($o['border_style'])}\"";
        if (!empty($o['border_color']))        $a .= " data-border-color=\"{$s($o['border_color'])}\"";
        if (isset($o['border_follow_theme']))  $a .= ' data-border-follow-theme="' . ($o['border_follow_theme'] ? 'true' : 'false') . '"';
        if (isset($o['show_menu_button']))     $a .= ' data-show-menu-button="'    . ($o['show_menu_button']    ? 'true' : 'false') . '"';
        if (!empty($o['menu_button_position']))$a .= " data-menu-button-position=\"{$s($o['menu_button_position'])}\"";
        if (!empty($o['menu_button_color']))   $a .= " data-menu-button-color=\"{$s($o['menu_button_color'])}\"";
        if (!empty($o['menu_button_size']))    $a .= " data-menu-button-size=\"{$s($o['menu_button_size'])}\"";
        // menu button icons
        foreach (['push','pull','copy','swap','clear','toggle','toggle_expanded','put','show_next'] as $act) {
            $k = 'menu_button_icon_' . $act;
            $d = 'data-menu-button-icon-' . str_replace('_', '-', $act);
            if (!empty($o[$k])) $a .= " {$d}=\"{$s($o[$k])}\"";
        }
        if (!empty($o['target_id']))              $a .= " data-target-id=\"{$s($o['target_id'])}\"";
        if (!empty($o['auto_reveal_interval']))   $a .= " data-auto-reveal-interval=\"{$s($o['auto_reveal_interval'])}\"";
        if (!empty($o['overlay_1_text']))         $a .= " data-overlay-1-text=\"{$s($o['overlay_1_text'])}\"";
        if (!empty($o['overlay_1_color']))        $a .= " data-overlay-1-color=\"{$s($o['overlay_1_color'])}\"";
        if (!empty($o['overlay_2_text']))         $a .= " data-overlay-2-text=\"{$s($o['overlay_2_text'])}\"";
        if (!empty($o['overlay_2_color']))        $a .= " data-overlay-2-color=\"{$s($o['overlay_2_color'])}\"";
        if (isset($o['overlay_invert']))          $a .= ' data-overlay-invert="'        . ($o['overlay_invert']   ? 'true' : 'false') . '"';
        if (!empty($o['carousel_interval']))      $a .= " data-carousel-interval=\"{$s($o['carousel_interval'])}\"";
        if (isset($o['carousel_indicator']))      $a .= ' data-carousel-indicator="'    . ($o['carousel_indicator'] ? 'true' : 'false') . '"';
        if (!empty($o['carousel_indicator_color']))  $a .= " data-carousel-indicator-color=\"{$s($o['carousel_indicator_color'])}\"";
        if (!empty($o['carousel_indicator_height'])) $a .= " data-carousel-indicator-height=\"{$s($o['carousel_indicator_height'])}\"";
        if (!empty($o['group_title_font_size']))  $a .= " data-group-title-font-size=\"{$s($o['group_title_font_size'])}\"";
        if (!empty($o['group_title_color']))      $a .= " data-group-title-color=\"{$s($o['group_title_color'])}\"";
        if (!empty($o['group_title_bg_color']))   $a .= " data-group-title-bg-color=\"{$s($o['group_title_bg_color'])}\"";
        if (!empty($o['group_title_padding']))    $a .= " data-group-title-padding=\"{$s($o['group_title_padding'])}\"";
        if (!empty($o['group_icon_size']))        $a .= " data-group-icon-size=\"{$s($o['group_icon_size'])}\"";
        if (!empty($o['group_collapsed_icon']))   $a .= " data-group-collapsed-icon=\"{$s($o['group_collapsed_icon'])}\"";
        if (!empty($o['group_expanded_icon']))    $a .= " data-group-expanded-icon=\"{$s($o['group_expanded_icon'])}\"";

        return $a;
    }

    /**
     * 產生 <dual-col> 的屬性字串
     */
    private static function buildColAttrs(array $o): string
    {
        $a = '';
        $s = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

        if (!empty($o['id']))               $a .= " id=\"{$s($o['id'])}\"";
        if (!empty($o['width']))            $a .= " width=\"{$s($o['width'])}\"";
        if (!empty($o['span']) && $o['span'] === 'all') $a .= ' span="all"';
        if (!empty($o['padding']))          $a .= " padding=\"{$s($o['padding'])}\"";
        if (isset($o['show_menu']) && !$o['show_menu']) $a .= ' show-menu="false"';
        if (!empty($o['menu_action']))      $a .= " menu-action=\"{$s($o['menu_action'])}\"";
        if (!empty($o['target']))           $a .= " target=\"{$s($o['target'])}\"";
        if (!empty($o['overlay_1_text']))   $a .= " overlay-1-text=\"{$s($o['overlay_1_text'])}\"";
        if (!empty($o['overlay_1_color']))  $a .= " overlay-1-color=\"{$s($o['overlay_1_color'])}\"";
        if (!empty($o['overlay_2_text']))   $a .= " overlay-2-text=\"{$s($o['overlay_2_text'])}\"";
        if (!empty($o['overlay_2_color']))  $a .= " overlay-2-color=\"{$s($o['overlay_2_color'])}\"";
        if (isset($o['overlay_invert']))    $a .= ' overlay-invert="' . ($o['overlay_invert'] ? 'true' : 'false') . '"';
        if (!empty($o['hover_source']))     $a .= " hover-source=\"{$s($o['hover_source'])}\"";
        if (!empty($o['hover_target']))     $a .= " hover-target=\"{$s($o['hover_target'])}\"";
        // per-col 輪播設定
        if (!empty($o['carousel_interval']))     $a .= " carousel-interval=\"{$s($o['carousel_interval'])}\"";
        if (isset($o['carousel_indicator']))     $a .= ' carousel-indicator="' . ($o['carousel_indicator'] ? 'true' : 'false') . '"';
        if (!empty($o['carousel_indicator_color']))  $a .= " carousel-indicator-color=\"{$s($o['carousel_indicator_color'])}\"";
        if (!empty($o['carousel_indicator_height'])) $a .= " carousel-indicator-height=\"{$s($o['carousel_indicator_height'])}\"";
        // quiz 欄位
        if (!empty($o['quiz_input']))       $a .= ' input-quiz';
        if (!empty($o['quiz_answer']))      $a .= ' answer-quiz';
        if (isset($o['quiz_placeholder']))  $a .= " quiz-placeholder=\"{$s($o['quiz_placeholder'])}\"";

        return $a;
    }

    /**
     * 純文字跳脫：先 htmlspecialchars 再 nl2br
     */
    private static function escapeText(string $text): string
    {
        return nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * 確保 id 安全（英數字與連字號）
     */
    private static function safeId(string $id): string
    {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '-', $id);
    }
}
