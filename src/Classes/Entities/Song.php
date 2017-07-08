<?php

namespace Smichaelsen\Burzzi\Entities;

/**
 * @Entity @Table(name="song")
 */
class Song
{

    const TYPE_CHOREO = 'choreo';
    const TYPE_WARMUP = 'warmup';

    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id = 0;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $artist = '';

    /**
     * @Column(type="string")
     * @var string
     */
    protected $title = '';

    /**
     * @Column(type="string")
     * @var string
     */
    protected $type = '';

    /**
     * @return string
     */
    public function getArtist(): string
    {
        return $this->artist;
    }

    /**
     * @param string $artist
     */
    public function setArtist(string $artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array($type, array(self::TYPE_CHOREO, self::TYPE_WARMUP))) {
            throw new \InvalidArgumentException('Invalid type');
        }
        $this->type = $type;
    }
}
