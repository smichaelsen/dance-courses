<?php

namespace Smichaelsen\Burzzi\Entities;

/**
 * @Entity @Table(name="course")
 */
class Course
{

    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ManyToMany(targetEntity="Participant", inversedBy="courses")
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
     * @var \Doctrine\Common\Collections\Collection|Participant[]
     */
    protected $participants;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
