<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Tests\Integration;

use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\Storage\PageStorage;
use Amara\Bundle\OneHydraBundle\Strategy\DefaultPageTransformStrategy;
use Amara\Bundle\OneHydraBundle\Tests\Fixtures\AppKernel;
use Amara\Bundle\OneHydraBundle\Twig\Extension\OneHydraExtension;
use Amara\OneHydra\Api;
use Amara\OneHydra\Http\HttpRequestBuilder;
use Amara\OneHydra\Http\Transport\GuzzleTransport;
use Amara\OneHydra\ResultBuilder\ResultBuilderEngine;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $kernel;

    private $entityManager;

    protected function setUp()
    {
        require_once __DIR__.'/../Fixtures/AppKernel.php';

        $kernel = new AppKernel('test', true);
        $kernel->boot();
        $this->kernel = $kernel;
        $this->container = $kernel->getContainer();

        $entityManager = $this->prophesize(EntityManager::class);
        $this->container->set('doctrine.orm.entity_manager', $entityManager->reveal());

        $this->entityManager = $entityManager;
    }

    public function testDefaultServicesAndParamsLookOkay()
    {
        $api = $this->container->get('amara_one_hydra.api');
        $this->assertInstanceOf(Api::class, $api);

        $httpRequestBuilder = $this->container->get('amara_one_hydra.api.http_request_builder');
        $this->assertInstanceOf(HttpRequestBuilder::class, $httpRequestBuilder);

        $transportGuzzle = $this->container->get('amara_one_hydra.api.transport.guzzle');
        $this->assertInstanceOf(GuzzleTransport::class, $transportGuzzle);

        $resultBuilderEngine = $this->container->get('amara_one_hydra.api.result_builder_engine');
        $this->assertInstanceOf(ResultBuilderEngine::class, $resultBuilderEngine);

        $guzzleClient = $this->container->get('amara_one_hydra.api.guzzle_client');
        $this->assertInstanceOf(Client::class, $guzzleClient);

        $pageManager = $this->container->get('amara_one_hydra.page_manager');
        $this->assertInstanceOf(PageManager::class, $pageManager);

        $pageStorage = $this->container->get('amara_one_hydra.page_storage');
        $this->assertInstanceOf(PageStorage::class, $pageStorage);

        $pageTransformStrategy = $this->container->get('amara_one_hydra.page_transform_strategy');
        $this->assertInstanceOf(DefaultPageTransformStrategy::class, $pageTransformStrategy);

        $twigExtension = $this->container->get('twig.onehydra_extension.default');
        $this->assertInstanceOf(OneHydraExtension::class, $twigExtension);

        $this->assertEquals(true, $this->container->getParameter('amara_one_hydra.is_uat'));
        $this->assertEquals(false, $this->container->getParameter('amara_one_hydra.is_not_uat'));
        $this->assertEquals('PT15M', $this->container->getParameter('amara_one_hydra.dateinterval'));
        $this->assertEquals(
            ['example' => ['auth_token' => 'authtoken1'], 'example2' => ['auth_token' => 'authtoken2']],
            $this->container->getParameter('amara_one_hydra.programs')
        );
    }
}