<ul class="sidebar-list">

    <!-- ダッシュボード -->
    <li class="sidebar-list__li">
        <a href="{{ route('dashboard') }}" class="sidebar-list-menu__title hover-white"><i class="sidebar-list-menu__tachometer-alt fas fa-home mr-2"></i>ホーム</a>
    </li>

    <!-- 案件一覧 -->
    <li class="sidebar-list__li">
        <a href="{{ route('contact.customers') }}" class="sidebar-list-menu__title hover-white"><i class="sidebar-list-menu__tachometer-alt fas fa-users mr-2"></i>案件一覧</a>
    </li>

    <!-- 新規案件登録 -->
    <li class="sidebar-list__li">
      <a href="{{ route('contact.form') }}" class="sidebar-list-menu__title hover-white"><i class="sidebar-list-menu__tachometer-alt fas fa-user-plus mr-2"></i>新規案件登録</a>
    </li>

    <!-- やることリスト -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title position-relative"><i class="fas fa-clipboard-list mr-2"></i>やることリスト {!! !empty($tasks['total']) ? '<span class="sidebar-list-menu-items__li-badge">'.$tasks['total'].'</span>' : '' !!}</p>
      <ul class="sidebar-list-menu-items">

        <!-- FC未依頼一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('unassigned.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>FC未振り分け一覧<span class='sidebar-list-menu-items__li-badge'>{{ !empty($tasks[1]) ? $tasks[1][1] : '' }}</span></a>
        </li>

        <!-- サンプル送付リスト -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('sample.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>サンプル送付リスト<span class='sidebar-list-menu-items__li-badge'>{{ !empty($tasks[1]) ? $tasks[1]['sample_send'] : '' }}</span></a>
        </li>

        <!-- 本部見積もり案件一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('quotations.admin.needs') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>本部見積もり案件一覧 <span class='sidebar-list-menu-items__li-badge'>{{ !empty($tasks[1][2]) ? $tasks[1][2] : '' }}</span></a>
        </li>

        <!-- 商談結果登録待ち一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('pending.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>商談結果登録待ち一覧<span class='sidebar-list-menu-items__li-badge'>{{ !empty($tasks[1][3]) ? $tasks[1][3] : '' }}</span></a>
        </li>

        <!-- 発送連絡待ち一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('dispatch.pending') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>部材発送連絡待ち一覧 <span class='sidebar-list-menu-items__li-badge'>{{ !empty($tasks[9]) ? $tasks[9] : '' }}</span></a>
        </li>

      </ul>
    </li>

    <!-- 確認リスト -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title position-relative"><i class="sidebar-list-menu__hourglass-half fas fa-hourglass-half mr-2"></i>進捗管理</p>
      <ul class="sidebar-list-menu-items">

        <!-- FC依頼済み一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('assigned.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>FC依頼済み一覧</a>
        </li>
        
        <!-- 見積書一覧 -->
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('quotations.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>見積もり書一覧</a>
        </li>

        <!-- 発注書一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('transactions') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>発注書一覧</a>
        </li>

        <!-- ###発注請書一覧### -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('transactions.admin.dispatched') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>発注請書一覧</a>
        </li>

        <!-- 請求書一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('invoices.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>請求書一覧</a>
        </li>

        <!-- 施工完了報告一覧 -->
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('report.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>施工完了報告一覧</a>
        </li>

        <!-- キャンセル案件一覧 -->
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('contact.cancel') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>キャンセル案件一覧</a>
        </li>

      </ul>
    </li>

    <!-- 在庫一覧 -->
    <li class="sidebar-list__li">
      <a href="{{ route('products.index') }}" class="sidebar-list-menu__title hover-white"><i class="sidebar-list-menu__tachometer-alt fas fa-boxes mr-2"></i>在庫一覧</a>
    </li>

    <!-- お知らせ機能 -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title"><i class="fas fa-envelope mr-2"></i>お知らせ機能</p>
      <ul class="sidebar-list-menu-items">
        <!-- お知らせ一覧 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('articles.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>お知らせ一覧</a>
        </li>
        <!-- お知らせ記事作成 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('articles.create') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>お知らせ記事作成</a>
        </li>
      </ul>
    </li>

    <!-- データ分析 -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title"><i class="sidebar-list-menu__trophy fas fa-chart-bar mr-2"></i>データ分析</p>
      <ul class="sidebar-list-menu-items">
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('analysis.index') }}"><i class="sidebar-list-menu__trophy fas fa-chevron-circle-right mr-2"></i>FC別</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('analysis.contacts') }}"><i class="sidebar-list-menu__trophy fas fa-chevron-circle-right mr-2"></i>本部問い合わせ数</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('analysis.contactdetail') }}"><i class="sidebar-list-menu__trophy fas fa-chevron-circle-right mr-2"></i>問い合わせ詳細</a>
        </li>
      </ul>
    </li>

    <!-- FCランキング -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title"><i class="sidebar-list-menu__trophy fas fa-trophy mr-2"></i>FCランキング</p>
      <ul class="sidebar-list-menu-items">
        <!-- 売り上げ -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('rankings.index', ['order' => 'sales', 'year' => date('Y'), 'month' => date('m') ]) }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>売り上げ</a>
        </li>
        <!-- 施工数 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('rankings.index', ['order' => 'number', 'year' => date('Y'), 'month' => date('m') ]) }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>施工数</a>
        </li>
      </ul>
    </li>

    <!-- FC管理 -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title"><i class="sidebar-list-menu__building fas fa-building mr-2"></i>FC管理</p>
      <ul class="sidebar-list-menu-items">
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('users.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>FC一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('users.contracts') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>契約更新FC一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('users.create') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>新規FC登録</a>
        </li>

      </ul>
    </li>

    <!-- アカウント設定 -->
    <li class="sidebar-list__li">
      <p class="sidebar-list-menu__title"><i class="sidebar-list-menu__cogs fas fa-cogs mr-2"></i>アカウント設定</p>
      <ul class="sidebar-list-menu-items">

      <!-- 通知日数設定 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('notifications.create') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>通知日数設定</a>
        </li>
      <!-- 会社休日カレンダー設定 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('settings.officeholiday') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>会社休日設定</a>
        </li>
      <!-- CSV export設定 -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('settings.csvexportoption') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>CSVエクスポート設定</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('settings.fcapplyareas.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>fc担当エリア設定</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('transactions.admin.shipping-price') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>送料設定</a>
        </li>
      </ul>
    </li>

<!--余った分----------------------------------------------------------------------------- -->
        <!-- <li class="sidebar-list-menu-items__li">
            <a href="{{ route('transactions.admin.dispatched') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>発注請書一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href=""><span class="sidebar-list-menu-items__li__circle-right mr-2">④</span></i>現場確認済み一覧</a>
        </li>
  
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('dispatched.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>部材発送連絡一覧</a>
        </li> 
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('contact.customers') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>案件一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('sample.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>サンプル送付リスト</a>
        </li>
        
        </ul>
    </li> 
  ------------------------------------------------------------------------------------ -->
    <!-- 顧客検索 -->
    <!-- <li class="sidebar-list__li">
      <a href="{{ route('search.index') }}" class="sidebar-list-menu__title">
            <i class="fas fa-search mr-2"></i>
            案件検索
      </a>
    </li> -->
</ul>
