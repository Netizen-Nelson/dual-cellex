<?php
require_once 'DualCell.php';

// ══════════════════════════════════════════════════════
//  模擬資料庫資料
// ══════════════════════════════════════════════════════

$drug = [
    'name'            => '阿斯匹靈',
    'generic'         => 'Acetylsalicylic Acid',
    'category'        => '非類固醇消炎止痛藥（NSAID）',
    'indication'      => '解熱、鎮痛、抗發炎、預防血栓',
    'dosage'          => '325–650 mg，每 4–6 小時，飯後服用',
    'max_daily'       => '4000 mg / day',
    'contraindication'=> "胃潰瘍患者\n孕婦（第三孕期）\n12 歲以下兒童",
    'interactions'    => 'Warfarin、Ibuprofen、酒精',
    'warning'         => "服用期間請勿飲酒\n出現耳鳴請立即停藥並回診",
    'storage'         => '室溫保存，避免陽光直射，遠離兒童',
];

$employees = [
    ['id'=>101,'name'=>'王大明','dept'=>'研發部','title'=>'資深工程師','hire'=>'2019-03-15','grade'=>'A', 'allergy'=>'Penicillin','note'=>"AWS 解決方案架構師\n核心 API 服務維護"],
    ['id'=>102,'name'=>'林美華','dept'=>'行銷部','title'=>'行銷專員',  'hire'=>'2021-07-01','grade'=>'B+','allergy'=>'無',        'note'=>"社群媒體經營\nGoogle Analytics 認證"],
    ['id'=>103,'name'=>'陳建國','dept'=>'財務部','title'=>'財務主任',  'hire'=>'2016-11-20','grade'=>'A+','allergy'=>'磺胺類',    'note'=>"CPA 執照\n年度財務稽核負責人"],
];

$products = [
    ['name'=>'TypeScript 全端開發實戰','author'=>'張志豪','price'=>680,'stock'=>142,'rating'=>4.8,'category'=>'程式設計'],
    ['name'=>'SQL 效能調校指南',       'author'=>'李建明','price'=>590,'stock'=>87, 'rating'=>4.6,'category'=>'資料庫'],
    ['name'=>'Linux 系統管理精要',     'author'=>'吳俊賢','price'=>720,'stock'=>56, 'rating'=>4.7,'category'=>'系統管理'],
    ['name'=>'UI/UX 設計思維',        'author'=>'黃雅婷','price'=>550,'stock'=>203,'rating'=>4.9,'category'=>'設計'],
];

$sop = [
    ['step'=>1,'title'=>'確認病患身分',   'desc'=>"核對姓名與病歷號\n確認過敏史\n確認目前用藥清單"],
    ['step'=>2,'title'=>'評估生命徵象',   'desc'=>"量測血壓、心跳、體溫、血氧\n記錄於護理紀錄\n異常值需立即通報"],
    ['step'=>3,'title'=>'執行醫囑',       'desc'=>"再次核對藥物劑量\n確認給藥途徑\n記錄給藥時間"],
    ['step'=>4,'title'=>'觀察與記錄',     'desc'=>"給藥後 15 分鐘內觀察\n記錄不良反應\n若有異常立即呼叫醫師"],
];

$quotes = [
    '質量不是行為，而是一種習慣。— 亞里斯多德',
    '成功是從一次失敗到下一次失敗，而不失去熱情。— 邱吉爾',
    '創新是區分領導者與追隨者的標誌。— 賈伯斯',
    '生活就像騎自行車，為了保持平衡，你必須繼續前進。— 愛因斯坦',
];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<title>DualCell PHP Class 範例</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg:      #0c0d0c;
    --shell:   #c6c7bd;
    --lavender:#C3A5E5;
    --special: #C8DD5A;
    --sky:     #08a9d1;
    --safe:    #40c99a;
    --info:    #5fafed;
    --stone:   #7090A8;
    --border:  #222322;
  }
  html, body {
    min-height: 100vh;
    background: var(--bg);
    color: var(--shell);
    font-family: 'Segoe UI','PingFang TC','Microsoft JhengHei',sans-serif;
    font-size: 18px;
    line-height: 1.8;
  }
  .wrap { max-width: 1200px; margin: 0 auto; padding: 48px 40px 100px; }

  h1 { font-size: 1.9rem; color: var(--lavender); margin-bottom: 6px; }
  .subtitle { color: var(--stone); font-size: 1rem; margin-bottom: 48px; }

  .section { margin-bottom: 56px; }
  .section-title {
    font-size: 1.05rem; font-weight: 700;
    color: var(--special);
    border-left: 3px solid var(--special);
    padding-left: 12px;
    margin-bottom: 18px;
    letter-spacing: 0.05em;
  }
  .desc-card {
    background: #111211;
    border: 1px solid #252625;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 16px;
    font-size: 0.92rem;
    color: #9a9b96;
    line-height: 1.9;
  }
  .desc-card code {
    background: #1e201e; color: var(--sky);
    padding: 1px 6px; border-radius: 4px;
    font-family: 'Cascadia Code','Fira Code','Consolas',monospace;
    font-size: 0.85rem;
  }

  hr { border: none; border-top: 1px solid var(--border); margin: 44px 0; }

  /* 推送目標區域 */
  .push-target {
    min-height: 60px;
    background: #141514;
    border: 1px dashed #2a2b2a;
    border-radius: 8px;
    padding: 14px 18px;
    margin-top: 14px;
    color: var(--stone);
    font-size: 0.9rem;
  }
  .push-target:empty::before { content: '⬆ 按下推送按鈕後，內容將顯示於此'; opacity: 0.5; }

  /* hover 預覽目標 */
  .hover-preview {
    min-height: 48px;
    background: #1a2830;
    border: 1px solid rgba(8,169,209,0.3);
    border-radius: 6px;
    padding: 10px 14px;
    margin-top: 12px;
    font-size: 0.9rem;
    color: var(--sky);
    transition: all 0.2s;
  }
  .hover-preview:empty::before { content: '滑鼠移到員工姓名欄位查看預覽'; opacity: 0.5; }

  /* badge */
  .badge { display:inline-block; padding:1px 8px; border-radius:4px; font-size:0.76rem; font-weight:700; }
  .badge-php { background: #2a1055; color: var(--lavender); }

  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
</style>
</head>
<body>
<div class="wrap">

  <h1>DualCell PHP Class 範例</h1>
  <p class="subtitle">模擬從資料庫讀取資料，展示六種常見使用情境</p>

  <!-- ══════════════════════════════════════
       範例一：rowsFromArray() — 藥物資料表
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例一：<code>DualCell::rowsFromArray()</code> — 藥物資料單筆展示</div>
    <div class="desc-card">
      直接把 DB row 關聯陣列傳入，每個 key/value 對自動產生一列兩欄。<br>
      所有欄位自動跳脫，含 <code>\n</code> 的欄位自動轉 <code>&lt;br&gt;</code>。<br>
      key 欄設定固定寬度與選單動作，value 欄設為無選單按鈕。
    </div>

    <?= DualCell::open('table-drug', [
        'theme' => 'warning',
        'cols'  => 2,
        'menu_button_icon_push' => 'bi-box-arrow-right',
        'target_id' => 'push-target-1',
    ]) ?>

    <?php
    // 以標籤友善名稱顯示
    $displayRow = [
        '藥品名稱' => $drug['name'] . '（' . $drug['generic'] . '）',
        '藥物分類' => $drug['category'],
        '適應症'   => $drug['indication'],
        '標準劑量' => $drug['dosage'],
        '每日上限' => $drug['max_daily'],
        '禁忌'     => $drug['contraindication'],
        '交互作用' => $drug['interactions'],
        '注意事項' => $drug['warning'],
        '儲存方式' => $drug['storage'],
    ];
    echo DualCell::rowsFromArray(
        $displayRow,
        ['width' => '140px', 'show_menu' => false],  // key 欄：固定寬，無選單
        ['menu_action' => 'push']                     // value 欄：推送按鈕
    );
    ?>

    <?= DualCell::close() ?>

    <div class="push-target" id="push-target-1"></div>

    <div class="desc-card" style="margin-top:14px">
      <span class="badge badge-php">PHP</span>
      <code>DualCell::rowsFromArray($dbRow, $keyOpts, $valOpts)</code>　
      key 欄固定寬 140px，value 欄有推送按鈕，按下後內容推送到下方目標區域。
    </div>
  </div>

  <!-- ══════════════════════════════════════
       範例二：群組 + 隱藏列 + show-next
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例二：群組 + 隱藏列 + <code>show-next</code> — SOP 逐步揭露</div>
    <div class="desc-card">
      每個步驟初始隱藏（<code>hidden</code>），第一列的按鈕動作為 <code>show-next</code>，點擊後逐步展開下一列。<br>
      包裹在可折疊群組內，群組標題列點擊可展開／收合。
    </div>

    <?= DualCell::open('table-sop', [
        'theme'                  => 'sky',
        'cols'                   => 2,
        'col_widths'             => '1:3',
        'menu_button_icon_show_next' => 'bi-chevron-double-down',
        'group_collapsed_icon'   => 'bi-chevron-right',
        'group_expanded_icon'    => 'bi-chevron-down',
        'border_follow_theme'    => true,
    ]) ?>

    <?= DualCell::groupOpen('標準給藥 SOP', [
        'title_icon'    => 'bi-clipboard2-pulse',
        'title_right'   => '共 ' . count($sop) . ' 步驟',
        'collapsed_icon'=> 'bi-chevron-right',
        'expanded_icon' => 'bi-chevron-down',
    ]) ?>

    <?php foreach ($sop as $i => $step):
        // 第一列可見，其餘隱藏
        $isFirst = ($i === 0);
        // 最後一列不需要 show-next 按鈕
        $isLast  = ($i === count($sop) - 1);

        $stepLabel = "<strong>Step {$step['step']}</strong>　{$step['title']}";
        $stepDesc  = nl2br(htmlspecialchars($step['desc'], ENT_QUOTES, 'UTF-8'));

        echo DualCell::row([
            DualCell::col($stepLabel, ['show_menu' => !$isLast, 'menu_action' => 'show-next']),
            DualCell::col($stepDesc,  ['show_menu' => false]),
        ], ['hidden' => !$isFirst]);
    endforeach ?>

    <?= DualCell::groupClose() ?>
    <?= DualCell::close() ?>

    <div class="desc-card" style="margin-top:14px">
      <span class="badge badge-php">PHP</span>
      <code>DualCell::row([$cols], ['hidden' => true])</code>　
      第一列可見，其餘 <code>hidden</code>，左欄 <code>show-next</code> 按鈕逐步揭露。
    </div>
  </div>

  <!-- ══════════════════════════════════════
       範例三：遮罩 overlay — 員工資料
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例三：遮罩 <code>overlay</code> + hover 預覽 — 員工列表</div>
    <div class="desc-card">
      姓名欄設定兩層遮罩（<code>overlay_1_text</code> / <code>overlay_2_text</code>），點兩次才揭開姓名。<br>
      姓名欄設定 <code>hover_source</code> + <code>hover_target</code>，滑鼠移入時在下方預覽區顯示備註。
    </div>

    <?php
    // hover 來源資料（隱藏 div）
    foreach ($employees as $emp):
        echo '<div id="note-' . $emp['id'] . '" style="display:none">';
        echo '<strong style="color:#08a9d1">' . htmlspecialchars($emp['name'], ENT_QUOTES, 'UTF-8') . '</strong>　';
        echo htmlspecialchars($emp['title'], ENT_QUOTES, 'UTF-8') . '<br>';
        echo '<small style="opacity:0.7">' . nl2br(htmlspecialchars($emp['note'], ENT_QUOTES, 'UTF-8')) . '</small>';
        echo '</div>';
    endforeach;
    ?>

    <?= DualCell::open('table-emp', [
        'theme'    => 'info',
        'cols'     => 4,
        'col_widths' => '2:2:1:1',
    ]) ?>

    <?= DualCell::row([
        DualCell::col('<strong style="color:#5fafed">姓名</strong>',     ['show_menu' => false]),
        DualCell::col('<strong style="color:#5fafed">部門 / 職稱</strong>',['show_menu' => false]),
        DualCell::col('<strong style="color:#5fafed">到職日</strong>',   ['show_menu' => false]),
        DualCell::col('<strong style="color:#5fafed">考核</strong>',     ['show_menu' => false]),
    ]) ?>

    <?php foreach ($employees as $emp):
        $gradeColor = str_starts_with($emp['grade'], 'A') ? '#40c99a' : '#5fafed';
    ?>
    <?= DualCell::row([
        DualCell::col(
            htmlspecialchars($emp['name'], ENT_QUOTES, 'UTF-8'),
            [
                'show_menu'     => false,
                'overlay_1_text'=> '⚠ 隱私資料',
                'overlay_1_color'=> 'warning',
                'overlay_2_text'=> '點擊解鎖',
                'overlay_2_color'=> 'sky',
                'hover_source'  => 'note-' . $emp['id'],
                'hover_target'  => 'emp-hover-preview',
            ]
        ),
        DualCell::colText($emp['dept'] . ' / ' . $emp['title'], ['show_menu' => false]),
        DualCell::colText($emp['hire'], ['show_menu' => false]),
        DualCell::col('<strong style="color:' . $gradeColor . '">' . htmlspecialchars($emp['grade'], ENT_QUOTES, 'UTF-8') . '</strong>', ['show_menu' => false]),
    ]) ?>
    <?php endforeach ?>

    <?= DualCell::close() ?>

    <div class="hover-preview" id="emp-hover-preview"></div>

    <div class="desc-card" style="margin-top:14px">
      <span class="badge badge-php">PHP</span>
      <code>DualCell::col($name, ['overlay_1_text'=>'...', 'hover_source'=>'...', 'hover_target'=>'...'])</code>
    </div>
  </div>

  <!-- ══════════════════════════════════════
       範例四：垂直輪播欄 + toggle slot
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例四：<code>colCarousel()</code> 垂直輪播 + <code>toggle</code> 展開 slot</div>
    <div class="desc-card">
      書籍列表：書名欄設定輪播（名言佳句輪流顯示），toggle 按鈕展開詳細資訊 slot。<br>
      每欄輪播內容從 PHP 陣列自動產生 <code>&lt;dual-item&gt;</code>，無需手動寫標籤。
    </div>

    <?= DualCell::open('table-books', [
        'theme'                     => 'lavender',
        'cols'                      => 3,
        'col_widths'                => '3:1:1',
        'carousel_interval'         => 3500,
        'menu_button_icon_toggle'   => 'bi-chevron-down',
        'menu_button_icon_toggle_expanded' => 'bi-chevron-up',
        'menu_button_icon_copy'     => 'bi-clipboard',
    ]) ?>

    <?php foreach ($products as $i => $book):
        // 每本書的詳細資訊 slot
        $slotHtml = '
            <div style="padding:4px 0;font-size:0.88rem;display:grid;grid-template-columns:80px 1fr;gap:4px 12px">
              <span style="color:#C3A5E5">分類</span><span>' . htmlspecialchars($book['category'], ENT_QUOTES, 'UTF-8') . '</span>
              <span style="color:#C3A5E5">作者</span><span>' . htmlspecialchars($book['author'],   ENT_QUOTES, 'UTF-8') . '</span>
              <span style="color:#C3A5E5">庫存</span><span>' . $book['stock'] . ' 件</span>
              <span style="color:#C3A5E5">評分</span><span style="color:#C8DD5A">★ ' . $book['rating'] . '</span>
            </div>';

        echo DualCell::row([
            DualCell::col(
                '<strong>' . htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8') . '</strong>',
                ['menu_action' => 'toggle']
            ),
            DualCell::col('NT$ ' . number_format($book['price']), ['menu_action' => 'copy']),
            DualCell::colCarousel(
                $quotes,
                ['show_menu' => false, 'carousel_interval' => 3000 + $i * 500]
            ),
        ], ['slot' => $slotHtml]);
    endforeach ?>

    <?= DualCell::close() ?>

    <div class="desc-card" style="margin-top:14px">
      <span class="badge badge-php">PHP</span>
      <code>DualCell::colCarousel($items, ['carousel_interval' => 3000])</code>　
      超過一個 item 自動啟用輪播；<code>row($cols, ['slot' => $html])</code> 提供可折疊的詳細資訊。
    </div>
  </div>

  <!-- ══════════════════════════════════════
       範例五：Custom Element + swap + put
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例五：Custom Element 語法 + <code>swap</code> / <code>put</code></div>
    <div class="desc-card">
      使用 <code>&lt;dual-cell&gt;</code> 自訂元素語法（不需要 id 初始化，HTML 自動觸發）。<br>
      左欄 <code>swap</code> 按鈕交換左右欄內容；右欄 <code>put</code> 按鈕將內容放置到指定儲存格。
    </div>

    <div class="grid-2">
      <?= DualCell::element('table-swap', [
          'theme'                => 'safe',
          'cols'                 => 2,
          'menu_button_icon_swap'=> 'bi-arrow-left-right',
          'menu_button_icon_put' => 'bi-box-arrow-down',
      ]) ?>

      <?php foreach ([['原始資料 A','原始資料 B'],['項目 C','項目 D'],['內容 E','內容 F']] as $pair):
        echo DualCell::row([
            DualCell::col($pair[0], ['menu_action' => 'swap']),
            DualCell::col($pair[1], ['menu_action' => 'put', 'target' => 'put-target-cell']),
        ]);
      endforeach ?>

      <?= DualCell::row([
          DualCell::col('<span style="color:#40c99a;font-size:0.85rem">↓ put 目標儲存格</span>', ['show_menu' => false]),
          DualCell::col('', ['id' => 'put-target-cell', 'show_menu' => false]),
      ]) ?>

      <?= DualCell::elementClose() ?>

      <!-- 說明 -->
      <div>
        <div class="desc-card">
          <strong style="color:#40c99a">swap</strong>（↔）<br>
          點擊左欄按鈕，將左右兩欄的內容對調。
        </div>
        <div class="desc-card" style="margin-top:12px">
          <strong style="color:#40c99a">put</strong>（↓）<br>
          點擊右欄按鈕，將該欄內容放置到 id 為 <code>put-target-cell</code> 的儲存格。
        </div>
        <div class="desc-card" style="margin-top:12px">
          <span class="badge badge-php">PHP</span>
          <code>DualCell::element('id', $opts)</code><br>
          使用 <code>&lt;dual-cell&gt;</code> 自訂元素，HTML 解析後自動初始化，不需要 JS 手動呼叫。
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════
       範例六：auto-reveal-interval 全域自動展開
  ══════════════════════════════════════ -->
  <div class="section">
    <div class="section-title">範例六：<code>auto_reveal_interval</code> — 定時逐步展開</div>
    <div class="desc-card">
      所有列（除第一列）初始隱藏，容器設定 <code>auto_reveal_interval</code>，
      頁面載入後每隔指定毫秒自動展開下一列，無需使用者互動。<br>
      適合儀表板資料、公告欄、排行榜等場景。
    </div>

    <?= DualCell::open('table-auto', [
        'theme'                => 'special',
        'cols'                 => 3,
        'col_widths'           => '1:2:1',
        'auto_reveal_interval' => 600,
        'show_menu_button'     => false,
    ]) ?>

    <?php
    // 標題列（永遠可見）
    echo DualCell::row([
        DualCell::col('<strong style="color:#C8DD5A">排名</strong>', ['show_menu' => false]),
        DualCell::col('<strong style="color:#C8DD5A">書名</strong>',  ['show_menu' => false]),
        DualCell::col('<strong style="color:#C8DD5A">本週銷量</strong>',['show_menu' => false]),
    ]);

    // 資料列：除第一筆外全部隱藏
    foreach ($products as $i => $book):
        $medal = match($i) { 0=>'🥇', 1=>'🥈', 2=>'🥉', default=>($i+1).'.' };
        echo DualCell::row([
            DualCell::col($medal, ['show_menu' => false]),
            DualCell::col('<strong>' . htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8') . '</strong><br><small style="opacity:0.6">' . htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') . '</small>', ['show_menu' => false]),
            DualCell::col('<span style="color:#C8DD5A;font-weight:700">' . rand(120, 480) . '</span> 本', ['show_menu' => false]),
        ], ['hidden' => $i > 0]);
    endforeach;
    ?>

    <?= DualCell::close() ?>

    <div class="desc-card" style="margin-top:14px">
      <span class="badge badge-php">PHP</span>
      <code>DualCell::open('id', ['auto_reveal_interval' => 600])</code>　
      容器設定後，所有 <code>hidden</code> 列每 600ms 自動依序展開。
    </div>
  </div>

  <hr>

  <!-- 方法速查 -->
  <div class="section">
    <div class="section-title">方法速查</div>
    <div class="desc-card">
      <table style="width:100%;border-collapse:collapse;font-size:0.88rem">
        <thead>
          <tr style="border-bottom:1px solid #2a2b2a">
            <th style="padding:8px 12px;text-align:left;color:var(--lavender)">方法</th>
            <th style="padding:8px 12px;text-align:left;color:var(--lavender)">escape 預設</th>
            <th style="padding:8px 12px;text-align:left;color:var(--lavender)">說明</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $methods = [
            ['open($id, $opts)',            '—',           '輸出容器開始 <div data-dual-cell>'],
            ['close()',                     '—',           '輸出容器結束 </div>'],
            ['element($id, $opts)',         '—',           '輸出 <dual-cell> 開始標籤'],
            ['elementClose()',             '—',           '輸出 </dual-cell>'],
            ['groupOpen($title, $opts)',    '—',           '輸出 <dual-group> 開始，可折疊群組'],
            ['groupClose()',               '—',           '輸出 </dual-group>'],
            ['row([$cols], $opts)',         '—',           '輸出 <dual-row>，傳入 col() 陣列'],
            ['col($html, $opts)',           'false',       '自行組 HTML，最彈性'],
            ['colText($text, $opts)',       'true（強制）', '純文字欄，\\n 自動轉 <br>'],
            ['colCarousel($items, $opts)',  'false',       '輪播欄，items 超過一個自動啟動'],
            ['rowFromPair($k,$v,…)',        'true（強制）', '單一 key/value 對 → 一列兩欄'],
            ['rowsFromArray($row,…)',       'true（強制）', '整個 DB row → 多列兩欄'],
            ['script($path)',              '—',           '引入 JS，只輸出一次'],
          ];
          foreach ($methods as $m):
          ?>
          <tr style="border-bottom:1px solid #1e201e">
            <td style="padding:7px 12px;color:var(--sky);font-family:monospace;font-size:0.82rem"><?= $m[0] ?></td>
            <td style="padding:7px 12px;font-size:0.85rem"><?= $m[1] ?></td>
            <td style="padding:7px 12px;color:#8a8b86;font-size:0.85rem"><?= $m[2] ?></td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?= DualCell::script('dual-cellex.js') ?>
</body>
</html>
