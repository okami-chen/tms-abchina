<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace OkamiChen\TmsAbchina\Support;

use GuzzleHttp\Client;
use Exception;
/**
 * Description of Abchina
 * @date 2018-7-27 11:18:22
 * @author dehua
 */
class Abchina {
    
    public function notifyCookie($cookie, $post){
        $config = [
            'base_uri'  => 'https://enjoy.abchina.com',
            'cookies'   => true,
            'timeout'   => 3.0
        ];
        $options = [
            'json' => $post,
            'curl' => [
                CURLOPT_COOKIE => $cookie
            ]
        ];
        $client = new Client($config);
        try {
            $response   = $client->post('/yh-web/jedis/resetSessionExpire', $options);
            $body   = json_decode($response->getBody()->getContents(), true);
            return $body;
        } catch (Exception $ex) {
            logger()->error($ex->getMessage());
            return [];
        }

    }
}
