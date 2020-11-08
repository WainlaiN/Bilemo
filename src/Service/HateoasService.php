<?php


namespace App\Service;


use Hateoas\HateoasBuilder;

class HateoasService
{
    public function serializeHypermedia($data)
    {
        $hateoas = HateoasBuilder::create()->build();

        $json = $hateoas->serialize($data, 'json');

        return $json;
    }

}