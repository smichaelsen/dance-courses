<?php

namespace Smichaelsen\Burzzi\Controller;

abstract class AbstractController extends \Smichaelsen\SaladBowl\AbstractController
{

    public function initializeAction()
    {
        $this->registerTwigFunctions();
    }

    /**
     *
     */
    protected function registerTwigFunctions()
    {
        static $isRegistered = false;
        if (!$isRegistered) {
            $this->view->addFunction('asset', function ($path) {
                $path = 'assets/' . trim($path, '/');
                return '//' . $_SERVER['HTTP_HOST'] . '/' . trim($path, '/');
            });
            $this->view->addFunction('url', function ($path) {
                $path = trim($path, '/') . '/';
                $urlParts = ['//' . $_SERVER['HTTP_HOST']];
                if (trim($path, '/')) {
                    $urlParts[] = trim($path, '/');
                }
                $url = join('/', $urlParts);
                return $url;
            });
            $isRegistered = true;
        }
    }

}
