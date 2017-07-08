<?php
namespace Smichaelsen\Burzzi\Controller;

use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smichaelsen\Burzzi\Entities\Course;

class CourseController extends AbstractController
{

    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $action = $request->getAttribute('action') ?? 'list';
        $this->view->assign('action', $action);
        switch ($action) {
            case 'list':
                $this->listAction($request, $response);
                break;
        }
    }

    protected function listAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $courses = $this->getCourseRepository()->findBy(
            [],
            ['uid' => 'DESC']
        );
        $this->view->assign('courses', $courses);
    }

    protected function getCourseRepository(): EntityRepository
    {
        static $courseRepository;
        if (!$courseRepository instanceof EntityRepository) {
            $courseRepository = $this->entityManager->getRepository(Course::class);
        }
        return $courseRepository;
    }
}
