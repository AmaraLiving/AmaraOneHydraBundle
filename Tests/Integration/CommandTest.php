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

use Amara\Bundle\OneHydraBundle\Command\OneHydraFetchCommand;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\Tests\Fixtures\AppKernel;
use Amara\OneHydra\Api;
use Amara\OneHydra\Http\HttpResponse;
use Amara\OneHydra\Model\PageInterface;
use Amara\OneHydra\Result\PageResult;
use Amara\OneHydra\Result\PagesResult;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandTest extends PHPUnit_Framework_TestCase
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

    public function testCommandWithMockedApiAndPageManager()
    {
        $application = new Application($this->kernel);
        $application->add(new OneHydraFetchCommand());

        $programId = 'example';

        $url1 = '/foo/bar';
        $url2 = '/foo/baz';

        $pagesResult = new PagesResult(new HttpResponse(), [$url1, $url2]);

        $page1 = $this->prophesize(PageInterface::class);
        $page2 = $this->prophesize(PageInterface::class);

        $pageResult1 = new PageResult(new HttpResponse(), $page1->reveal());
        $pageResult2 = new PageResult(new HttpResponse(), $page2->reveal());

        $api = $this->prophesize(Api::class);

        $requestAttributes = [
            'auth_token' => 'authtoken1',
        ];

        $api->getPagesResult(
            50,
            Argument::type('DateTime'),
            $requestAttributes
        )->willReturn($pagesResult);

        $api->getPageResult(
            $url1,
            $requestAttributes
        )->willReturn($pageResult1);

        $api->getPageResult(
            $url2,
            $requestAttributes
        )->willReturn($pageResult2);

        $this->container->set('amara_one_hydra.api', $api->reveal());

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->addPage($page1, $programId)->shouldBeCalled();
        $pageManager->addPage($page2, $programId)->shouldBeCalled();

        $this->container->set('amara_one_hydra.page_manager', $pageManager->reveal());

        $command = $application->find('onehydra:fetch');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--programId' => $programId]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}