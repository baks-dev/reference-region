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

namespace BaksDev\Reference\Region\Repository\ReferenceRegionChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Region\Entity\Invariable\RegionInvariable;
use BaksDev\Reference\Region\Entity\Region;
use BaksDev\Reference\Region\Entity\Trans\RegionTrans;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use Generator;

final readonly class ReferenceRegionChoiceRepository implements ReferenceRegionChoiceInterface
{
    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    /**
     * Метод возвращает список идентификаторов регионов
     *
     * @return Generator<RegionUid>|false
     */
    public function getRegionChoice(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->addSelect('region.id AS value')
            ->from(Region::class, 'region');

        $dbal->join(
            'region',
            RegionInvariable::class,
            'invariable',
            'invariable.main = region.id AND invariable.active = true',
        );

        $dbal
            ->addSelect('trans.name AS option')
            ->leftJoin(
                'region',
                RegionTrans::class,
                'trans',
                'trans.event = region.event AND trans.local = :local',
            );

        $dbal->orderBy('invariable.sort');
        $dbal->addOrderBy('trans.name');

        return $dbal
            //->enableCache('reference-region', 86400)
            ->fetchAllHydrate(RegionUid::class);
    }

}
