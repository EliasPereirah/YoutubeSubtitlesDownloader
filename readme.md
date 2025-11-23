# YouTube Subtitles Downloader
PHP implementation for downloading YouTube subtitles using yt-dlp
# How to Use?
Install yt-dlp https://github.com/yt-dlp/yt-dlp

Then run:
```shel
composer update
```

A practical example of how to obtain the subtitles of a YouTube video:

```php
<?php
require_once __DIR__ . "/bootstrap.php";
$CaptionHandle = new \App\CaptionsHandle();
$video_url = "https://www.youtube.com/watch?v=8ch_H8pt9M8"; // it can be just the ID too

$cookie_file = __DIR__."/my_yt_cookie_file.txt";
$CaptionHandle->loginWithCookieFile($cookie_file);

try {
    $caption_url = $CaptionHandle->getSubtitleURL($video_url);
    if ($caption_url) {
        $captions_content = $CaptionHandle->httpRequest($caption_url);

        // Remove XML/HTML from subtitles - replacing "<" with " <" is to prevent some words from sticking to another
        $captions_content = str_replace("<", " <", $captions_content);
        $captions_content = strip_tags($captions_content);

        echo "Caption content:<br>";
        echo $captions_content;
    } else {
        echo "Couldn't get the URL.";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

```

# When login is required
If you are using it outside your home network, for example in the cloud, you may need to log in to get the YouTube 
subtitles, to do this you can do the following. 

- Get YouTube login cookies
- Place the cookies inside the file `my_yt_cookie_file.txt` (make sure it is not publicly accessible)
- Uncomment the line:
```php
//$cookie_file = __DIR__."/my_yt_cookie_file.txt";
//$CaptionHandle->loginWithCookieFile($cookie_file);
 ```
This is because YouTube may require login.

# Chrome/Edge Extension
How do I get cookies? One way is to use an extension that allows you to download cookies. 
Choose a secure one because your cookie data could allow anyone to log into your account. 

A popular one is this one: https://chromewebstore.google.com/detail/cclelndahbckbenkjhflpdbgdldlbecc

I recommend that you always check the code before using it.

Avoid using your main Google account.

**Download cookies in Netscape format**

Make sure you are only downloading YouTube cookies and not all of your browser cookies.

# That's all
I hope this code is useful and that you make excellent use of it.
