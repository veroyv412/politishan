<?php

class App_Process
{
    public static function storePolitishiansToDatabaseForFirstTime()
    {
        $config = Zend_Registry::get('twitterConfig');
        $profiles = $config->twitter->profiles->toArray();

        foreach ($profiles as $key => $twitter_account)
        {
            try
            {
                $synonyms = $config->profile->synonym->$twitter_account->toArray();

                $model_politishian = new Model_Politician();
                $model_politishian->twitter_account = $twitter_account;
                $model_politishian->synonyms = $synonyms;

                $model_politishian->save();
            }
            catch (Shanty_Mongo_Exception $e)
            {
                App_Utils::log($e->getMessage());
                return false;
            }
        }

        //self::loadTwitterData();
        //self::loadKloutData();

        return true;
    }

    public static function loadTwitterData()
    {
        $politicians = Model_Politician::fetchAll();
        $screen_names = array();

        foreach ($politicians as $key => $politician)
        {
            $screen_names[] = $politician->twitter_account;
        }

        $screen_names = implode(",", $screen_names);

        $twitter_data = Twitter_API::user('json', 'lookup', array('screen_name' => $screen_names) );

        foreach ($twitter_data as $key => $data)
        {
            
             $politician = Model_Politician::fetchOne(array( 'twitter_account' => $data['screen_name']));

             $politician->name = $data['name'];
             $politician->followers_count = $data['followers_count'];
             $politician->profile_image_url = str_replace('_normal', '_bigger', $data['profile_image_url']);
             $politician->description = $data['description'];

             $last_tweet = new Model_Tweet();
             $last_tweet->id = $data['status']['id'];
             $last_tweet->created_at = strtotime($data['status']['created_at']);
             $last_tweet->text = $data['status']['text'];

             $_last_mentioned_tweet_data = Twitter_API::search('json', '@'.$politician->twitter_account);
             $last_mentioned_tweet_data = $_last_mentioned_tweet_data['results'];
             $last_mentioned_tweet = new Model_Tweet();
             $last_mentioned_tweet->id = $last_mentioned_tweet_data[0]['id'];
             $last_mentioned_tweet->created_at = strtotime($last_mentioned_tweet_data[0]['created_at']);
             $last_mentioned_tweet->text = $last_mentioned_tweet_data[0]['text'];

             $politician->last_tweet = $last_tweet;
             $politician->last_mentioned_tweet = $last_mentioned_tweet;

             $politician->save();
        }

    }

    public static function loadKloutData()
    {
    	$config = Zend_Registry::get('kloutConfig');
        $limit_count =  $config['limit_count'];

        $politicians = Model_Politician::fetchAll();

        /**
         * Foreach politishan we need to grab his klout_score and save it into a
         * historical collection. We also need to update the last klout_score for each
         * politishan
         */
        foreach ($politicians as $key => $politician)
        {
            $kloutData = Klout_API::getUserData('json', 'show', array('users' => $politician->twitter_account ));

            //We need to insert a row to KloutData collection to be able to take the average later with all the new information
            $model_kloutData = new Model_KloutData();
            $model_kloutData->created_at = strtotime("now");
            $model_kloutData->politician_id = $politician->_id;
            $model_kloutData->klout_score = $kloutData['users'][0]['score']['kscore'];
            $model_kloutData->true_reach = $kloutData['users'][0]['score']['true_reach'];
            $model_kloutData->save();

            $politician->klout_score = $kloutData['users'][0]['score']['kscore'];
            $politician->true_reach = $kloutData['users'][0]['score']['true_reach'];
            $politician->save();
        }
    }

    public static function getTweetsByPolitishan( $twitter_account )
    {
        $politician = Model_Politician::fetchOne(array( 'twitter_account' => $twitter_account));
        $synonyms = $politician->synonyms;
        $synomyms_list = array();

        foreach( $politician->synonyms as $s )
        {
            $synomyms_list[] = $s;
        }

        //Lets get the last medicion so we can get the last tweet_id
        $medicion = Model_Medicion::all(array('politishan_id' => $politician->_id))->sort( array('created_at' => -1))->limit(1);
        
        /**
         * rpp = The number of tweets to return per page, up to a max of 100.
         */
        $params = array('rpp' => 100);
        if ( $medicion->count() > 0 )
        {
            $_list = array_values($medicion->export());
            $_medicion = $_list[0];

            $params['since_id'] = $_medicion['lastTweetOffset'];
        }
        $q = implode(" OR ", $synomyms_list);

        $data = Twitter_API::search('json', $q, $params );

        return $data;
    }

    public static function getReservedWordsList()
    {
        $config = Zend_Registry::get('reservedWordsConfig');
        $list = $config->reservedWords;

        $toReturn = array();

        foreach ($list as $key => $value)
        {
            $_modelReservedWord = new Model_ReservedWord();
            $_modelReservedWord->set_word($value->word);
            $_modelReservedWord->set_connotation((int)$value->connotation);

            $toReturn[] = $_modelReservedWord;
        }

        return $toReturn;
    }

    public static function doFullProcess()
    {

    	$config = Zend_Registry::get('config');

        $app_log = new App_Log($config['log']['file_path']);

        $app_log->log('************ Comenzando Medicion ************');

        $politishans = Model_Politician::fetchAll();
        foreach ($politishans as $politishan_key => $politishan)
        {
	
		$app_log->log('Twitter_Account: ' . $politishan->twitter_account);
            $count_positives = 0;
            $count_negatives = 0;
            $model_medicion = new Model_Medicion();

            $tweets_data = self::getTweetsByPolitishan($politishan->twitter_account);
            foreach ($tweets_data['results'] as $tweet_key => $tweet)
            {
                $reservedWords = self::getReservedWordsList();
                foreach ($reservedWords as $reservedWord_key => $reservedWord)
                {
                    $synonyms = $politishan->synonyms;
                    foreach ($synonyms as $synonym_key => $synonym)
                    {
                        $rules = new Model_RuleIterator();
                        foreach ($rules as $rule_key => $rule)
                        {
                            $rule->set_reservedWord( $reservedWord );
                            $rule->set_id( $synonym );

                            if ($rule->isValid($tweet['text']))
                            {
                                $app_log->log('Rule: ' . $rule->get_pattern());
                                $app_log->log('Reserved Word: ' . $reservedWord->get_word());
                                $app_log->log('ID: ' . $synonym);
                                $app_log->log('Tweet: ' . $tweet['text']);
								
								if ( $reservedWord->get_connotation() == 1 )
								{
									$app_log->log('Connotation: Positive');
									$count_positives++;
								}
								else
								{
									$app_log->log('Connotation: Negative');
									$count_negatives++;
								}
                                
                            }
                        }
                    }
                }
            }

            $model_medicion->created_at = time();
            $model_medicion->politishan_id = $politishan->_id;
            $model_medicion->positiveTweetsCount = $count_positives;
            $model_medicion->negativeTweetsCount = $count_negatives;
            $model_medicion->fetchedTweetsCount = sizeof($tweets_data['results']);
            $model_medicion->lastTweetOffset = $tweets_data['max_id_str'];
            $model_medicion->save();
			
			$app_log->log('PositiveTweetsCount: ' . $count_positives);
			$app_log->log('NegativeTweetsCount: ' . $count_negatives);
			$app_log->log('FetchedTweetsCount: ' . sizeof($tweets_data['results']));
			$app_log->log('LastTweetOffset: ' . $tweets_data['max_id_str']);

            self::calculatePositiveImageByPolitishan( $politishan );
            
            $app_log->log('----------------------');
        }

        $app_log->log('************ Finalizando Medicion ************');

    }

    public static function calculatePositiveImageByPolitishan( $politishan )
    {
    	$config = Zend_Registry::get('config');
        $amountBackwardMedition = $config['amountBackwardMedition'];
        $app_log = new App_Log($config['log']['file_path']);
    	
        //Need to grab the last example: 96 meditions (this will represent one day meditions)
        $meditions = Model_Medicion::all( array( 'politishan_id' => $politishan->_id ) )->sort( array('created_at' => -1))->limit($amountBackwardMedition);

        $sum_positive = 0;
        $sum_negative = 0;
        $total = 0;
        foreach ($meditions as $key => $value)
        {
            $sum_positive = $sum_positive + $value->positiveTweetsCount;
            $sum_negative = $sum_negative + $value->negativeTweetsCount;
        }

        if (( $sum_positive + $sum_negative  ) > 0 )
        {
            $total = round(( $sum_positive ) / ( $sum_positive + $sum_negative ),2);
        }

        $politishan->positive_image = $total * 100;
        $politishan->total_positive = $sum_positive;
        $politishan->total_negative = $sum_negative;

        $politishan->save();
		
        $app_log->log('Positive Image: ' . $politishan->positive_image . "[Total: round(( sum_positive ) / ( sum_positive + sum_negative ),2)][Positive Image: total*100]");
        $app_log->log('Total Positive: ' . $politishan->total_positive);
        $app_log->log('Total Negative: ' . $politishan->total_negative);
    }
}
