<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler as RestViewHandler;
use JMS\Serializer\SerializationContext;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandler;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin ViewHandler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ViewHandlerSpec extends ObjectBehavior
{
    function let(RestViewHandler $restViewHandler)
    {
        $this->beConstructedWith($restViewHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ViewHandler');
    }
    
    function it_implements_view_handler_interface()
    {
        $this->shouldImplement(ViewHandlerInterface::class);
    }

    function it_handles_view_normally_for_html_requests(
        RequestConfiguration $requestConfiguration,
        RestViewHandler $restViewHandler,
        Response $response
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $view = View::create();

        $restViewHandler->handle($view)->willReturn($response);

        $this->handle($requestConfiguration, $view)->shouldReturn($response);
    }

    function it_sets_proper_values_for_non_html_requests(
        RequestConfiguration $requestConfiguration,
        RestViewHandler $restViewHandler,
        Response $response
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $view = View::create();
        $view->setSerializationContext(new SerializationContext());

        $requestConfiguration->getSerializationGroups()->willReturn(array('Detailed'));
        $requestConfiguration->getSerializationVersion()->willReturn('2.0.0');

        $restViewHandler->setExclusionStrategyGroups(array('Detailed'))->shouldBeCalled();
        $restViewHandler->setExclusionStrategyVersion('2.0.0')->shouldBeCalled();

        $restViewHandler->handle($view)->willReturn($response);

        $this->handle($requestConfiguration, $view)->shouldReturn($response);
    }
}
