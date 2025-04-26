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

namespace BaksDev\Reference\Region\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Region\Entity\Invariable\RegionInvariable;
use BaksDev\Reference\Region\Entity\Modify\RegionModify;
use BaksDev\Reference\Region\Entity\Region;
use BaksDev\Reference\Region\Entity\Trans\RegionTrans;
use BaksDev\Reference\Region\Type\Event\RegionEventUid;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* RegionEvent */


#[ORM\Entity]
#[ORM\Table(name: 'region_event')]
class RegionEvent extends EntityEvent
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: RegionEventUid::TYPE)]
    private RegionEventUid $id;

    /** ID Region */
    #[ORM\Column(type: RegionUid::TYPE, nullable: false)]
    private ?RegionUid $region = null;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: RegionModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private RegionModify $modify;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: RegionInvariable::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private RegionInvariable $invariable;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: RegionTrans::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $translate;

    public function __construct()
    {
        $this->id = new RegionEventUid();
        $this->modify = new RegionModify($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }


    public function getId(): RegionEventUid
    {
        return $this->id;
    }


    public function setMain(RegionUid|Region $region): void
    {
        $this->region = $region instanceof Region ? $region->getId() : $region;
    }


    public function getMain(): ?RegionUid
    {
        return $this->region;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof RegionEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof RegionEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getNameByLocale(Locale $locale): ?string
    {
        $name = null;

        /** @var RegionTrans $trans */
        foreach($this->translate as $trans)
        {
            if($name = $trans->name($locale))
            {
                break;
            }
        }

        return $name;
    }

}
