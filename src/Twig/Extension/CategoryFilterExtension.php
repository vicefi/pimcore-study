<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace App\Twig\Extension;

use App\Model\DataObject\Product\CustomProductCategory;
use App\Website\LinkGenerator\CategoryLinkGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryFilterExtension extends AbstractExtension
{
    /**
     * @var CategoryLinkGenerator
     */
    protected $categoryLinkGenerator;

    /**
     * CategoryFilterExtension constructor.
     *
     * @param CategoryLinkGenerator $categoryLinkGenerator
     */
    public function __construct(CategoryLinkGenerator $categoryLinkGenerator)
    {
        $this->categoryLinkGenerator = $categoryLinkGenerator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('app_category_filter_generate_link', [$this, 'generateLink']),
            new TwigFunction('app_category_filter_prepare_data', [$this, 'prepareData']),
        ];
    }

    public function prepareData($currentValue, CustomProductCategory $rootCategory = null)
    {
        $data = new \stdClass();

        $data->parentCategories = [];
        $data->currentCategory = $this->getCurrentCategory($currentValue);
        //var_dump($data->currentCategory);
        if ($data->currentCategory) {
            $data->parentCategories = $data->currentCategory->getParentCategoryList($rootCategory);
        }

        $data->subCategories = $this->getSubCategories($data->currentCategory, $rootCategory);

        return $data;
    }

    /**
     * @param $currentValue
     *
     * @return CustomProductCategory
     */
    public function getCurrentCategory($currentValue)
    {
        return CustomProductCategory::getById($currentValue);
    }

    public function getSubCategories(CustomProductCategory $currentCategory = null, $rootCategory = null)
    {
        $subCategories = [];

        $parent = $currentCategory ?: $rootCategory;

        if ($parent) {
            $subCategories = array_filter($parent->getChildren(), function ($item) {
                return $item instanceof CustomProductCategory && $item->isPublished();
            });

//            foreach($currentCategory->getChildren() as $subCategory) {
//                $subCategories[] = $subCategory;
//            }
        } else {
        }

        return $subCategories;
    }

    /**
     * @param CustomProductCategory $category
     * @param CustomProductCategory|null $rootCategory
     * @param bool $reset
     *
     * @return string
     */
    public function generateLink(CustomProductCategory $category, CustomProductCategory $rootCategory = null, $reset = false): string
    {
        return $this->categoryLinkGenerator->generate($category, ['rootCategory' => $rootCategory], $reset);
    }
}
