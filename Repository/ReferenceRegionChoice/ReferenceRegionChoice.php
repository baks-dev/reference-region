<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Region\Entity as RegionEntity;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ReferenceRegionChoice implements ReferenceRegionChoiceInterface
{

    private TranslatorInterface $translator;
    private ORMQueryBuilder $ORMQueryBuilder;


    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        TranslatorInterface $translator
    )
    {

        $this->translator = $translator;
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }


    public function getRegionChoice()
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        //$qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);

        $select = sprintf('new %s(region.id, region_trans.name)', RegionUid::class);
        $qb->select($select);

        $qb->from(RegionEntity\Region::class, 'region');

        $qb->join(RegionEntity\Event\RegionEvent::class,
            'region_event',
            'WITH',
            'region_event.id = region.event AND region_event.active = true'
        );

        $qb->leftJoin(RegionEntity\Trans\RegionTrans::class,
            'region_trans',
            'WITH',
            'region_trans.event = region_event.id AND region_trans.local = :local'
        );

        $qb->orderBy('region_event.sort');
        $qb->addOrderBy('region_trans.name');

        $qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);


        /* Кешируем результат ORM */
        return $qb->enableCache('reference-region', 86400)->getResult();
    }

}