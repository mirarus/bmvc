<?php

/**
 * Filter
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

namespace BMVC\Core;

class Filter
{

    private static function filterDataValue(object $object)
    {
        if (is_object($object)) {
            if (isset($object->string) && $object->string != '') {
                $string = $object->string;
                if (isset($object->filters) && is_array($object->filters)) {
                    foreach ($object->filters as $key => $value) {
                        if (is_callable($key)) {
                            array_unshift($value, $string);
                            $string = call_user_func_array($key, $value);
                        }
                    }
                }
                return $string;
            }
            return false;
        }
        return false;
    }

    private static function prepareDataObject(string $string, array $array=[])
    {
        if (isset($string) && $string != '') {

            $object = new \stdClass();
            $object->string = $string;
            $object->filters = [
                'strip_tags' => [], 
                'addslashes' => [], 
                'htmlspecialchars' => [ENT_QUOTES]
            ];

            if (count($array) > 0) {
                $object->filters = $array;
            }
            return $object;
        }
        return false;
    }

    static function filterXSS(array $filterArray, array $skipArray=[])
    {
        if (is_array($filterArray) && count($filterArray) > 0) {
            foreach ($filterArray as $key => $value) {
                if (!in_array($key, $skipArray)) {
                    if ($value != '' && !is_array($value) && !is_object($value)) {

                        $objectStr = self::prepareDataObject($value, [
                            'htmlspecialchars' => [ENT_QUOTES]
                        ]);
                        $filterArray[$key] = self::filterDataValue($objectStr);
                    }
                }
            }
            return $filterArray;
        }
        return false;
    }

    static function filterDB(string $text)
    {
        $check[1] = chr(34); // symbol "
        $check[2] = chr(39); // symbol '
        $check[3] = chr(92); // symbol /
        $check[4] = chr(96); // symbol `
        $check[5] = "drop table";
        $check[6] = "update";
        $check[7] = "alter table";
        $check[8] = "drop database";
        $check[9] = "drop";
        $check[10] = "select";
        $check[11] = "delete";
        $check[12] = "insert";
        $check[13] = "alter";
        $check[14] = "destroy";
        $check[15] = "table";
        $check[16] = "database";
        $check[17] = "union";
        $check[18] = "TABLE_NAME";
        $check[19] = "1=1";
        $check[20] = 'or 1';
        $check[21] = 'exec';
        $check[22] = 'INFORMATION_SCHEMA';
        $check[23] = 'like';
        $check[24] = 'COLUMNS';
        $check[25] = 'into';
        $check[26] = 'VALUES';
        $check[27] = 'kill';
        $check[28] = 'union';
        $check[29] = '$';
        $check[30] = '<?php';
        $check[31] = '?>';
        $y = 1;
        $x = sizeof($check);
        while ($y <= $x) {
            $target = strpos($text, $check[$y]);
            if ($target !== false)
                $text = str_replace($check[$y], "", $text);
            $y++;
        }
        return $text;
    }
}