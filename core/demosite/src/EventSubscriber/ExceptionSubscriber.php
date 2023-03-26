<?php

namespace App\EventSubscriber;

use App\Exception\ValidateException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class ExceptionSubscriber implements EventSubscriberInterface
{

    /**
     * @return array<string, array<int, array<int, int|string>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => [
                ['onKernelException', 0]
            ]
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $exception = $event->getThrowable();

        if ($exception instanceof BadRequestHttpException) {
            $response = new JsonResponse($this->buildResponseData($exception));
            $response->setStatusCode(400);

            $event->setResponse($response);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $response = new JsonResponse($this->buildResponseData($exception));
            $response->setStatusCode(403);

            $event->setResponse($response);
        }

        if ($exception instanceof ValidateException) {
            $response = new JsonResponse($this->buildResponseData($exception));
            $response->setStatusCode(422);

            $event->setResponse($response);
        }
    }

    /**
     * @param Throwable $exception
     * @return array<string, string>
     */
    private function buildResponseData(
        Throwable $exception
    ): array
    {
        return [
            'title' => 'An error occurred',
            'detail' => $exception->getMessage(),
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10'
        ];
    }
}
