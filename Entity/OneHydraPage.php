<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Entity;

use Amara\OneHydra\Model\PageInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * OneHydraPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="onehydra_pages", indexes={@ORM\Index(name="page_name", columns={"page_name"})})
 */
class OneHydraPage implements OneHydraPageInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="page_name", type="string", length=255, nullable=false)
     */
    private $pageName;

    /**
     * @var PageInterface
     *
     * @ORM\Column(name="page_object", type="object")
     */
    private $pageObject;

    /**
     * @var string
     *
     * @ORM\Column(name="program_id", type="string")
     */
    private $programId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }

    /**
     * @return PageInterface
     */
    public function getPageObject()
    {
        return $this->pageObject;
    }

    /**
     * @param PageInterface $pageObject
     */
    public function setPageObject($pageObject)
    {
        $this->pageObject = $pageObject;
    }

    /**
     * @return string
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * @param string $programId
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}