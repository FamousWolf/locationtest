<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }


    /**
     * Add a new location
     *
     * @param Location $location
     * @return void
     */
    public function add(Location $location): void
    {
        $this->getEntityManager()->persist($location);
        $this->getEntityManager()->flush();
    }

    /**
     * Find by coordinates and distance in meters
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $distance
     * @return array
     * @throws Exception
     */
    public function findByCoordinatesAndDistance(float $latitude, float $longitude, int $distance): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            SELECT
                name,
                longitude,
                latitude,
                ST_Distance_Sphere(point(:longitude, :latitude), point(longitude, latitude)) AS distance
            FROM
                location
            WHERE
                ST_Distance_Sphere(point(:longitude, :latitude), point(longitude, latitude)) <= :distance
            ORDER BY
                ST_Distance_Sphere(point(:longitude, :latitude), point(longitude, latitude))
        ';
        // @todo Find a way not to use `ST_Distance_Sphere` 3 times. Throws an `UnknownFieldException` if I use `distance` in `WHERE` and `ORDER BY`
        // @todo Find a way to use `queryBuilder`

        $result = $connection->executeQuery(
            $query,
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance' => $distance
            ]
        );

        return $result->fetchAllAssociative();
    }
}
