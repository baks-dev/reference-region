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

namespace BaksDev\Reference\Region\DataFixtures\Regions;

use BaksDev\Reference\Region\Entity\Trans\RegionTrans;
use BaksDev\Reference\Region\UseCase\Admin\NewEdit;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class RegionsFixtures extends Fixture
{
	private NewEdit\RegionHandler $handler;
	
	
	public function __construct(NewEdit\RegionHandler $handler) {
		$this->handler = $handler;
	}
	
	
	public function load(ObjectManager $manager) : void
	{
		# php bin/console doctrine:fixtures:load --append
		
		$regions = include __DIR__.'/regions.php';
		
		foreach($regions as $region)
		{

			$RegionDTO = new NewEdit\RegionDTO();
			$persist = false;
			
			/** @var \BaksDev\Core\Type\Locale\Locale $local  */
			foreach(Locale::cases() as $local)
			{
				$RegionTrans = $manager->getRepository(RegionTrans::class)->findOneBy(['name' => $region[$local->getValue()], 'local' => $local->getValue()]);
				
				if($RegionTrans)
				{
					$Event = $RegionTrans->getEvent();
					$Event->getDto($RegionDTO);
				}
				
				if(!$RegionTrans)
				{
					$RegionTransDTO = new NewEdit\Trans\RegionTransDTO();
					$RegionTransDTO->setLocal($local);
					$RegionTransDTO->setName($region[$local->getValue()]);
					$RegionDTO->addTranslate($RegionTransDTO);
					$persist = true;
				}
			}
			

			if($persist)
			{
				$this->handler->handle($RegionDTO);
			}
		}

	}
}