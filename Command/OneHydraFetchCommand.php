<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Command;

use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\OneHydra\Api;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * OneHydraFetchCommand
 */
class OneHydraFetchCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {

        $this
            ->setName('onehydra:fetch')
            ->setDescription('Fetches pages from OneHydra')
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
                'If is set all the pages are requested (up to 5000)'
            )
            ->addOption(
                'programId',
                null,
                InputOption::VALUE_OPTIONAL,
                'The programId to fetch pages for',
                null
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $count = $input->getOption('count');
        $since = null;

        if (!$input->getOption('all')) {
            $since = new \DateTime();
            $since->sub(new \DateInterval($container->getParameter('amara_one_hydra.dateinterval')));
        } else {
            $count = null;
        }

        $api = $this->getOneHydraApi();
        $pageManager = $this->getPageManager();

        $availablePrograms = $container->getParameter('amara_one_hydra.programs');

        $programsToFetch = [];
        if ($input->getOption('programId') !== null) {
            $programId = $input->getOption('programId');

            if (!isset($availablePrograms[$programId])) {
                $output->writeln('<error>There is no program with id='.$programId.'</error>');

                return 1;
            }

            $programsToFetch[$programId] = $availablePrograms[$programId];
        } else {
            $programsToFetch = $availablePrograms;
        }

        foreach ($programsToFetch as $programId => $programDetails) {
            $authToken = $programDetails['auth_token'];

            // We'll pass the auth key for our program through via a request attribute
            $requestAttributes = [
                'auth_token' => $authToken,
            ];

            $output->writeln("<comment>OneHydra programId: ".$programId."</comment>");

            $pagesResult = $api->getPagesResult($count, $since, $requestAttributes);
            $pageUrls = $pagesResult->getPageUrls();

            // List of all pages
            $output->writeln('<info>Fetching a list of the pages to update</info>');
            $progress = new ProgressBar($output, count($pageUrls));
            $progress->setRedrawFrequency(1);

            $output->writeln('<info>Fetching and updating each page</info>');

            foreach ($pageUrls as $pageUrl) {
                // Load the page from the OneHydra API
                $pageResult = $api->getPageResult($pageUrl, $requestAttributes);
                $page = $pageResult->getPage();

                // Save it on our system
                $pageManager->addPage($page, $programId);

                $progress->advance();
            }
        }

        $output->writeln('<info>Done!</info>');

        return 0;
    }

    /**
     * @return Api
     */
    private function getOneHydraApi()
    {
        return $this->getContainer()->get('amara_one_hydra.api');
    }

    /**
     * @return PageManager
     */
    private function getPageManager()
    {
        return $this->getContainer()->get('amara_one_hydra.page_manager');
    }
}