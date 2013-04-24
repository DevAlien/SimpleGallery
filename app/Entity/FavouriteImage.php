<?php

namespace app\Entity;

use Doctrine\ORM\Mapping as ORM;

use app\Entity\Exception\InvalidFieldException;

/**
 * app\Entity\FavouriteImage
 *
 * @Table(name="favourite_image")
 * @Entity()
 *
 */
class FavouriteImage
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $url
     *
     * @Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var string $description
     *
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return FavouriteImage
     */
    public function setUrl($url)
    {
        if(!$this->isValidUrl($url))
            throw new InvalidFieldException('The URL provided ( ' . $url . ' ) is not a valid URL', 2000);

        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param stringn $description
     * @return FavouriteImage
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Check if the URL is a valid URL
     *
     * @param string $url The URL of the Favourite
     *
     * @return boolean
     */
    private function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function toArray()
    {
        $data = array(
            'id' => $this->getId(),
            'url' => $this->getUrl(),
            'description' => $this->getDescription());
        
        return $data;
    }
}