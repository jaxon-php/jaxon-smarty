<?php

jaxon()->sentry()->addViewRenderer('smarty', function(){
    return new Jaxon\Smarty\View();
});
