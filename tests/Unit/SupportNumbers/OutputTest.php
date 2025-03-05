<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\SupportNumbers;

use App\Btw\UuCalculator;
use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Repository\SupportNumbersRepository;
use App\SupportNumbers\Output;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;
use Twig\Error\LoaderError;

class OutputTest extends TestCase
{
    use ProphecyTrait;

    private ArrayAdapter $cache;
    private ObjectProphecy|Environment $twig;

    private SupportNumbersRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->twig = $this->prophesize(Environment::class);
        $this->repository = $this->createMock(SupportNumbersRepository::class);
    }

    public function testGetDataByState(): void
    {
        $number3 = (new SupportNumbersLandesliste())->setName('Landesliste Berlin');
        $number1 = (new SupportNumbersLandesliste())
            ->setName('Landesliste Berlin')
            ->setBundesland('Berlin')
            ->setApproved(1)
            ->setUnapproved(0);

        $number2 = (new SupportNumbersDirektkandidat())
            ->setName('Direktkandidat WK 999')
            ->setApproved(2)
            ->setBundesland('Berlin')
            ->setWahlkreis(WahlkreisBuilder::build()->setThreshold(200))
            ->setUnapproved(0);

        $number4 = (new SupportNumbersDirektkandidat())
            ->setName('Direktkandidat WK 998')
            ->setApproved(200)
            ->setBundesland('Berlin')
            ->setWahlkreis(WahlkreisBuilder::build()->setThreshold(200))
            ->setUnapproved(0);

        $number5 = (new SupportNumbersDirektkandidat())
            ->setName('Direktkandidat WK 997')
            ->setApproved(1800)
            ->setBundesland('Berlin')
            ->setWahlkreis(WahlkreisBuilder::build()->setThreshold(200))
            ->setUnapproved(0);

        $number6 = (new SupportNumbersDirektkandidat())
            ->setName('Direktkandidat WK 996')
            ->setApproved(2300)
            ->setBundesland('Berlin')
            ->setWahlkreis(WahlkreisBuilder::build()->setThreshold(200))
            ->setUnapproved(0);

        $this->repository->expects($this->once())->method('findByState')
            ->willReturn([$number1, $number2, $number3, $number4, $number5, $number6]);
        $this->repository->expects($this->atLeastOnce())->method('getNumbersByName')
            ->willReturnOnConsecutiveCalls(
                ['approved' => 1, 'unapproved' => 0],
                ['approved' => 2, 'unapproved' => 0],
                ['approved' => 800, 'unapproved' => 0],
                ['approved' => 1800, 'unapproved' => 0],
                ['approved' => 2300, 'unapproved' => 0],
            );
        $this->twig->render('wsa/checkmark.html.twig')->shouldBeCalled()->willReturn('SUCCESS');
        $result = $this->getService()->getDataByState('Berlin');
        $this->assertCount(5, $result);
    }

    public function testGetDataByStateWithRenderError(): void
    {
        $number = (new SupportNumbersDirektkandidat())
            ->setName('Direktkandidat WK 996')
            ->setApproved(2300)
            ->setBundesland('Berlin')
            ->setWahlkreis(WahlkreisBuilder::build()->setThreshold(1))
            ->setUnapproved(0);

        $this->repository->expects($this->once())->method('findByState')
            ->willReturn([$number]);
        $this->repository->expects($this->atLeastOnce())->method('getNumbersByName')
            ->willReturnOnConsecutiveCalls(
                ['approved' => 2300, 'unapproved' => 0],
            );
        $this->twig->render('wsa/checkmark.html.twig')->shouldBeCalled()->willThrow(new LoaderError(''));
        $this->getService()->getDataByState('Berlin');
    }

    private function getService(): Output
    {
        return new Output(
            new AsciiSlugger(),
            $this->cache,
            $this->twig->reveal(),
            new UuCalculator(),
            $this->repository,
            new NullLogger()
        );
    }
}
