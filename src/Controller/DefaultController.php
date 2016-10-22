<?php
namespace Bare\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class DefaultController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function defaultAction(Request $request)
    {
        return new JsonResponse([
            'message' => 'Hello world!'
        ], 200);
    }
}
