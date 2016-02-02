<?php
namespace FA;

use Entity\User;

class Parser
{
    /**
     * @var \FA\Config
     */
    protected $config;

    /**
     * @var \FA\Url
     */
    protected $url;

    public function __construct(\FA\Config $config, \FA\Url $url)
    {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * Run a message through all of the processing steps.
     * Commonly used for comments or other open-response text areas.
     *
     * @param $string
     * @return mixed|string
     */
    public function message($string)
    {
        $string = $this->escape($string);
        $string = $this->smilies($string);
        $string = $this->userIcons($string);
        $string = $this->bbcode($string);
        $string = $this->links($string);

        return $string;
    }

    /**
     * Escape a string for printing, and apply NL2BR.
     *
     * @param string $string
     * @param boolean $nl2br Whether to apply the NL2BR rule.
     * @return mixed|string
     */
    public function escape($string, $nl2br = true)
    {
        $string = str_replace('fromCharCode', '', $string);
        // $string = stripslashes($string);
        $string = htmlspecialchars($string);
        $string = str_replace("'", "&#39;", $string);

        if ($nl2br)
            $string = nl2br($string);

        return $string;
    }

    /**
     * Convert smilies into images for display.
     *
     * @param $string
     * @return mixed
     */
    public function smilies($string)
    {
        $smilies = $this->config->fa->smilies->toArray();

        foreach($smilies as $key => $val)
            $string = str_ireplace($key, '<img alt="" src="'.$this->url->getStatic('img/smilies/'.$val).'">', $string);

        return $string;
    }


    /**
     * Adds support for generating user icons for the following shortcuts:
     * :iconusername:, :linkusername:, @username and @@username
     * (Note: this function assumes the text has been run through filter().)
     *
     * @param $string
     * @return mixed
     */
    public function userIcons($string)
    {
        // :iconusername:
        $string = preg_replace_callback('|\:icon([-\w\d_\[\]\^`~.]+?)\:|i', function($matches) {
            $username = $matches[1];
            $user_url = $this->url->get('user/'.User::getLowerCase($username));
            $user_avatar = User::getUserAvatar($username, time());

            return '<a href="'.$user_url.'" class="iconusername"><img src="'.$user_avatar.'" align="middle" title="'.$username.'" alt="'.$username.'">&nbsp;&nbsp;'.$username.'</a>';
        }, $string);

        // :usernameicon:
        $string = preg_replace_callback('|\:([-\w\d_\[\]\^`~.]+?)icon\:|i', function($matches) {
            $username = $matches[1];
            $user_url = $this->url->get('user/'.User::getLowerCase($username));
            $user_avatar = User::getUserAvatar($username, time());

            return '<a href="'.$user_url.'" class="iconusername"><img src="'.$user_avatar.'" align="middle" title="'.$username.'" alt="'.$username.'"></a>';
        }, $string);

        // @@username
        $string = preg_replace_callback('!(^|\s)@@([-\w\d_\[\]\^`~.]{2,})(?=$|\s|[<:])!mi', function($matches) {
            $username = $matches[2];
            $user_url = $this->url->get('user/'.User::getLowerCase($username));
            $user_avatar = User::getUserAvatar($username, time());

            return '<a href="'.$user_url.'" class="iconusername"><img src="'.$user_avatar.'" align="middle" title="'.$username.'" alt="'.$username.'" />&nbsp;&nbsp;'.$username.'</a>';
        }, $string);

        // @username
        $string = preg_replace_callback('!(^|\s)@([-\w\d_\[\]\^`~.]{2,})(?=$|\s|[<:])!mi', function($matches) {
            $username = $matches[2];
            $user_url = $this->url->get('user/'.User::getLowerCase($username));

            return '<a href="'.$user_url.'" class="linkusername">'.$username.'</a>';
        }, $string);

        // :linkusername:
        $string = preg_replace_callback('|\:link([-\w\d_\[\]\^`~.]+?)\:|i', function($matches) {
            $username = $matches[1];
            $user_url = $this->url->get('user/'.User::getLowerCase($username));

            return '<a href="'.$user_url.'" class="linkusername">'.$username.'</a>';
        }, $string);

        return $string;
    }

    /**
     * Simple and fast BBCode parser.
     *
     * @param $string
     * @return mixed
     */
    public function bbcode($string)
    {
        $patterns = array(
            '/\[b\](.*?)\[\/b\]/is',            //  bold
            '/\[i\](.*?)\[\/i\]/is',            //  italic
            '/\[u\](.*?)\[\/u\]/is',            //  underlined
            '/\[s\](.*?)\[\/s\]/is',            //  strikeout
            '/\[sub\](.*?)\[\/sub\]/is',        //  subscript
            '/\[sup\](.*?)\[\/sup\]/is',        //  supscript
            '/(<br\s\/>)?[-]{5,}(<br\s\/>)?/s',
            '/\(c\)/is',
            '/\(r\)/is',
            '/\(tm\)/is',
            '/\[quote=(.+?)\]\s*(.+?)\s*\[\/quote\]/ism',
            '/\[quote\]\s*(.+?)\s*\[\/quote\]/ism',
            '/\[color=([\w\d#]+?)\]\s*(.+?)\s*\[\/color\]/is',
            '/\[left\](.+?)\[\/left\]/is',
            '/\[center\](.+?)\[\/center\]/is',
            '/\[right\](.+?)\[\/right\]/is',
            '/\[pre\](.*?)\[\/pre\]/is'  //  code, e.g <pre>
        );

        $replaces = array(
            '<strong class="bbcode bbcode_b">$1</strong>',
            '<i class="bbcode bbcode_i">$1</i>',
            '<u class="bbcode bbcode_u">$1</u>',
            '<s class="bbcode bbcode_s">$1</s>',
            '<sub class="bbcode bbcode_sub">$1</sub>',
            '<sup class="bbcode bbcode_sup">$1</sup>',
            '<hr class="bbcode bbcode_hr">',
            '&copy;',
            '&reg;',
            '&trade;',
            '<div class="bbcode bbcode_quote"><span>$1 wrote:</span>$2</div>',
            '<div class="bbcode bbcode_quote">$1</div>',
            '<span class="bbcode" style="color: $1;">$2</span>',
            '<div class="bbcode bbcode_left">$1</div>',
            '<div class="bbcode bbcode_center">$1</div>',
            '<div class="bbcode bbcode_right">$1</div>',
            '<pre class="bbcode bbcode_pre">$1</pre>'
        );

        return preg_replace($patterns, $replaces, $string);
    }

    /**
     * Parse the supplied text for any kind of hyperlinks and "linkify" them.
     * Assumes the text provided has already been ran through filter().
     *
     * @param $string
     * @param bool|false $is_admin
     * @param bool|false $parse_video
     * @param bool|false $embed_video
     * @return mixed
     */
    public function links($string, $is_admin=FALSE, $parse_video=FALSE, $embed_video=FALSE)
    {
        $patterns = array();
        $replaces = array();

        // Strip Javascript links.
        $patterns[] = '!\[url=\s*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:.*?\](.*?)\[/url\]!i';
        $replaces[] = '$1';

        $patterns[] = '!\[url\]\s*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:(.*?)\[/url\]!i';
        $replaces[] = '$1';

        if($is_admin) {
            $patterns[] = '!\[img\](.+)\[/img\]!i';
            $replaces[] = '<img class="bbcode bbcode_img" src="$1">';
        }

        $result = preg_replace($patterns, $replaces, $string);

        // named [url] tag
        $result = preg_replace_callback('!\[url=([^\]]+)\](.+?)\[/url\]!i', function($matches) {
            $url  = trim($matches[1]);
            $text = trim($matches[2]);

            if(!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('!^/[/_a-zA-Z0-9]+/?!', $url)) {
                $url = '#';
            }
            return '<a class="auto_link" href="'.$url.'">'.$text.'</a>';
        }, $result);

        // anonymous [url] tag
        $result = preg_replace_callback('!\[url\](.*?)\[/url\]!i', function($matches) {
            $url = $matches[1];

            if(!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('!^/[/_a-zA-Z0-9]+/?!', $url)) {
                $url = '#';
            }
            return shorten_url($url, $url);
        }, $result);


        if($parse_video) {
            // youtube video
            $patterns = array(
                '!\[yt\](https?://www\.youtube\.com/watch\?v=([A-Za-z0-9._%-]+)([^\[]*))\[/yt\]!i',
                '!\[yt\](https?://youtu\.be/([A-Za-z0-9._%-]+)([^\[]*))\[/yt\]!i'
            );

            $result = preg_replace_callback($patterns, function($matches) use($embed_video) {
                $vid = $matches[2];

                if($embed_video) {
                    //
                    $url = 'https://www.youtube.com/v/'.$vid;
                    return ''.
                    '<object class="auto_link youtube" width="560" height="340">'.
                    '<param name="movie" value="'.$url.'&color1=0x6A7283&color2=0x6A7283"></param>'.
                    '<param name="allowFullScreen" value="true"></param>'.
                    '<param name="allowscriptaccess" value="always"></param>'.

                    '<embed src="'.$url.'&color1=0x6A7283&color2=0x6A7283" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed>'.
                    '</object>';
                } else {
                    return $matches[1];
                }
            }, $result);
        }

        // email addres
        $result = preg_replace_callback('!(([.-_\w\d]+)(\.[-_\w\d]+)*@([-\w\d]+)(\.[-\w\d]+)+)(\s|$)!i', function($matches) {
            $email = trim($matches[1]);
            return '<a class="auto_link email" href="mailto:'.$email.'">'.str_replace(array('@'), array('[at]') , $email).'</a>'.$matches[6];
        }, $result);

        // proper links
        $result = preg_replace_callback('!([">]?)((https?|ftp)://[^\s"<]+)(["<]?)!i', function($matches) {
            $url = trim($matches[2]);
            return strlen($matches[1]) > 0 ? $matches[0] : $this->_shortenUrl($url, $url).$matches[4];
        }, $result);

        // plain links
        $result = preg_replace_callback('!(^|[\s\]>\(])(www.[^<> \n\r\)]+)!i', function($matches) {
            $url = trim($matches[2]);
            return strlen($url) > 0 ? $matches[1].$this->_shortenUrl('http://'.$url, $url) : $matches[0];
        }, $result);

        return $result;
    }

    /**
     * Helper function to generate truncated versions of long URLs.
     *
     * @param $url
     * @param $text
     * @param int $max_text_size
     * @param float $split_bias
     * @return string
     */
    protected function _shortenUrl($url, $text, $max_text_size=50, $split_bias=0.7)
    {
        $length   = strlen($text);

        $url_href = $url;
        $url_text = $text;
        $additional_class = '';

        if($length > $max_text_size) {
            $gap_start = round($max_text_size * $split_bias);
            $gap_end   = $length - ($max_text_size - $gap_start) + 1;
            $url_text  = str_replace('.', '&#46;', substr($text, 0, $gap_start)).'.....'.substr($url, $gap_end);
            $additional_class = ' auto_link_shortened';
        }

        $new_url = '<a href="'.$url_href.'" title="'.str_replace('.', '&#46;', $url_href).'" class="auto_link'.$additional_class.'">'.$url_text.'</a>';

        return $new_url;
    }

}