<?php

namespace Smichaelsen\Burzzi\Entities;

use Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="participant")
 */
class Participant
{

    /**
     * @var Collection|Course[]
     *
     * @ManyToMany(targetEntity="Course", mappedBy="participants")
     */
    protected $courses;

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
     * @return Collection|Course[]
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @param Collection|Course[] $courses
     */
    public function setCourses(Collection $courses)
    {
        $this->courses = $courses;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
