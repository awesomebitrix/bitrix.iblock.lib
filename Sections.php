<?php

/**
 * Bitrix lib iblock (webgsite.ru)
 * Библиотека облегчающая работу с инфоблоками битрикса
 *
 * @author    Falur <ienakaev@ya.ru>
 * @link      https://github.com/falur/bitrix.iblock.lib
 * @copyright 2015 - 2016 webgsite.ru
 * @license   GNU General Public License http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Falur\Bitrix\Iblock;

use Bitrix\Main\Loader;
use CIBlockSection;
use CFile;

Loader::includeModule('iblock');

class Sections
{
    protected static $arSelect = [
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
        'CODE',
        'SORT',
        'UF_*'
    ];

    public static function getSections($arFilter = [], $arSort = [], $imgCache = false)
    {
        $arSelect = self::$arSelect;
        $arResult = ['SECTIONS' => [], 'SPAGINATION' => ''];

        $rsSections = CIBlockSection::GetList($arSort, $arFilter, true, $arSelect);

        while ($arSection = $rsSections->GetNext()) {
            self::setImages($arSection, $imgCache);
            $arResult['SECTIONS'][] = $arSection;
        }

        return $arResult;
    }

    public static function getSection($arFilter = [], $imgCache = false)
    {
        $arSelect = self::$arSelect;
        $arResult = [];

        $arSection = CIBlockSection::GetList($arSort, $arFilter, true, $arSelect)->GetNext();

        self::setImages($arSection, $imgCache);

        $arResult = $arSection;

        return $arResult;
    }

    public static function setImages(&$arSection, $imgCache)
    {
        $img_cache_type = isset($imgCache['type']) ? $imgCache['type'] : BX_RESIZE_IMAGE_EXACT;
        $img_cache_size = isset($imgCache['size']) ? $imgCache['size'] : $imgCache;

        $arSection['PICTURE'] =
            0 < $arSection['PICTURE']
            ? CFile::GetFileArray($arSection['PICTURE'])
            : null;

        $arSection['DETAIL_PICTURE'] =
            0 < $arSection['DETAIL_PICTURE']
            ? CFile::GetFileArray($arSection['DETAIL_PICTURE'])
            : null;

        $arSection['PICTURE_CACHE'] = 
            is_array($imgCache) && $arSection['PICTURE']
            ? CFile::ResizeImageGet($arSection['PICTURE'], $img_cache_size, $img_cache_type)
            : null;
                
         $arSection['DETAIL_PICTURE_CACHE'] =
            is_array($imgCache) && $arSection['DETAIL_PICTURE']
            ? CFile::ResizeImageGet($arSection['DETAIL_PICTURE'], $img_cache_size, $img_cache_type)
            : null;
    }

    public static function getPath($iblock_id, $section_id)
    {
        $rsSections = CIBlockSection::GetNavChain($iblock_id, $section_id);

        $arResult  = [];
        while ($arSection = $rsSections->GetNext()) {
            $arResult[] = $arSection;
        }

        return $arResult;
    }
}
