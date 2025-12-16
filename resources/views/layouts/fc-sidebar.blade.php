<ul class="sidebar-list">
    <li class="sidebar-list__li d-none d-sm-block">
        <a href="{{ route('dashboard') }}" class="sidebar-list-menu__title hover-white"><i class="sidebar-list-menu__tachometer-alt fas fa-home mr-2"></i>ホーム</a>
    </li>
  @if($user->allow_email=='1')
    <li class="sidebar-list__li d-none d-sm-block">
      <p class="sidebar-list-menu__title">
          <i class="fas fa-envelope mr-2"></i>
          お問い合わせ管理
      </p>
      <ul class="sidebar-list-menu-items">
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('contact.form') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right mr-2"></i>自己獲得案件登録</a>
        </li>
        <li>
          <p class="sidebar-list-menu__title-2nd">
            <i class="sidebar-list-menu-items__li__circle-right fas fa-align-justify mr-2"></i>
            顧客対応リスト
            <span class="sidebar-list-menu-items__li-badge-top">{{ !empty($totalTasks) ? $totalTasks : '' }}</span>
          </p>
          <!-- アコーディオン２段目 -->
          <ol class="sidebar-list-menu-items">
            <li class="sidebar-list-menu-items__li">
              <a href="{{ route('assigned.list') }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">①</span>アポ取り未完了一覧</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[2]) ? $tasks[2] : '' }}</span>
            </li>
            <li class="sidebar-list-menu-items__li">
              <a href="{{ route('before.report') }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">②</span>現場確認報告</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[3]) ? $tasks[3] : '' }}</span>
            </li>            
            <li class="sidebar-list-menu-items__li">
              <a href="{{route('quotations.needs') }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">③</span>見積もり未作成案件一覧</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[4]) ? $tasks[4] : '' }}</span>
            </li>
            <li class="sidebar-list-menu-items__li">
              <a href="{{ route('pending.list')  }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">④</span>商談結果入力待ち一覧</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[5]) ? $tasks[5] : '' }}</span>
            </li>
            <li class="sidebar-list-menu-items__li">
              <a href="{{ route('transaction.pending.list') }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">⑤</span>部材発注待ち案件一覧</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[6]) ? $tasks[6] : '' }}</span>
            </li>  
            <!-- <li class="sidebar-list-menu-items__li">
              <a href="{{ route('transaction.payment.pending.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>部材発注支払い待ち一覧</a>
            </li>   -->
            <li class="sidebar-list-menu-items__li">
              <a href="{{ route('report.pending') }}"><span class="sidebar-list-menu-items__li__circle-right mr-2">⑥</span>施工完了報告待ち一覧</a>
              <span class="sidebar-list-menu-items__li-badge">{{ !empty($tasks[10]) ? $tasks[10] : '' }}</span>
            </li>
          </ol>
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('dispatched.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>部材発送連絡一覧</a>
          </li>  
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('notifications.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>通知一覧</a>
          </li>
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('contact.cancel') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>キャンセル案件</a>
          </li>
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('report.list') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>完了案件一覧</a>
          </li>
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('contact.customers') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>案件一覧</a>
          </li>
        </li>
      </ul>
    </li>
    <li class="sidebar-list__li d-none d-sm-block">
      <p class="sidebar-list-menu__title">
        <i class="fas fa-box mr-2"></i>
        在庫管理
      </p>
      <ul class="sidebar-list-menu-items">
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('products.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>商品一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
            <a class="disabled"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>資材発注</a>
        </li>  
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('quotations.index') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>見積もり書一覧</a>
        </li>
        <li class="sidebar-list-menu-items__li">
            <a href="{{ route('transactions') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>発注書一覧</a>
        </li>
      </ul>
    </li>
  @endif
    <li class="sidebar-list__li d-none d-sm-block">
        <p class="sidebar-list-menu__title">
            <i class="fas fa-trophy mr-2"></i>
            FCランキング
        </p>
        <ul class="sidebar-list-menu-items">
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('rankings.index', ['order' => 'sales']) }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>売り上げ</a>
          </li>
          <li class="sidebar-list-menu-items__li">
            <a href="{{ route('rankings.index', ['order' => 'number']) }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>施工数</a>
          </li>
        </ul>
    </li>
    <li class="sidebar-list__li d-none d-sm-block">
      <p class="sidebar-list-menu__title" style='position: relative;'>
        <i class="fas fa-envelope mr-2"></i>
        <a class='color-white hover-white' href="{{ route('articles.index') }}">お知らせ一覧</a>
      </p>
    </li>
    <!-- データ分析 -->
    <li class="sidebar-list__li">
      <a href="{{ route('analysis.fc.index') }}" class="sidebar-list-menu__title"><i class="sidebar-list-menu__trophy fas fa-chart-bar mr-2"></i>データ分析</a>
    </li>


    <!-- 顧客検索 -->
        <!--<li class="sidebar-list__li">
      <a href="{{ route('search.index') }}" class="sidebar-list-menu__title">
            <i class="fas fa-search mr-2"></i>
            顧客検索
      </a>
    </li>
    -->
    <li class="sidebar-list__li d-none d-sm-block">
      <p class="sidebar-list-menu__title">
          <i class="fas fa-cogs mr-2"></i>
          アカウント設定
      </p>
      <ul class="sidebar-list-menu-items">
    <!-- 定期利用料 -->    
        <!-- <li class="sidebar-list-menu-items__li">
          <a href="{{ route('payments.subscribe.create') }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>定期利用料支払い</a>
        </li>
        -->
        <li class="sidebar-list-menu-items__li">
          <a href="{{ route('users.edit', ['id' => $user->id ] ) }}"><i class="sidebar-list-menu-items__li__circle-right fas fa-chevron-circle-right"></i>プロフィール編集</a>
        </li>
      </ul>
    </li>
    {{-- スマートフォンここから --}}
    <li class="sidebar-list__li d-block d-sm-none">
      <a href="{{ route('before.report') }}" class="sidebar-list-menu__title bg-info"><i class="sidebar-list-menu-items__li__circle-right fas fa-flag mr-3"></i>現場確認報告</a>
    </li>            

    <li class="sidebar-list__li d-block d-sm-none">
      <a href="{{ route('report.pending') }}" class="sidebar-list-menu__title bg-info"><i class="sidebar-list-menu-items__li__circle-right fas fa-flag mr-3"></i>施工完了報告</a>
    </li>

    <li class="sidebar-list__li d-block d-sm-none">
      <a href="{{ route('dispatched.list') }}" class="sidebar-list-menu__title bg-info"><i class="sidebar-list-menu-items__li__circle-right fas fa-flag mr-3"></i>発送連絡一覧</a>
    </li>

    <li class="sidebar-list__li d-none d-sm-block">
      <a href="{{ 'https://docs.google.com/forms/d/e/1FAIpQLSdPEyUnSfoy4LPNSFB08GN6PtepH3crYhi0Z-rX8pyQSTsZnw/viewform?usp=pp_url&entry.542165515=ID:'. $user->id . ' ' . $user->company_name . '&entry.1088564451=' . $_SERVER['REQUEST_URI']}}" target="blank" class="sidebar-list-menu__title"><i class="sidebar-list-menu-items__li__circle-right fas fa-comment-dots mr-3"></i>不具合報告</a>
    </li>
    <!-- 空白部分 -->
    <li class="sidebar-list__li d-none d-sm-block">
        <p class="sidebar-list-menu__title sidebar-list-menu__space"></p>
    </li>
</ul>

