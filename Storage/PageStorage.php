<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Storage;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\OneHydra\Model\PageInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * PageStorage
 *
 * Doctrine ORM implementation of PageStorage
 */
class PageStorage implements PageStorageInterface
{
    /** @var EntityManager */
    public $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addPage(PageInterface $page, $pageName, $programId)
    {
        $this->removeIfExists($pageName, $programId);

        $this->persistPage($page, $pageName, $programId);

        $this->postCreation($page, $pageName, $programId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageEntity($pageName, $programId)
    {
        return $this->getOneHydraPageRepository()->findOneBy(
            [
                'pageName' => $pageName,
                'programId' => $programId,
            ]
        );
    }

    /**
     * @param PageInterface $pageObject
     * @param string $pageName
     * @param string $programId
     */
    protected function postCreation(PageInterface $pageObject, $pageName, $programId)
    {
    }

    /**
     * @return EntityRepository
     */
    private function getOneHydraPageRepository()
    {
        return $this->entityManager->getRepository('AmaraOneHydraBundle:OneHydraPage');
    }

    /**
     * @param string $pageName
     * @param string $programId
     */
    private function removeIfExists($pageName, $programId)
    {
        $page = $this->getPageEntity($pageName, $programId);

        if ($page) {
            $this->entityManager->remove($page);
        }
    }

    /**
     * @param PageInterface $pageObject
     * @param string $programId
     */
    private function persistPage(PageInterface $pageObject, $pageName, $programId)
    {
        $pageEntity = new OneHydraPage();
        $pageEntity->setPageName($pageName);
        $pageEntity->setPageObject($pageObject);
        $pageEntity->setProgramId($programId);
        $pageEntity->setCreatedAt(new \DateTime());

        $this->entityManager->persist($pageEntity);
        $this->entityManager->flush();
    }
}