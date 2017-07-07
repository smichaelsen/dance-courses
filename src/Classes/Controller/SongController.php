<?php
namespace Smichaelsen\Burzzi\Controller;

use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smichaelsen\Burzzi\Entities\Song;

class SongController extends AbstractController
{

    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $action = $request->getAttribute('action') ?? 'list';
        $this->view->assign('action', $action);
        switch ($action) {
            case 'edit':
                $this->editAction($request, $response);
                break;
            case 'list':
                $this->listAction($request, $response);
                break;
        }
    }

    protected function listAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $choreos = $this->getSongRepository()->findBy(
            ['type' => 'choreo'],
            ['artist' => 'ASC', 'title' => 'DESC']
        );
        $warmups = $this->getSongRepository()->findBy(
            ['type' => 'warmup'],
            ['artist' => 'ASC', 'title' => 'DESC']
        );
        $this->view->assign('choreos', $choreos);
        $this->view->assign('warmups', $warmups);
    }

    protected function editAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $song = $this->getSongRepository()->findOneBy(['id' => (int)$request->getAttribute('id')]);
        $this->view->assign('song', $song);
    }

    protected function getSongRepository(): EntityRepository
    {
        static $songRepository;
        if (!$songRepository instanceof EntityRepository) {
            $songRepository = $this->entityManager->getRepository(Song::class);
        }
        return $songRepository;
    }
}
