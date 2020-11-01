<?php


namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


class PaginatorService
{


    /**
     * @param QueryBuilder|Query $query
     * @param $pageSize
     * @return Paginator
     */
    public function paginate($query, $pageSize, $page)
    {

        $paginator = new Paginator($query);

        $totalItems = $this->total($paginator);

        $pagesCount = ceil($totalItems / $pageSize);

        // now get one page's items:

        $data = $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1)) // set the offset
            ->setMaxResults($pageSize) // set the limit
            ->getResult();


        return $data;
    }

    public function total(Paginator $paginator)
    {
        return $paginator->count();
    }


}