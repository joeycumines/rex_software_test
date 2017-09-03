<?php

namespace RexSoftwareTest\ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class SwaggerUiRedirectController extends Controller
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/")
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        $url = array_merge(['scheme' => 'http', 'host' => 'localhost', 'port' => 80], parse_url($this->requestStack->getCurrentRequest()->getUri()));
        $url = sprintf(
            '%s://%s%s',
            $url['scheme'],
            $url['host'],
            80 !== $url['port'] ? ':' . $url['port'] : ''
        );
        return new RedirectResponse(sprintf('%s/swagger-ui/index.html?url=%s/api', $url, $url));
    }
}
