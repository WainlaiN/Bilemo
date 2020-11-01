<?php


namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;


class PaginatorService
{

    private $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;

    }

    public function paginate($query, $pageSize): Paginator
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