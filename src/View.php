<?php

namespace Jaxon\Smarty;

use Jaxon\Sentry\Interfaces\View as ViewInterface;
use Jaxon\Sentry\View\Store;

class View implements ViewInterface
{
    use \Jaxon\Sentry\View\Namespaces;

    /**
     * Render a view
     * 
     * @param Store         $store        A store populated with the view data
     * 
     * @return string        The string representation of the view
     */
    public function render(Store $store)
    {
        $sViewName = $store->getViewName();
        $sNamespace = $store->getNamespace();
        // For this view renderer, the view name doesn't need to be prepended with the namespace.
        $nNsLen = strlen($sNamespace) + 2;
        if(substr($sViewName, 0, $nNsLen) == $sNamespace . '::')
        {
            $sViewName = substr($sViewName, $nNsLen);
        }

        // View namespace
        $this->setCurrentNamespace($sNamespace);

        // View data
        $xRenderer = new \Smarty;
        $xRenderer->addTemplateDir($this->sDirectory, $sNamespace);
        // $xRenderer->clearAllAssign();
        foreach($store->getViewData() as $sName => $xValue)
        {
            $xRenderer->assign($sName, $xValue);
        }

        // Render the template
        return trim($xRenderer->fetch($sViewName . $this->sExtension), " \t\n");
    }
}
