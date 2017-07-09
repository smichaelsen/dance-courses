<?php

namespace Smichaelsen\Burzzi\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smichaelsen\Burzzi\Entities\Course;
use Smichaelsen\Burzzi\Entities\Participant;
use Smichaelsen\Burzzi\Entities\Song;
use Smichaelsen\Burzzi\Service\SongRecommendationService;

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
                $course = $this->getCourseRepository()->findOneBy(['id' => (int) $courseData['id']]);
            } else {
                $course = new course();
            }
            if (!empty($courseData['start_date'])) {
                $course->setStartDateByString($courseData['start_date'], 'd.m.Y');
            }
            $course->getChoreos()->clear();
            if (!empty($courseData['choreos'])) {
                $choreos = $this->getSongRepository()->findBy(['id' => array_map('intval', $courseData['choreos'])]);
                foreach ($choreos as $choreo) {
                    $course->getChoreos()->add($choreo);
                }
            }
            $course->getWarmups()->clear();
            if (!empty($courseData['warmups'])) {
                $warmups = $this->getSongRepository()->findBy(['id' => array_map('intval', $courseData['warmups'])]);
                foreach ($warmups as $warmup) {
                    $course->getWarmups()->add($warmup);
                }
            }
            $participants = new ArrayCollection();
            if (!empty($courseData['new_participants_list'])) {
                $participantNames = array_map('trim', explode("\n", $courseData['new_participants_list']));
                foreach ($participantNames as $participantName) {
                    if (empty($participantName)) {
                        continue;
                    }
                    $participant = $this->getParticipantRepository()->findOneBy([
                        'name' => $participantName
                    ]);
                    if (!$participant instanceof Participant) {
                        $participant = new Participant();
                        $participant->setName($participantName);
                    }
                    $participant->getCourses()->add($course);
                    $participants->add($participant);
                }
            }
            if (!empty($courseData['participants'])) {
                foreach ($courseData['participants'] as $participantId) {
                    $participant = $this->getParticipantRepository()->findOneBy([
                        'id' => (int) $participantId
                    ]);
                    if ($participant instanceof Participant) {
                        $participant->getCourses()->add($course);
                        $participants->add($participant);
                    }
                }
            }
            $course->setParticipants($participants);
            $this->entityManager->persist($course);
        } elseif ($action === 'delete') {
            if ($courseData['id'] > 0 && !empty($courseData['confirmDelete'])) {
                $course = $this->getcourseRepository()->findOneBy(['id' => (int) $courseData['id']]);
                $this->entityManager->remove($course);
            }
        }
        $this->redirect('/courses');
    }

    protected function createAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->view->assign('course', new Course());
        $participants = $this->getParticipantRepository()->findBy([], ['name' => 'ASC']);
        $participantOptions = [];
        foreach ($participants as $participant) {
            $option = [
                'entity' => $participant,
            ];
            $participantOptions[] = $option;
        }
        $this->view->assign('participantOptions', $participantOptions);
    }

    protected function editAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var Course $course */
        $course = $this->getCourseRepository()->findOneBy(['id' => (int) $request->getAttribute('id')]);
        $this->view->assign('course', $course);

        $participants = $this->getParticipantRepository()->findBy([], ['name' => 'ASC']);
        $participantOptions = [];
        foreach ($participants as $participant) {
            $option = [
                'entity' => $participant,
                'selected' => $course->getParticipants()->contains($participant),
            ];
            $participantOptions[] = $option;
        }
        $this->view->assign('participantOptions', $participantOptions);

        $songRecommendationService = new SongRecommendationService();
        $previousCoursesCriteria = new Criteria();
        $previousCoursesCriteria->where(Criteria::expr()->lt('id', $course->getId()));
        $previousCourses = $this->getCourseRepository()->matching($previousCoursesCriteria);

        $choreos = $this->getSongRepository()->findBy(
            ['type' => 'choreo'],
            ['artist' => 'ASC', 'title' => 'DESC']
        );
        $choreoOptions = $songRecommendationService->sortByLowestPreviousOccurrence(
            $choreos,
            $course->getParticipants(),
            $previousCourses,
            'choreo'
        );
        foreach ($choreoOptions as $key => $choreoOption) {
            $choreoOption['selected'] = $course->getChoreos()->contains($choreoOption['entity']);
            if (count($choreoOption['occurredForParticipants'])) {
                $choreoOption['description'] = count($choreoOption['occurredForParticipants']) . ' Teilnehmer hatten diesen Song schon: ' . join(', ', $choreoOption['occurredForParticipants']->toArray());
            }
            $choreoOptions[$key] = $choreoOption;
        }
        $this->view->assign('choreoOptions', $choreoOptions);

        $warmups = $this->getSongRepository()->findBy(
            ['type' => 'warmup'],
            ['artist' => 'ASC', 'title' => 'DESC']
        );
        $warmupOptions = [];
        foreach ($warmups as $warmup) {
            $option = [
                'entity' => $warmup,
                'selected' => $course->getWarmups()->contains($warmup),
            ];
            $warmupOptions[] = $option;
        }
        $this->view->assign('warmupOptions', $warmupOptions);
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

    protected function getParticipantRepository(): EntityRepository
    {
        static $participantRepository;
        if (!$participantRepository instanceof EntityRepository) {
            $participantRepository = $this->entityManager->getRepository(Participant::class);
        }
        return $participantRepository;
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
