<?php
namespace app\Tests\Lib;

use app\Entity\FavouriteImage;
use app\Entity\Exception\InvalidFieldException;
use app\Lib\Exception\DuplicateEntryException;
use app\Lib\FavouritesManager;

class FavouritesManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFavouritesManagerConstructorReturnsAnErrorIfWrongEmClassIsPassed()
    {
        $favouritesManager = new FavouritesManager(null, null);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFavouritesManagerConstructorReturnsAnErrorIfWrongLoggerClassIsPassed()
    {
        $favouritesManager = new FavouritesManager($this->getEm(), null);
    }

    public function testFavouritesManagerConstructorHappyPath()
    {
        $favouritesManager = $this->getFavoruitesManager();

        $this->assertInstanceOf('\app\Lib\FavouritesManager', $favouritesManager);
    }

    public function testFavouritesManagerAddFavouriteHappyPath()
    {
        $favouritesManager = $this->getFavoruitesManager();
        $this->assertTrue($favouritesManager->addFavourite('http://www.google.com'));

    }

    public function testFavouritesManagerAddFavouriteInvalidUrlReturnsFalse()
    {
        $favouritesManager = $this->getFavoruitesManager();
        $this->assertFalse($favouritesManager->addFavourite('invalid'));
    }

    /**
     * @expectedException \app\Lib\Exception\DuplicateEntryException
     */
    public function testFavouritesManagerAddFavouriteDuplicateEntryThrowsDuplicateEntryException()
    {
        $favouritesManager = $this->getFavoruitesManager(true);
        $favouritesManager->addFavourite('http://www.google.com');
    }

    public function testFavouritesManagerDeleteFavouriteNoImageFoundMustReturnFalse()
    {
        $favouritesManager = $this->getFavoruitesManager(false,false);
        $this->assertFalse($favouritesManager->deleteFavourite(1));
    }

    public function testFavouritesManagerDeleteFavouriteHappyPath()
    {
        $favouritesManager = $this->getFavoruitesManager();
        $this->assertTrue($favouritesManager->deleteFavourite(1));
    }

    public function testFavouritesManagerGetFavouritesMustReturnAnArray()
    {
        $favouritesManager = $this->getFavoruitesManager();
        $this->assertTrue(is_array($favouritesManager->getFavourites()));
    }

    public function testFavouritesManagerEditFavouriteHappyPath()
    {
        $favouritesManager = $this->getFavoruitesManager();
        $this->assertTrue($favouritesManager->editFavourite(1, 'test'));
    }

    public function testFavouritesManagerEditFavouritesReturnsFalseWhenWrongIdIsGiven()
    {
        $favouritesManager = $this->getFavoruitesManager(false, false);
        $this->assertfalse($favouritesManager->editFavourite(1, 'test'));
    }

    private function getEm($duplicate = false, $image = true)
    {
        $em = $this->getMock('\Doctrine\ORM\EntityManager', array('flush', 'persist', 'findOneById', 'getRepository', 'remove'), array(), '', false,false,false);
        $repository = $this->getMock('repository', array('findOneById', 'findAll'), array(), '', false,false,false);

        if($image === true)
            $favouriteImage = new FavouriteImage();
        else
            $favouriteImage = null;

        $repository->expects($this->any())
                 ->method('findOneById')
                 ->will($this->returnValue($favouriteImage));

        $repository->expects($this->any())
                 ->method('findAll')
                 ->will($this->returnValue(array()));

        $em->expects($this->any())
                 ->method('getRepository')
                 ->will($this->returnValue($repository));

        if($duplicate === true)
            $em->expects($this->any())
                 ->method('flush')
                 ->will($this->throwException(new \app\Lib\Exception\DuplicateEntryException()));
        else
            $em->expects($this->any())
                 ->method('flush')
                 ->will($this->returnValue(true));

        return $em;
    }

    private function getLogger()
    {
        $logger = $this->getMock('\Devfw\Logger\KLogger', array('logInfo', 'logWarning'), array(), '', false,false);

        return $logger;
    }

    private function getFavoruitesManager($duplicate = false, $image = true)
    {
        $favouritesManager = new FavouritesManager($this->getEm($duplicate, $image), $this->getLogger());

        return $favouritesManager;
    }
}