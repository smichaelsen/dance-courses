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
        $courseData = $parameters = $request->getParsedBody()['course'];
        if ($action === 'submit') {
            /** @var Course $course */
            if ($courseData['id'] > 0) {
                $course = $this->getcourseRepository()->findOneBy(['id' => (int)$courseData['id']]);
            } else {
                $course = new course();
            }
            if (!empty($courseData['start_date'])) {
                $course->setStartDateByString($courseData['start_date'], 'd.m.Y');
            }
            $this->entityManager->persist($course);
        } elseif ($action === 'delete') {
            if ($courseData['id'] > 0 && !empty($courseData['confirmDelete'])) {
                $course = $this->getcourseRepository()->findOneBy(['id' => (int)$courseData['id']]);
                $this->entityManager->remove($course);
            }
        }
        $this->redirect('/courses');
    }

    protected function createAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->view->assign('course', new Course());
    }

    protected function editAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $course = $this->getCourseRepository()->findOneBy(['id' => (int)$request->getAttribute('id')]);
        $this->view->assign('course', $course);
    }

    protected function listAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $courses = $this->getCourseRepository()->findBy(
            [],
            ['id' => 'DESC']
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
