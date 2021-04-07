<?php

namespace Zhineng\Checkpoint\Tencent;

/**
 * @link https://en.wikipedia.org/wiki/ISO/IEC_5218
 */
class Gender
{
    public const NOT_KNOWN = 0;
    public const MALE = 1;
    public const FEMALE = 2;
    public const NOT_APPLICABLE = 9;

    public static function make(string|null $value): int|null
    {
        $lookup = [
            '男' => static::MALE,
            '女' => static::FEMALE,
        ];

        return $lookup[$value] ?? $value;
    }
}