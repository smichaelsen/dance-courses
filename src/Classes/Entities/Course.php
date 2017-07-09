<?php

namespace Smichaelsen\Burzzi\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="course")
 */
class Course
{


    /**
     * @ManyToMany(targetEntity="Song", cascade={"persist"})
     * @JoinTable(
     *  name="course_choreo_mm",
     *  joinColumns={
     *      @JoinColumn(name="course_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @JoinColumn(name="song_id", referencedColumnName="id")
     *  }
     * )
     *
     * @var Collection|Song[]
     */
    protected $choreos;

    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id = 0;

    /**
     * @Column(type="date")
     * @var \DateTimeInterface
     */
    protected $startDate;

    /**
     * @ManyToMany(targetEntity="Participant", inversedBy="courses", cascade={"persist"})
     * @JoinTable(
     *  name="course_participant_mm",
     *  joinColumns={
     *      @JoinColumn(name="course_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @JoinColumn(name="participant_id", referencedColumnName="id")
     *  }
     * )
     *
     * @var Collection|Participant[]
     */
    protected $participants;

    /**
     * @ManyToMany(targetEntity="Song", cascade={"persist"})
     * @JoinTable(
     *  name="course_warmup_mm",
     *  joinColumns={
     *      @JoinColumn(name="course_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @JoinColumn(name="song_id", referencedColumnName="id")
     *  }
     * )
     *
     * @var Collection|Song[]
     */
    protected $warmups;

    /**
     * @return Collection|Song[]
     */
    public function getChoreos()
    {
        if (!$this->choreos instanceof Collection) {
            $this->choreos = new ArrayCollection();
        }
        return $this->choreos;
    }

    /**
     * @param Collection $choreos
     */
    public function setChoreos($choreos)
    {
        $this->choreos = $choreos;
    }

    /**
     * @return Collection|Song[]
     */
    public function getWarmups()
    {
        if (!$this->warmups instanceof Collection) {
            $this->warmups = new ArrayCollection();
        }
        return $this->warmups;
    }

    /**
     * @param Collection|Song[] $warmups
     */
    public function setWarmups($warmups)
    {
        $this->warmups = $warmups;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate ?? new \DateTime();
    }

    public function setStartDate(\DateTimeInterface $startDate)
    {
        $this->startDate = $startDate;
    }

    public function setStartDateByString(string $timestring, string $format)
    {
        $this->startDate = \DateTime::createFromFormat($format, $timestring);
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function setParticipants(Collection $participants)
    {
        $this->participants = $participants;
    }
}
