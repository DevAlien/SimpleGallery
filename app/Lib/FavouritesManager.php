<?php
/**
 * The manager for the Favourites, it takes care of the Favourites, add, remove, modify etc
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

namespace app\Lib;

use app\models\Favourite;

use app\Entity\FavouriteImage;
use app\Entity\Exception\InvalidFieldException;
use app\Lib\Exception\DuplicateEntryException;

/**
 * The manager for the Favourites, it takes care of the Favourites, add, remove, modify etc
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class FavouritesManager {

    /**
     * Doctrine Entity Manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * logger
     *
     * @var \libs\logger\KLogger
     */
    private $logger;

    /**
     * It prepares the attributes, db and logger
     *
     * @param \Doctrine\ORM\EntityManager $db The PDO object with we can access the database
     * @param \libs\logger\KLogger $logger The logger so we can log if something happens
     */
    public function __construct( \Doctrine\ORM\EntityManager $em, \Devfw\Logger\KLogger $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Edit a Favourite, you can change the description at the moment
     *
     * @param int $id The id of the favourite to modify
     * @param string $description the new description of the favourite
     *
     * @return boolean
     */
    public function editFavourite($id, $description) {
        $this->getLogger()->logInfo('Editing Favourite, ID: ' . $id . ' - Description: ' . $description );
        $favouriteImage = $this->getEm()->getRepository('Simplegallery:FavouriteImage')->findOneById($id);
        if($favouriteImage === null)
            return false;
        
        $favouriteImage->setDescription($description);
        $this->getEm()->persist($favouriteImage);
        $this->getEm()->flush();
        
        return true;
    }

    /**
     * Given an Id it deletes that Favourite
     *
     * @param int $id The id of the favourite to delete
     *
     * @return boolean
     */
    public function deleteFavourite($id) {
        $this->getLogger()->logInfo('Deleting Favourite, ID: ' . $id );
        $favouriteImage = $this->getEm()->getRepository('Simplegallery:FavouriteImage')->findOneById($id);
        if($favouriteImage === null)
            return false;

        $this->getEm()->remove($favouriteImage);
        $this->getEm()->flush();

        return true;
    }

    /**
     * Get all the favourites
     *
     * @return array
     */
    public function getFavourites() {
        $this->getLogger()->logInfo('Get Favourites');
        $favouriteImageRepo = $this->getEm()->getRepository('Simplegallery:FavouriteImage');
        $favouriteImages = $favouriteImageRepo->findAll();

        return $favouriteImages;
    }

    /**
     * Add a new favourite
     *
     * @param string $url The url of the new favourite
     * @param string $description the new description of the favourite
     *
     * @return boolean
     */
    public function addFavourite($url) {
        $favourite = $this->createFavourite($url);
        if (is_null($favourite)) {
            return false;
        }

        try{
            $this->getEm()->persist($favourite);
            $this->getEm()->flush();
        } catch( \Doctrine\DBAL\DBALException $e ) {
            //Unfortunately doctrine doesn't have an ErrorCode for this, so we have to check the message
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->getLogger()->logWarn(__METHOD__ . ' -> ' . $e->getMessage());
                throw new DuplicateEntryException('The url "' . $favourite->getUrl() . '" is already in the database, it can\'t be added again');
            }
            else throw $e;
        }

        return true;
    }

    /**
     * Creates a Favourite object if it's possible
     *
     * @param string $url The url of the Favourite
     *
     * @return mixed \app\models\Favourite or null if not possible to create
     */
    private function createFavourite($url) {
        try{
            $favouriteImage = new FavouriteImage();
            $favouriteImage->setUrl($url);
        } catch (InvalidFieldException $e) {
            $this->getLogger()->logWarn(__METHOD__ . ' -> ' . $e->getMessage());
            return null;
        }

        return $favouriteImage;
    }

    /**
     * Getter for the Entity Manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm() {
        return $this->em;
    }

    /**
     * Setter for the Entity Manager
     *
     * @param \Doctrine\ORM\EntityManager $em Doctrine's Entity Manager
     */
    public function setEm($em) {
        $this->em = $em;
    }

    /**
     * Getter for the logger
     *
     * @return \libs\logger\KLogger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Setter for the Logger
     *
     * @param \libs\logger\KLogger $logger The Logger
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }
}