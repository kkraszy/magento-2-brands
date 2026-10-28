<?php

declare(strict_types=1);

namespace Kkkonrad\Brands\Model;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\UrlInterface;
use Magento\Swatches\Helper\Data as SwatchHelper;
use Magento\Swatches\Helper\Media as SwatchMedia;
use Magento\Swatches\Model\Swatch;

/**
 * Brands are the options of core's `manufacturer` attribute; the logo is the
 * option's visual swatch image (original upload, not the 30x20 thumbnail).
 */
class Brands
{
    private ?array $all = null;

    public function __construct(
        private readonly EavConfig $eavConfig,
        private readonly SwatchHelper $swatchHelper,
        private readonly SwatchMedia $swatchMedia,
        private readonly TranslitUrl $translitUrl,
        private readonly UrlInterface $url
    ) {
    }

    public function getAttribute(): AbstractAttribute
    {
        return $this->eavConfig->getAttribute(Product::ENTITY, 'manufacturer');
    }

    /**
     * @return array<string, array{id: int, name: string, slug: string, logo: ?string, url: string}> by slug, A-Z
     */
    public function getAll(): array
    {
        if ($this->all !== null) {
            return $this->all;
        }
        $options = $this->getAttribute()->getSource()->getAllOptions(false);
        $swatches = $options ? $this->swatchHelper->getSwatchesByOptionsId(array_column($options, 'value')) : [];
        $this->all = [];
        foreach ($options as $option) {
            $id = (int)$option['value'];
            $name = (string)$option['label'];
            // ponytail: slug from the label, a duplicate name shadows the first; add a url_key if that happens
            $slug = $this->translitUrl->filter($name);
            $swatch = $swatches[$id] ?? null;
            $this->all[$slug] = [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'logo' => ($swatch['type'] ?? null) == Swatch::SWATCH_TYPE_VISUAL_IMAGE && $swatch['value']
                    ? $this->swatchMedia->getSwatchMediaUrl() . $swatch['value']
                    : null,
                'url' => $this->url->getDirectUrl('producenci/' . $slug),
            ];
        }
        uasort($this->all, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return $this->all;
    }

    public function get(string $slug): ?array
    {
        return $this->getAll()[$slug] ?? null;
    }

    public function getListUrl(): string
    {
        return $this->url->getDirectUrl('producenci');
    }
}
