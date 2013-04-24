<?php
namespace app\Controller;

use Devfw\Controller\Controller;
use app\Lib\RandomImagesManager;
use app\Lib\RandomImages\Exceptions\CouldNotDownloadRadomImagesException;
use app\Lib\RandomImages\Exceptions\CouldNotFindSourceRadomImagesException;
use app\Lib\FavouritesManager;
use app\Entity\FavouriteImage;

class HomeController extends Controller {

    public function indexAction() {
        $favourite = $this->getFavouriteManager();
        $em = $this->get('em');
        echo get_class($em);
        echo $this->get('template')->burn('index', 'html');
    }

    public function ajaxDeleteFavouriteAction() {
        $favourite = $this->getFavouriteManager();
        if($favourite->deleteFavourite($this->getParam('id')))
            echo json_encode(array('response' => true));
        else
            echo json_encode(array('response' => false, 'message' => 'Something went wrong'));
    }

    public function ajaxUpdateFavouriteAction() {
        $favourite = $this->getFavouriteManager();
        $description = htmlspecialchars($_POST['description']);

        if($favourite->editFavourite($this->getParam('id'), $description))
            echo json_encode(array('response' => true, 'description' => $description));
        else
            echo json_encode(array('response' => false, 'message' => 'Something went wrong'));
    }

    public function favouritesAction() {
            $favourite = $this->getFavouriteManager();
            $favourites = $favourite->getFavourites();

            $this->get('template')->assign('number', count($favourites));
            $this->get('template')->assign('favourites', $favourites);
            echo $this->get('template')->burn('favourites', 'html');
    }

    public function ajaxAddToFavouriteAction() {
        if(isset($_POST['url'])){
            $favourite = $this->getFavouriteManager();

            try{
                if($favourite->addFavourite($_POST['url']))
                    echo json_encode(array('response' => true));
                else
                    echo json_encode(array('response' => false, 'message' => 'The url provided is not valid'));
            } catch(\app\Lib\Exception\DuplicateEntryException $e) {
                echo json_encode(array('response' => false, 'message' => $e->getMessage()));
            }
        } 
        else
            echo json_encode(array('response' => false, 'message' => 'No POST parameter named url found'));
    }

    public function randomImagesAction() {
        try {
            $randomImages = new RandomImagesManager();
            $images = $randomImages->get($this->getParam('source'));

            $this->get('template')->assign('images', $images);
            echo $this->get('template')->burn('images', 'html');

        } catch(CouldNotDownloadRadomImagesException $e) {
            $this->get('logger')->logError($e->getMessage());
            $this->get('template')->assign('error', $e->getMessage());
            echo $this->get('template')->burn('error', 'html');
        } catch(CouldNotFindSourceRadomImagesException $e) {
            $this->get('logger')->logError($e->getMessage());
            $this->get('template')->assign('error', $e->getMessage());
            echo $this->get('template')->burn('error', 'html');
        }
    }

    public function apiAction() {
        echo $this->get('template')->burn('api', 'html');
    }

    private function getFavouriteManager() {
        return new FavouritesManager($this->get('em'), $this->get('logger'));
    }


}