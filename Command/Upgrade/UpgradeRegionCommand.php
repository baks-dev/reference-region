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

namespace BaksDev\Reference\Region\Command\Upgrade;

use BaksDev\Core\Command\Update\ProjectUpgradeInterface;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Field\Country\Type\Country\Collection\CountryInterface;
use BaksDev\Reference\Region\Entity\Region;
use BaksDev\Reference\Region\Type\Id\RegionUid;
use BaksDev\Reference\Region\Type\Regions\RegionCollection;
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
    private DBALQueryBuilder $DBALQueryBuilder;
    private RegionHandler $handler;


    public function __construct(
        #[AutowireIterator('baks.country')] private readonly iterable $country,
        private readonly RegionCollection $regions,
        private readonly TranslatorInterface $translator,
        DBALQueryBuilder $DBALQueryBuilder,
        RegionHandler $handler
    )
    {
        parent::__construct();

        $this->DBALQueryBuilder = $DBALQueryBuilder;
        $this->handler = $handler;
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


        foreach($this->regions->cases($country) as $region)
        {
            dump($region);
        }

        return;



        $regions = include __DIR__.'/regions.php';

        foreach($regions as $region)
        {
            $RegionUid = new RegionUid();
            $md5 = md5(implode('', $region));
            $RegionUid->md5($md5);

            /** Делаем проверку на существующий регион */

            $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

            $isExists = $dbal
                ->from(Region::class, 'main')
                ->where('id = :main')
                ->setParameter('main', $RegionUid, RegionUid::TYPE)
                ->fetchExist();

            if($isExists)
            {
                continue;
            }

            $RegionDTO = new RegionDTO();
            $RegionDTO->withRegion($RegionUid);

            /** @var Locale $local */
            foreach(Locale::cases() as $local)
            {
                $RegionTransDTO = new RegionTransDTO();
                $RegionTransDTO->setLocal($local);
                $RegionTransDTO->setName($region[$local->getLocalValue()]);
                $RegionDTO->addTranslate($RegionTransDTO);
            }

            $this->handler->handle($RegionDTO);

        }
    }


}
