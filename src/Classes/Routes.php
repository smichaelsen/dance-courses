<?php

namespace Smichaelsen\Burzzi;

use Aura\Router\Map;
use Smichaelsen\Burzzi\Controller\IndexController;
use Smichaelsen\Burzzi\Controller\SongsController;
use Smichaelsen\SaladBowl\RoutesClassInterface;

class Routes implements RoutesClassInterface
{

    public function configure(Map $map)
    {
        $map->get('index', '/', IndexController::class);
        $map->get('songs', '/songs', SongsController::class);
    }

}

