<?php

namespace Falur\Iblock;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

class Elements 
{
	protected static $arSelect  = [
			'ID',
			'IBLOCK_ID',
			'CODE',
			'XML_ID',
			'NAME',
			'ACTIVE',
			'DATE_ACTIVE_FROM',
			'DATE_ACTIVE_TO',
			'SORT',
			'PREVIEW_TEXT',
			'PREVIEW_TEXT_TYPE',
			'DETAIL_TEXT',
			'DETAIL_TEXT_TYPE',
			'DATE_CREATE',
			'CREATED_BY',
			'TIMESTAMP_X',
			'MODIFIED_BY',
			'TAGS',
			'IBLOCK_SECTION_ID',
			'DETAIL_PAGE_URL',
			'DETAIL_PICTURE',
			'PREVIEW_PICTURE',
			'SHOW_COUNTER',
			'PROPERTY_*'
		];

	public static function getElements($arFilter = [], $arSort = [], $pagination = false, $img_cache = false)
	{
		$arSelect = self::$arSelect;
		$arResult = array('ITEMS' => [], 'PAGINATION' => '');

		if ($pagination)
			$arNavParams = [
				'nPageSize' => $pagination,
			];
		else
			$arNavParams = false;
			
		
		$img_cache_type = isset($img_cache['type']) ? $img_cache['type'] : BX_RESIZE_IMAGE_EXACT;
		$img_cache_size = isset($img_cache['size']) ? $img_cache['size'] : $img_cache;
		
		$rsElements = \CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
		while ($ob = $rsElements->GetNextElement())
		{
			$arItem = $ob->GetFields();
			$arItem['PROPERTIES'] = $ob->GetProperties();

			$arItem['PREVIEW_PICTURE'] = (0 < $arItem['PREVIEW_PICTURE'] ? \CFile::GetFileArray($arItem['PREVIEW_PICTURE']) : false);
			$arItem['DETAIL_PICTURE']  = (0 < $arItem['DETAIL_PICTURE']  ? \CFile::GetFileArray($arItem['DETAIL_PICTURE'])  : false);
			

			if (is_array($img_cache) && $arItem['PREVIEW_PICTURE']) 
			{
				$arItem['PREVIEW_PICTURE_CACHE'] = \CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			if (is_array($img_cache) && $arItem['DETAIL_PICTURE']) 
			{
				$arItem['DETAIL_PICTURE_CACHE'] = \CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			$arButtons = \CIBlock::GetPanelButtons(
					$arItem['IBLOCK_ID'], 
					$arItem['ID'], 
					0, 
					[
						'SECTION_BUTTONS' => false, 
						'SESSID' => false
					]
			);
			$arItem['EDIT_LINK']   = $arButtons['edit']['edit_element']['ACTION_URL'];
			$arItem['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];

			$arResult['ITEMS'][] = $arItem;
		}
		
		if ($pagination)
			$arResult['PAGINATION'] = $rsElements->GetPageNavStringEx($navComponentObject, 'Страницы:', '.default');
		
		return $arResult;
	}
	
	public static function getElement($arFilter = [], $img_cache = false)
	{
		$arSelect = self::$arSelect;
		$arResult = array();
		
		$img_cache_type = isset($img_cache['type']) ? $img_cache['type'] : BX_RESIZE_IMAGE_EXACT;
		$img_cache_size = isset($img_cache['size']) ? $img_cache['size'] : $img_cache;

		$rsElements = \CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		while ($ob = $rsElements->GetNextElement())
		{
			$arItem = $ob->GetFields();
			$arItem['PROPERTIES'] = $ob->GetProperties();

			$arItem['PREVIEW_PICTURE'] = (0 < $arItem['PREVIEW_PICTURE'] ? \CFile::GetFileArray($arItem['PREVIEW_PICTURE']) : false);
			$arItem['DETAIL_PICTURE']  = (0 < $arItem['DETAIL_PICTURE']  ? \CFile::GetFileArray($arItem['DETAIL_PICTURE'])  : false);
			
			if (is_array($img_cache) && $arItem['PREVIEW_PICTURE']) 
			{
				$arItem['PREVIEW_PICTURE_CACHE'] = \CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			if (is_array($img_cache) && $arItem['DETAIL_PICTURE']) 
			{
				$arItem['DETAIL_PICTURE_CACHE'] = \CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			$arButtons = \CIBlock::GetPanelButtons(
					$arItem['IBLOCK_ID'], 
					$arItem['ID'], 
					0, 
					[
						'SECTION_BUTTONS' => false, 
						'SESSID' => false
					]
			);
			$arItem['EDIT_LINK']   = $arButtons['edit']['edit_element']['ACTION_URL'];
			$arItem['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];
		}
		
		$arResult = $arItem;
		
		return $arResult;
	}
}
