<?php

declare(strict_types=1);

namespace Kkkonrad\Brands\Block;

use Kkkonrad\Brands\Model\Brands as BrandsModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Tags the page with the attribute's cache tag, so saving the manufacturer
 * attribute (new brand, new logo) purges every page that lists brands.
 */
class Brands extends Template implements IdentityInterface
{
    public function __construct(Context $context, private readonly BrandsModel $brands, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getBrands(): array
    {
        return $this->brands->getAll();
    }

    public function getListUrl(): string
    {
        return $this->brands->getListUrl();
    }

    public function getIdentities()
    {
        return $this->brands->getAttribute()->getIdentities();
    }
}
