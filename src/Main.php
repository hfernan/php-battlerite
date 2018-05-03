<?php

namespace guastallaigor\PhpBattlerite;

use GuzzleHttp\Exception\RequestException;

 /**
  * PHP-Battlerite easy API
  *
  * @category  Games
  * @package   src
  * Main class
  * @author    Igor Guastalla de Lima  <limaguastallaigor@gmail.com>
  * @copyright 2018 PHP Battlerite
  * @license   MIT https://github.com/guastallaigor/php-battlerite/blob/master/LICENSE
  * @link      https://github.com/guastallaigor/php-battlerite
  */
class Main
{
    /**
     * API URL root of Battlerite.
     *
     * @var string
     */
    private static $apiUrl = "https://api.dc01.gamelockerapp.com/shards/global/";

    /**
     * Guzzle Client variable to send all requests.
     *
     * @var string
     */
    private $client;

    /**
     * API Key of your development battlerite account.
     *
     * @String
     */
    private $apiKey;

    /**
     * @var  \guastallaigor\PhpBattlerite\Config
     */
    private $config;

    /**
     * Main constructor.
     *
     * @param \guastallaigor\PhpBattlerite\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Method to set your API Key provided by your Battlerite development account.
     *
     * @param [String] $apiKey
     * @return void
     */
    public function setAPIKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Function that is going to make all the requests you need.
     *
     * @param String $method
     * @param String $request
     * @param array $filter
     *
     * @return object
     */
    public function sendRequest($method, $request, $filter = [])
    {
        $url = self::$apiUrl . $request;
        $header = [
            "Authorization" => "Bearer " . $this->apiKey,
            "Accept" =>  "application/vnd.api+json"
        ];

        try {
            $response = $this->client->request(
                $method,
                $url,
                [
                    "query" => $filter,
                    "connect_timeout" => 10,
                    "headers" => $header,
                ]
            );

            return json_decode($response->getBody()->getContents());
        } catch (RequestException $error) {
            $response = $this->statusCodeHandling($error);
            dd($response);
            return $response;
        }
    }

    /**
     * Get a single player request.
     *
     * @param [String] $id
     * @return object
     */
    public function getPlayer($id)
    {
        return $this->sendRequest('GET', 'players/' . $id);
    }

    /**
     * Get a collection of players.
     *
     * @param string $ids
     * @param string $type
     *
     * @return object
     */
    public function getPlayers($ids, $type = 'playerIds')
    {
        $filter = ['filter['. $type .']' => $ids];
        return $this->sendRequest('GET', 'players', $filter);
    }

    /**
     * Get a collection of teams.
     *
     * @param array $filter
     * @return object
     */
    public function getTeams($filter = [])
    {
        return $this->sendRequest('GET', 'teams', $filter);
    }

    /**
     * Get Battlerite status.
     *
     * @return object
     */
    public function getStatus()
    {
        return $this->sendRequest('GET', 'status');
    }

    public function getTelemetry()
    {
        $response = $this->sendRequest('GET', 'matches');
        dd($response);
    }

    /**
     * Function to handle unexpected errors.
     *
     * @param [RequestException] $error
     * @return void
     */
    protected function statusCodeHandling($error)
    {
        $response = [
            "statuscode" => $error->getResponse()->getStatusCode(),
            "error" => $error->getResponse()->getBody(true)->getContents(),
        ];

        return $response;
    }
}