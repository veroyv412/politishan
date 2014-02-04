<?php
class Twitter_API
{
    /** Returns tweets that match a specified query.
     * 
     * @param string $format JSON | ATOM
     * @param string $text Text to search
     * @param array $params array('show_user' => true )
     * @return json JSON
     */
    public static function search( $format, $text, $params = array() )
    {
        $twitterSearch  = new Zend_Service_Twitter_Search();
        $search = $twitterSearch->search($text, $params);

        return $search;
    }

    /** Displays Users Information
     *
     * @param string $format JSON | ATOM
     * @param string $action show | lookup | search | suggestions
     * @tutorial http://apiwiki.twitter.com/w/page/22554679/Twitter-API-Documentation
     * @param array $params array('show_user' => true )
     * @return json JSON
     */
    public static function user( $format, $action, $params = array() )
    {
        $uri = "http://api.twitter.com/1/users/";
        $uri.= $action . "." . $format;
        $uri.= "?" . http_build_query($params);

        $client = new Zend_Http_Client();
        $client->setUri( $uri );
        $result = Zend_Json::decode($client->request("GET")->getBody());

        return $result;
    }
}