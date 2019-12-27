<?php

namespace app\common\base;

use think\Controller;

class DigitalHelper extends Controller {

    const MAX = '99999999.99';

    protected static $SCALE = '2';

    /**
     * 数字比较
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return int
     */
    public static function comp($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bccomp($left, $right, $scale);
    }

    /**
     * 加法运算
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return string
     */
    public static function add($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bcadd($left, $right, $scale);
    }

    /**
     * 减法运算
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return string
     */
    public static function sub($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bcsub($left, $right, $scale);
    }

    /**
     * 乘法运算
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return string
     */
    public static function mul($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bcmul($left, $right, $scale);
    }

    /**
     * 除法运算
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return string
     */
    public static function div($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bcdiv($left, $right, $scale);
    }

    /**
     * 数字的成方
     * @param string $left
     * @param string $right
     * @param int $scale
     * @return string
     */
    public static function pow($left, $right, $scale = null) {
        $scale = static::scale($scale);
        return bcpow($left, $right, $scale);
    }

    protected static function scale($scale) {
        return is_null($scale) ? static::$SCALE : $scale;
    }


}