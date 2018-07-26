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
    protected $signature = 'tms:abchina:exchange 
            {--force= : 强制循环}
            {--sleep= : 休眠毫秒,默认休眠300毫秒}';

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
        $force  = $this->option('force');
        $sleep  = $this->option('sleep') ?? 300;
        
        if($force){
            
            while(true){
                $this->secKill($active, $data);
                list($msec, $sec) = explode(' ', microtime());
                $mtime =  (float)sprintf('%.06f', (floatval($msec) + floatval($sec)));
                $this->line($mtime);
                usleep($sleep);
            }
        }else{
            $this->secKill($active, $data);
        }
        
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
        $result['title']    = array_get($active, 'yhDetail.ruleName');
        $result['desc'] = array_get($active, 'yhDetail.ruleDesc');

        $headers    = [
            '状态','结果','活动','描述'
        ];
        $rows   = [
            array_get($result, 'status'),
            array_get($result, 'result'),
            array_get($result, 'title'),
            array_get($result, 'desc')
            
        ];
        
//        $this->table($headers, $rows);
        
        $this->line('状态 : '.array_get($result, 'status'));
        $this->line('结果 : '.array_get($result, 'result'));
        $this->line('活动 : '.array_get($result, 'title'));
        $this->line('描述 : '.array_get($result, 'desc'));
        
        $this->split();
    }
    
    protected function split(){
        $this->line('----------------------------------------------');
    }
    
}
