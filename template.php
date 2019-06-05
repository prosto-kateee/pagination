<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult['NavPageCount'] > 5) {
	if ($arResult["NavPageNomer"] > 3) {
		$arResult["nStartPage"]++;
		$threePointsAndFirstPage = true;
	}
	if($arResult["NavPageNomer"] < ($arResult["NavPageCount"]-2)) {
		$arResult["nEndPage"]--;
		$threePointsAndLastPage = true;
	}
}

?>
	<div class="bx-pagination">
		<div class="bx-pagination-container ">
	<?
	if(($arResult["NavPageNomer"] - 1) == 1){
		$next_link = $arResult["sUrlPath"].$strNavQueryStringFull;
		$next_class = '';
	} elseif(($arResult["NavPageNomer"] - 1) > 1) {
		$next_link = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] - 1);
		$next_class = '';
	} else {
		$next_link = '';
		$next_class = 'is-hidden';
	}
	if(($arResult["NavPageNomer"] + 1) <= $arResult["NavPageCount"]){
		$prev_link = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] + 1);
		$prev_class = '';
	} else {
		$prev_link = '';
		$prev_class = 'is-hidden';
	}
	?>
		<ul>	
			<?if(!empty($next_link)):?>
				<a href="<?=$next_link?>" class="<?=$next_class?> arrow"><span> < <?=GetMessage('round_nav_back');?> </span></a>
			<?endif;?>
	
			<? if($threePointsAndFirstPage): ?>
					<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
					<li><span class="point_sep">...</span></li>
			<? endif; ?>
			 
			<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
				<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
					<li><a class="bx-active"><span><?=$arResult["nStartPage"]?></span></a></li>
				<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
					<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
				<?else:?>
					<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
				<?endif?>
				<?$arResult["nStartPage"]++?>
			<?endwhile?>
			
			<? if($threePointsAndLastPage): ?>
					<li><span class="point_sep">...</span></li>
					<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
			<? endif; ?>
			
			<?if(!empty($prev_link)):?>
				<a href="<?=$prev_link?>" class="<?=$prev_class?> arrow"><span> <?=GetMessage('round_nav_forward');?> > </span></a>
			<?endif;?>
		</ul>
	<div style="clear:both"></div>
	</div>
</div>
<?
	unset($threePointsAndFirstPage);
	unset($threePointsAndLastPage);
?>

<?
//Выводим метки для добавления <link rel="prev/next">: начало
$nav_index = (int)$arResult['NavNum'];
$quantity_pages = (int)$arResult['NavPageCount'];

if($nav_index >= 1) {
	if((int)$_GET['PAGEN_'.$nav_index] > 1) {
		$current_page = $_GET['PAGEN_'.$nav_index];
	} else {
		$current_page = 1;
	}
}
if($quantity_pages > 1 && $current_page >= 1 && $current_page <= $quantity_pages) {
	echo '<!-- has_stranation_pagen_'.$nav_index.' -->';
	if($current_page >= $quantity_pages)
	 	echo '<!-- it_last_page_stranation -->';
}
//Выводим метки для добавления <link rel="prev/next">: конец
?>
