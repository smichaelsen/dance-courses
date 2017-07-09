<?php
namespace Smichaelsen\Burzzi\Service;

use Doctrine\Common\Collections\Collection;
use Smichaelsen\Burzzi\Entities\Course;
use Smichaelsen\Burzzi\Entities\Participant;
use Smichaelsen\Burzzi\Entities\Song;

class SongRecommendationService
{

    /**
     * @param Collection|Song[] $songs
     * @param Collection|Participant[] $participants
     * @param Collection|Course[] $previousCourses
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public function sortByLowestPreviousOccurrence(Collection $songs, Collection $participants, Collection $previousCourses, string $type): array
    {
        $songCollection = [];
        $occurrenceThreshold = 0;
        $remainingSongs = clone $songs;
        while (count($songCollection) < count($songs)) {
            foreach ($remainingSongs as $song) {
                $occurrences = 0;
                foreach ($previousCourses as $previousCourse) {
                    if ($type === Song::TYPE_WARMUP) {
                        $courseContainsSong = $previousCourse->getWarmups()->contains($song);
                    } elseif ($type === Song::TYPE_CHOREO) {
                        $courseContainsSong = $previousCourse->getChoreos()->contains($song);
                    } else {
                        throw new \Exception('Unrecognized song type', 1499585530);
                    }
                    if ($courseContainsSong) {
                        foreach ($participants as $participant) {
                            if ($previousCourse->getParticipants()->contains($participant)) {
                                $occurrences++;
                            }
                        }
                    }
                }
                if ($occurrences <= $occurrenceThreshold) {
                    $collectionItem = [
                        'entity' => $song,
                        'occurrences' => $occurrences
                    ];
                    $songCollection[] = $collectionItem;
                }
            }
            $occurrenceThreshold++;
        }
        return $songCollection;
    }

}
