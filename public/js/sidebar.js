/*

<script src="{{ asset('js/jquery/jquery-3.4.1.min.js') }}" defer></script>

*/
jQuery (function (){
	
    //.sidebar-listの中のp要素がクリックされたら
	$('.sidebar-list .sidebar-list-menu__title').click(function(){
 
		//クリックされた.sidebar-listの中のp要素に隣接する.sidebar-listの中の.sidebar-list-menu-itemsを開いたり閉じたりする。
		$(this).next('.sidebar-list .sidebar-list-menu-items').slideToggle();
 
		//クリックされた.sidebar-listの中のp要素以外の.sidebar-listの中のp要素に隣接する.sidebar-listの中の.sidebar-list-menu-itemsを閉じる
		$('.sidebar-list .sidebar-list-menu__title').not($(this)).next('.sidebar-list .sidebar-list-menu-items').slideUp();
		$('.sidebar-list .sidebar-list-menu__title-2nd').not($(this)).next('.sidebar-list .sidebar-list-menu-items').slideUp();
 
	});

	$('.sidebar-list .sidebar-list-menu__title-2nd').click(function(){
		$(this).next('.sidebar-list .sidebar-list-menu-items').slideToggle();
	});
})

