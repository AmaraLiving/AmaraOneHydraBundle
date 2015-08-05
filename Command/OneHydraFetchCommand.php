<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 08/07/15
 * Time: 12:01
 */

namespace Amara\Bundle\OneHydraBundle\Command;

use Amara\OneHydra\Container;
use Amara\OneHydra\Api\Api;
use Amara\OneHydra\Http\RequestBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OneHydraFetchCommand extends ContainerAwareCommand {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('onehydra:fetch')
			->setDescription('Fetchs all the pages infos from OneHydra')
			->addOption(
				'count',
				null,
				InputOption::VALUE_OPTIONAL,
				'How many pages to require',
				50
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				'If is set all the pages are requested (UP TO 5000)'
			)
			->addOption(
				'host',
				null,
				InputOption::VALUE_OPTIONAL,
				'The program to use',
				'uk'
			);
	}


	/**
	 * @return \Pimple\Container
	 */
	private function getOneHydraContainer() {
		return Container::getContainer();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$container = $this->getContainer();
		$oneHydraContainer = $this->getOneHydraContainer();

		$reqParams = [];

		$reqParams['count'] = $input->getOption('count');

		if(!$input->getOption('all')) {
			$since = new \DateTime();
			$since->sub(new \DateInterval($container->getParameter('amara_one_hydra.dateinterval')));
			$reqParams['since'] = urlencode($since->format('c'));
		} else {
			unset($reqParams['count']);
		}

		/** @var RequestBuilder $requestBuilder */
		$requestBuilder = $oneHydraContainer['request_builder'];

		/** @var \Amara\OneHydra\Api\Api $api */
		$api = $oneHydraContainer['api'];

		$programs = $container->getParameter('amara_one_hydra.programs');

		$oneHydraParams = $programs[$input->getOption('host')];

		$api->setAuthToken($oneHydraParams['authToken']);
		//$api->setBaseUrl($endpoint);


		$output->writeln('');
		$output->writeln("<comment>OneHydra host: {$input->getOption('host')} ({$api->getBaseUrl()})</comment>");
		$output->writeln('');

		$requestPages = $requestBuilder->setService(Api::EP_PAGES);

		$resultPages = $api->execute($requestPages->setParams($reqParams)->build())->getBody();

		$pages = $resultPages->Pages->PageUrls;


		// List of all pages
		$output->writeln('<info>* Get the pages</info>');
		$progress = $this->getHelper('progress');
		$progress->start($output, count($pages));
		$progress->setRedrawFrequency(1);

		/** @var \Amara\OneHydra\Factory\ObjectFactory $objectFactory */
		$objectFactory = $oneHydraContainer['object_factory'];

		$output->writeln('<info>* Get single page infos</info>');
		$output->writeln('');

		$requestBuilder->setService(Api::EP_PAGE);

		$pageManager = $container->get('onehydra_pagemanager');

		foreach ($pages as $page) {
			$requestBuilder->setParams(['url' => $page]);
			$pageObject= $objectFactory->makeFromResponse($api->execute($requestBuilder->build(false)), 'page', ['pageName' => $page]);

			$pageManager->addPage($pageObject, 'uk');


			$progress->advance();
		}

		$output->writeln('');
		$output->writeln('');
	}
}