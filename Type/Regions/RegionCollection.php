<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Region\Type\Regions;

use BaksDev\Field\Country\Type\Country\CountryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class RegionCollection
{
    public function __construct(
        #[AutowireIterator('baks.region', defaultPriorityMethod: 'priority')] private iterable $status
    ) {}

    /**
     * Возвращает массив из значений Region
     * @return array<RegionInterface>
     */
    public function cases(?CountryInterface $country = null): array
    {
        $case = null;

        foreach($this->status as $key => $value)
        {
            /** @var RegionInterface $region */
            $region = new $value();

            if(true === is_null($country))
            {
                $case[$region::priority().$key] = $region;
            }

            if(false === is_null($country) && $region->country()::equals($country))
            {
                $case[$region::priority().$key] = $region;
            }
        }

        ksort($case);

        return $case;
    }

}
