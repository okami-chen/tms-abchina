<?php

namespace OkamiChen\TmsAbchina\Console\Command;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Encore\Admin\Config\ConfigModel;

class ExchangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:abchina:exchange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '农行产品秒杀';
    
    protected $cacheKey = 'abchina:detail:';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now    = config('abchina:exchange');
        if(!$now){
            $this->error('请先配置秒杀');
            return false;
        }
        $data   = json_decode($now, true);
        $name   = $this->cacheKey.$data['actPicId'];
        $ret    = ConfigModel::where(['name'=>$name])->first();
        if(!$ret){
            $this->error('未找到活动详情');
            return true;
        }
        $active = json_decode($ret->value, true);
        $this->secKill($active, $data);
    }
    
    protected function secKill($active, $data){
        $config = [
            'base_uri'  => 'https://enjoy.abchina.com',
            'timeout'   => 3.0,
            'cookies'   => true,
        ];
        $client = new Client($config);
        $option = [
            'json' => [
                "sessionId"=> array_get($data, 'sessionId'),
                "ruleNo"=> array_get($active, 'yhDetail.ruleNo'),
                "actNo"=> array_get($active, 'yhDetail.actNo'),
                "discType"=> array_get($active, 'yhDetail.discType'),
                "actType"=> array_get($active, 'yhDetail.actType'),
                "appr"=> array_get($active, 'yhDetail.appr'),
            ],
//            'curl'  => [
//                CURLOPT_COOKIE  => config('abchina:cookie')
//            ]
        ];
        $response   = $client->post('/yh-web/customer/giftTokenDraw', $option);
        $result     = json_decode($response->getBody()->getContents(), true);
        dd($result);
    }
    
}
