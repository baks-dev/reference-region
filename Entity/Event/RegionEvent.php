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

namespace BaksDev\Reference\Region\Entity\Event;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use BaksDev\Reference\Region\Entity\Modify\RegionModify;
use BaksDev\Reference\Region\Type\Event\RegionEventUid;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use InvalidArgumentException;

/* RegionEvent */


#[ORM\Entity]
#[ORM\Table(name: 'region_event')]
class RegionEvent extends EntityEvent
{
	public const TABLE = 'region_event';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: RegionEventUid::TYPE)]
	private RegionEventUid $id;
	
	/** ID Region */
	#[ORM\Column(type: RegionUid::TYPE, nullable: false)]
	private ?RegionUid $main = null;
	
	/** Модификатор */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: RegionModify::class, cascade: ['all'])]
	private RegionModify $modify;
	
	/** Перевод */
	//#[ORM\OneToMany(mappedBy: 'event', targetEntity: RegionTrans::class, cascade: ['all'])]
	//private Collection $translate;
	
	public function __construct()
	{
		$this->id = new RegionEventUid();
		$this->modify = new RegionModify($this);
		
	}
	
	
	public function __clone()
	{
		$this->id = new RegionEventUid();
	}
	
	
	public function __toString() : string
	{
		return (string) $this->id;
	}
	
	
	public function getId() : RegionEventUid
	{
		return $this->id;
	}
	
	
	public function setMain(RegionUid|Region $main) : void
	{
		$this->main = $main instanceof Region ? $main->getId() : $main;
	}
	
	
	public function getMain() : ?RegionUid
	{
		return $this->main;
	}
	
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof RegionEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof RegionEventInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	//	public function isModifyActionEquals(ModifyActionEnum $action) : bool
	//	{
	//		return $this->modify->equals($action);
	//	}
	
	//	public function getUploadClass() : RegionImage
	//	{
	//		return $this->image ?: $this->image = new RegionImage($this);
	//	}
	
	//	public function getNameByLocale(Locale $locale) : ?string
	//	{
	//		$name = null;
	//		
	//		/** @var RegionTrans $trans */
	//		foreach($this->translate as $trans)
	//		{
	//			if($name = $trans->name($locale))
	//			{
	//				break;
	//			}
	//		}
	//		
	//		return $name;
	//	}
}