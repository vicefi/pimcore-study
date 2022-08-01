<?php

namespace App\Model\DataObject;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Pimcore\Model\DataObject\Product;

class BlogNews extends Product implements IndexableInterface
{
    public function getOSDoIndexProduct(): bool
    {
        return $this->isPublished();
    }

    public function getPriceSystemName(): ?string
    {
        return $this->getPriceSystemName();
    }

    public function isActive(bool $inProductList = false): bool
    {
        return $this->isActive();
    }

    public function getOSIndexType(): ?string
    {
        return $this->getType();
    }

    public function getOSParentId()
    {
        return $this->getParentId();
    }

    public function getCategories(): ?array
    {
        return $this->getCategories();
    }

    public function getId()
    {
        return $this->getId();
    }

    public function getClassId()
    {
        return $this->getClassId();
    }
}

