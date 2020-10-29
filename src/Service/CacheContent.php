<?php


namespace App\Service;



use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CacheContent
 *
 * @package App\Service
 */
class CacheContent
{


    /**
     * @param $request
     * @param JsonResponse $response
     * @return JsonResponse
     */
    public function CheckCache($request, JsonResponse $response)
    {
        //add ETag to response to identify resource
        $response->setEtag(md5($response->getContent()));

        //add cache to response
        $response->setPublic()
            ->setMaxAge(3600);

        //check if response is different from cache
        if ($response->isNotModified($request)) {

            return $response;
        }

        //need to revalidate if age is outdated
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;

    }

}