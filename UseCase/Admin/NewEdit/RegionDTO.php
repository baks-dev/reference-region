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

namespace BaksDev\Reference\Region\UseCase\Admin\NewEdit;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Region\Entity\Event\RegionEventInterface;
use BaksDev\Reference\Region\Type\Event\RegionEventUid;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use BaksDev\Reference\Region\UseCase\Admin\NewEdit\Invariable\RegionInvariableDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class RegionDTO implements RegionEventInterface
{
    /** Идентификатор региона */
    #[Assert\Uuid]
    private ?RegionUid $region = null;

    /** Идентификатор события */
    #[Assert\Uuid]
    private ?RegionEventUid $id = null;

    /** Перевод */
    #[Assert\Valid]
    private ArrayCollection $translate;

    private RegionInvariableDTO $invariable;

    public function __construct()
    {
        $this->translate = new ArrayCollection();
        $this->invariable = new RegionInvariableDTO();
    }

    public function getEvent(): ?RegionEventUid
    {
        return $this->id;
    }


    /**
     * Id
     */
    public function getId(): ?RegionEventUid
    {
        return $this->id;
    }

    public function setId(?RegionEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Region
     */
    public function getRegion(): ?RegionUid
    {
        return $this->region;
    }

    public function withRegion(?RegionUid $region): self
    {
        $this->region = $region;
        return $this;
    }


    /** Перевод */

    public function setTranslate(ArrayCollection $trans): void
    {
        $this->translate = $trans;
    }


    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $RegionTransDTO = new Trans\RegionTransDTO();
            $RegionTransDTO->setLocal($locale);
            $this->addTranslate($RegionTransDTO);
        }

        return $this->translate;
    }


    public function addTranslate(Trans\RegionTransDTO $trans): void
    {
        if(empty($trans->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->translate->contains($trans))
        {
            $this->translate->add($trans);
        }
    }


    public function removeTranslate(Trans\RegionTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }

    /**
     * Invariable
     */
    public function getInvariable(): RegionInvariableDTO
    {
        return $this->invariable;
    }

}
