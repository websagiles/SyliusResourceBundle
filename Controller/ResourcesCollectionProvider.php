<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface
{
    /**
     * @var PagerfantaFactory
     */
    private $pagerfantaRepresentationFactory;

    /**
     * @param PagerfantaFactory $pagerfantaRepresentationFactory
     */
    public function __construct(PagerfantaFactory $pagerfantaRepresentationFactory)
    {
        $this->pagerfantaRepresentationFactory = $pagerfantaRepresentationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (null !== $repositoryMethod = $requestConfiguration->getRepositoryMethod()) {
            $callable = array($repository, $repositoryMethod);

            return call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());
        }

        if (!$requestConfiguration->isPaginated() && !$requestConfiguration->isLimited()) {
            return $repository->findAll();
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        $request = $requestConfiguration->getRequest();

        /** @var Pagerfanta $paginator */
        $paginator = $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
        $paginator->setCurrentPage($request->query->get('page', 1));

        if (!$requestConfiguration->isHtmlRequest()) {
            $route = new Route($request->attributes->get('_route'), array_merge($request->attributes->get('_route_params'), $request->query->all()));

            return $this->pagerfantaRepresentationFactory->createRepresentation($paginator, $route);
        }

        return $paginator;
    }
}
