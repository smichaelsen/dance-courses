<?php

namespace Smichaelsen\Burzzi\Entities;

use Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="course")
 */
class Course
{

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
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
