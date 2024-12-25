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

namespace BaksDev\Reference\Region\Repository\CurrentRegion;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Reference\Region\Entity\Event\RegionEvent;
use BaksDev\Reference\Region\Entity\Region;
use BaksDev\Reference\Region\Type\Event\RegionEventUid;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use InvalidArgumentException;

final class CurrentRegionEventRepository implements CurrentRegionEventInterface
{
    private RegionUid|false $region = false;

    private RegionEventUid|false $event = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function region(Region|RegionUid|string $region): self
    {
        if(false !== $this->event)
        {
            throw new InvalidArgumentException('RegionEventUid уже присвоен');
        }

        if($region instanceof RegionUid)
        {
            $this->region = $region;
        }

        if($region instanceof Region)
        {
            $this->region = $region->getId();
        }

        if(is_string($region))
        {
            $this->region = new RegionUid($region);
        }

        return $this;
    }

    public function event(RegionEvent|RegionEventUid|string $event): self
    {
        if(false !== $this->region)
        {
            throw new InvalidArgumentException('RegionUid уже присвоен');
        }

        if($event instanceof RegionEventUid)
        {
            $this->event = $event;
        }

        if($event instanceof RegionEvent)
        {
            $this->event = $event->getId();
        }

        if(is_string($event))
        {
            $this->event = new RegionEventUid($event);
        }

        return $this;
    }

    /**
     * Метод возвращает активное событие региона
     */
    public function find(): RegionEvent|false
    {
        if(false === $this->region && false === $this->event)
        {
            throw new InvalidArgumentException('Необходимо передать один из параметров region|event');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm->select('event');

        if($this->region instanceof RegionUid)
        {
            $orm
                ->from(Region::class, 'region')
                ->where('region.id = :id')
                ->setParameter('id', $this->region, RegionUid::TYPE);

        }

        if($this->event instanceof RegionEventUid)
        {
            $orm
                ->from(RegionEvent::class, 'pre_event')
                ->where('pre_event.id = :id')
                ->setParameter('id', $this->region, RegionUid::TYPE);

            $orm->leftJoin(
                Region::class,
                'region',
                'WITH',
                'region.id = pre_event.region'
            );
        }

        $orm->leftJoin(
            RegionEvent::class,
            'event',
            'WITH',
            'event.id = region.event'
        );

        return $orm->getQuery()->getOneOrNullResult() ?: false;
    }
}