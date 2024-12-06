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

namespace BaksDev\Reference\Region\Type\Regions\Russia;

use BaksDev\Field\Country\Type\Country\Collection\CountryInterface;
use BaksDev\Field\Country\Type\Country\Russia;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use BaksDev\Reference\Region\Type\Regions\RegionInterface;

/**
 * Москва и Московская обл.
 */
final class Moscow implements RegionInterface
{
    public const string TYPE = 'msk';

    public const string ID = '201042a6-c35d-7bc4-9cb9-ef8bc1c8711e';

    public function __toString(): string
    {
        return self::TYPE;
    }

    public function country(): CountryInterface
    {
        return new Russia();
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

        return in_array($value, [self::TYPE, self::ID]);
    }
}