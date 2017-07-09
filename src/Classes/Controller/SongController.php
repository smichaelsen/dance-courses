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
            case 'create':
                $this->createAction($request, $response);
                break;
            case 'edit':
                $this->editAction($request, $response);
                break;
            case 'list':
                $this->listAction($request, $response);
                break;
        }
    }

    public function post(ServerRequestInterface $request, ResponseInterface $response)
    {
        $action = $request->getAttribute('action') ?? 'submit';
        $songData = $parameters = $request->getParsedBody()['song'];
        if ($action === 'submit') {
            /** @var Song $song */
            if ($songData['id'] > 0) {
                $song = $this->getSongRepository()->findOneBy(['id' => (int)$songData['id']]);
            } else {
                $song = new Song();
            }
            if (!empty($songData['artist'])) {
                $song->setArtist($songData['artist']);
            }
            if (!empty($songData['title'])) {
                $song->setTitle($songData['title']);
            }
            if (!empty($songData['type'])) {
                $song->setType($songData['type']);
            }
            $this->entityManager->persist($song);
        } elseif ($action === 'delete') {
            if ($songData['id'] > 0 && !empty($songData['confirmDelete'])) {
                $song = $this->getSongRepository()->findOneBy(['id' => (int)$songData['id']]);
                $this->entityManager->remove($song);
            }
        }
        $this->redirect('/songs');
    }

    protected function createAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->view->assign('song', new Song());
    }

    protected function editAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $song = $this->getSongRepository()->findOneBy(['id' => (int)$request->getAttribute('id')]);
        $this->view->assign('song', $song);
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

    protected function getSongRepository(): EntityRepository
    {
        static $songRepository;
        if (!$songRepository instanceof EntityRepository) {
            $songRepository = $this->entityManager->getRepository(Song::class);
        }
        return $songRepository;
    }
}
