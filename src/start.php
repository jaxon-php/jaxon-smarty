<?php

jaxon()->di()->getViewManager()->addRenderer('smarty', function () {
    return new Jaxon\Smarty\View();
});
