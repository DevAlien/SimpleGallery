<?php

include 'http.php';
include 'SimpleGalleryClient.php';

$simpleGalleryClient = new SimpleGalleryClient();
$images = $simpleGalleryClient->getRandomImages();

echo '<h2>Random Images</h2>';
foreach( $images as $image) {
    echo $image . '<br />';
}
if($images)
    $imageToAdd = $images[0];

$favourites = $simpleGalleryClient->getFavourites();
echo '<h2>Favourites - ' . count($favourites) . '</h2>';
foreach( $favourites as $favourite) {
    echo $favourite->id;
}

echo '<h2>Add Favourite</h2>';
$simpleGalleryClient->addFavourite($imageToAdd);

$favourites = $simpleGalleryClient->getFavourites();
echo '<h2>Favourites - ' . count($favourites) . '</h2>';

echo '<h2>Edit Favourite</h2>';
echo 'OLD: ' . ((strlen($favourites[0]->description) > 0) ? $favourites[0]->description : 'No Description');
$simpleGalleryClient->editFavourite($favourites[0]->id, 'This is a new Description with time - ' . time());
$favourites = $simpleGalleryClient->getFavourites();
echo ' - NEW: ' . ((strlen($favourites[0]->description) > 0) ? $favourites[0]->description : 'No Description');

echo '<h2>Delete Favourite</h2>';
$simpleGalleryClient->deleteFavourite($favourites[0]->id);

$favourites = $simpleGalleryClient->getFavourites();
echo '<h2>Favourites - ' . count($favourites) . '</h2>';