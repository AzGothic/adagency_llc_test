<?php
namespace app\helper;

class arrayHelper
{
    public static function merge()
    {
        $arrays = func_get_args();
        $res    = array_shift($arrays);
        while (!empty($arrays)) {
            $next = array_shift($arrays);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    }
                    else {
                        $res[$k] = $v;
                    }
                }
                elseif (is_array($v) and isset($res[$k]) and is_array($res[$k])) {
                    $res[$k] = static::merge($res[$k], $v);
                }
                else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }
}