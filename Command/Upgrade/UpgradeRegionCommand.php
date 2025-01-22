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

namespace BaksDev\Reference\Region\Command\Upgrade;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Field\Country\Type\Country\Collection\CountryInterface;
use BaksDev\Reference\Region\Repository\CurrentRegion\CurrentRegionEventInterface;
use BaksDev\Reference\Region\Type\Regions\RegionCollection;
use BaksDev\Reference\Region\Type\Regions\RegionInterface;
use BaksDev\Reference\Region\UseCase\Admin\NewEdit\RegionDTO;
use BaksDev\Reference\Region\UseCase\Admin\NewEdit\RegionHandler;
use BaksDev\Reference\Region\UseCase\Admin\NewEdit\Trans\RegionTransDTO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'baks:reference-region:upgrade',
    description: 'Обновляет список регионов',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class UpgradeRegionCommand extends Command
{

    public function __construct(
        #[AutowireIterator('baks.country')] private readonly iterable $country,
        private readonly RegionCollection $regions,
        private readonly TranslatorInterface $translator,
        private readonly CurrentRegionEventInterface $CurrentRegionEvent,
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly RegionHandler $handler
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Обновляем список регионов');

        $helper = $this->getHelper('question');

        $questions[] = 'Все';

        /** @var CountryInterface $country */
        foreach($this->country as $country)
        {
            $value = $country->getValue();
            $questions[$value] = $this->translator->trans($value, domain: 'field-country');
        }

        $question = new ChoiceQuestion(
            'Профиль пользователя',
            $questions,
            0
        );

        $result = $helper->ask($input, $output, $question);

        if($result === 'Все')
        {
            foreach($this->country as $country)
            {
                $this->update($country);
            }

            $io->success('Cписок регионов успешно обновлен');

            return Command::SUCCESS;
        }


        $update = null;

        foreach($this->country as $country)
        {
            if($country->getValue() === $result)
            {
                $update = $country;
                break;
            }
        }

        if($update)
        {
            $this->update($update);
            $io->success(sprintf('Регион %s успешно обновлен', $this->translator->trans($update->getValue(), domain: 'field-country')));
        }

        return Command::SUCCESS;
    }

    public function update(CountryInterface $country): void
    {
        /** @var RegionInterface $region */
        foreach($this->regions->cases($country) as $region)
        {
            $RegionUid = $region::getRegionUid();

            $RegionEvent = $this
                ->CurrentRegionEvent
                ->region($RegionUid)
                ->find();

            if($RegionEvent)
            {
                continue;
            }

            $RegionDTO = new RegionDTO();
            $RegionDTO->withRegion($RegionUid);

            /** @var Locale $local */
            foreach(Locale::cases() as $local)
            {
                $name = $this->translator->trans($region::ID, domain: 'reference-region', locale: $local->getLocalValue());

                $RegionTransDTO = new RegionTransDTO();
                $RegionTransDTO->setLocal($local);
                $RegionTransDTO->setName($name);
                $RegionDTO->addTranslate($RegionTransDTO);
            }

            $this->handler->handle($RegionDTO);

        }
    }


}
