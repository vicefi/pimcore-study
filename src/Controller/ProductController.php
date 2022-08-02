<?php

namespace App\Controller;

use App\Website\Navigation\BreadcrumbHelperService;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\ListHelper;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Config;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\FilterDefinition;
use Pimcore\Model\DataObject\ProductCategory;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontendController
{
    public function productListAction(Request $request, Factory $ecommerceFactory, PaginatorInterface $paginator, ListHelper $listHelper)
    {
        $params = array_merge($request->query->all(), $request->attributes->all());

        $params['parentCategoryIds'] = $params['category'] ?? null;

        $category = ProductCategory::getById($params['category'] ?? null);
        $params['category'] = $category;

        $indexService = $ecommerceFactory->getIndexService();
        $productListing = $indexService->getProductListForCurrentTenant();
        $productListing->setVariantMode(ProductListInterface::VARIANT_MODE_VARIANTS_ONLY);
        $params['productListing'] = $productListing;

        // Current filter loading
        if ($category) {
            $filterDefinition = $category->getFilterDefinition();

            //TODO We can track segments for personalization on this step after
        }

        if ($request->get('filterdefinition') instanceof FilterDefinition) {
            $filterDefinition = $request->get('filterdefinition');
        }

        // It seems that this made for make sure that we have filter definition
        if (empty($filterDefinition)) {
            $filterDefinition = Config::getWebsiteConfig()->get('fallbackFilterdefinition');
        }

        $filterService = $ecommerceFactory->getFilterService();
        var_dump($filterService->getFilterType('FilterSelect'));
        $listHelper->setupProductList($filterDefinition, $productListing, $params, $filterService, true);
        $params['filterService'] = $filterService;
        $params['filterDefinition'] = $filterDefinition;

        $paginator = $paginator->paginate(
            $productListing,
            $request->get('page', 1),
            10
        );

        $params['results'] = $paginator;

        return $this->render('eshop/products-list.html.twig', $params);
    }
}
