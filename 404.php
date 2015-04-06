<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<title>NOPE - That's a 404</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>
<style type="text/css">
body,td,th {
    font-family: "Arial Black", Gadget, sans-serif;
    font-size: 16px;
    color: #FFF;
}
body {
    background-color: #000;
}
input, button, select, option, textarea {
	font-family: "Arial Black", Gadget, sans-serif;
    font-size: 100%;
}
a {
    color: #FFF;
}

</style>
<body>
<center><h1>404</h1></center>
<?php
########################################################################################################################
#                                      FIRST TIME SETUP INSTRUCTIONS
# 1. Create folder ./swf/ and populate it with flash files and images for mobile users (Recommended)
# 2. Create text file with messages for users. One per line. Line will be selected and displayed at random for the user
# 3. create file ./swf/404cache (does not need to contain any data)
# 4. Run 404.php?&refresh=true to build first time cache
# 5. Setup .htaccess to point to this file for 404 errors (Optional)
########################################################################################################################
require_once 'Mobile_Detect.php'; #super awesome class to detect mobile - source http://mobiledetect.net/
$detect = new Mobile_Detect;

function beWitty()
{
# Lets come up with something witty (arguably) for the 404 subtext.
$replyfile = fopen("./swf/reply.txt", "r"); #grab file with errors
while (!feof($replyfile)) {
    $replies[] = fgets($replyfile); #loop through and drop each error in to the array. drops the last character off since it counts the carraige return
}
fclose($replyfile); #BE KIND. CLOSE HANDLES! :D
$rand = array_rand($replies); #random line from array
echo $replies[$rand]; #spit it out

	print "<br> In the meantime, a highly sophisticated AI has put this temporary page together based on your personality and interests!<br> <br>";
}

function returnswf() #do this to pump out a flash from the cache file
{
    $file = fopen("404cache", "r");
    while (!feof($file)) {
        $swf[] = fgets($file); #loops through cache file and populates each line to an array
    }
    fclose($file);
    foreach ($swf as $v) {
        $val      = explode("|", $v);
        //DEFINE
        $URL[]    = $val[0];
        $width[]  = $val[1];
        $height[] = $val[2];
    }
    if (count($swf) > 0) #lets make sure there actually are swfs in the folder
        {
        $rando = array_rand($swf); #random line from array
        if ($_GET["f"]) {
        	$flash = $_GET["f"]; #checks for ?&refresh=true in the URL and adds it to $refresh
    	}
    	if ($flash) 
    	{
    		$rando = $flash;
    	}
        echo '<center><div class="embed-wrapper"><div class="arve-embed-container"><object ' . $height[$rando] . $width[$rando] . ' data="' . $URL[$rando] . '"></object></center></div></div>'; #flash embed. array indexes for url and meta should match
        #echo '<div style="font-size: 1px;"><a href="http://nervesocket.com/' . $URL[$rando] . '">' . $URL[$rando] . '</a></div>'; #link for download
        echo '<br><div style="font-size: 8px;"><a href=\'' . $URL[$rando] . '\' download="flash.swf"><input type="button" value="Download Flash" /></a> <a href="http://'.$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF].'?&f='.$rando.'">Direct Link</a></div>'; #download button for download and direct link
    } else {
        echo "<center><img src=http://i.imgur.com/GNm9aP4.png border=0></center>";
        echo "Seriously though, first a 404 and now he has no funny flash videos to cycle through...";
    }
    
}

function returnimg($dirname = "./swf/")
{
    $pattern = "\.(jpg|jpeg|png|gif|bmp)$"; #lets just search the swf folder for stuff that works on mobile like images
    $files   = array();
    if ($handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) { #same idea as flash files, lets stick it in an array and find a random one
            if (eregi($pattern, $file)) {
                $myimages[] = $dirname . $file;
            }
        }
        
        closedir($handle);
    }
    if (count($myimages) > 0) #lets make sure there actually are images in the folder
        {
        $rando = array_rand($myimages); #random line from array
        echo "<center><img src=" . $myimages[$rando] . " border=0></center>";
    } else {
        echo "<center><img src=http://i.imgur.com/Wy9tKJH.jpg border=0></center>";
        echo "Seriously though, first a 404 and now he has no funny images to cycle through...";
    }
}

function buildcache($dirname = "./swf/") #choose directory and add ./ to start if pulling from local
{
    $pattern  = "\.(swf)$"; #setup filter for file types. not sure what would happen if an image was selected but maybe future project for images
    $files    = array();
    $curimage = 0;
    
    $cache = fopen('404cache', 'a'); #may not totally work if the file does not exist
    ftruncate($cache, 0); #kill the cache
    if ($handle = opendir($dirname)) {
    	$first = true;
        while (false !== ($file = readdir($handle))) { #loop through all files in the folder
            if (eregi($pattern, $file)) {
            	if ($first == false) fwrite($cache, "\r\n"); #write a carrige return to file unless this is the first loop
                #$myimages[] = $dirname . $file;
                $info  = getimagesize($dirname . $file); #grab the meta from the flash
                #FOR SMALL POS FLASH FILES. MAKE THEM BIG!(or smaller if too big as a nice biproduct)
                $multi = round(800 / $info[1], 1, PHP_ROUND_HALF_DOWN);
                if ($info[0] * $multi > 1800) { #as if i know how the fuck aspect ratios work
                    $multi = round(1800 / $info[0], 1, PHP_ROUND_HALF_DOWN); #this is the new multiplier used when files are really wide
                }
                #crank dat souja boi
                $info[1] = $info[1] * $multi;
                $info[0] = $info[0] * $multi;
                fwrite($cache, $dirname . $file . "|width=" . $info[0] . "|height=" . $info[1]); #write to array.
                $first = false;
            }
        }
        closedir($handle); #BE KIND. CLOSE HANDLES
    }
}

if ($_GET["f"]) {
	$flash = $_GET["f"]; #checks for ?&refresh=true in the URL and adds it to $refresh
}
if ($flash) 
{
	$rando = $flash;
	echo 'Looks like you were directly linked to this flash on my 404 error page. To see random cycled ones on the actual error page click <a href="http://'.$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF].'">HERE</a><br><br>';
}
else
{
beWitty();
}

if (!$detect->isMobile()) { #This says the browser is NOT a mobile device - phone, tablet etc
    #first lets see if i wanted to manually refresh the page
    if ($_GET["refresh"]) {
        $refresh = $_GET["refresh"]; #checks for ?&refresh=true in the URL and adds it to $refresh
    }
    if (filemtime("404cache") < (time() - 2419200) || ($refresh == "yes")) #checks if the file date is > 4 weeks old or if i'm manually refreshing
        {
        buildcache(); #rebuilt cache function
        returnswf(); #spit out flash
        print "BTW I just rebuilt the cache of all this crap... It's for speed and junk :)"; #this is one part debugging one part friendly output
    } else
        returnswf(); #oh, didn't want to refresh. kay here is flash built from cache file :)
} else #mobile code below
    {
    returnimg();
    echo "<script>navigator.vibrate(100);</script>";#make it vibrate just cuz
    print "Haha, made ya vibrate!<br> Trust me, the 404 page gets even more exciting from a computer browser :)";
}
?>
</body>
</html>
