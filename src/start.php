<?php

namespace Jaxon\Smarty;

use function Jaxon\jaxon;

jaxon()->di()->getViewRenderer()
    ->addRenderer('smarty', fn() => new View());
