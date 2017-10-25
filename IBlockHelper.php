<?

use Bitrix\Main\Application,
    Bitrix\Main\Loader;

class IBlockHelper
{
    private static $cache,
        $sCacheDir = 'iblock_elements';

    /**
     * @param array $arFilter
     * @param array $arSelect
     * @param null $arOrder
     * @param null $iLimit
     * @param int $iCacheTime
     * @return array
     */
    public static function GetList(
        $arFilter = [],
        $arSelect = [],
        $arOrder = null,
        $iLimit = null,
        $iCacheTime = 604800
    ) {
        $arIBlockItems = array();

        $sCacheId = md5(serialize([
            $arOrder,
            $arFilter,
            $arSelect,
            $iLimit
        ]));

        $obCache = Application::getInstance()->getCache();

        if (is_null(self::$cache[$sCacheId])) {

            if ($obCache->initCache($iCacheTime, $sCacheId, self::$sCacheDir)) {
                $arIBlockItems = $obCache->getVars();
            } elseif (Loader::includeModule('iblock')) {
                $iLimit = intval($iLimit);
                $navStartParams = $iLimit > 0 ? ['nTopCount' => $iLimit] : false;
                $rsIBlockItems = \CIBlockElement::GetList($arOrder, $arFilter, false, $navStartParams, $arSelect);

                while ($arItem = $rsIBlockItems->Fetch()) {
                    $arIBlockItems[] = $arItem;
                }

                if ($obCache->startDataCache()) {
                    $obCache->endDataCache($arIBlockItems);
                }
            }

            self::$cache[$sCacheId] = $arIBlockItems;
        } else {
            $arIBlockItems = self::$cache[$sCacheId];
        }

        return $arIBlockItems;
    }
}