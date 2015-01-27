<?php

namespace VirtualIdentity\AggregatorBundle\Twig\Extension;

use VirtualIdentity\AggregatorBundle\Entity\UnifiedSocialEntity;
use VirtualIdentity\AggregatorBundle\Services\AggregatorConverterService;

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;
use VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface;
use VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface;
use VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface;


function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Twig Extension that handles conversions for SocialEntities of the Hydra-Bundle
 * Functions:
 *      - hydra_feed_text: Extracts text of UnifiedSocialEntity and replaces 
 *          links, twitter @users twitter #hashtags and facebook #hashtags with real links
 *      - hydra_feed_user: Extracts username of instagram, facebook and twitter entity
 *                      returns empty string for youtube entity since youtube items do not have username
 *      - hydra_feed_friendlytype: Returns friendly typename for entity (facebook, twitter, youtube, instagram)
 *      - hydra_feed_linktopost: Returns link to the post
 *      - hydra_feed_imageurl: URL to image; replaces facebook smaller images with larger ones
 *      - hydra_feed_userprofilelink: returns link to user profile
 */
class HydraTwigExtension extends \Twig_Extension
{
    private $converter;

    public function __construct(AggregatorConverterService $converter)
    {
        $this->converter = $converter;
    }

    public function getFilters()
    {
        return array(
            'hydra_feed_text' => new \Twig_Filter_Method($this, 'feedText'),
            'hydra_feed_user' => new \Twig_Filter_Method($this, 'feedUser'),
            'hydra_feed_friendlytype' => new \Twig_Filter_Method($this, 'friendlyType'),
            'hydra_feed_linktopost'=> new \Twig_Filter_Method($this, 'linkToPost'),
            'hydra_feed_imageurl'=> new \Twig_Filter_Method($this, 'imageUrl'),
            'hydra_feed_userprofilelink' => new \Twig_Filter_Method($this, 'userProfileLink'),
        );
    }

    public function imageUrl(UnifiedSocialEntity $item) {
        if (strcmp($item->getType(), "VirtualIdentity\\FacebookBundle\\Entity\\FacebookEntity") == 0) {
            $url = $item->getImageUrl();
            if (endsWith($url, "_s.jpg")) {
                return substr($url, 0, -6) . "_b.jpg";
            }
            if (endsWith($url, "_s.png")) {
                return substr($url, 0, -6) . "_b.png";
            }
            if (endsWith($url, "_s.gif")) {
                return substr($url, 0, -6) . "_b.gif";
            }   
        }
        return $item->getImageUrl();
    }

    public function cropText($str, $maxLength)
    {
        $parts = preg_split('/([\s\n\r]+)/', $str, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $maxLength) { 
                break; 
            }
        }
        return implode(array_slice($parts, 0, $last_part));        
    }

    public function feedText(UnifiedSocialEntity $item, $maxLength = 1000)
    {
        $str = $item->getText();
        $str = $this->cropText($str, $maxLength);
        if (strcmp($item->getType(), "VirtualIdentity\\TwitterBundle\\Entity\\TwitterEntity") == 0) {
            // Replace Twitter Usernames with links
            $str = preg_replace_callback(
                "/@(\w+)/i",
                function($matches) {
                    return "<a href=\"http://twitter.com/" . urlencode($matches[1]) . 
                    "\" target=\"_blank\">" . $matches[0] . "</a>";
                },
                $str);

            // Replace Twitter HashTags with links
            $str = preg_replace_callback(
                "/#([^\s]+)/i",
                function($matches) {
                    return "<a href=\"http://twitter.com/search/" . urlencode($matches[1]) . 
                        "\" target=\"_blank\">" . $matches[0] . "</a>";
                },
                $str);
        }
        else if (strcmp($item->getType(), "VirtualIdentity\\FacebookBundle\\Entity\\FacebookEntity") == 0) {
            // Replace Facebook HashTags with links
            $socialEntity = $this->converter->getOriginalEntity($item);
            if (empty($str)) {
                $str = $socialEntity->getMessage();
                $str = $this->cropText($str, $maxLength);
                if (empty($str)) {
                    $str = $socialEntity->getStory();
                    $str = $this->cropText($str, $maxLength);
                }
            }
            $str = preg_replace_callback(
                "/#([^\s]+)/i",
                function($matches) {
                    return "<a href=\"https://www.facebook.com/hashtag/" . urlencode($matches[1]) . 
                        "\" target=\"_blank\">" . $matches[0] . "</a>";
                },
                $str);
        }
        return $this->make_clickable($str);
    }

    private function getUserNameAndLink(UnifiedSocialEntity $item)
    {
        $socialEntity = $this->converter->getOriginalEntity($item);
        $name = "";
        $link = "";
        if ($socialEntity instanceof TwitterEntityInterface) {
            $name = $socialEntity->getUserScreenName();
            $link = "https://www.twitter.com/" . $name;
        }
        else if ($socialEntity instanceof FacebookEntityInterface) {
            $name = $socialEntity->getFromName();
            $link = "https://www.facebook.com/" . $socialEntity->getFromId();
        }
        else if ($socialEntity instanceof InstagramEntityInterface) {
            $name = $socialEntity->getUserUsername();
            $link = "http://instagram.com/" . $name;
        }
        return array('name' => $name, 'link' => $link);     
    }

    public function userProfileLink(UnifiedSocialEntity $item)
    {
        $ar = $this->getUserNameAndLink($item);
        return $ar['link'];
    }

    public function feedUser(UnifiedSocialEntity $item)
    {
        $ar = $this->getUserNameAndLink($item);
        $name = $ar['name'];
        $link = $ar['link'];
        if (empty($link)) {
            return $name;
        }
        return '<a href="' . $link . '" target="_blank">' . $name . '</a>';
    }

    public function friendlyType(UnifiedSocialEntity $item)
    {
        $socialEntity = $this->converter->getOriginalEntity($item);
        if ($socialEntity instanceof TwitterEntityInterface) {
            return "twitter";
        }
        else if ($socialEntity instanceof FacebookEntityInterface) {
            return "facebook";
        }
        else if ($socialEntity instanceof InstagramEntityInterface) {
            return "instagram";
        }
        else if ($socialEntity instanceof YoutubeEntityInterface) {
            return "youtube";
        }        
        return "";       
    }


    public function linkToPost(UnifiedSocialEntity $item)
    {
        $socialEntity = $this->converter->getOriginalEntity($item);
        if ($socialEntity instanceof TwitterEntityInterface) {
            return "https://twitter.com/" . $socialEntity->getUserScreenName() . "/status/" . 
                $socialEntity->getIdStr();
        }
        else if ($socialEntity instanceof FacebookEntityInterface) {
            $raw = $socialEntity->getRaw();
            $decoded = json_decode($raw);
            if (!empty($decoded) && isset($decoded->actions)) {
                $actions = $decoded->actions;
                if (!empty($actions) && count($actions) > 0 && isset($actions[0]->link)) {
                    return $actions[0]->link;
                }
            }
        }
        else if ($socialEntity instanceof InstagramEntityInterface) {
            $raw = $socialEntity->getRaw();
            $decoded = json_decode($raw);
            if (!empty($decoded) && isset($decoded->link)) {
                return $decoded->link;
            }
        }
        return "";  
    }

    public function getName()
    {
        return 'hydra_feed_extension';
    }

   function _make_url_clickable_cb($matches) 
    {
        $ret = '';
        $url = $matches[2];
     
        if ( empty($url) )
            return $matches[0];
        // removed trailing [.,;:] from URL
        if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
            $ret = substr($url, -1);
            $url = substr($url, 0, strlen($url)-1);
        }
        return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$url</a>" . $ret;
    }
     
    function _make_web_ftp_clickable_cb($matches) 
    {    
        $ret = '';
        $dest = $matches[2];
        $dest = 'http://' . $dest;
     
        if ( empty($dest) )
            return $matches[0];
        // removed trailing [,;:] from URL
        if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
            $ret = substr($dest, -1);
            $dest = substr($dest, 0, strlen($dest)-1);
        }
        return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target=\"_blank\">$dest</a>" . $ret;
    }
     
    function _make_email_clickable_cb($matches) 
    {
        $email = $matches[2] . '@' . $matches[3];
        return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
    }
     
    function make_clickable($ret) 
    {
        $ret = ' ' . $ret;
        // in testing, using arrays here was found to be faster
        $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 
            array($this, '_make_url_clickable_cb'), $ret);
        $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 
            array($this, '_make_web_ftp_clickable_cb'), $ret);
        $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 
            array($this, '_make_email_clickable_cb'), $ret);
     
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        $ret = trim($ret);
        return $ret;
    }  
}  