<?php

if (!$gallery) { 
        $gallery = new stdClass(); 
}
if (!$gallery->app) { 
        $gallery->app = new stdClass(); 
}

/* Version  */
$gallery->app->config_version = 31;

/* Features */
$gallery->app->feature["zip"] = 1;
$gallery->app->feature["rewrite"] = 1;
$gallery->app->feature["mirror"] = 0; // (missing <i>mirrorSites</i> -- it's optional)

/* Constants */
$gallery->app->galleryTitle = "De Vleermuis";
$gallery->app->pnmDir = "/usr/local/netpbm/bin";
$gallery->app->ImPath = "/usr/local/bin";
$gallery->app->graphics = "NetPBM";
$gallery->app->highlight_size = "140";
$gallery->app->zipinfo = "/usr/bin/zipinfo";
$gallery->app->unzip = "/usr/bin/unzip";
$gallery->app->use_exif = "/usr/sbin/jhead";
$gallery->app->movieThumbnail = "/home/scene24/public_html/vleermuis/images/movie.thumb.jpg";
$gallery->app->albumDir = "/home/scene24/public_html/vleermuis/albums";
$gallery->app->tmpDir = "/home/scene24/public_html/vleermuis/tmp";
$gallery->app->photoAlbumURL = "http://www.scene24.net/vleermuis";
$gallery->app->albumDirURL = "http://www.scene24.net/vleermuis/albums";
// optional <i>mirrorSites</i> missing
$gallery->app->showAlbumTree = "yes";
$gallery->app->cacheExif = "no";
$gallery->app->jpegImageQuality = "75";
$gallery->app->timeLimit = "30";
$gallery->app->debug = "no";
$gallery->app->use_flock = "yes";
$gallery->app->expectedExecStatus = "0";
$gallery->app->sessionVar = "bat_session";
$gallery->app->userDir = "/home/scene24/public_html/vleermuis/albums/.users";
$gallery->app->pnmtojpeg = "pnmtojpeg";

/* Defaults */
$gallery->app->default["bordercolor"] = "#666666";
$gallery->app->default["border"] = "4";
$gallery->app->default["font"] = "verdana";
$gallery->app->default["cols"] = "4";
$gallery->app->default["rows"] = "6";
$gallery->app->default["thumb_size"] = "140";
$gallery->app->default["resize_size"] = "600";
$gallery->app->default["fit_to_window"] = "yes";
$gallery->app->default["use_fullOnly"] = "yes";
$gallery->app->default["print_photos"] = "none";
$gallery->app->default["returnto"] = "yes";
$gallery->app->default["showOwners"] = "no";
$gallery->app->default["albumsPerPage"] = "10";
$gallery->app->default["showSearchEngine"] = "no";
$gallery->app->default["useOriginalFileNames"] = "yes";
$gallery->app->default["display_clicks"] = "yes";
$gallery->app->default["public_comments"] = "yes";
?>
