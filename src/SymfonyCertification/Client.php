<?php

namespace SymfonyCertification;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

class Client extends AbstractBrowser
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doRequest($request): Response
    {
        $response = $request->getContent() ?? 'no content';
        return new Response($response);
    }
}
