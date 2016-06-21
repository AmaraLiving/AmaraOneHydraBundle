<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Twig\Extension;

use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * OneHydraExtension
 */
class OneHydraExtension extends Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @param RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param PageManager $pageManager
     */
    public function setPageManager(PageManager $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isSuggested($request = null)
    {
        if (is_null($request)) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (is_null($request)) {
            // We still have no request, so we cannot look up the page
            return false;
        }

        if ($page = $this->pageManager->getPageByRequest($request)) {
            $pageObject = $page->getPageObject();

            return $pageObject->isSuggested();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'onehydra_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'oneHydraHeadContent' => new Twig_SimpleFunction('oneHydraHeadContent', [$this, 'getOneHydraHeadContent']),
            'oneHydraIsSuggestedPage' => new Twig_SimpleFunction('oneHydraIsSuggestedPage', [$this, 'isSuggested']),
        ];
    }

    /**
     * @param string $key
     * @param string $defaultValue
     * @param Request $request
     * @return string
     */
    public function getOneHydraHeadContent($key, $defaultValue, $request = null)
    {
        if (is_null($request)) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (is_null($request)) {
            // We still have no request, so we cannot look up the page
            return false;
        }

        if ($page = $this->pageManager->getPageByRequest($request)) {

            if ($pageObject = $page->getPageObject()) {

                $methodName = 'get'.ucwords(strtolower($key));

                if (method_exists($pageObject, $methodName)) {
                    $value = $pageObject->$methodName();

                    return (!is_null($value) ? ($value) : $defaultValue);
                }
            }
        }

        return $defaultValue;
    }
}