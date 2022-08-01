<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\ProductCategory;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontendController
{
    public function productListAction(Request $request, PaginatorInterface $paginator)
    {
        $data = array_merge($request->query->all(), $request->attributes->all());

        $ecommerceFactory = Factory::getInstance();

        $data['categories'] = $params['category'] ?? null;

        $category = ProductCategory::getById($data['category'] ?? null);
        $data['category'] = $category;

        $data['filterDefinition'] = null;
        if ($category) {
            //$filterDefinition = $category->getFilterdefinition();
            //$data['filterDefinition'] = $filterDefinition;
        }


        $indexService = $ecommerceFactory->getIndexService();
        $filterService = $ecommerceFactory->getFilterService();
        $data['filterService'] = $filterService;
        $productListing = $indexService->getProductListForCurrentTenant();
        $data['listing'] = $productListing;

        $paginator = $paginator->paginate(
            $productListing,
            $request->get('page', 1),
            10
        );

        $data['results'] = $paginator;

        return $this->render('eshop/products-list.html.twig', $data);
    }
}
