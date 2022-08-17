<?php

namespace App\Controller;

use App\Model\DataObject\Product\CustomProductCategory;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\ListHelper;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Config;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\FilterDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends FrontendController
{
    /**
     * @Route("/shop/{path}{categoryname}~c{category}", name="products-list", defaults={"path"=""}, requirements={"path"=".*?", "categoryname"="[\w-]+", "category"="\d+"})
     * @param Request $request
     * @param Factory $ecommerceFactory
     * @param PaginatorInterface $paginator
     * @param ListHelper $listHelper
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productListAction(Request $request, Factory $ecommerceFactory, PaginatorInterface $paginator, ListHelper $listHelper)
    {
        $params = array_merge($request->query->all(), $request->attributes->all());

        $params['parentCategoryIds'] = $params['category'] ?? null;

        $category = CustomProductCategory::getById($params['category'] ?? null);
        $params['category'] = $category;

        $indexService = $ecommerceFactory->getIndexService();
        $productListing = $indexService->getProductListForTenant('ESTenant');
        //$productListing->setVariantMode(ProductListInterface::VARIANT_MODE_VARIANTS_ONLY);
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
