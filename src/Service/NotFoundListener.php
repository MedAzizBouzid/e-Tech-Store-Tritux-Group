<?php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotFoundListener implements EventSubscriberInterface
{
private $urlGenerator;

public function __construct(UrlGeneratorInterface $urlGenerator)
{
$this->urlGenerator = $urlGenerator;
}

public function onKernelException(ExceptionEvent $event)
{
$exception = $event->getThrowable();

// Check if the exception is a ResourceNotFoundException
if ($exception instanceof NotFoundHttpException) {
// Redirect to the custom 404 page
$response = new RedirectResponse($this->urlGenerator->generate('app_404'));
$event->setResponse($response);
}
}

public static function getSubscribedEvents()
{
return [
'kernel.exception' => 'onKernelException',
];
}
}