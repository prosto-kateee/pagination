<?
// SEO <link rel="prev"...>, <link rel="next"...>, <title name="example_Страница- 1">
AddEventHandler("main", "OnEndBufferContent", "AddSeoLinks");
function AddSeoLinks(&$content)
{
	global $APPLICATION;
	$arFindNum = array();
	$arHeadPage = array();
	// хост с добавлением протокола и без порта
	$stHttpProt = (CMain::IsHTTPS()) ? "https://" : "http://";
	$stServerHost = $stHttpProt . preg_replace('/:[0-9]{1,}/', "", $_SERVER['HTTP_HOST']);
	// теги в <head>
	preg_match("/(?P<beforehead>(.*))<head>/is", $content, $arBeforeHeadPage);
	preg_match("/<head>(?P<headPage>(.*))<\/head>/is", $content, $arHeadPage);
	preg_match("/<\/head>(?P<afterhead>(.*))/is", $content, $arAfterHeadPage);
	if (!empty($arBeforeHeadPage["beforehead"]) && !empty($arAfterHeadPage["afterhead"]) && !empty($arHeadPage["headPage"])) {
		$stBeforehead = $arBeforeHeadPage["beforehead"];
		$headPage = $arHeadPage["headPage"];
		$stAfterhead = $arAfterHeadPage["afterhead"];
	}

	//Проверка на 404 страницу
	if (defined("ERROR_404") != true) {
		if ($content != false && strpos($content, '<!-- has_stranation_pagen') != false) {
			// выбираем из get-строки NavNum
			preg_match('/(<!-- has_stranation_pagen_)(?P<num>[0-9]{1,})?/i', $content, $arFindNum);
			if (!empty($arFindNum['num'])) {
				$stPageNum = "PAGEN_" . $arFindNum['num'];
			}

			// выбираем из get-строки NavPageNomer
			$nCurrentPage = 1;
			if (isset($_GET[$stPageNum]) && (int)$_GET[$stPageNum] > 1) {
				$nCurrentPage = (int)$_GET[$stPageNum];
			}

			//формируем url предыдущей страницы <link rel="prev" ...>
			$stUrl = $stServerHost . $APPLICATION->GetCurUri("", false);
			if ($nCurrentPage > 1) {
				if ($nCurrentPage == 2) {
					$stUrlPrev = preg_replace("/$stPageNum=$nCurrentPage/i", "", $stUrl);
					//очистка на случай, если останется последний символ ? или &
					$stUrlPrev = trim($stUrlPrev, '?&');
				} else {
					$nPrevPage = $nCurrentPage - 1;
					$stUrlPrev = preg_replace("/$stPageNum=$nCurrentPage/i", "$stPageNum=$nPrevPage", $stUrl);
				}
			}

			//формируем url следующей страницы для <link rel="next" ...>
			if (strpos($content, '<!-- it_last_page_stranation -->') == false) {
				$nNextPage = $nCurrentPage + 1;
				if($nCurrentPage == 1) {
					$stUrlNext = $stServerHost . $APPLICATION->GetCurUri("$stPageNum=$nNextPage", false);
				} else {
					$stUrlNext = preg_replace("/$stPageNum=$nCurrentPage/i", "$stPageNum=$nNextPage", $stUrl);
				}
			}

			// добавляем в <head>
			if ($stUrlPrev) $stUrls = '<link rel="prev" href="' . $stUrlPrev . '" />' . " ";
			if ($stUrlNext) $stUrls .= '<link rel="next" href="' . $stUrlNext . '" />';
			$content = $stBeforehead . "<head>" . $headPage . $stUrls . "</head>" . $stAfterhead;
			//AddMessage2Log($content);

			// при пагинации добавление в <title> куска " Страница - n"
			if ($nCurrentPage != 1) {
				preg_match("/<title>(?P<title>.*)<\/title>/is", $content, $stOldTitle);
				$content = preg_replace("/<title>(.*)<\/title>/is", "<title>" . $stOldTitle["title"] . " Страница - " . $nCurrentPage . "</title>", $content);
			}
		}
	}
}




AddEventHandler("main", "OnEpilog", "addTagLinkRel");
function addTagLinkRel() {
   global $APPLICATION;    
   
   //Проверка на 404 страницу
   $is_page_404 = false; 
   if(defined("ERROR_404") == true)
	   $is_page_404 = true;           
   
   //Добавление тегов rel=prev/next: начало
   if($is_page_404 == false)
   {
	  
	  $page_content = ob_get_contents();
	  if($page_content != false && strpos($page_content, '<!-- has_stranation_pagen') !== false)
	  {
		// preg_match('/(PAGEN_)([0-9]{1,})/i', $_SERVER['REQUEST_URI'], $find);
		preg_match('/(<!-- has_stranation_pagen_)([0-9]{1,})?/i', $page_content, $find);
		if(!empty($find)) {
			$pagen_param = "PAGEN_".$find[2];
			// $pagen_num = $find[2];
		}
		 
		if(isset($_GET[$pagen_param]) && (int)$_GET[$pagen_param] > 1)
			$current_page = (int)$_GET[$pagen_param];
		else
			$current_page = 1;
		
		// при пагинации добавление в <title> куска " Страница - n"
		if($current_page != 1) {
			$title = $APPLICATION->GetProperty("title");
			$newTitle = $APPLICATION->SetPageProperty("title", $title." Страница - ".$current_page);
		}
		 
		 //Проверка на последнюю страницу
		 $it_last_page = false;
		 if(strpos($page_content, '<!-- it_last_page_stranation -->') != false)
			$it_last_page = true;

		 //Следующая страница
		 if($it_last_page == false)
		 {
			// $element_url = 'http://'.$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam('PAGEN_1='.($current_page+1), array('PAGEN_1'));
			$element_url = 'https://'.$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam($pagen_param.'='.($current_page+1), array($pagen_param));
			$APPLICATION->AddHeadString('<link rel="next" href="'.$element_url.'" />', true);
		 }

		 //Предыдущая страница
		 if($current_page > 1) {
			if($current_page == 2) {
			   $element_url = 'https://'.$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam('', array($pagen_param));
			   //очистка на случай, если останется последний символ ?
			   $element_url = trim($element_url, '?');
			} else {
				// $element_url = 'http://'.$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam('PAGEN_1='.($current_page-1), array('PAGEN_1'));
				$element_url = 'https://'.$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam($pagen_param.'='.($current_page-1), array($pagen_param));
			}
			
			$APPLICATION->AddHeadString('<link rel="prev" href="'.$element_url.'" />', true);
		 }
	  }
	  
   }
   //Добавление тегов rel=prev/next: конец
}
