<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use App\Contracts\RepositoryAwareMigration;
use App\Entity\Document;
use App\Entity\Wahlkreis;
use Doctrine\DBAL\Schema\Schema;

final class Version20241210195414 extends RepositoryAwareMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $wahlkreisRepository = $this->em->getRepository(Wahlkreis::class);
        $documentRepo = $this->getRepository(Document::class);

        foreach ($documentRepo->findBy(['type' => 'Direktkandidaten']) as $document) {
            $wahlkreis = $wahlkreisRepository->findOneBy(
                [
                    'number' => $document->getWkNr(),
                ]
            );
            $document->setWahlkreis($wahlkreis);

            $documentRepo->save($document);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'Update document set wahlkreis_id = NULL'
        );
    }
}
