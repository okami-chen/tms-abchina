<?php

namespace OkamiChen\TmsAbchina\Console\Command;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Encore\Admin\Config\ConfigModel;
use Exception;

class MonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:abchina:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '农行产品秒杀';

    
    private $totalPageCount;
    private $counter = 1;
    private $concurrency = 10;  // 同时并发抓取
    private $active = [];

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
        try {
            $this->getActiveList();
        } catch (Exception $ex) {
            $this->line($ex->getMessage());
        }
    }
    
    
    /**
     * 活动详情
     */
    protected function getActiveDetail($ruleNo=null, $actNo=null, $actType=null, $discType=null){
        $config = [
            'base_uri'  => 'https://enjoy.abchina.com',
            'timeout'   => 3.0
        ];
        $client = new Client($config);
        $option = [
            'json' => [
                "ruleNo" => $ruleNo,
                "actNo" => $actNo,
                "discType" => $discType,
                "actType" => $actType,
                "cityCode" => "289",
                "longitude" => "121.364438",
                "latitude" => "31.226119",
                "pageNo" => "1",
                "rowsPerPage" => "2"
            ]
        ];
        $response   = $client->post('/yh-web/rights/rightsdetails', $option);
        $result     = json_decode($response->getBody()->getContents(), true);
        $name       = 'abchina:detail:'. array_get($result, 'result.yhDetail.actPicId');
        cache()->put($name, $result['result'], 3600);
        return $result['result'];
    }


    /**
     * 活动列表
     * @return type
     */
    protected function getActiveList(){
        $config = [
            'base_uri'  => 'https://enjoy.abchina.com',
            'timeout'   => 3.0
        ];
        $client = new Client($config);
        $option = [
            'json' => [
                "type" => "A,B,C,D,E,F",
                "cityCode" => "289",
                "longitude" => "121.364176",
                "latitude" => "31.226325",
                "pageNo" => "1",
                "countPerPage" => "10",
                "secKillFlag" => "1"
            ]
        ];
        $response   = $client->post('/yh-web/rights/list', $option);
        $result     = json_decode($response->getBody()->getContents(), true);
        $actives    = array_get($result, 'result.items');
        $headers    = [
            '活动名称','活动时间'
        ];
        $rows       = [];
        
        foreach ($actives as $key => $active) {
            $this->active[] = $active;
            $name   = 'abchina:active:'.$active['actPicId'];
            cache()->put($name, $active, 3600);

            $detail = $this->getActiveDetail($active['ruleNo'], $active['actNo'], $active['actType'], $active['discType']);
            $time   = 
                substr($detail['yhDetail']['actEdDate'], 0,4) .'-'.
                substr($detail['yhDetail']['actEdDate'], 4,2) .'-'.
                substr($detail['yhDetail']['actEdDate'], 6,2) .' '.
                substr($detail['yhDetail']['secKillStTime'], 0, 2).':' . 
                substr($detail['yhDetail']['secKillStTime'], 2, 2).':' .
                substr($detail['yhDetail']['secKillStTime'], 4, 2);
            $rows[] = [
                $active['ruleName'],
                $time
            ];
        }
        $this->table($headers, $rows);
        return $actives;
    }
    
}
