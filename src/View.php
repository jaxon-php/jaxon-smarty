<?php

namespace Jaxon\Smarty;

use Jaxon\Sentry\Interfaces\View as ViewInterface;
use Jaxon\Sentry\View\Store;

class View implements ViewInterface
{
    /**
     * The Smarty template renderer
     *
     * @var Smarty
     */
    protected $xRenderer = null;

    /**
     * The template extensions
     *
     * @var array
     */
    protected $aExtensions = array();

    /**
     * The view constructor
     * 
     * @return
     */
    public function __construct()
    {
        $this->xRenderer = new \Smarty;
    }

    /**
     * Add a namespace to this view renderer
     *
     * @param string        $sNamespace         The namespace name
     * @param string        $sDirectory         The namespace directory
     * @param string        $sExtension         The extension to append to template names
     *
     * @return void
     */
    public function addNamespace($sNamespace, $sDirectory, $sExtension = '')
    {
        $this->xRenderer->addTemplateDir($sDirectory, $sNamespace);
        $this->aExtensions[$sNamespace] = $sExtension;
    }

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
        // View data
        $this->xRenderer->clearAllAssign();
        foreach($store->getViewData() as $sName => $xValue)
        {
            $this->xRenderer->assign($sName, $xValue);
        }
        // View extension
        $sExtension = key_exists($sNamespace, $this->aExtensions) ? $this->aExtensions[$sNamespace] : '';
        // Render the template
        return trim($this->xRenderer->fetch($sViewName . $sExtension), " \t\n");
    }
}
