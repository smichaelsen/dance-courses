<?php

namespace Smichaelsen\Burzzi;

use Aura\Router\Map;
use Smichaelsen\Burzzi\Controller\CourseController;
use Smichaelsen\Burzzi\Controller\IndexController;
use Smichaelsen\Burzzi\Controller\SongController;
use Smichaelsen\SaladBowl\RoutesClassInterface;

class Routes implements RoutesClassInterface
{

    public function configure(Map $map)
    {
        $map->get('index', '/', IndexController::class);

        $map->get('course', '/course{/action,id}', CourseController::class);
        $map->get('courses', '/courses', CourseController::class);
        $map->post('submitCourse', '/course/{action}', CourseController::class);

        $map->get('song', '/song{/action,id}', SongController::class);
        $map->get('songs', '/songs', SongController::class);
        $map->post('submitSong', '/song/{action}', SongController::class);
    }

}
