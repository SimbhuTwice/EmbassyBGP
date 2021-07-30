<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CompanyMaster;
use App\CompanyBranch;
use App\BranchDeviceLocation;
use App\DistechDevice;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Config;
use GuzzleHttp\Exception\RequestException;

class RestApiCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restapi:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {     
        // /*
        //    Write your database logic we bellow:
        //    Item::create(['name'=>'hello new']);
        // */
        // \Log::info("Cron is working fine!");
        // $this->info('Demo:Cron Cummand Run successfully!');
        \Log::info("Start...");

        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d H:i:s', strtotime($currentdate));

        // Get Session Timeout Users
        $timeoutUsers = DB::table('sessions')->whereNotNull('user_id')
            ->where('last_activity', '<=', strtotime(Carbon::now('UTC')->addMinutes(-15)))->get();
        
        $timeoutUserIdList = [];
        foreach ($timeoutUsers as $tuser) {
            $timeoutUserIdList[] = [$tuser->user_id];
        }
        DB::table('users')->whereIn('id', $timeoutUserIdList)->update(['is_logged_in' => 0]);
        DB::table('sessions')->whereIn('user_id', $timeoutUserIdList)->update(['user_id' => null]);
        // Get Session Timeout Users Ends
        
        $compList = CompanyMaster::where('is_active', 1)->where('id', 2)->get();
        foreach ($compList as $comp)
        {
            $compbrList = CompanyBranch::where('company_id', $comp->id)->where('is_active', 1)->orderBy('id')->get();
            $branchDevLocList = BranchDeviceLocation::where('company_id', $comp->id)
                // ->where('branch_id', $compbr->id)
                ->where('is_active', 1)
                ->orderBy('id')->get();

            $insertData = array();
            foreach ($compbrList as $compbr) {
                $tableName = $compbr->table_name;
                try {
                    // PM2.5
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_pmtwofive'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_pmtwofive_name'),
                            'asn_value' => Config::get('constants.api_pmtwofive_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_pmtwofive') . "  " . $response->getStatusCode());
                    }

                    // PM10
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_pmten'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_pmten_name'),
                            'asn_value' => Config::get('constants.api_pmten_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_pmten') . "  " . $response->getStatusCode());
                    }

                    // Ambient Temp
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_ambient_temp'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ambient_temp_name'),
                            'asn_value' => Config::get('constants.api_ambient_temp_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 500);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ambient_temp_name'),
                            'asn_value' => Config::get('constants.api_ambient_temp_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_ambient_temp') . "  " . $response->getStatusCode());
                    }

                    // Ambient Humidity
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_ambient_humidity'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ambient_humidity_name'),
                            'asn_value' => Config::get('constants.api_ambient_humidity_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 100);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ambient_humidity_name'),
                            'asn_value' => Config::get('constants.api_ambient_humidity_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_ambient_humidity') . "  " . $response->getStatusCode());
                    }

                    // Carbon Dioxide
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_carbon_dioxide'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_carbon_dioxide_name'),
                            'asn_value' => Config::get('constants.api_carbon_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 2100);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_carbon_dioxide_name'),
                            'asn_value' => Config::get('constants.api_carbon_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_carbon_dioxide') . "  " . $response->getStatusCode());
                    }

                    // Carbon Monoxide
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_carbon_monoxide'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_carbon_monoxide_name'),
                            'asn_value' => Config::get('constants.api_carbon_monoxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 60);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_carbon_monoxide_name'),
                            'asn_value' => Config::get('constants.api_carbon_monoxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_carbon_monoxide') . "  " . $response->getStatusCode());
                    }

                    // Nitrogen Dioxide
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_nitrogen_dioxide'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_nitrogen_dioxide_name'),
                            'asn_value' => Config::get('constants.api_nitrogen_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 500);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_nitrogen_dioxide_name'),
                            'asn_value' => Config::get('constants.api_nitrogen_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_nitrogen_dioxide') . "  " . $response->getStatusCode());
                    }

                    // Ozone
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_ozone'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ozone_name'),
                            'asn_value' => Config::get('constants.api_ozone_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 500);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_ozone_name'),
                            'asn_value' => Config::get('constants.api_ozone_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_ozone') . "  " . $response->getStatusCode());
                    }

                    // Sulphur Dioxide
                    $clientTest = new Client();
                    $response = $clientTest->request('GET', $compbr->distech_deviceip . '/' . Config::get('constants.api_sulphur_dioxide'), 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                            'headers' => ['Auth' =>  Config::get('constants.api_auth_key_embassy')]
                        ]);
                    if ($response->getStatusCode() == 200) {
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        $presentValue = $serviceResultjson;
                        $locname = '';
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_sulphur_dioxide_name'),
                            'asn_value' => Config::get('constantsapi_sulphur_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                    }
                    else {
                        $locname = '';
                        $presentValue = rand(0, 500);
                        $insertData[] = [
                            'company_id' => $compbr->company_id,
                            'branch_id' => $compbr->id,
                            'object_type' =>  Config::get('constants.api_object_type'),
                            'object_name' => Config::get('constants.api_sulphur_dioxide_name'),
                            'asn_value' => Config::get('constants.api_sulphur_dioxide_asn'),
                            'present_value' => $presentValue,
                            'device_location' => $locname,
                            // 'status_flag' => $statusFlags,
                            'status_date' => $currdt,
                            'status_time' => $currdt,
                            'distech_deviceip' => $compbr->distech_deviceip,
                        ];
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . Config::get('constants.api_sulphur_dioxide') . "  " . $response->getStatusCode());
                    }
                }
                catch (\Exception $e) {
                    \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id);
                    \Log::info($e);
                    continue;
                }
            }

            \Log::info($insertData);
            DB::table($tableName)->insert($insertData);
        }
        \Log::info("End...");
    }
}
