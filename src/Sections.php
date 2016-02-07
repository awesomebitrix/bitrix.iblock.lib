<?php

namespace Falur\Iblock;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

class Sections 
{
	protected static $arSelect =  [
			'ID',
			'NAME',
			'LEFT_MARGIN',
			'RIGHT_MARGIN',
			'DEPTH_LEVEL',
			'IBLOCK_ID',
			'IBLOCK_SECTION_ID',
			'LIST_PAGE_URL',
			'SECTION_PAGE_URL',
			'DESCRIPTION',
			'PICTURE',
			'ACTIVE',
			'GLOBAL_ACTIVE',
			'CODE'
		];

	public static function getSections($arFilter = [], $arSort = [], $img_cache = false)
	{
		$arSelect = self::$arSelect;
		
		$arResult = ['SECTIONS' => [], 'SPAGINATION' => ''];

		$img_cache_type = isset($img_cache['type']) ? $img_cache['type'] : BX_RESIZE_IMAGE_EXACT;
		$img_cache_size = isset($img_cache['size']) ? $img_cache['size'] : $img_cache;
		
		$rsSections = \CIBlockSection::GetList($arSort, $arFilter, true, $arSelect);
		while ($arSection = $rsSections->GetNext())
		{
			$arSection['PICTURE'] = (0 < $arSection['PICTURE'] ? \CFile::GetFileArray($arSection['PICTURE']) : false);
			$arSection['DETAIL_PICTURE'] = (0 < $arSection['DETAIL_PICTURE'] ? \CFile::GetFileArray($arSection['DETAIL_PICTURE']) : false);
			
			if (is_array($img_cache) && $arSection['PICTURE']) 
			{
				$arSection['PICTURE_CACHE'] = \CFile::ResizeImageGet($arSection['PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			if (is_array($img_cache) && $arSection['DETAIL_PICTURE']) 
			{
				$arSection['DETAIL_PICTURE_CACHE'] = \CFile::ResizeImageGet($arSection['DETAIL_PICTURE'], $img_cache_size, $img_cache_type);
			}
			
			$arResult['SECTIONS'][] = $arSection;
		}
		
		return $arResult;
	}
	
	public static function getSection($arFilter = [], $img_cache = false)
	{
		$arSelect = self::$arSelect;
		$arResult = [];

		$img_cache_type = isset($img_cache['type']) ? $img_cache['type'] : BX_RESIZE_IMAGE_EXACT;
		$img_cache_size = isset($img_cache['size']) ? $img_cache['size'] : $img_cache;
		
		$arSection = \CIBlockSection::GetList($arSort, $arFilter, true, $arSelect)
									->GetNext();

		$arSection['PICTURE'] = (0 < $arSection['PICTURE'] ? \CFile::GetFileArray($arSection['PICTURE']) : false);
		$arSection['DETAIL_PICTURE'] = (0 < $arSection['DETAIL_PICTURE'] ? \CFile::GetFileArray($arSection['DETAIL_PICTURE']) : false);

		if (is_array($img_cache) && $arSection['PICTURE']) 
		{
			$arSection['PICTURE_CACHE'] = \CFile::ResizeImageGet($arSection['PICTURE'], $img_cache_size, $img_cache_type);
		}

		if (is_array($img_cache) && $arSection['DETAIL_PICTURE']) 
		{
			$arSection['DETAIL_PICTURE_CACHE'] = \CFile::ResizeImageGet($arSection['DETAIL_PICTURE'], $img_cache_size, $img_cache_type);
		}

		$arResult = $arSection;
		
		return $arResult;
	}
	
	public static function getPath($iblock_id, $section_id)
	{
		$rsSections = \CIBlockSection::GetNavChain($iblock_id, $section_id);
		
		$arResult = [];
		while($arSection = $rsSections->GetNext())
		{
			$arResult[] = $arSection;
		}
		
		return $arResult;
	}
}
