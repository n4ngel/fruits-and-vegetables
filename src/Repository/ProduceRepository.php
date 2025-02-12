<?php

namespace App\Repository;

use App\Entity\Produce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produce>
 */
class ProduceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produce::class);
    }

    public static function buildCriteriaFromFilters(array $filters): Criteria
	{
		$criteria = Criteria::create();

		foreach ($filters as $key => $value) {
			[$field, $operator] = explode('_', $key) + [null, null];
			if ($field && property_exists(Produce::class, $field)) {
				$expression = match ($operator) {
					'eq' => Criteria::expr()->eq($field, $value),
					'neq' => Criteria::expr()->neq($field, $value),
					'gt' => Criteria::expr()->gt($field, $value),
					'gte' => Criteria::expr()->gte($field, $value),
					'lt' => Criteria::expr()->lt($field, $value),
					'lte' => Criteria::expr()->lte($field, $value),
					default => null,
				};
				if ($expression) {
					$criteria->andWhere($expression);
				}
			}
		}

		return $criteria;
	}

    //    public function findOneBySomeField($value): ?Produce
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
