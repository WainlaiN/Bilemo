<?php


namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;


class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $customResponse = new JsonResponse(['status'=> false, 'message' => $exception->getMessage()],403);

        $event->setResponse($customResponse);
    }

}