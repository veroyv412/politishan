<?php
class Klout_API
{
    private static $key;


    public static function loadKey()
    {
        $kloutConfig = Zend_Registry::get('kloutConfig');
        self::$key = $kloutConfig['key'];
    }

    /** klout: This method allows you to retrieve a Klout score in XML or JSON.
     * |
     * http://api.klout.com/1/klout.xml?key=[your_api_key]&users=[usernames]
     *
     * @param string $format [xml/json]
     * @param string $action klout
     * @param array $params array('users' => 'user_name')
     * @return json JSON
     * @tutorial http://developer.klout.com/docs/read/api/API
     */
    public static function getScoreData( $format, $action, $params = array() )
    {
        self::loadKey();
        $uri = "http://api.klout.com/1/";
        $uri.= $action . "." . $format;
        $uri.= "?key=".self::$key ."&" . http_build_query($params);

        $client = new Zend_Http_Client();
        $client->setUri( $uri );
        $result = Zend_Json::decode($client->request("GET")->getBody());

        return $result;
    }

    /** show: This method allows you to retrieve user objects (see Response Formats) in XML or JSON.
     * |
     * topics: his method allows you to retrieve the top 3 topic objects (see Response Formats) for a given user in XML or JSON.
     * |
     * http://api.klout.com/1/users/show.[xml_or_json]?key=[your_api_key]&amp;users=[usernames]
     *
     * @param string $format [xml/json]
     * @param string $action [show/topics]
     * @param array $params array('users' => 'user_name')
     * @return json JSON
     * @tutorial http://developer.klout.com/docs/read/api/API
     */
    public static function getUserData( $format, $action, $params = array() )
    {
        self::loadKey();
        $uri = "http://api.klout.com/1/users/";
        $uri.= $action . "." . $format;
        $uri.= "?key=".self::$key ."&" . http_build_query($params);

        $client = new Zend_Http_Client();
        $client->setUri( $uri );
        $result = Zend_Json::decode($client->request("GET")->getBody());

        return $result;
    }

    /** influenced_by:This method allows you to retrieve up to 5 user score pairs (see Response Formats)
     * for users that are influenced by the given influencer in XML or JSON.
     * |
     * influenced_by: This method allows you to retrieve up to 5 user score pairs (see Response Formats) for users that are influencers of the given user in XML or JSON.
     * |
     * http://api.klout.com/1/soi/influenced_by.[xml_or_json]?key=[your_api_key]&users=[users]
     *
     * @param string $format [xml/json]
     * @param string $action [influenced_by/influencer_of]
     * @param array $params array('users' => 'user_name')
     * @return json JSON
     * @tutorial http://developer.klout.com/docs/read/api/API
     */
    public static function getRelationshipData( $format, $action, $params = array() )
    {
        self::loadKey();
        $uri = "http://api.klout.com/1/soi/";
        $uri.= $action . "." . $format;
        $uri.= "?key=".self::$key ."&" . http_build_query($params);

        $client = new Zend_Http_Client();
        $client->setUri( $uri );
        $result = Zend_Json::decode($client->request("GET")->getBody());

        return $result;
    }
}
?>
