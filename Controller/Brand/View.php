<?php

declare(strict_types=1);

namespace Kkkonrad\Brands\Controller\Brand;

use Kkkonrad\Brands\Model\Brands;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * One brand's products in the theme's own product list (same block, toolbar
 * and cards as a category), fed a collection filtered by `manufacturer` - the
 * way core's advanced search result does it.
 */
class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $forwardFactory,
        private readonly Brands $brands,
        private readonly CollectionFactory $collectionFactory,
        private readonly CatalogConfig $catalogConfig,
        private readonly Visibility $visibility,
        private readonly Stock $stock
    ) {
    }

    public function execute()
    {
        $brand = $this->brands->get((string)$this->request->getParam('slug'));
        if (!$brand) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set($brand['name']);
        $layout = $page->getLayout();
        $layout->getBlock('kkkonrad.brand.header')->setData('brand', $brand);
        $layout->getBlock('kkkonrad.brand.products')->setCollection($this->products($brand['id']));
        return $page;
    }

    private function products(int $brandId)
    {
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToFilter('manufacturer', $brandId)
            ->setVisibility($this->visibility->getVisibleInCatalogIds())
            ->addStoreFilter()
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite();
        $this->stock->addIsInStockFilterToCollection($collection);
        return $collection;
    }
}
