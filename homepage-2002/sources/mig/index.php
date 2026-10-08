<?php
$version = '1.3.4';
$commentFilePerImage    = FALSE;
$distURL                = 'http://mig.sourceforge.net/';
$exifFormatString       = '|%c|';
$folderNameLength       = 15;
$imagePopLocationBar    = FALSE;
$imagePopMenuBar        = FALSE;
$imagePopToolBar        = FALSE;
$imagePopType           = 'reuse';
$imagePopup             = FALSE;
$maintAddr              = 'webmaster@mydomain.com';
$markerLabel            = 'th';
$markerType             = 'suffix';
$maxFolderColumns       = 2;
$maxThumbColumns        = 4;
$maxThumbRows           = 5;
$mig_language           = 'en';
$noThumbs               = FALSE;
$pageTitle              = 'My Photo Album';
$pathConvertFlag        = FALSE;
$pathConvertRegex       = '';
$pathConvertTarget      = '';
$phpNukeCompatible      = FALSE;
$phpNukeRoot            = '';
$phpWebThingsCompatible = FALSE;
$phpWebThingsRoot       = '';
$randomFolderThumbs     = FALSE;
$sortType               = 'default';
$suppressAltTags        = FALSE;
$suppressImageInfo      = FALSE;
$thumbSubdir            = 'thumbs';
$useThumbSubdir         = TRUE;
$viewFolderCount        = FALSE;

function buildBackLink ( $baseURL, $currDir, $type, $homeLink, $homeLabel,
                         $noThumbs, $startFrom )
{

    global $mig_config;

    // $type notes whether we want a "back" link or "up one level" link.
    if ($type == 'back' or $noThumbs) {
        $label = $mig_config['lang']['up_one'];
    } elseif ($type == 'up') {
        $label = $mig_config['lang']['thumbview'];
    }

    // don't send a link back if we're a the root of the tree
    if ($currDir == '.') {
        if ($homeLink != '') {

            if ($homeLabel == '') {
                $homeLabel = $homeLink;
            } else {
                // Get rid of spaces due to silly formatting in MSIE
                $homeLabel = str_replace(' ', '&nbsp;', $homeLabel);
            }

            // Build a link to the "home" page
            $retval  = '<font size="-1">[&nbsp;<a href="'
                     . $homeLink
                     . '">'
                     . $mig_config['lang']['backhome']
                     . '&nbsp;'
                     . $homeLabel
                     . '</a>&nbsp;]</font><br><br>';
        } else {
            $retval = '<br>';
        }
        return $retval;
    }

    // Trim off the last directory, so we go "back" one.
    $junk = ereg_replace('/[^/]+$', '', $currDir);
    $newCurrDir = migURLencode($junk);

    $retval = '<font size="-1">[&nbsp;<a href="'
            . $baseURL . '?currDir=' . $newCurrDir;
    if ($startFrom) {
        $retval .= '&startFrom=' . $startFrom;
    }
    $retval .= '">' . $label . '</a>&nbsp;]</font><br><br>';

    return $retval;

}   // -- End of buildBackLink()


// buildDirList() - creates list of directories available

function buildDirList ( $baseURL, $albumDir, $albumURLroot, $currDir,
                        $imageDir, $useThumbSubdir, $thumbSubdir,
                        $maxColumns, $hidden, $presorted, $viewFolderCount,
                        $markerType, $markerLabel, $ficons,
                        $randomFolderThumbs, $folderNameLength )
{
    global $mig_config;

    $oldCurrDir = $currDir;         // Stash this to build full path

    // Create a URL-encoded version of $currDir
    $enc_currdir = $currDir;
    $currDir = rawurldecode($enc_currdir);

    $directories = array ();                    // prototypes
    $counts = array ();
    $countdir = array ();
    $samples = array ();

    if (is_dir("$albumDir/$currDir")) {
        $dir = opendir("$albumDir/$currDir");   // Open directory handle
    } else {
        print "ERROR: no such currDir '$currDir'<br>";
        exit;
    }

    while ($file = readdir($dir)) {

        // Ignore . and .. and make sure it's a directory
        if ($file != '.' && $file != '..'
            && is_dir("$albumDir/$currDir/$file")) {

            // Ignore anything that's hidden or was already sorted.
            if (! $hidden[$file] && ! $presorted[$file]) {
                // Stash file in an array
                    $directories[$file] = TRUE;
            }
        }
    }

    closedir($dir);

    ksort($directories);    // sort so we can yank them in sorted order
    reset($directories);    // reset array pointer to beginning

    // snatch each element from $directories and shove it on the end of
    // $presorted
    while (list($file,$junk) = each($directories)) {
        $presorted[$file] = TRUE;
    }

    reset($presorted);          // reset array pointer

    // Iterate through all folders now that we have our final list.
    while (list($file,$junk) = each($presorted)) {

        $folder = "$albumDir/$currDir/$file";

        // Calculate how many images in the folder if desired
        if ($viewFolderCount) {
            $counts[$file] = getNumberOfImages($folder, $useThumbSubdir,
                                               $markerType, $markerLabel);
            $countdir[$file] = getNumberOfDirs($folder, $useThumbSubdir,
                                               $markerType, $markerLabel);
        }

        // Handle random folder thumbnails if desired
        if ($randomFolderThumbs) {
            $samples[$file] = getRandomThumb($file, $folder, $useThumbSubdir,
                                             $thumbSubdir, $albumURLroot,
                                             $currDir, $markerType,
                                             $markerLabel);
        }
    }

    reset($presorted);

    // Track columns
    $row = 0;
    $col = 0;
    $maxColumns--;  // Tricks $maxColumns into working since it
                    // really starts at 0, not 1

    while (list($file,$junk) = each($presorted)) {

        // Start a new row if appropriate
        if ($col == 0) {
            $directoryList .= '<tr>';
        }

        // Surmise the full path to work with
        $newCurrDir = $oldCurrDir . '/' . $file;

        // URL-encode the directory name in case it contains spaces
        // or other weirdness.
        $enc_file = migURLencode($newCurrDir);

        // Build the link itself for re-use below
        $linkURL = '<a href="' . $baseURL
                 . '?pageType=folder&currDir=' . $enc_file . '">';

        // Reword $file so it doesn't allow wrapping of the label
        // (fixes odd formatting bug in MSIE).
        // Also, render _ as a space.
        // Also, shorten filename length if using random thumbs,
        // to make the table cleaner
        $nbspfile = $file;
        if ($randomFolderThumbs && strlen($nbspfile) > $folderNameLength) {
            $nbspfile = substr($nbspfile,0,$folderNameLength-1) . '(..)';
        }
        $nbspfile = str_replace(' ', '&nbsp;', $nbspfile);
        $nbspfile = str_replace('_', '&nbsp;', $nbspfile);

        // Build the full link (icon plus folder name) and tack it on
        // the end of the list.
        $directoryList .= '<td valign="bottom" class="folder">'
                        . $linkURL . '<img src="';
        if ($ficons[$file]) {
            $directoryList .= $ficons[$file];
        } elseif ($samples[$file]) {
            $directoryList .= $samples[$file];
        } else {
            $directoryList .= $imageDir . '/folder.gif';
        }

        if (! $samples[$file]) {
            $sep = '&nbsp;';
        } else {
            $sep = '<br>';
        }

        $altlabel = str_replace('_', ' ', $file);
        $directoryList .= '" border="0" alt="' . $altlabel . '"></a>' . $sep
                       . $linkURL . '<font size="-2">' . $nbspfile
                       . '</font></a>';

        if ($viewFolderCount &&
                (($counts[$file] > 0) || ($countdir[$file] > 0)) )
        {
            $directoryList .= $sep . '(' . $countdir[$file] . '/'
                            . $counts[$file] . ')';
        }

        $directoryList .= '</td>';

        // Keep track of what row/column we're on
        if ($col == $maxColumns) {
            $directoryList .= '</tr>';
            $row++;
            $col = 0;
        } else {
            $col++;
        }
    }

    // If there aren't any subfolders to look at, then just say so.
    if ($directoryList == '') {
        return 'NULL';
    } elseif (!eregi('</tr>$', $directoryList)) {
        // Stick a </tr> on the end if it isn't there already
        $directoryList .= '</tr>';
    }

    return $directoryList;

}   // -- End of buildDirList()


// buildImageList() - creates a list of images available

function buildImageList ( $baseURL, $baseDir, $albumDir, $currDir,
                          $albumURLroot, $maxColumns, $maxRows,
                          $markerType, $markerLabel,
                          $directoryList, $suppressImageInfo, $useThumbSubdir,
                          $thumbSubdir, $noThumbs, $thumbExt, $suppressAltTags,
                          $sortType, $hidden, $presorted, $description,
                          $imagePopup, $imagePopType, $imagePopLocationBar,
                          $imagePopMenuBar, $imagePopToolBar,
                          $commentFilePerImage, $startFrom )
{
    global $mig_config;

    if (is_dir("$albumDir/$currDir")) {
        $dir = opendir("$albumDir/$currDir"); // Open directory handle
    } else {
        print "ERROR: no such currDir '$currDir'<br>";
        exit;
    }

    $row = 0;               // Counters for the table formatting
    $col = 0;

    $maxColumns--;          // Tricks maxColumns into working since it
                            // really starts at 0, not 1.

    $maxRows--;             // same for rows

    // prototype the arrays
    $imagefiles     = array ();
    $filedates      = array ();

    $thumbsInFolder = 0;

    while ($file = readdir($dir)) {
        // Skip over thumbnails
        if (!$useThumbSubdir) {  // unless $useThumbSubdir is set,
                                 // then don't waste time on this check

            if ($markerType == 'suffix' && ereg("_$markerLabel\.[^.]+$", $file)
                && validFileType($file)) {
                    continue;
            }

            if ($markerType == 'prefix' && ereg("^$markerLabel\_", $file)) {
                continue;
            }

        }

        // We'll look at this one only if it's a file, it's not hidden,
        // and it matches our list of approved extensions
        if (is_file("$albumDir/$currDir/$file") && ! $hidden[$file]
                        && ! $presorted[$file] && validFileType($file))
        {
            // Increase thumb counter
            ++$thumbsInFolder;

            // Stash file in an array
            $imagefiles[$file] = TRUE;

            // and stash a timestamp as well if needed
            if (ereg("bydate.*", $sortType)) {
                $timestamp = filemtime("$albumDir/$currDir/$file");
                $filedates["$timestamp-$file"] = $file;
            }
        }
    }

    ksort($imagefiles); // sort, so we get a sorted list to stuff onto the
                        // end of $presorted

    reset($imagefiles); // reset array pointer

    if ($sortType == "bydate-ascend") {
        ksort($filedates);
        reset($filedates);

    } elseif ($sortType == "bydate-descend") {
        krsort($filedates);
        reset($filedates);
    }

    // Join the two sorted lists together into a single list
    if (ereg("bydate.*", $sortType)) {
        while(list($junk,$file) = each($filedates)) {
            $presorted[$file] = TRUE;
        }

    } else {
        while (list($file,$junk) = each($imagefiles)) {
            $presorted[$file] = TRUE;
        }
    }

    reset($presorted);          // reset array pointer

    // Set up pagination environment
    $max_col = $maxColumns + 1;
    $max_row = $maxRows + 1;
    $firstThumb = $startFrom * $max_col * $max_row;
    // This rounds off any fractional part
    $pages = ceil($thumbsInFolder / ($max_col * $max_row));

    // Handle pagination
    if ($thumbsInFolder > ($max_col * $max_row)) {

        if ($startFrom) {
            $start_img = ($startFrom * $max_col * $max_row) + 1;

            if (($start_img+($max_col*$max_row)-1) >= $thumbsInFolder) {
                // This must be the last page.
                $end_img = $thumbsInFolder;
            } else {
                // Not the first, not last - some middle page.
                $end_img = ($startFrom+1) * $max_col * $max_row;
            }

        } else {
            // Absence of $startFrom means we're on page 1 (startFrom=0).
            // Therefore, we can easily calculate what we need.
            $start_img = 1;
            $end_img = $max_col * $max_row;
        }

        // Fetch template phrase to work with.
        $phrase = $mig_config['lang']['total_images'];
        // %t is total images in folder
        $phrase = str_replace('%t', $thumbsInFolder, $phrase);
        // %s is start image
        $phrase = str_replace('%s', $start_img, $phrase);
        // %e is end image
        $phrase = str_replace('%e', $end_img, $phrase);

        $imageList .= '<tr><td colspan=' . $max_col . ' align="center">'
                    . $phrase;

        if ($startFrom) {
            $prevPage = $startFrom - 1;

            $imageList .= '<a href="' . $baseURL
                        . '?pageType=folder&currDir=' . $currDir
                        . '&startFrom=' . $prevPage
                        . '">&laquo;</A>&nbsp;&nbsp;';
        }

        for ($i = 1; $i <= $pages; ++$i) {
            if (floor(($i - 11) / 20) == (($i - 11) / 20)) {
                $imageList .= '<br>';
            }
            if ($i == ($startFrom + 1)) {
                $imageList .= '<b>' . $i . '</b>&nbsp;&nbsp;';
            } else {
                $ib = $i - 1;
                $imageList .= '<a href="' . $baseURL
                            . '?pageType=folder&currDir=' . $currDir
                            . '&startFrom=' . $ib . '">'
                            . $i . '</a>&nbsp;&nbsp;';
            }
        }

        if (($startFrom + 1) < $pages) {
            $nextPage = $startFrom + 1;
            $imageList .= '<a href="' . $baseURL
                        . '?pageType=folder&currDir=' . $currDir
                        . '&startFrom=' . $nextPage . '">&raquo;</A>';
        }

        $imageList .= '</td></tr>';
    }

    $thumbCounter = -1;

    while (list($file,$junk) = each($presorted)) {

        ++$thumbCounter;

        if ($thumbCounter >= $firstThumb && $row <= $maxRows) {

            // Only look at valid image types
            if (validFileType($file)) {

                // If this is a new row, start a new <TR>
                if ($col == 0) {
                    $imageList .= '<tr>';
                }

                $img = buildImageURL($baseURL, $baseDir, $albumDir, $currDir,
                                     $albumURLroot, $file,
                                     $suppressImageInfo, $markerType,
                                     $markerLabel, $useThumbSubdir,
                                     $thumbSubdir, $noThumbs, $thumbExt,
                                     $suppressAltTags, $description,
                                     $imagePopup, $imagePopType,
                                     $imagePopLocationBar, $imagePopMenuBar,
                                     $imagePopToolBar, $commentFilePerImage,
                                     $startFrom);
                $imageList .= $img;

                // Keep track of what row and column we are on
                if ($col == $maxColumns) {
                    $imageList .= '</tr>';
                    ++$row;
                    $col = 0;
                } else {
                    ++$col;
                }
            }
        }
    }

    closedir($dir);

    // If there aren't any images to work with, just say so.
    if ($imageList == '') {
        $imageList = 'NULL';
    } elseif (!eregi('</tr>$', $imageList)) {
        // Stick a </tr> on the end if it isn't there already.
        $imageList .= '</tr>';
    }

    return $imageList;

}   // -- End of buildImageList()


// buildImageURL() - spit out HTML for a particular image

function buildImageURL ( $baseURL, $baseDir, $albumDir, $currDir,
                         $albumURLroot, $filename, $suppressImageInfo,
                         $markerType, $markerLabel,
                         $useThumbSubdir, $thumbSubdir, $noThumbs, $thumbExt,
                         $suppressAltTags, $description, $imagePopup,
                         $imagePopType, $imagePopLocationBar,
                         $imagePopMenuBar, $imagePopToolBar,
                         $commentFilePerImage, $startFrom )
{
    global $mig_config;

    $fname = getFileName($filename);
    $ext = getFileExtension($filename);

    // newCurrDir is currDir without leading './'
    $newCurrDir = getNewCurrDir($currDir);

    // URL-encode currDir, keeping an old copy too
    $oldCurrDir = $currDir;
    $currDir = migURLencode($currDir);

    // URL-encoded the filename
    $newFname = rawurlencode($fname);

    // Only show a thumbnail if one exists.  Otherwise use a default
    // "generic" thumbnail image.

    if ($useThumbSubdir) {

        if ($thumbExt) {
            $thumbFile = "$albumDir/$oldCurrDir/$thumbSubdir/$fname.$thumbExt";
        } else {
            $thumbFile = "$albumDir/$oldCurrDir/$thumbSubdir/$fname.$ext";
        }

    } else {

        if ($markerType == 'prefix') {
            $thumbFile  = "$albumDir/$oldCurrDir/$markerLabel";

            if ($thumbExt) {
                $thumbFile .= "_$fname.$thumbExt";
            } else {
                $thumbFile .= "_$fname.$ext";
            }
        }

        if ($markerType == 'suffix') {
            $thumbFile  = "$albumDir/$oldCurrDir/$fname";

            if ($thumbExt) {
                $thumbFile .= "_$markerLabel.$thumbExt";
            } else {
                $thumbFile .= "_$markerLabel.$ext";
            }
        }
    }

    if (file_exists($thumbFile)) {
        if ($useThumbSubdir) {
            $thumbImage  = "$albumURLroot/$currDir/$thumbSubdir";

            if ($thumbExt) {
                $thumbImage .= "/$fname.$thumbExt";
            } else {
                $thumbImage .= "/$fname.$ext";
            }

        } else {

            if ($markerType == 'prefix') {
                $thumbImage  = "$albumURLroot/$currDir/$markerLabel";

                if ($thumbExt) {
                    $thumbImage .= "_$fname.$thumbExt";
                } else {
                    $thumbImage .= "_$fname.$ext";
                }
            }

            if ($markerType == 'suffix') {
                $thumbImage  = "$albumURLroot/$currDir/$fname";

                if ($thumbExt) {
                    $thumbImage .= "_$markerLabel.$thumbExt";
                } else {
                    $thumbImage .= "_$markerLabel.$ext";
                }
            }
        }
        $thumbImage = migURLencode($thumbImage);

    } else {
        $newRoot = ereg_replace('/[^/]+$', '', $baseURL);
        $thumbImage = $newRoot . '/images/no_thumb.gif';
    }

    // Get description, if any
    if ($commentFilePerImage) {
        $alt_desc = getImageDescFromFile("$fname.$ext", $albumDir, $currDir);
        // Get a conventional comment if there isn't one here.
        if (! $alt_desc) {
            $alt_desc = getImageDescription("$fname.$ext", $description);
        }
    } else {
        $alt_desc = getImageDescription("$fname.$ext", $description);
    }

    $alt_desc = strip_tags($alt_desc);

    // Figure out the image's size (in bytes and pixels) for display
    $imageFile = "$albumDir/$oldCurrDir/$fname.$ext";

    // Figure out the pixels
    $imageProps = GetImageSize($imageFile);
    $imageWidth = $imageProps[0];
    $imageHeight = $imageProps[1];

    // Figure out the bytes
    $imageSize = filesize($imageFile);

    if ($imageSize > 1048576) {
        $imageSize = sprintf('%01.1f', $imageSize / 1024 / 1024) . 'MB';
    } elseif ($imageSize > 1024) {
        $imageSize = sprintf('%01.1f', $imageSize / 1024) . 'KB';
    } else {
        $imageSize = $imageSize . $mig_config['lang']['bytes'];
    }

    // Figure out thumbnail geometry
    $thumbHTML = '';
    if (file_exists($thumbFile)) {
        $thumbProps = GetImageSize($thumbFile);
        $thumbHTML = $thumbProps[3];
    }

    // beginning of the table cell
    $url = '<td class="image"><a';

    if (!$suppressAltTags) {
        $url .= ' title="' . $alt_desc . '"';
    }

    $url .= ' href="';

    // set up the image pop-up if appropriate to do so
    if ($imagePopup) {
        $popup_width = $imageWidth + 30;
        $popup_height = $imageHeight + 150;
        $url .= '#" onClick="window.open(\'';
    }

    $url .= $baseURL . '?currDir='
         . $currDir . '&pageType=image&image=' . $newFname
         . '.' . $ext;

    if ($startFrom) {
        $url .= '&startFrom=' . $startFrom;
    }

    if ($imagePopup) {
        $url .= "','";

        if ($imagePopType == 'reuse') {
            $url .= 'mig_window_11190874';
        } else {
            $url .= 'mig_window_' . time() . '_' . $newFname;
        }

        $url .= "','width=$popup_width,height=$popup_height,"
              . "resizable=yes,scrollbars=1";

        // Set up various toolbar options if requested

        if ($imagePopLocationBar) {
            $url .= ",location=1";
        }
        if ($imagePopToolBar) {
            $url .= ",toolbar=1";
        }
        if ($imagePopMenuBar) {
            $url .= ",menubar=1";
        }

        $url .= "');";
    }

    $url .= '">';

    // If $noThumbs is true, just print the image filename rather
    // than the <IMG> tag pointing to a thumbnail.
    if ($noThumbs) {
        $url .= "$newFname.$ext";
    } else {
        $url .= '<img src="' . $thumbImage . '"';
            // Only print the ALT tag if it's wanted.
            if (! $suppressAltTags) {
                $url .= ' alt="' . $alt_desc . '"';
            }

        $url .= ' border="0" ' . $thumbHTML . '>';
    }

    $url .= '</a>';     // End the <A> element

    // If $suppressImageInfo is FALSE, show the image info
    if (!$suppressImageInfo) {
        $url .= '<br><font size="-1">';
        if (!$noThumbs) {
            $url .= $fname . '.' . $ext . '<br>';
        }

        $url .= '(' . $imageWidth . 'x' . $imageHeight . ', '
             . $imageSize . ')</font>';
    }

    $url .= '</td>';        // Close table cell
    return $url;

}   // -- End of buildImageURL()


// buildNextPrevLinks() - Build a link to the "next" and "previous"
//                        images.

function buildNextPrevLinks ( $baseURL, $albumDir, $currDir, $image,
                              $markerType, $markerLabel,
                              $hidden, $presorted, $sortType, $startFrom )
{
    global $mig_config;

    // newCurrDir is currDir without the leading './'
    $newCurrDir = getNewCurrDir($currDir);

    if (is_dir("$albumDir/$currDir")) {
        $dir = opendir("$albumDir/$currDir");  // Open directory handle
    } else {
        print "ERROR: no such currDir '$currDir'<br>";
        exit;
    }

    // Gather all files into an array
    $fileList = array ();
    while ($file = readdir($dir)) {

        // Ignore thumbnails
        if ($markerType == 'prefix' && ereg("^$markerLabel\_", $file)) {
            continue;
        }

        if ($markerType == 'suffix' && ereg("_$markerLabel\.[^.]+$", $file)
            && validFileType($file)) {
                continue;
        }

        // Only look at valid image formats
        if (! validFileType($file)) {
            continue; 
        }

        // Ignore the hidden images
        if ($hidden[$file]) {
            continue;
        }

        // Make sure this is a file, not a directory.
        // and make sure it isn't presorted
        if (is_file("$albumDir/$currDir/$file") && ! $presorted[$file]) {
            $fileList[$file] = TRUE;
            // Store a date, too, if needed
            if (ereg("bydate.*", $sortType)) {
                $timestamp = filemtime("$albumDir/$currDir/$file");
                $filedates["$timestamp-$file"] = $file;
            }
        }
    }

    closedir($dir); 

    ksort($fileList);       // sort, so we see sorted results
    reset($fileList);       // reset array pointer

    if ($sortType == "bydate-ascend") {
        ksort($filedates);
        reset($filedates);

    } elseif ($sortType == "bydate-descend") {
        krsort($filedates);
        reset($filedates);
    }

    // Generated final sorted list
    if (ereg("bydate.*", $sortType)) {
        // since $filedates is sorted by date, and date is
        // the key, the key is pointless to put in the list now.
        // so we store the value, not the key, in $presorted
        while (list($junk,$file) = each($filedates)) {
            $presorted[$file] = TRUE;
        }

    } else {
        // however, here we have real data in the key, so we push
        // the key, not the value, into $presorted.
        while (list($file,$junk) = each($fileList)) {
            $presorted[$file] = TRUE;
        }
    }

    reset($presorted);      // reset array pointer

    // Gather all files into an array

    $i = 1;                 // iteration counter, etc

    // Yes, position 0 is garbage.  Makes the math easier later.
    $fList = array ( 'blah' ); 

    while (list($file, $junk) = each($presorted)) {
    
        // If "this" is the one we're looking for, mark it as such.
        if ($file == $image) {
            $ThisImagePos = $i;
        }

        $fList[$i] = $file;     // Stash filename in the array
        $i++;                   // increment the counter, of course.
    } 
    reset($fList);

    $i--;                       // Get rid of the last increment...

    // Next is one more than $ThisImagePos.  Test if that has a value
    // and if it does, consider it "next".
    if ($fList[$ThisImagePos+1]) {
        $next = migURLencode($fList[$ThisImagePos+1]);
    } else {
        $next = 'NA';
    }

    // Previous must always be one less than the current index.  If
    // that has a value, that is.  Unless the current index is "1" in
    // which case we know there is no previous.
    
    if ($ThisImagePos == 1) {
        $prev = 'NA';
    } elseif ($fList[$ThisImagePos-1]) {
        $prev = migURLencode($fList[$ThisImagePos-1]); 
    }

    // URL-encode currDir
    $currDir = migURLencode($currDir);

    // newCurrDir is currDir without the leading './'
    $newCurrDir = getNewCurrDir($currDir);

    // If there is no previous image, show a greyed-out link
    if ($prev == 'NA') {
        $pLink = '<font size="-1">[&nbsp;<font color="#999999">'
               . $mig_config['lang']['previmage']
               . '</font>&nbsp;]</font>';

    // else show a real link
    } else {
        $pLink = '<font size="-1">[&nbsp;<a href="' . $baseURL
               . '?pageType=image&currDir=' . $currDir . '&image='
               . $prev;
        if ($startFrom) {
            $pLink .= '&startFrom=' . $startFrom;
        }
        $pLink .= '">' . $mig_config['lang']['previmage']
                . '</a>&nbsp;]</font>';
    }

    // If there is no next image, show a greyed-out link
    if ($next == 'NA') {
        $nLink = '<font size="-1">[&nbsp;<font color="#999999">'
               . $mig_config['lang']['nextimage']
               . '</font>&nbsp;]</font>';
    // else show a real link
    } else {
        $nLink = '<font size="-1">[&nbsp;<a href="' . $baseURL
               . '?pageType=image&currDir=' . $currDir . '&image='
               . $next;
        if ($startFrom) {
            $nLink .= '&startFrom=' . $startFrom;
        }
        $nLink .= '">' . $mig_config['lang']['nextimage']
                . '</a>&nbsp;]</font>';
    }

    // Current position in the list
    $currPos = '#' . $ThisImagePos . '&nbsp;of&nbsp;' . $i;

    $retval = array( $nLink, $pLink, $currPos );
    return $retval;

}   // -- End of buildNextPrevLinks()


// buildYouAreHere() - build the "You are here" line for the top
//                     of each page

function buildYouAreHere ( $baseURL, $currDir, $image )
{

    global $mig_config;

    // Use $workingCopy so we don't trash value of $currDir
    $workingCopy = $currDir;

    // Loop until we get down to just the '.'
    while ($workingCopy != '.') {

        // $label is the "last" thing in the path. Strip up to that
        $label = ereg_replace('^.*/', '', $workingCopy);
        // Render underscores as spaces and turn spaces into &nbsp;
        $label = str_replace('_', '&nbsp;', $label);
        $label = str_replace(' ', '&nbsp;', $label);

        // Get a URL-encoded copy of $workingCopy
        $encodedCopy = migURLencode($workingCopy);

        if ($image == '' && $workingCopy == $currDir) {
            $url = '&nbsp;:&nbsp;<b>' . $label . '</b>';
        } else {
            $url = '&nbsp;:&nbsp;<a href="' . $baseURL . '?currDir='
                 . $encodedCopy . '">' . $label . '</a>';
        }

        // Strip the last piece off of $workingCopy to go to next loop
        $workingCopy = ereg_replace('/[^/]+$', '', $workingCopy);

        // Build up the final path over each loop iteration
        $x = $hereString;
        $hereString = $url . $x;
    }

    // If we're down to '.' as our currDir then this is 'Main'
    if ($currDir == '.') {
        $url = '<b>' . $mig_config['lang']['main'] . '</b>';
        $x = $hereString;
        $hereString = $url . $x;

    // Or if we're not, then Main should be a link instead of just text
    } else {
        $url = '<a href="' . $baseURL . '?currDir=' . $workingCopy
             . '">' . $mig_config['lang']['main'] . '</a>';
        $x = $hereString;
        $hereString = $url . $x;
    }

    // If there's an image, tack it onto the end of the hereString
    if ($image != '') {
        $hereString .= '&nbsp;:&nbsp;<b>' . $image . '</b>';
    }

    $x = $hereString;
    $hereString = '<font size="-1">' . $x . '</font>';
    return $hereString;

}   // -- End of buildYouAreHere()


// convertIncludePath() - Converts the path used by include() if needed.
//                        (Not normally needed, but some installs of PHP
//                        demand this).

function convertIncludePath ( $flag, $path='', $regex, $new )
{
    if ($flag) {
        $path = ereg_replace($regex, $new, $path);
    }

    return $path;

}   // -- End of convertIncludePath()


// descriptionFrame() - Same thing as folderFrame() for descriptions.

function descriptionFrame ( $input )
{

    $retval = '<table border="0" cellpadding="10" width="60%">'
            . '<tr><td class="desc">' . $input . '</td></tr></table><br>';

    return $retval;

}   // -- End of descriptionFrame()


// folderFrame() - frames stuff in HTML table code... avoids template
//                 problems in places where there are images but no folders,
//                 or vice versa.

function folderFrame ( $input, $randomFolderThumbs, $maxColumns )
{
    if ($randomFolderThumbs) {
        $pad = 5;
    } else {
        $pad = 2;
    }

    $retval = '<table border="0" cellpadding="'.$pad.'" cellspacing="0">'
            . '<tr><td class="folder" colspan="' . $maxColumns
            . '">' . $input . '</td></tr></table><br>';

    return $retval;

}   // -- End of folderFrame()


// formatExifData() - formats EXIF data according to $exifFormatString.

function formatExifData ( $formatString, $exifData )
{

    // %a   Aperture
    // %c   Comment
    // %f   Flash used?
    // %i   ISO rating
    // %l   Focal length
    // %m   Camera model
    // %s   Shutter speed

    // %Y   Year
    // %M   Month
    // %D   Day
    // %T   Time

    $table = array ( 'c' => $exifData['comment'],
                     'a' => $exifData['aperture'],
                     'f' => $exifData['flash'],
                     'i' => $exifData['iso'],
                     'l' => $exifData['foclen'],
                     'm' => $exifData['model'],
                     's' => $exifData['shutter'],
                     'Y' => $exifData['year'],
                     'M' => $exifData['month'],
                     'D' => $exifData['day'],
                     'T' => $exifData['time']           );

    // get rid of trailing | character if there is one
    $formatString = ereg_replace('\|$', '', $formatString);

    // Nibble away at format string until it is empty
    while ($formatString) {

        // Try to match a block (a block is a pipe followed by a format
        // atom (such as %c) surrounded by optional text)

        if (ereg('^\|[^|]*%[a-zA-Z][^|]*', $formatString, $matches)) {

            $x = $matches[0];               // entire match (this is the
                                            // block pattern, which we
                                            // can work on as a whole now)

            $x = str_replace('|','', $x);   // get rid of leading | char

            // $changeflag is used to tell us if we should bother
            // printing this block at all.  If none of the format
            // characters in this block can be expanded, we never set
            // $changeflag to TRUE.  If it's not TRUE at the end of this
            // while(), the block is just dumped.
            $changeflag = FALSE;

            // Keep on going until every %X atom has been examined and
            // expanded.

            while (ereg('%([a-zA-Z])', $x, $lettermatch)) {

                // which letter matched?
                $letter = $lettermatch[1];

                // If this can be expanded, do so.  If it can be,
                // set $changeflag to TRUE so we know to include this
                // block instead of dumping it.
                if ($table[$letter]) {
                    $newtext = $table[$letter];
                    $changeflag = TRUE;
                }

                // Do interpolation
                $x = str_replace("%$letter", $newtext, $x);
            }

            // Only if $changeflag is TRUE do we bother tacking this
            // onto the final product.
            if ($changeflag) {
                $newstr .= $x;
            }

            // shrink format string by one block.
            $formatString = ereg_replace('^\|[^|]+', '', $formatString);
        }
    }

    return $newstr;

}   // -- End of formatExifData()


// getExifDescription() - Fetches a comment if available from the
//                        Exif comments file (exif.inf) as well as
//                        fetching EXIF data

function getExifDescription ( $albumDir, $currDir, $image, $formatString )
{

    global $mig_config;

    $aperture   = array ();
    $day        = array ();
    $desc       = array ();
    $flash      = array ();
    $foclen     = array ();
    $iso        = array ();
    $model      = array ();
    $month      = array ();
    $shutter    = array ();
    $time       = array ();
    $year       = array ();

    if (file_exists("$albumDir/$currDir/exif.inf")) {

        $file = fopen("$albumDir/$currDir/exif.inf", 'r');
        $line = fgets($file, 4096);     // get first line
        while (!feof($file)) {

            if (ereg('^File name    : ', $line)) {
                $fname = str_replace('File name    : ', '', $line);
                $fname = chop($fname);

            } elseif (ereg('^Comment      : ', $line)) {
                $comment = str_replace('Comment      : ', '', $line);
                $comment = chop($comment);
                $desc[$fname] = $comment;

            }

            if (ereg('^Camera model : ', $line)) {
                $x = str_replace('Camera model : ', '', $line);
                $x = chop($x);
                $model[$fname] = $x;

            // This one apparently sometimes has a space after
            // the colon, sometimes not.  Try to work either way.
            } elseif (ereg('^Exposure time: ?', $line)) {
                $x = ereg_replace('^Exposure time: ?', '', $line);
                if (ereg('\(', $x)) {
                    $x = ereg_replace('^.*\(', '', $x);
                    $x = ereg_replace('\).*$', '', $x);
                }
                $x = chop($x);
                $shutter[$fname] = $x;

            } elseif (ereg('^Aperture     : ', $line)) {
                $x = str_replace('Aperture     : ', '', $line);
                // make it fN.N instead of f/N.N
                $x = ereg_replace('/', '', $x);
                $x = chop($x);
                $aperture[$fname] = $x;

            } elseif (ereg('^Focal length : ', $line)) {
                $x = str_replace('Focal length : ', '', $line);
                if (ereg('35mm equiv', $x)) {
                    $x = ereg_replace('^.*alent: ', '', $x);
                    $x = chop($x);
                    $x = ereg_replace('\)$', '', $x);
                }
                $foclen[$fname] = $x;

            } elseif (ereg('^ISO equiv.   : ', $line)) {
                $x = str_replace('ISO equiv.   : ', '', $line);
                $x = chop($x);
                $iso[$fname] = $x;

            } elseif (ereg('^Flash used   : Yes', $line)) {
                $flash[$fname] = $mig_config['lang']['flash_used'];

            } elseif (ereg('^Date/Time    : ', $line)) {
                $x = str_replace('Date/Time    : ', '', $line);
                $x = chop($x);

                // Turn into human readable format and record
                list($w,$x,$y,$z) = parseExifDate($x);

                $year[$fname]     = $w;
                $month[$fname]    = $x;
                $day[$fname]      = $y;
                $time[$fname]     = $z;
            }

            $line = fgets($file, 4096);
        }

        fclose($file);

        $exifData = array ( 'comment'   => $desc[$image],
                            'model'     => $model[$image],
                            'year'      => $year[$image],
                            'month'     => $month[$image],
                            'day'       => $day[$image],
                            'time'      => $time[$image],
                            'iso'       => $iso[$image],
                            'foclen'    => $foclen[$image],
                            'shutter'   => $shutter[$image],
                            'aperture'  => $aperture[$image],
                            'flash'     => $flash[$image]        );

        $retval = formatExifData($formatString, $exifData);

        return $retval;

    } else {
        return '';
    }

}   // -- End of getExifDescription()


// getFileExtension() - figure out a file's extension and return it.

function getFileExtension ( $file )
{

    // Strip off the extension part of the filename
    $ext = ereg_replace('^.*\.', '', $file);

    return $ext;

}   // -- End of getFileExtension()


// getFileName() - figure out a file's name sans extension.

function getFileName ( $file )
{

    // Strip off the non-extension part of the filename
    $fname = ereg_replace('\.[^\.]+$', '', $file);

    return $fname;

}   // -- End of getFileName()


// getImageDescFromFile() - Fetches an image description from a
//                          per-image comment file (used if
//                          $commentFilePerImage is TRUE)

function getImageDescFromFile ( $image, $albumDir, $currDir )
{

    $imageDesc = '';
    $fname = getFileName($image);

    if (file_exists("$albumDir/$currDir/$fname.txt")) {

        $file = fopen("$albumDir/$currDir/$fname.txt", 'r');
        $line = fgets($file, 4096);     // get first line

        // This double-check exists so that files ending without
        // a proper newline character are not truncated.
        // This says "while (not EOF) and ($line is not empty)"...
        while ( $line || ! feof($file)) {
            $line = trim($line);
            $imageDesc .= "$line ";
            $line = fgets($file, 4096); // get next line
        }

        fclose($file);

    } else {
        // File doesn't exist?  Okay, return false.
        return FALSE;
    }

    return $imageDesc;

}   // -- End of getImageDescFromFile()


// getImageDescription() - Fetches an image description from the
//                         comments file (mig.cf)

function getImageDescription ( $image, $description )
{

    $imageDesc = '';
    if ($description[$image]) {
        $imageDesc = $description[$image];
    }
    return $imageDesc;

}   // -- End of getImageDescription()


// getNewCurrDir() - replaces the silly old $newCurrDir being all
//                   over the place.  Especially in the URI string itself.

function getNewCurrDir ( $currDir )
{

    // This just rips off the leading './' off currDir if it exists
    $newCurrDir = ereg_replace('^\.\/', '', $currDir);
    $newCurrDir = migURLencode($newCurrDir);

    return $newCurrDir;

}   // -- End of getNewCurrDir()


// getNumberOfDirs() - Counts subdirectories in a given folder

function getNumberOfDirs ( $folder, $useThumbSubdir, $markerType,
                           $markerLabel )
{
    if (is_dir($folder)) {
        $dir = opendir($folder);    // Open directory handle
    } else {
        return 0;
    }

    $count = 0;
    while ($file = readdir($dir)) {
        if ($file != '.' && $file != '..' && $file != 'thumbs'
            && is_dir("$folder/$file"))
        {
            ++$count;
        }
    }
    
    return $count;

}   // -- End of getNumberOfDirs()


// getNumberOfImages() - counts images in a given folder

function getNumberOfImages ( $folder, $useThumbSubdir, $markerType,
                             $markerLabel )
{
    if (is_dir($folder)) {
        $dir = opendir($folder);    // Open directory handle
    } else {
        return 0;
    }

    $count = 0;

    while ($file = readdir($dir)) {
        // Skip over thumbnails
        if (!$useThumbSubdir) {  // unless $useThumbSubdir is set,
                                 // then don't waste time on this check

            if ($markerType == 'suffix' && ereg("_$markerLabel\.[^.]+$",$file)
                && validFileType($file)) {
                    continue;
            }
            if ($markerType == 'prefix' && ereg("^$markerLabel\_", $file)) {
                continue;
            }
        }

        // We'll look at this one only if it's a file and it matches our list
        // of approved extensions
        if (is_file("$folder/$file") && validFileType($file)) {
                $count++;
        }
    }

    return $count;

}   // -- End of getNumberOfImages()


// getRandomThumb() - Find a random thumbnail to show instead of the folder
//                    icon.

function getRandomThumb ( $file, $folder, $useThumbSubdir, $thumbSubdir,
                          $albumURLroot, $currDir, $markerType, $markerLabel )
{
    // SECTION ONE ...
    // If we're using thumbnail subdirectories

    if ($useThumbSubdir) {
        $myThumbDir = $folder . '/' . $thumbSubdir;

        if (is_dir($myThumbDir)) {
            $readSample = opendir($myThumbDir);
            while ($sample = readdir($readSample)) {
                if ($sample != '.' && $sample != '..') {
                    if (validFileType($sample)) {
                        $mySample = $albumURLroot . '/' . $currDir
                                  . '/' . $file . '/' .$thumbSubdir
                                  . '/' . $sample;
                        return $mySample;
                    }
                }
            }
            closedir($readSample);

        } elseif (is_dir($folder)) {

            $dirlist = opendir($folder);

            while ($item = readdir($dirlist)) {
                if (is_dir("$folder/$item") && $item != '.'
                            && $item != '..')
                {
                    $mySample = getRandomThumb($file.'/'.$item,
                                     $folder.'/'.$item,
                                     $useThumbSubdir, $thumbSubdir,
                                     $albumURLroot, $currDir,
                                     $markerType, $markerLabel);

                    if ($mySample) {
                        return $mySample;
                    }
                }
            }
            closedir($dirlist);
        }

    // SECTION TWO...
    // Not using thumbnail subdirectories

    } else {

        if (is_dir($folder)) {
            $readSample = opendir($folder);
        } else {
            return FALSE;
        }

        while ($sample = readdir($readSample)) {
            if ($markerType == 'prefix') {
                if (ereg("^$markerLabel\_", $sample)
                    && validFileType($sample))
                {
                    $mySample = $sample;
                }
            } elseif ($markerType == 'suffix') {
                if (ereg("_$markerLabel\.[^.]+$", $sample)
                    && validFileType($sample))
                {
                    $mySample = $sample;
                }

            } else {
                print 'ERROR: no markerType set in getRandomThumb()';
                exit;
            }

            if ($mySample) {
                $mySample = $albumURLroot . '/' . $currDir . '/' . $file
                          . '/' . $mySample;
                return $mySample;
            } else {
                $dirlist = opendir($folder);
                while ($item = readdir($dirlist)) {
                    if (is_dir("$folder/$item") && $item != '.'
                               && $item != '..')
                    {
                        $mySample = getRandomThumb($file.'/'.$item,
                                        $folder.'/'.$item,
                                        $useThumbSubdir, $thumbSubdir,
                                        $albumURLroot, $currDir,
                                        $markerType, $markerLabel);

                        if ($mySample) {
                            return $mySample;
                        }
                    }
                }
                closedir($dirlist);
            }
        }
        
        closedir($readSample);
    }

    return FALSE;

}   // -- End of getRandomThumb()


// imageFrame() - Same thing as folderFrame() but for image tables.

function imageFrame ( $input )
{

    $retval = '<table border="0" cellpadding="5" cellspacing="0"'
            . ' class="image"><tr><td>' . $input . '</td></tr></table><br>';

    return $retval;

}   // -- End of imageFrame()


// migURLencode() - fixes a problem where "/" turns into "%2F" when
//                  using rawurlencode()

function migURLencode ( $string )
{

    $new = rawurldecode($string);   // decode first
    $new = rawurlencode($new);      // then encode

    $new = str_replace('%2F', '/', $new);       // slash (/)

    return $new;

}   // -- End of migURLencode()


// parseExifDate() - parses an EXIF date string and returns it in a
//                   more human-readable format.

function parseExifDate ( $stamp )
{

    global $mig_config;

    // Separate into a date and a time
    list($date,$time) = split(' ', $stamp);

    // Parse date
    list($year, $month, $day) = split(':', $date);

    // Turn numeric month into a 3-character month string
    $month = $mig_config['lang']['month'][$month];

    // Parse time
    list($hour, $minute, $second) = split(':', $time);

    // Translate into 12-hour time
    switch ($hour) {
        case '00':
            $time = '12:' . $minute . $mig_config['lang']['am'];
            break;
        case '01':
        case '02':
        case '03':
        case '04':
        case '05':
        case '06':
        case '07':
        case '08':
        case '09':
        case '10':
        case '11':
            $time = $hour . ':' . $minute . $mig_config['lang']['am'];
            break;
        case '12':
            $time = $hour . ':' . $minute . $mig_config['lang']['pm'];
            break;
        case '13':
        case '14':
        case '15':
        case '16':
        case '17':
        case '18':
        case '19':
        case '20':
        case '21':
        case '22':
        case '23':
            $time = ($hour - 12) . ':' . $minute . $mig_config['lang']['pm'];
            break;
    }

    $retval = array ( $year, $month, $day, $time );

    return ($retval);

}   // -- End of parseExifDate()


// parseMigCf - Parse a mig.cf file for sort and hidden blocks

function parseMigCf ( $directory, $useThumbSubdir, $thumbSubdir )
{

    // What file to parse
    $cfgfile = 'mig.cf';

    // Prototypes
    $hidden         = array ();
    $presort_dir    = array ();
    $presort_img    = array ();
    $desc           = array ();
    $ficons         = array ();

    // Hide thumbnail subdirectory if one is in use.
    if ($useThumbSubdir) {
        $hidden[$thumbSubdir] = TRUE;
    }

    if (file_exists("$directory/$cfgfile")) {
        $file = fopen("$directory/$cfgfile", 'r');
        $line = fgets($file, 4096);     // get first line

        while (! feof($file)) {

            // Parse <hidden> blocks
            if (eregi('^<hidden>', $line)) {
                $line = fgets($file, 4096);
                while (! eregi('^</hidden>', $line)) {
                    $line = trim($line);
                    $hidden[$line] = TRUE;
                    $line = fgets($file, 4096);
                }
            }

            // Parse <sort> structure
            if (eregi('^<sort>', $line)) {
                $line = fgets($file, 4096);
                while (! eregi('^</sort>', $line)) {
                    $line = trim($line);

                    if (is_file("$directory/$line")) {
                        $presort_img[$line] = TRUE;
                    } elseif (is_dir("$directory/$line")) {
                        $presort_dir[$line] = TRUE;
                    }

                    $line = fgets($file, 4096);
                }
            }

            // Parse <bulletin> structure
            if (eregi('^<bulletin>', $line)) {
                $line = fgets($file, 4096);
                while (! eregi('^</bulletin>', $line)) {
                    $bulletin .= $line;
                    $line = fgets($file, 4096);
                }
            }

            // Parse <comment> structure
            if (eregi('^<comment', $line)) {
                $commfilename = trim($line);
                $commfilename = str_replace('">', '', $commfilename);
                $commfilename = eregi_replace('^<comment "','',$commfilename);
                $line = fgets($file, 4096);
                while (! eregi('^</comment', $line)) {
                    $line = trim($line);
                    $mycomment .= "$line ";
                    $line = fgets($file, 4096);
                }
                $desc[$commfilename] = $mycomment;
                $commfilename = '';
                $mycomment = '';
            }

            // Parse FolderIcon lines
            if (eregi('^foldericon ', $line)) {
                $x = trim($line);
                list($y, $folder, $icon) = explode(' ', $x);
                $ficons[$folder] = $icon;
            }

            // Parse FolderTemplate lines
            if (eregi('^foldertemplate ', $line)) {
                $x = trim($line);
                list($y, $template) = explode(' ', $x);
            }

            // Parse PageTitle lines
            if (eregi('^pagetitle ', $line)) {
                $x = trim($line);
                $pagetitle = eregi_replace('^pagetitle ', '', $x);
            }

            // Parse MaintAddr lines
            if (eregi('^maintaddr ', $line)) {
                $x = trim($line);
                $maintaddr = eregi_replace('^maintaddr ', '', $x);
            }

            // Parse MaxFolderColumns lines
            if (eregi('^maxfoldercolumns ', $line)) {
                $x = trim($line);
                list($y, $fcols) = explode(' ', $x);
            }

            // Parse MaxThumbColumns lines
            if (eregi('^maxthumbcolumns ', $line)) {
                $x = trim($line);
                list($y, $tcols) = explode(' ', $x);
            }

            // Parse MaxThumbRows lines
            if (eregi('^maxthumbrows ', $line)) {
                $x = trim($line);
                list($y, $trows) = explode(' ', $x);
            }

            // Get next line
            $line = fgets($file, 4096);

        } // end of main while() loop

        fclose($file);
    }

    $retval = array ($hidden, $presort_dir, $presort_img, $desc,
                     $bulletin, $ficons, $template, $pagetitle,
                     $fcols, $tcols, $trows, $maintaddr);
    return $retval;

}   //  -- End of parseMigCf()


// printTemplate() - prints HTML page from a template file

function printTemplate ( $baseURL, $templateDir, $templateFile, $version,
                         $maintAddr, $folderList, $imageList, $backLink,
                         $albumURLroot, $image, $currDir, $newCurrDir,
                         $pageTitle, $prevLink, $nextLink, $currPos,
                         $description, $youAreHere, $distURL, $albumDir,
                         $pathConvertFlag, $pathConvertRegex,
                         $pathConvertTarget )
{
    // Only prepend a path if one isn't there.  For unix-like systems this
    // checks for a leading slash, for Windows-like system it checks for
    // a leading drive letter.
    if (! ereg('^/', $templateFile) && ! eregi("^[a-z]:", $templateFile)) {
        $templateFile = $albumDir . '/' . $newCurrDir . '/' . $templateFile;
    }

    // Panic if the template file doesn't exist.
    if (! file_exists($templateFile)) {
        print "ERROR: $templateFile does not exist!";
        exit;
    }

    $file = fopen($templateFile,'r');    // Open template file
    $line = fgets($file, 4096);                         // Get first line

    while (! feof($file)) {             // Loop until EOF

        // Look for include directives and process them
        if (ereg('^#include', $line)) {
            $orig_line = $line;
            $line = trim($line);
            $line = str_replace('#include "', '', $line);
            $line = str_replace('";', '', $line);
            if (strstr($line, '/')) {
                $line = '<!-- ERROR: #include directive failed.'
                      . ' Path included a "/" character, indicating'
                      . ' an absolute or relative path.  All included'
                      . ' files must be located in the templates/'
                      . ' subdirectory. Directive was:'
                      . "\n     $orig_line\n-->\n";
                print $line;
            } else {
                $incl_file = $line;
                if (file_exists("$templateDir/$incl_file")) {

                    if (function_exists('virtual')) {
                        // virtual() doesn't like absolute paths,
                        // apparently, so just pass it a relative one.
                        $tmplDir = ereg_replace("^.*/", "", $templateDir);
                        virtual("$tmplDir/$incl_file");
                    } else {
                        include( convertIncludePath($pathConvertFlag,
                                   "$templateDir/$incl_file",
                                   $pathConvertRegex, $pathConvertTarget));
                    }

                } else {
                    // If the file doesn't exist, complain.
                    $line = '<!-- ERROR: #include directive failed.'
                          . ' Named file ' . $incl_file
                          . ' does not exist.  Directive was:'
                          . "\n    $orig_line\n-->\n";
                    print $line;
                }
            }

        } else {

            // Make sure this is URL encoded
            $encodedImageURL = migURLencode($image);

            if ($image) {
                // Get image pixel size for <IMG> element
                $imageProps = GetImageSize("$albumDir/$currDir/$image");
                $imageSize = $imageProps[3];
            }

            // List of valid tags
            $replacement_list = array (
                'baseURL', 'maintAddr', 'version', 'folderList',
                'imageList', 'backLink', 'currDir', 'newCurrDir',
                'image', 'albumURLroot', 'pageTitle', 'nextLink',
                'prevLink', 'currPos', 'description', 'youAreHere',
                'distURL', 'encodedImageURL', 'imageSize'
            );

            // Do substitution for various variables
            while (list($key,$val) = each($replacement_list)) {
                $line = str_replace("%%$val%%", $$val, $line);
            }

            print $line;                // Print resulting line
        }
        $line = fgets($file, 4096);     // Grab another line
    }

    fclose($file);
    return TRUE;

}    // -- End of printTemplate()


// validFileType() - Returns TRUE if filetype is supported by Mig

function validFileType ( $filename )
{
    $ext = getFileExtension($filename);

    // "Image" file types
    if (eregi('^(jpg|gif|png|jpeg|jpe)$', $ext)) {
        return TRUE;
    }

    // Nothing matched
    return FALSE;

}   // -- End of validFileType()


// English (default)
$mig_config['lang_lib']['en'] = array (
    'am'            => 'AM',
    'pm'            => 'PM',
    'backhome'      => 'back&nbsp;to',
    'bytes'         => '&nbsp;bytes',
    'flash_used'    => 'flash&nbsp;used',
    'main'          => 'Main',
    'must_auth'     => 'You&nbsp;must&nbsp;enter&nbsp;a&nbsp;valid'
                     . '&nbsp;username&nbsp;and&nbsp;password&nbsp;to'
                     . '&nbsp;enter',
    'nextimage'     => 'next&nbsp;image',
    'no_contents'   => 'No&nbsp;contents.',
    'previmage'     => 'previous&nbsp;image',
    'thumbview'     => 'back&nbsp;to&nbsp;thumbnail&nbsp;view',
    // total_images is special.  It has three elements you can use:
    //     %t :    Total images in folder
    //     %s :    First image shown this page
    //     %e :    Last image shown this page
    'total_images'  => 'Showing&nbsp;images&nbsp;%s-%e&nbsp;of&nbsp;%t'
                     . '&nbsp;total<br>',
    'up_one'        => 'up&nbsp;one&nbsp;level',
    'month'         => array ( '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
                               '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                               '07' => 'Jul', '08' => 'Aug', '09' => 'Sep',
                               '10' => 'Oct', '11' => 'Nov', '12' => 'Dec')
);



// Dutch - courtesy of Erik@Braindisorder.org
$mig_config['lang_lib']['nl'] = array (
    'am'            => 'AM',
    'pm'            => 'PM',
    'backhome'      => 'Terug&nbsp;naar',
    'bytes'         => '&nbsp;bytes',
    'flash_used'    => 'flash&nbsp;used',
    'main'          => 'Hoofdmenu',
    'must_auth'     => 'Je&nbsp;moet&nbsp;een&nbsp;geldige&nbsp;naam&nbsp;en'
                     . '&nbsp;wachtwoord&nbsp;invoeren&nbsp;om&nbsp;hier'
                     . '&nbsp;naar&nbsp;binnen&nbsp;te&nbsp;gaan',
    'nextimage'     => 'volgende&nbsp;foto',
    'no_contents'   => 'Geen&nbsp;commentaar.',
    'previmage'     => 'vorige&nbsp;foto',
    'thumbview'     => 'terug&nbsp;naar&nbsp;overzicht',
    // total_images is special.  It has three elements you can use:
    //     %t :    Total images in folder
    //     %s :    First image shown this page
    //     %e :    Last image shown this page
    'total_images'  => 'Showing&nbsp;images&nbsp;%s-%e&nbsp;of&nbsp;%t'
                     . '&nbsp;total<br>',
    'up_one'        => 'een&nbsp;niveau&nbsp;omhoog',
    'month'         => array ( '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
                               '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                               '07' => 'Jul', '08' => 'Aug', '09' => 'Sep',
                               '10' => 'Oct', '11' => 'Nov', '12' => 'Dec')
);




//
// Main logic
//

// URL to use to call myself again
if ($PHP_SELF) {                                        // register_globals
    $baseURL = $PHP_SELF;
} else {                                                // track_vars
    $baseURL = $HTTP_SERVER_VARS['PHP_SELF'];
}

// Base directory of installation
if ($PATH_TRANSLATED) {                                 // register_globals
    $baseDir = dirname($PATH_TRANSLATED);
} else {                                                // track_vars
    $baseDir = dirname($HTTP_SERVER_VARS['PATH_TRANSLATED']);
}

// Locate and load configuration
if (file_exists($baseDir . '/mig/config.php')) {
    // Found it - we're in Nuke mode
    $configFile = $baseDir . '/mig/config.php';
} elseif (file_exists($baseDir . '/config.php')) {
    // Found it - regular mode
    $configFile = $baseDir . '/config.php';
} else {
    // Uh oh.
    print "FATAL ERROR: Can't find Mig configuration!";
    exit;
}
include(convertIncludePath($pathConvertFlag, $configFile, $pathConvertRegex,
            $pathConvertTarget));

// Return an error if too many modes are set at once
if ($phpNukeCompatible && $phpWebThingsCompatible) {
    print "FATAL ERROR: both \$phpNukeCompatible and"
        . " \$phpWebThingsCompatible are TRUE!\n";
    exit;
}

// Change settings for Nuke mode if appropriate
if ($phpNukeCompatible) {
    $baseDir .= '/mig';
    if (! $phpNukeRoot) {      
        print "FATAL ERROR: \$phpNukeRoot not defined!\n";
        exit;
    }
    $result = chdir($phpNukeRoot);
    if (! $result) {
        print "FATAL ERROR: can not chdir() to \$phpNukeRoot!\n";
        exit;
    }
    // Detect PostNuke if it's there
    if (file_exists('includes/pnAPI.php')) {
        include('includes/pnAPI.php');
        pnInit();
    }

// or for PhpWebThings...
} elseif ($phpWebThingsCompatible) {
    $baseDir .= '/mig';
    if (! $phpWebThingsRoot) {
        print "FATAL ERROR: \$phpWebThingsRoot not defined!\n";
        exit;
    }
    $result = chdir($phpWebThingsRoot);
    if (! $result) {
        print "FATAL ERROR: can not chdir() to \$phpWebThingsRoot!\n";
        exit;
    }
    // phpWebThings library
    if (file_exists('core/main.php')) {
        include('core/main.php');
    } else {
        print "FATAL ERROR: phpWebThings lib missing!\n";
        exit;
    }
}

// Get currDir.  If there isn't one, default to '.'
if (! $currDir && ! $HTTP_GET_VARS['currDir']) {
    $currDir = '.';
} elseif ($HTTP_GET_VARS['currDir']) {
    $currDir = $HTTP_GET_VARS['currDir'];
}

// Strip URL encoding
$currDir = rawurldecode($currDir);

// Get image, if there is one.
if (! $image) {
    $image = $HTTP_GET_VARS['image'];
}

// Get pageType.  If there isn't one, default to "folder"
if (! $pageType) {
    $pageType = $HTTP_GET_VARS['pageType'];
}

if (! $pageType) {
    $pageType = 'folder';
}

if (! $jump) {
    $jump = $HTTP_GET_VARS['jump'];         // for track_vars
}

if (! $startFrom) {
    $startFrom = $HTTP_GET_VARS['startFrom'];
}

// Grab appropriate language from library
$mig_config['lang'] = $mig_config['lang_lib'][$mig_language];

// Backward compatibility with older config.php/mig.cfg versions
if ($maxColumns) {
    $maxThumbColumns = $maxColumns;
}

// Get rid of \'s if magic_quotes_gpc is turned on (causes problems).
if (get_magic_quotes_gpc() == 1) {
    $currDir = stripslashes($currDir);
    if ($image) {
        $image = stripslashes($image);
    }
}

// Turn off magic_quotes_runtime (causes trouble with some installations)
set_magic_quotes_runtime(0);

//
// Handle any password authentication needs
//

$workCopy = $currDir;     // temporary copy of currDir

while ($workCopy) {

    if ($protect[$workCopy]) {

        // Try to get around the track_vars/register_globals problem
        if (! $PHP_AUTH_USER) {
            $PHP_AUTH_USER = $HTTP_SERVER_VARS['PHP_AUTH_USER'];
        }
        if (! $PHP_AUTH_PW) {
            $PHP_AUTH_PW = $HTTP_SERVER_VARS['PHP_AUTH_PW'];
        }

        // If there's not a username yet, fetch one by popping up a
        // login dialog box
        if (! $PHP_AUTH_USER) {
            header('WWW-Authenticate: Basic realm="protected"');
            header('HTTP/1.0 401 Unauthorized');
            print $mig_config['lang']['must_auth'];
            exit;

        } else {
            // Case #2: password/user are present but don't match up
            // with our known user base.  Reject the attempt.
            if ( crypt($PHP_AUTH_PW,
                       substr($protect[$workCopy][$PHP_AUTH_USER],0,2))
                 != $protect[$workCopy][$PHP_AUTH_USER] )
            {
                header('WWW-Authenticate: Basic realm="protected"');
                header('HTTP/1.0 401 Unauthorized');
                print $mig_config['lang']['must_auth'];
                exit;
            }
        }
        break;      // Since we had a match let's stop this loop
    }

    // if $workCopy is already down to '.' just nullify to end loop
    if ($workCopy == '.') {
        $workCopy = FALSE;
    } else {
        // pare $workCopy down one directory at a time
        // so we can check back all the way to '.'
        $workCopy = ereg_replace('/[^/]+$', '', $workCopy);
    }
}

$albumDir = $baseDir . '/albums';     // Where albums live
// If you change the directory here also make sure to change $albumURLroot

$templateDir = $baseDir . '/templates'; // Where templates live

// baseURL with the scriptname torn off the end
$baseHref = ereg_replace('/[^/]+$', '', $baseURL);
// Adjust for Nuke mode if appropriate
if ($phpNukeCompatible || $phpWebThingsCompatible) {
    $baseHref .= '/mig';
}

// Location of image library (for instance, where icons are kept)
$imageDir = $baseHref . '/images';

// Root where album images are living
$albumURLroot = $baseHref . '/albums';
// NOTE: Sometimes Windows users have to set this manually, like:
// $albumURLroot = '/mig/albums';

// Well, GIGO... set default to sane if someone screws up their
// config file
if ($markerType != 'prefix' && $markerType != 'suffix' ) {
    $markerType = 'suffix';
}

if (! $markerLabel) {
    $markerLabel = 'th';
}

// (Try to) get around the track_vars vs. register_globals problem
if (!$SERVER_NAME) {
    $SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
    $PATH_INFO = $HTTP_SERVER_VARS['PATH_INFO'];
}

// Is this a jump-tag URL?
if ($jump && $jumpMap[$jump] && $SERVER_NAME) {
    header("Location: http://$SERVER_NAME$baseURL?$jumpMap[$jump]");
    exit;
}

// Jump-tag using PATH_INFO rather than "....?jump=x" URI
if ($PATH_INFO && $jumpMap[$PATH_INFO] && $SERVER_NAME) {
    header("Location: http://$SERVER_NAME$baseURL?$jumpMap[$PATH_INFO]");
    exit;
}

// Fetch mig.cf information
list($hidden, $presort_dir, $presort_img, $desc, $bulletin, $ficons,
     $folderTemplate, $folderPageTitle, $folderFolderCols, $folderThumbCols,
     $folderThumbRows, $folderMaintAddr)
  = parseMigCf("$albumDir/$currDir", $useThumbSubdir, $thumbSubdir);

// Determine page title to use
if ($folderPageTitle) {
    $pageTitle = $folderPageTitle;
}

// Set per-folder $maintAddr if one was defined
if ($folderMaintAddr) {
    $maintAddr = $folderMaintAddr;
}

// Is this a phpNuke compatible site?
if ($phpNukeCompatible) {

    if (! isset($mainfile)) {
        include('mainfile.php');
    }
    include('header.php');

    // A table to nest Mig in, inside the PHPNuke framework
    print '<table width="100%" border="0" cellspacing="0" cellpadding="2"'
        . ' bgcolor="#000000"><tr><td>'
        . '<table width="100%" border="0" cellspacing="1" cellpadding="7"'
        . ' bgcolor="#FFFFFF"><tr><td>';

// Is this a phpWebThings site?
} elseif ($phpWebThingsCompatible) {
    draw_header();
    theme_draw_center_box_open($pageTitle);
}

// Look at currDir from a security angle.  Don't let folks go outside
// the album directory base
if (strstr($currDir, '..')) {
    print "SECURITY VIOLATION - ABANDON SHIP";
    exit;
}

// strip URL encoding here too
$image = rawurldecode($image);

// if pageType is "folder") generate a folder view

if ($pageType == 'folder') {

    // Determine which template to use
    if ($folderTemplate) {
        $templateFile = $folderTemplate;
    } elseif ($phpNukeCompatible || $phpWebThingsCompatible) {
        $templateFile = $templateDir . '/mig_folder.php';
    } else {
        $templateFile = $templateDir . '/folder.html';
    }

    // Determine columns and rows to use
    if ($folderFolderCols) {
        $maxFolderColumns = $folderFolderCols;
    }

    if ($folderThumbCols) {
        $maxThumbColumns = $folderThumbCols;
    }

    // Generate some HTML to pass to the template printer

    // list of available folders
    $folderList = buildDirList($baseURL, $albumDir, $albumURLroot, $currDir,
                               $imageDir, $useThumbSubdir, $thumbSubdir,
                               $maxFolderColumns, $hidden, $presort_dir,
                               $viewFolderCount, $markerType,
                               $markerLabel, $ficons, $randomFolderThumbs,
                               $folderNameLength);
    // list of available images
    $imageList = buildImageList($baseURL, $baseDir, $albumDir, $currDir,
                                $albumURLroot, $maxThumbColumns,
                                $maxThumbRows, $markerType, $markerLabel,
                                $folderList, $suppressImageInfo,
                                $useThumbSubdir, $thumbSubdir, $noThumbs,
                                $thumbExt, $suppressAltTags, $sortType,
                                $hidden, $presort_img, $desc, $imagePopup,
                                $imagePopType, $imagePopLocationBar,
                                $imagePopMenuBar, $imagePopToolBar,
                                $commentFilePerImage, $startFrom);

    // Only frame the lists in table code when appropriate

    // no folders or images - print the "no contents" line
    if ($folderList == 'NULL' && $imageList == 'NULL') {
        $folderList = $mig_config['lang']['no_contents'];
        $folderList = folderFrame($folderList, $randomFolderThumbs,
                                  $maxFolderColumns);
        $imageList = '';

    // images, no folders.  Frame the imagelist in a table
    } elseif ($folderList == 'NULL' && $imageList != 'NULL') {
        $folderList = '';
        $imageList = imageFrame($imageList);

    // folders but no images.  Frame the folderlist in a table
    } elseif ($imageList == 'NULL' && $folderList != 'NULL') {
        $imageList = '';
        $folderList = folderFrame($folderList, $randomFolderThumbs,
                                  $maxFolderColumns);

    // We have folders and we have images, so frame both in tables.
    } else {
        $folderList = folderFrame($folderList, $randomFolderThumbs,
                                  $maxFolderColumns);
        $imageList = imageFrame($imageList);
    }

    // We have a bulletin
    if ($bulletin != '') {
        $bulletin = descriptionFrame($bulletin);
    }

    // build the "back" link
    $backLink = buildBackLink($baseURL, $currDir, 'back', $homeLink,
                              $homeLabel, $noThumbs, '');

    // build the "you are here" line
    $youAreHere = buildYouAreHere($baseURL, $currDir, '');

    // newcurrdir is currdir without the leading './'
    $newCurrDir = getNewCurrDir($currDir);

    // parse the template file and print to stdout
    printTemplate($baseURL, $templateDir, $templateFile, $version, $maintAddr,
                  $folderList, $imageList, $backLink, '', '', '',
                  $newCurrDir, $pageTitle, '', '', '', $bulletin,
                  $youAreHere, $distURL, $albumDir, $pathConvertFlag,
                  $pathConvertRegex, $pathConvertTarget);


// If pageType is "image", show an image

} elseif ($pageType == 'image') {

    // Trick the back link into going to the right place by adding
    // a bogus directory at the end
    $backLink = buildBackLink($baseURL, "$currDir/blah", 'up', '', '',
                              $noThumbs, $startFrom);

    // Get the "next image" and "previous image" links, and the current
    // position (#x of y)
    $Links = array ();
    $Links = buildNextPrevLinks($baseURL, $albumDir, $currDir, $image,
                                $markerType, $markerLabel,
                                $hidden, $presort_img, $sortType, $startFrom);
    list($nextLink, $prevLink, $currPos) = $Links;

    // Get image description
    if ($commentFilePerImage) {
        $description  = getImageDescFromFile($image, $albumDir, $currDir);
        // If getImageDescFromFile() returned false, get the normal
        // comment if there is one.
        if (! $description) {
            $description  = getImageDescription($image, $desc);
        }
    } else {
        $description  = getImageDescription($image, $desc);
    }

    $exifDescription = getExifDescription($albumDir, $currDir, $image,
                                          $exifFormatString);

    // If there's a description but no exifDescription, just make the
    // exifDescription the description
    if ($exifDescription && ! $description) {
        $description = $exifDescription;
        unset($exifDescription);
    }

    // If both descriptions are non-NULL, separate them with an <HR>
    if ($description && $exifDescription) {
        $description .= '<hr>';
        $description .= $exifDescription;
    }

    // If there's a description at all, frame it in a table.
    if ($description != '') {
        $description = descriptionFrame($description);
    }

    // Build the "you are here" line
    $youAreHere = buildYouAreHere($baseURL, $currDir, $image);

    // Which template to use.
    if ($phpNukeCompatible || $phpWebThingsCompatible) {
        $templateFile = $templateDir . '/mig_image.php';
    } else {
        $templateFile = $templateDir . '/image.html';
    }

    // newcurrdir is currdir without the leading './'
    $newCurrDir = getNewCurrDir($currDir);

    // Send it all to the template printer to dump to stdout
    printTemplate($baseURL, $templateDir, $templateFile, $version, $maintAddr,
                  '', '', $backLink, $albumURLroot, $image, $currDir,
                  $newCurrDir, $pageTitle, $prevLink, $nextLink, $currPos,
                  $description, $youAreHere, $distURL, $albumDir,
                  $pathConvertFlag, $pathConvertRegex, $pathConvertTarget);
}

// If in PHPNuke mode, finish up the tables and such needed for PHPNuke
if ($phpNukeCompatible) {
    print '</table></center></td></tr></table>';
    include('footer.php');
} elseif ($phpWebThingsCompatible) {
    theme_draw_center_box_close();
    draw_news(true);
    draw_footer();
}

?>
