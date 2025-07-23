<?php

namespace Jaxon\Smarty;

use Jaxon\App\View\Store;
use Jaxon\App\View\ViewInterface;
use Jaxon\Script\Call\JxnCall;
use Jaxon\Script\JsExpr;
use Smarty\Smarty;

use function Jaxon\attr;
use function Jaxon\je;
use function Jaxon\jo;
use function Jaxon\jq;
use function Jaxon\rq;
use function ltrim;
use function str_replace;
use function trim;

class View implements ViewInterface
{
    /**
     * @var Smarty|null
     */
    private ?Smarty $xRenderer = null;

    /**
     * @var array
     */
    private array $aExtensions = [];

    /**
     * @param bool $js
     * @param bool $css
     *
     * @return string
     */
    private function jxnScript(bool $js = false, bool $css = false): string
    {
        return jaxon()->script($js, $css);
    }

    /**
     * @param JxnCall $comp
     *
     * @return string
     */
    private function jxnHtml(JxnCall $comp): string
    {
        return attr()->html($comp);
    }

    /**
     * @param JxnCall $comp
     * @param string $item
     *
     * @return string
     */
    private function jxnBind(JxnCall $comp, string $item = ''): string
    {
        return attr()->bind($comp, $item);
    }

    /**
     * @param JxnCall $comp
     *
     * @return string
     */
    private function jxnPagination(JxnCall $comp): string
    {
        return attr()->pagination($comp);
    }

    /**
     * @param string $event
     * @param JsExpr $call
     *
     * @return string
     */
    private function jxnOn(string $event, JsExpr $call): string
    {
        return attr()->on($event, $call);
    }

    /**
     * @param JsExpr $call
     *
     * @return string
     */
    private function jxnClick(JsExpr $call): string
    {
        return attr()->click($call);
    }

    /**
     * @param array $events
     *
     * @return string
     */
    private function jxnEvent(array $events): string
    {
        return isset($events[0]) && is_array($events[0]) ?
            attr()->events($events) : attr()->event($events);
    }

    /**
     * @param string $sClass
     * @param string $sCode
     *
     * @return string
     */
    private function jxnPackage(string $sClass, string $sCode = 'html'): string
    {
        return attr()->package($sClass, $sCode);
    }

    /**
     * @return Smarty
     */
    private function _renderer(): Smarty
    {
        if(!$this->xRenderer)
        {
            $this->xRenderer = new Smarty();
            $this->xRenderer->setCompileDir(__DIR__ . '/../complie');
            $this->xRenderer->setConfigDir(__DIR__ . '/../config');
            $this->xRenderer->setCacheDir(__DIR__ . '/../cache');

            // Functions for Jaxon js and CSS codes
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnCss',
                fn() => jaxon()->css());
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnJs',
                fn() => jaxon()->js());
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnScript',
                fn($aParams) => $this->jxnScript(...$aParams));

            // Functions for custom Jaxon attributes
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnHtml',
                fn($aParams) => $this->jxnHtml(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnBind',
                fn($aParams) => $this->jxnBind(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnPagination',
                fn($aParams) => $this->jxnPagination(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnOn',
                fn($aParams) => $this->jxnOn(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnClick',
                fn($aParams) => $this->jxnClick(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnEvent',
                fn($aParams) => $this->jxnEvent(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jxnPackage',
                fn($aParams) => $this->jxnPackage(...$aParams));

            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jq',
                fn($aParams) => jq(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'je',
                fn($aParams) => je(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'jo',
                fn($aParams) => jo(...$aParams));
            $this->xRenderer->registerPlugin(Smarty::PLUGIN_FUNCTION, 'rq',
                fn($aParams) => rq(...$aParams));
        }
        return $this->xRenderer;
    }

    /**
     * @inheritDoc
     */
    public function addNamespace(string $sNamespace, string $sDirectory, string $sExtension = ''): void
    {
        $this->aExtensions[$sNamespace] = '.' . ltrim($sExtension, '.');
        $this->_renderer()->addTemplateDir($sDirectory, $sNamespace);
    }

    /**
     * Render a view
     * 
     * @param Store $store A store populated with the view data
     * 
     * @return string
     */
    public function render(Store $store): string
    {
        $sNamespace = $store->getNamespace();
        $sViewName = !$sNamespace ? $store->getViewName() :
            $sNamespace . '/' . $store->getViewName();
        $sViewName = str_replace('.', '/', $sViewName);
        if(isset($this->aExtensions[$sNamespace]))
        {
            $sViewName .= $this->aExtensions[$sNamespace];
        }

        $xRenderer = $this->_renderer();
        $xRenderer->clearAllAssign();
        foreach($store->getViewData() as $sName => $xValue)
        {
            $xRenderer->assign($sName, $xValue);
        }

        // Render the template
        return trim($xRenderer->fetch($sViewName), " \t\n");
    }
}
