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

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NewResourceFactory implements NewResourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory)
    {
        if (null === $method = $requestConfiguration->getFactoryMethod()) {
            return $factory->createNew();
        }

        $callable = array($factory, $method);
        $arguments = $requestConfiguration->getFactoryArguments();

        return call_user_func_array($callable, $arguments);
    }
}
