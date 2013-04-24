<?php
namespace app\Controller;

use Devfw\Controller\Controller;
use app\Lib\RandomImagesManager;
use app\Lib\RandomImages\Exceptions\CouldNotDownloadRadomImagesException;
use app\Lib\RandomImages\Exceptions\CouldNotFindSourceRadomImagesException;
use app\Lib\FavouritesManager;
use app\Entity\FavouriteImage;
class APIController extends Controller {

    /**
     * Delete a Favourite
     *
     * @method DELETE
     */
    public function deleteFavouriteAction() {
        $favourite = $this->getFavouriteManager();
        if($favourite->deleteFavourite($this->getParam('id')))
            echo json_encode(array('response' => true));
        else
            echo json_encode(array('response' => false, 'message' => 'Something went wrong'));
        exit;
    }

    /**
     * Update a Favourite
     *
     * @method PUT
     */
    public function updateFavouriteAction() {
        $favourite = $this->getFavouriteManager();
        $description = htmlspecialchars($_POST['description']);

        if($favourite->editFavourite($this->getParam('id'), $description))
            echo json_encode(array('response' => true, 'description' => $description));
        else
            echo json_encode(array('response' => false, 'message' => 'Something went wrong'));
        exit;
    }

    /**
     * Get Favourites
     *
     * @method GET
     */
    public function favouritesAction() {
        $favourite = $this->getFavouriteManager();
        $favourites = $favourite->getFavourites();

        $favs = array();
        foreach($favourites as $favourite)
            $favs[] = $favourite->toArray();
        echo json_encode($favs);
    }

    /**
     * Add a Favourite
     *
     * @method POST
     */
    public function addFavouriteAction() {
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

    /**
     * Get Images
     *
     * @method GET
     */
    public function randomImagesAction() {
        try {
            $randomImages = new RandomImagesManager();
            $images = $randomImages->get($this->getParam('source'));

            echo json_encode($images);

        } catch(CouldNotDownloadRadomImagesException $e) {
            $this->get('logger')->logError($e->getMessage());
            echo json_encode(array('response' => false, 'message' => $e->getMessage()));
        } catch(CouldNotFindSourceRadomImagesException $e) {
            $this->get('logger')->logError($e->getMessage());
            echo json_encode(array('response' => false, 'message' => $e->getMessage()));
        }
    }

    private function getFavouriteManager() {
        return new FavouritesManager($this->get('em'), $this->get('logger'));
    }
}