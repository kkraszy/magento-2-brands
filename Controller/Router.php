<?php

declare(strict_types=1);

namespace Kkkonrad\Brands\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

/**
 * /producenci/<slug> -> producenci/brand/view/slug/<slug>. The forward sets the
 * module name, so the second pass skips this router and the standard one
 * dispatches it.
 */
class Router implements RouterInterface
{
    public function __construct(private readonly ActionFactory $actionFactory)
    {
    }

    public function match(RequestInterface $request)
    {
        if ($request->getModuleName()
            || !preg_match('#^/producenci/([a-z0-9-]+)/?$#', $request->getPathInfo(), $m)
        ) {
            return null;
        }
        $request->setModuleName('producenci')
            ->setControllerName('brand')
            ->setActionName('view')
            ->setParam('slug', $m[1]);
        return $this->actionFactory->create(Forward::class);
    }
}
