<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="nl" xml:lang="nl">
	
	<head>
	<title>Creating icons for Mac OS X</title>
	<!-- about this page -->
	<meta http-equiv="content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="description" content="Everything you need to know to start designing high resolution icons for Mac OS X." />
	<meta name="keywords" content="thumbnail,icon,icons,mac, os x,osx,transparencies,masking,layers,photoshop,iconographer,pic2icon" />
	<meta name="author" content="Frederik Vandaele" />
	<!-- stylesheets -->
	<link href="../index.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
	.block img {border: 1px solid #000000; margin: 15px 0px 20px 0px;}
	</style>
	<!-- techtweak -->
	<meta http-equiv="imagetoolbar" content="yes" />
	<!-- scripts -->
	<script src="../modified.js" type="text/javascript"></script>		</head>
	
<body>
<h1>Creating icons for OS X</h1>
<p>This is a basic tutorial for creating those nice lookin' high-res icons for 
  Mac OSX. You'll need a copy of <a
href="http://www.adobe.com/products/photoshop/main.html">Photoshop</a>, <a
href="http://www.mscape.com/products/iconographer.html">Iconographer</a> and <a
href="http://www.sugarcubesoftware.com/sw/index.php?pic2icon">Pic2Icon</a>. You 
  can download an unlimited trial of Iconographer. Pic2Icons is simply... free. 
  Isn't that nice!</p>
<p>&nbsp;</p>
<p> </p>
<p><img src="../arrow.gif" class="../arrow" width="11" height="15" alt="" /> Download the source
<a href="artwork_source.zip">Photoshop files and the icon</a> (162Kb)</p>
<p><img src="../arrow.gif" class="../arrow" width="11" height="15" alt="" /> Download 
  the high-res versions of the <a href="artwork_screenshots.zip">screenshots</a> 
  (1,4MB) used in this tutorial</p>
<p>&nbsp;</p>
<p><img src="splash.jpg" alt="Creating icons for OS X" width="471" height="159" /></p>
<p>&nbsp;</p>
<h2>1. Laying out the artwork</h2>
<div class="block">
<ol>
<li>Start with a new transparent Photoshop file of at least 256pixels x 256pixels. Later
on we'll size it down, but larger files tend to give you more freedom and control over
the artwork.<br />
 <img src="1-1.gif" alt="Creating a new file" width="450" height="338" /></li>
<li>Paste or draw your artwork for the icon. You can use as many layers as you want, use
shadows or any other layer effect, apply transparencies to layers, blur edges, ... Just
keep the following in mind: 
<ul>
<li>make sure there is enough contrast in your artwork for your icon to retain good
visibility on either a very dark or a very bright background</li>
<li>make sure shadows or blurred parts of the artwork lay entirely within the borders of
the image. If not, you will get very ugly results in the final icon</li>
</ul>
</li>
<li>Save your work as a Photoshop file<br />
 <img src="1-2.gif" alt="Designing the icon" width="450" height="338" /></li>
</ol>
</div>
<h2>2. Masking</h2>
<div class="block">
<ol>
<li>Open a copy of your Photoshop file</li>
<li>Merge all layers to one layer by choosing <b>Layer -> Merge Visible</b>. Do not
flatten your artwork, as we still want some transparency in the icon (unless off course
it is your intention not to have any).<br />
 <img src="2-2.gif" alt="Merge Visible" width="450" height="338" /></li>
<li>Choose <b>Image -&gt; Resize</b> to resize your artwork to 128 x 128 pixels (the
default dimensions of an OSX icon)<br />
 <img src="2-3.gif" alt="Resizeing" width="450" height="338" /></li>
<li><b>Cmd-click</b> the only layer left in the Photoshop file. Now the merged artwork is
selected, including any shadows or transparent parts in the image. You can use the
Quickmask (switch at the bottom of the Toolbar) if you wish, to take a closer look at the
selection.<br />
 <img src="2-4.gif" alt="Select the artwork" width="450" height="338" /></li>
<li>Choose <b>Select -&gt; Save Selection</b>. Press <b>Cmd-d</b> to deselect
everything.<br />
 <img src="2-5.gif" alt="Save the selection" width="450" height="338" /></li>
<li>Click the Channels pallet to examine the alpha channel you just created. Take a close
look to the edges of the artwork: see the shadows? Excellent!<br />
 <img src="2-6.gif" alt="Examining the alpha channel" width="450" height="338" /></li>
</ol>
</div>
<h2>3. Flattening</h2>
<div class="block">
  <p>Here things get a bit tricky. Our Photoshop file now contains two elements: 
    a layer with our pretty colored artwork, and a grayscale alpha channel. This 
    alpha channel is essential for the correct display of the icon: it tells Mac 
    OSX what parts of the artwork layer it should display and what parts not. 
    If fact, all OSX icons are just plain flat square images. However, OSX shows 
    only parts of them, giving them the illusion of being nicely cut floating 
    objects.</p>
  <p>&nbsp;</p>
<p>We are about to flatten our entire image, but before that we must first choose a color
for the background. This background will not be visible in the final icon, but it will
determine what color the parts of the icon get which are not 100% black or 100% white in
your alpha channel. Or simply put: if a part of your alpha channel is 50% gray, the final
icon will display a 50% transparent part of your flattened artwork. In most cases, the
background color will be black, as shadows tend to be.</p>
<ol>
<li>Create a new layer (<b>Layer -&gt; New</b>) and drag it below your artwork
layer.</li>
<li>Make sure nothing is selected by pressing <b>Cmd-d</b></li>
<li>Choose <b>Edit -&gt; Fill</b> to give our new empty layer the correct background
color<br />
 <img src="3-3.gif" alt="Fill the backgroun layer" width="450" height="338" /></li>
<li>Choose <b>Layer -&gt; Flatten</b> image<br />
 <img src="3-4.gif" alt="Flatten your artwork with the background layer" width="450" height="338" /></li>
</ol>
</div>
<h2>4. Creating the icon file</h2>
<div class="block">
<ol>
<li>Open Iconographer. Choose <b>File -&gt; New</b> icon</li>
<li>Choose <b>Window -&gt; Show members</b></li>
<li>Back in Photoshop, copy your flattened layer (called 'Background' now) by typing
<b>Cmd-a</b> to select and <b>Cmd-c</b> to copy everything.<br />
 <img src="4-3.gif" alt="Select the flattened icon" width="450" height="338" /></li>
<li>In Iconographer, click the empty 'Thumbnail 32-bit icon' square in the Members pane
and paste (<b>Cmd-v</b>) your artwork<br />
 <img src="4-4.gif" alt="Pasting in Iconographer" width="450" height="338" /></li>
<li>Back in Photoshop again, go to your Channels pallet, choose your alpha channel by
clicking on it and invert it by typing <b>Cmd-i</b> (Iconographer reads an alpha channel
differently).<br />
 <img src="4-5.gif" alt="Inverting the alpha channel" width="450" height="338" /></li>
<li>Copy the inverted alpha channel, just as you did for the artwork, but this time paste
it into the 'Thumbnail 8-bit mask' square.</li>
<li>Save your Iconographer file as an icns file. Choose 'Generate Mask' on the warning
dialog. Don't worry about this. We'll deal with this later on.<br />
 <img src="4-7.gif" alt="Saving your icns file" width="450" height="338" /></li>
<li>Open Pic2Icon and drag the icns file on the 'Progress' tab.<br />
 <img src="4-8.gif" alt="Dragging the icns file on Pic2icon" width="450" height="338" /></li>
<li>The icns file now uses your icon as its own icon! Drag it onto your Dock (near the
Trashcan) to see it come to life!<br />
 <img src="4-9.gif" alt="Let it come to life!" width="450" height="338" /></li>
</ol>
</div>
<h2>5. Completing the icns file</h2>
<div class="block">
  <p>The fun part is over. Your icns file - an 'ic(o)ns' file - now only contains 
    the artwork (and alpha channel) of the 128 pixel variant of the icon. For 
    an icon to be complete, you should also create a 48, 32 and 16 pixel variants 
    of the icon. Mac OSX Finder and OS9 use these low-res icons for display in 
    listviews and as a 'cornerstone' during scaling in the Dock. </p>
  <p>&nbsp;</p>
  <p>You simply need to go back to part 2 of this tutorial (Masking), but instead 
    of resizing your file to 128pixel x 128pixels, resize it to one of the lower 
    res variants: Huge (48x48), Large (32x32) and Small (16x16). Joy! Don't waste 
    your time designing for the 1-bit icons and masks in the Members pane of Iconographer, 
    unless you are utterly bored and have very few friends. You can leave them 
    blank just as they are. Those 1-bit variants cannot contain our fine blending 
    of shadows and transparencies: they either show a pixel of your artwork or 
    don't, but not 50% of a pixel and 50% of the background, as our 8-bit masks 
    do. The 1-bit variants are used by dated operating systems, so there is no 
    need for you to design them if you're only designing for an OSX program.</p>
  <p>&nbsp;</p>
  <p> </p>
  <p>Well, that's all folks!</p>
  <p>&nbsp;</p>
</div>
<hr />
<div id="bottom">
<p><b>Author</b>: <a href="http://www.scene24.net/projects/">Frederik Vandaele</a> for <a
href="http://www.artwork-systems.com">Artwork Systems</a><br />
 <b>Online since:</b> Friday, June, 20 2003 - <b>File changed:</b> 
 <script type="text/javascript" >document.write(customDateString())</script></p>
</div>
</body>
</html>
<?php include("/home/scene24/public_html/logger.php")?>
