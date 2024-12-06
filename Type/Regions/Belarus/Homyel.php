<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Region\Type\Regions\Belarus;

use BaksDev\Field\Country\Type\Country\Belarus;
use BaksDev\Field\Country\Type\Country\Collection\CountryInterface;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use BaksDev\Reference\Region\Type\Regions\RegionInterface;

/**
 * Гомель
 */
final class Homyel implements RegionInterface
{
    public const string TYPE = 'gm';

    public const string ID = '60d71f7e-24c2-75fb-ad24-ebed593c4966';

    public function __toString(): string
    {
        return self::TYPE;
    }

    public function country(): CountryInterface
    {
        return new Belarus();
    }

    public static function getRegionUid(): RegionUid
    {
        return new RegionUid(self::ID);
    }

    public static function priority(): int
    {
        return 100;
    }

    public static function equals(mixed $value): bool
    {
        $value = (string) mb_strtolower($value);

        return in_array($value, [self::TYPE, self::ID, 'homyel', 'gomel oblast', 'gomel region']);
    }
}