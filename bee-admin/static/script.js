jQuery(document).ready(function ($) {

	$("body.wp-admin[class*='appbeebee'] #poststuff").prepend("<div class='bee-admin-thead'><div class='bee-admin-thead__inside-container'><div class='bee-admin-thead__logo-container'><a class='bee-admin-thead__logo-link' href='admin.php?page=appbeebee'><svg width='26' height='26' viewBox='0 0 231 231' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><title>beebee-logo</title><defs><circle id='path-1' cx='115.5' cy='115.5' r='115.5'></circle></defs><g id='页面-1' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'><g id='beebee-logo'><mask id='mask-2' fill='white'><use xlink:href='#path-1'></use></mask><use id='椭圆_1' fill-opacity='0.8' fill='#FFB800' fill-rule='nonzero' xlink:href='#path-1'></use><path d='M115.5,0 C51.712,0 0,51.711 0,115.5 C0,152.49 17.391,185.415 44.438,206.554 L44.438,79 C44.438,69.335 52.273,61.5 61.938,61.5 C71.604,61.5 79.438,69.335 79.438,79 L79.438,86.393 L82.074,83.252 C88.287,75.849 99.325,74.883 106.729,81.095 C114.133,87.308 115.099,98.346 108.887,105.75 L79.438,140.844 L79.439,151.832 C85.711,144.814 96.468,144.002 103.729,150.095 C111.133,156.308 112.099,167.346 105.887,174.75 L79.532,206.158 L79.438,206.266 L79.438,225.25 C90.786,228.977 102.906,231 115.5,231 C120.144,231 124.721,230.718 129.221,230.185 C128.713,228.546 128.438,226.805 128.438,225 L128.438,60 C128.438,50.335 136.273,42.5 145.938,42.5 C155.604,42.5 163.438,50.335 163.438,60 L163.438,67.394 L166.074,64.252 C172.287,56.849 183.325,55.883 190.729,62.095 C198.133,68.308 199.099,79.346 192.886,86.75 L163.438,121.844 L163.438,132.861 C169.709,125.831 180.464,124.999 187.729,131.095 C195.133,137.308 196.099,148.346 189.886,155.75 L163.532,187.158 C163.502,187.193 163.469,187.223 163.438,187.258 L163.438,220.608 C203.299,202.398 231,162.186 231,115.5 C231,51.711 179.289,0 115.5,0' id='Fill-1' fill='#000000' mask='url(#mask-2)'></path></g></g></svg>比比小程序</a></div><div class='bee-admin-thead__nav'><span class='button-group'><a href='https://www.appbeebee.com/' target='_blank' type='button' class='button'>比比社区</a><a href='https://www.appbeebee.com/' target='_blank' type='button' class='button'>获取帮助</a><a href='https://www.appbeebee.com/' target='_blank' type='button' class='button'>更多主题</a></span></div></div></div>")

	var $domain = 'https://booker.demo.appbeebee.com/'
	var $themechecked = $('#beeapp-form input[type=radio][name*="choosetheme"]:checked');
	var $text = $themechecked.data("title") + $themechecked.data("vision") + '<br/>' + $themechecked.data("subtit");
	if ($themechecked.length > 0) {
		$themechecked.parents('.theme').addClass('active')
		$('#themechecked-tit').html('<div class="bee-title-header"><h2>已选用：' + $text + '</h2></div><h2 class="bee-blurb">' + $themechecked.data("desc") + '</h2><p class="bee-blurb">适合人群：' + $themechecked.data("crowd") + '</p>');
		var outHtml = $themechecked.parents('.theme').prop("outerHTML"); //获取到Html，包括当前节点
		$("#theme-list").prepend(outHtml); //追加到div1内部
		$themechecked.parents('.theme').remove(); //删除原来的html
	}
	$('.theme-screenshot').on('click', function (e) {
		var $themelink = $(this).data("link") ? $(this).data("link") : $domain
		window.open($themelink);
	})

	$(".input-disabled label,.input-disabled input").attr("disabled", "disabled");
	if ($(".acf-field[data-name=bee_book_dbpage] input").val() != '') {
		$(".acf-th[data-name=bee_book_dbpage] .beetips").attr('href', $(".acf-field[data-name=bee_book_dbpage] input").val()).css('display', 'inline');
	}
	if ($(".acf-field[data-name=bee_book_dbcover_l] input").val() != '') {
		$(".acf-th[data-name=bee_book_dbcover_l] .beetips").attr('href', $(".acf-field[data-name=bee_book_dbcover_l] input").val()).css('display', 'inline');
	}
	if ($(".acf-field[data-name=bee_book_dbcover_s] input").val() != '') {
		$(".acf-th[data-name=bee_book_dbcover_s] .beetips").attr('href', $(".acf-field[data-name=bee_book_dbcover_s] input").val()).css('display', 'inline');
	}


	$(".acf-field[data-name=bee_book_isbn] input").bind('input propertychange', function () {
		$("#bookautomsg span").remove();
		var isbn = $(".acf-field[data-name=bee_book_isbn] input").val();
		var reg = /^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/;
		var isbnreg = reg.test(isbn);
		if (!isbn) {
			$("#bookautomsg").prepend("<span style='color:#dba617;'>请先输入ISBN</span>");

		} else if (!isbnreg) {
			$("#bookautomsg").prepend("<span style='color:#d63638;'>请输入正确的ISBN</span>");
		} else {
			// query
			$("#bookautomsg .tips").remove();
			$.ajax({
				url: ajaxurl,
				type: 'GET',
				dataType: 'json',
				async: true,
				data: {
					action: 'automsglibrary',
					isbn: isbn
				},
				timeout: 15000,
				beforeSend: function () {
					$("#bookautomsg").prepend("<span style='color:#dba617;'>正在抓取，请稍等...</span>");
				},
				success: function (json) {
					console.log(json);
					var translator = json.translator ? ' / ' + json.translator : '';
					var publisher = json.publisher ? ' / ' + json.publisher : '';
					var published = json.published ? ' / ' + json.published : '';

					$(".acf-field[data-name=bee_book_dbid] input").val(json.id);
					$(".acf-field[data-name=bee_book_title] input").val(json.title);
					$(".acf-field[data-name=bee_book_price] input").val(json.price);

					$(".acf-field[data-name=bee_book_dbpage] input").val(json.url);
					$(".acf-field[data-name=bee_book_dbcover_l] input").val(json.logo);
					$(".acf-field[data-name=bee_book_dbcover_s] input").val(json.logos);

					$(".acf-field[data-name=bee_book_author] input").val(json.author);
					$(".acf-field[data-name=bee_book_translator] input").val(json.translator);
					$(".acf-field[data-name=bee_book_publisher] input").val(json.publisher);
					$(".acf-field[data-name=bee_book_published] input").val(json.published);
					$(".acf-field[data-name=bee_book_page] input").val(json.page);
					$(".acf-field[data-name=bee_book_designed] input").val(json.designed);

					$("#todoubanlogo").prepend('<a href="' + json.logo + '" target="_blank" style="width: 70px;height: 85px;border-radius: 5px;display:block;background-image:url(' + json.logo + ');background-size: cover;background-repeat: no-repeat;background-position: center;"><img src="https://img2.doubanio.com/view/subject/s/public/s33825712.jpg" alt="" /></a>');

					$("div.editor-post-title textarea").val(json.title);
					$("div.editor-post-excerpt textarea").val(json.author + translator + publisher + published)

					if (!json.title || json.title == null) {
						$(".beetips").css('display', 'none');
						$("#bookautomsg span").remove();
						$("#bookautomsg").prepend("<span style='color:#d63638;'>获取失败，请检查ISBN是否输出正确</span>")
					} else {
						$(".acf-field[data-name=bee_book_isbn] input").val(json.isbn);

						$(".acf-th[data-name=bee_book_dbpage] .beetips").attr('href', json.url).css('display', 'inline');
						$(".acf-th[data-name=bee_book_dbcover_l] .beetips").attr('href', json.logo).css('display', 'inline');
						$(".acf-th[data-name=bee_book_dbcover_s] .beetips").attr('href', json.logos).css('display', 'inline');

						$("#bookautomsg span").remove();
						$("#bookautomsg").prepend("<span style='color:#00a32a'>恭喜！书籍信息获取完成</span>");

					}

				},
				error: function (json) {
					//this.hideLoading();
					$("#bookautomsg span").remove();
					$("#bookautomsg").prepend("<span style='color:#d63638;'>获取失败，请检查网络</span>")

				},
				complete: function () {
					//this.hideLoading();
				}
			});
		}

	});
});