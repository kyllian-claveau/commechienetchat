<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findNotesByVeterinaire($veterinaireId)
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.rendezVous', 'rv')
            ->leftJoin('rv.veterinaire', 'v')
            ->andWhere('v.id = :veterinaireId')
            ->setParameter('veterinaireId', $veterinaireId);

        return $qb->getQuery()->getResult();
    }

    public function getAverageNoteByVeterinaire($veterinaireId)
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.rendezVous', 'rv')
            ->leftJoin('rv.veterinaire', 'v')
            ->select('AVG(n.note) as averageNote')
            ->andWhere('v.id = :veterinaireId')
            ->setParameter('veterinaireId', $veterinaireId);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getAverageRatingByClient(User $client): ?float
    {
        $query = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as average_rating')
            ->andWhere('r.client = :client')
            ->setParameter('client', $client)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
