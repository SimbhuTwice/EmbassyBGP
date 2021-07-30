<?php

return array(

    'ExecutionTimeOut'  => 0,
    'guzzleTimeOut'     => 5, 
    'api_object_type' => 'air-quality',
    'api_auth_key_embassy' => '029D39BED4FD7ED944608CFE80C59E73EC505340494CCD0787B6DCA7CDEA78EB',

    'api_pmtwofive' => 'getpmtwofive',
    'api_pmtwofive_name' => 'PM 2.5',
    'api_pmtwofive_asn' => 'pm_2.5',

    'api_pmten' => 'getpmten',
    'api_pmten_name' => 'PM 10',
    'api_pmten_asn' => 'pm_10',

    'api_ambient_temp' => 'getambient_temp',
    'api_ambient_temp_name' => 'Ambient Temp',
    'api_ambient_temp_asn' => 'ambient_temp',

    'api_ambient_humidity' => 'getambient_humidity',
    'api_ambient_humidity_name' => 'Ambient Humidity',
    'api_ambient_humidity_asn' => 'ambient_humidity',

    'api_carbon_dioxide' => 'getcarbon_dioxide',
    'api_carbon_dioxide_name' => 'Carbon Dioxide (CO2)',
    'api_carbon_dioxide_asn' => 'carbon_dioxide',

    'api_carbon_monoxide' => 'getcarbon_monoxide',
    'api_carbon_monoxide_name' => 'Carbon Monoxide (CO)',
    'api_carbon_monoxide_asn' => 'carbon_monoxide',

    'api_nitrogen_dioxide' => 'getnitrogen_dioxide',
    'api_nitrogen_dioxide_name' => 'Nitrogen Dioxide (NO2)',
    'api_nitrogen_dioxide_asn' => 'nitrogen_dioxide',

    'api_ozone' => 'getozone',
    'api_ozone_name' => 'Ozone (O3)',
    'api_ozone_asn' => 'ozone',

    'api_sulphur_dioxide' => 'getsulphur_dioxide',
    'api_sulphur_dioxide_name' => 'Sulphur Dioxide (SO2)',
    'api_sulphur_dioxide_asn' => 'sulphur_dioxide',
);
?>
