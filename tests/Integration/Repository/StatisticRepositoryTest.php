<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\Statistic;
use App\Repository\StatisticRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatisticRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;
    private const string REPO_CLASS = StatisticRepository::class;
    private StatisticRepository $repo;

    public function testFindAllBundeslaender(): void
    {
        $statistic = new Statistic();
        $statistic->setType('Type A');
        $statistic->setName('Name A');
        $statistic->setApproved(20);
        $statistic->setBundesland('Berlin');
        $statistic->setUpdatedAt(new \DateTimeImmutable());
        $this->repo->save($statistic);

        $this->assertSame(
            [
                'Berlin',
            ],
            $this->repo->findAllBundeslaender()
        );
    }

    public function testFindByState(): void
    {
        $statistic = new Statistic();
        $statistic->setType('Type A');
        $statistic->setName('Name A');
        $statistic->setApproved(20);
        $statistic->setBundesland('Berlin');
        $statistic->setUpdatedAt(new \DateTimeImmutable());
        $this->repo->save($statistic);
        $this->assertCount(1, $this->repo->findByState('Berlin'));
    }

    public function testFindByStateAndArea(): void
    {
        $statistic = new Statistic();
        $statistic->setType('Type A');
        $statistic->setName('Name A');
        $statistic->setApproved(20);
        $statistic->setBundesland('Berlin');
        $statistic->setUpdatedAt(new \DateTimeImmutable());
        $this->repo->save($statistic);
        $statistic2 = new Statistic();
        $statistic2->setType('Type B');
        $statistic2->setName('Name B');
        $statistic2->setApproved(20);
        $statistic2->setBundesland('Berlin');
        $statistic2->setUpdatedAt(new \DateTimeImmutable());
        $this->repo->save($statistic2);
        $this->assertSame($statistic2->getType(), $this->repo->findByStateAndArea('Berlin', 'Name B')->getType());
    }

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $statistic = new Statistic();
        $statistic->setType('Type A');
        $statistic->setName('Name A');
        $statistic->setApproved(20);
        $statistic->setBundesland('Berlin');
        $statistic->setUpdatedAt(new \DateTimeImmutable());
        $this->repo->save($statistic);

        $this->assertCount(1, $this->repo->findAll());
    }
}
