<?php
namespace Smichaelsen\Burzzi\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smichaelsen\Burzzi\Entities\Song;

class SongsController extends AbstractController
{

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getParsedBody();
        $songs = $this->entityManager->getRepository(Song::class)->findBy(
            [],
            ['artist' => 'ASC', 'title' => 'DESC']
        );
        $this->view->assign('songs', $songs);
    }

}
