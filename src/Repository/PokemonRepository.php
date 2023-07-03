<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 *
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Pokemon $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Pokemon $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function countPokemons($formData) : int {
        return $this->getPokeQuery($formData)
            ->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getPokeQuery($formData){
        $qb = $this->createQueryBuilder("p");
        if ($formData !== null) {
            if ($formData["name"] !== null) {
                $qb->andWhere('p.name LIKE :name')
                    ->setParameter('name', '%' . $formData['name'] . '%' );
            }

            if ($formData["legendary"] !== null) {
                $qb->andWhere('p.legendary = :legendary')
                    ->setParameter('legendary', $formData["legendary"] );
            }

            if ($formData["type"] !== null && $formData["type"]->getId() !== null) {
                $qb->join('p.type', 't')
                    ->andWhere($qb->expr()->in('t.id', ':type'))
                    ->setParameter('type', $formData["type"] );
            }

            if ($formData["generation"] !== null) {
                $qb->andWhere('p.generation = :generation')
                    ->setParameter('generation', $formData["generation"] );
            }
        }
        return $qb;

    }


    public function paginate(int $page, int $pageSize, $formData) {
        $qb = $this->getPokeQuery($formData);
        $offset = ($page - 1) * $pageSize;
        $qb->setFirstResult($offset)
            ->setMaxResults($pageSize);
        return $qb->getQuery()->getResult();
    }


    // /**
    //  * @return Pokemon[] Returns an array of Pokemon objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pokemon
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
