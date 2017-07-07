<?php
namespace Smichaelsen\Burzzi\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smichaelsen\Burzzi\Entities\Course;

class IndexController extends AbstractController
{

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $courses = $this->entityManager->getRepository(Course::class)->findBy(
            [],
            ['id' => 'DESC'],
            5
        );
        $this->view->assign('courses', $courses);
    }

}
