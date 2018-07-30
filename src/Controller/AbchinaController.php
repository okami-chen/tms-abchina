<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace OkamiChen\TmsAbchina\Controller;

use App\Http\Controllers\Controller;
use Encore\Admin\Config\ConfigModel;
use OkamiChen\TmsAbchina\Support\Abchina;
/**
 * Description of AbchinaController
 * @date 2018-7-27 10:35:30
 * @author dehua
 */
class AbchinaController extends Controller {

    public function doSession(Abchina $abc){
        $headers    = [
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Headers'=> 'Origin, Content-Type, Cookie, Accept',
            'Access-Control-Allow-Methods'=> 'GET, POST, PATCH, PUT, OPTIONS'
        ];
        $where   = [
            'name'  => 'abchina:exchange'
        ];
        
        $config = ConfigModel::where($where)->first();
        if($config && request()->has('sessionId')){
            $value  = json_decode($config->value, true);
            $value['sessionId'] = request('sessionId');
            $config->value  = json_encode($value, JSON_UNESCAPED_UNICODE);
            $config->updated_at = date('Y-m-d H:i:s');
            $config->save();
        }
        $logger = [
            'data'  => request()->all(),
            'cookie'    => request()->header('cookie'),
        ];
        
        logger('proxy', $logger);
        
        $body = $abc->notifyCookie($logger['cookie'], $logger['data']);
        
        return response()->json(['status'=>'success','result'=>null,'time'=>time(),'body'=>$body], 200, $headers);
    }
}
