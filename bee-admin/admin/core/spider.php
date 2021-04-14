<?php
if ( !defined( 'ABSPATH' ) ) exit;

//开始增加自动获取豆瓣书影音脚本
add_action('wp_ajax_nopriv_automsglibrary', 'automsglibrary_callback');
add_action('wp_ajax_automsglibrary', 'automsglibrary_callback');


function cut($content, $start, $end)
{
	$r = explode($start, $content);
	if (isset($r[1])) {
		$r = explode($end, $r[1]);
		return $r[0];
	}
	return '';
}

function automsglibrary_callback()
{
	$isbn = sanitize_text_field($_GET['isbn']);
	$surl = 'https://book.douban.com/isbn/' . $isbn . '/';
	$response = wp_remote_get($surl);
	if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
		$data = $response['body'];
	}
	$search = array(" ", "　", "\n", "\r", "\t");
	$replace = array("", "", "", "", "");
	$data_1 = cut($data, 'application/ld+json">', '</script>');
	$data_1 = json_decode($data_1, true);
	$res['isbn'] = $data_1['isbn'];
	$res['title'] = $data_1['name'];

	$authors = $data_1['author'];
	if (!empty($authors)) {
		$__authors = '';
		foreach ($authors as $author) {
			$__authors .= sprintf(
				'%1$s ',
				$author['name']
			);
		} 
		$res['author'] = $__authors;
	}

	$res['url'] = $data_1['url'];

	$res['id'] = cut($res['url'], 'subject/', '/');

	$res['logo'] = cut($data, 'data-pic="', '"');

	$res['logos'] = str_replace('subject/l/public','subject/s/public',$res['logo']); 

	$publisher_txt = cut($data, '出版社:</span>', '<br/>');
	$publisher = str_replace($search, $replace, $publisher_txt);
	$res['publisher'] = $publisher;

	$published_txt = cut($data, '出版年:</span>', '<br/>');
	$published = str_replace($search, $replace, $published_txt);
	$res['published'] = $published;

	$page_txt = cut($data, '页数:</span>', '<br/>');
	$page = str_replace($search, $replace, $page_txt);
	$res['page'] = $page;

	$translator_html = cut($data, '译者</span>:', '</span><br/>');
	$translator_txt = strip_tags($translator_html);
	$translator = str_replace($search, $replace, $translator_txt);
	$res['translator'] = $translator;

	$price_txt = cut($data, '定价:</span>', '<br/>');
	$price = str_replace($search, $replace, $price_txt);
	if ($price == '') {
		$price = '未知';
	}
	$res['price'] = $price;

	$designed_txt = cut($data, '装帧:</span>', '<br/>');
	$designed = str_replace($search, $replace, $designed_txt);
	$res['designed'] = $designed;

	// $description = cut($data,'class="intro">','</p>');
	// $description = explode('<p>',$description)[1];
	// if($description==''){
	//   $description ='未知';
	// }
	// $res['description'] =$description;

	$res = json_encode($res, true);
	echo $res;
	
	exit;
}