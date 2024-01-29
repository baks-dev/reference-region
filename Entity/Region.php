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

namespace BaksDev\Reference\Region\Entity;

use BaksDev\Reference\Region\Entity\Event\RegionEvent;
use BaksDev\Reference\Region\Type\Event\RegionEventUid;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/* Region */


#[ORM\Entity]
#[ORM\Table(name: 'region')]
class Region
{
	public const TABLE = 'region';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: RegionUid::TYPE)]
	private RegionUid $id;
	
	/** ID События */
	#[ORM\Column(type: RegionEventUid::TYPE, unique: true)]
	private RegionEventUid $event;

	public function __construct()
	{
		$this->id = new RegionUid();
	}

    public function setRegion(RegionUid $id): self
    {
        $this->id = $id;
        return $this;
    }
	
	
	public function getId() : RegionUid
	{
		return $this->id;
	}
	
	
	public function getEvent() : RegionEventUid
	{
		return $this->event;
	}
	
	
	public function setEvent(RegionEventUid|RegionEvent $event) : void
	{
		$this->event = $event instanceof RegionEvent ? $event->getId() : $event;
	}
	
}