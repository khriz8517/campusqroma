<?php
global $CFG;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

global $DB;

function execCurl($data) {
    $curl = curl_init();

    $url = $data['url'];
    $postFields = $data['postFields'];
    $httpHeader = $data['httpHeader'];
    $httpMethod = $data['httpMethod'];

    $curlSetOptArray = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $httpMethod
    );

    if($httpMethod == 'POST') {
        $curlSetOptArray[CURLOPT_POSTFIELDS] = $postFields;
    }
    $curlSetOptArray[CURLOPT_HTTPHEADER] = $httpHeader;

    curl_setopt_array($curl, $curlSetOptArray);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        print curl_error($curl);
    }

    curl_close($curl);
    $responseData = json_decode($response,true);
    return $responseData;
}
function getADToken() {
    $data = array(
        'url' => 'https://login.microsoftonline.com/cppqsa.onmicrosoft.com/oauth2/v2.0/token',
        'postFields' => http_build_query(array('grant_type' => 'client_credentials',
            'client_id' => 'c45d7b04-10e2-427f-8be0-6fe59a9b9a09',
            'client_secret' => '~~Ea4e8k.F_K_4236KMz3CK~1fPk_0gaD8',
            'scope' => 'https://graph.microsoft.com/.default'), '', '&'),
        'httpMethod' => 'POST',
        'httpHeader' => array('host: login.microsoftonline.com',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: buid=0.AQYASG_it5IiFEqjVRrrhImuPRgFD1jTGCJMjrrTt_PN72QGAAA.AQABAAEAAAAGV_bv21oQQ4ROqh0_1-tAW_7_lkPgDNNQcc9ndJ6-VT_fKycsxUQA_fsiaenVHh0m1dZmFiOVou0VgVUcdSWQcKUXNWy0yeSTtMjrE4vBvIZsvOjiuXWYPgfnevpPNZAgAA; fpc=AlKOys_Nd-FDqTUucSXhED6Lv60HAQAAAEAZ29YOAAAA; x-ms-gateway-slice=estsfd; stsservicecookie=estsfd')
    );

    $responseData = execCurl($data);
    return $responseData['access_token'];
}
function getADUsers($key, $skipToken='') {
    if($key>0) {
        $skipToken = '&$skiptoken='.$skipToken;
    }

    $data = array(
        'url' => 'https://graph.microsoft.com/v1.0/users?$select=businessPhones,displayName,givenName,jobTitle,mail,mobilePhone,officeLocation,surname,userPrincipalName,id,department,faxNumber,employeeID,postalCode,companyName,city'.$skipToken,
        'httpMethod' => 'GET',
        'httpHeader' => array("Authorization: ". getADToken())
    );
    $responseData = execCurl($data);
    return $responseData;
}

$key = 0;
$skipToken = '';
$usersValues = array();
$allUsers = array();

while(true) {
    if($key>1 && $skipToken=='') {
        break;
    }
    $allUsers[] = getADUsers($key, $skipToken);

    $needle = '$skiptoken=';
    $skipToken = substr($allUsers[$key]['@odata.nextLink'], strpos($allUsers[$key]['@odata.nextLink'], $needle) + strlen($needle));
    $key++;
}

foreach($allUsers as $allUser) {
    foreach($allUser['value'] as $key=>$val) {
        $usersValues[$count] = $val;
        $count++;
    }
}

echo '<pre>';
var_dump($usersValues);
exit;

foreach($usersValues as $key=>$userAD) {
    $userObj = new stdClass();
    $userPrincipalName = $userAD['userPrincipalName'];
    $userObj->username = isset($userPrincipalName) ? $userPrincipalName : ' ';
    $userObj->firstname = isset($userAD['givenName']) ? $userAD['givenName'] : ' ';
    $userObj->lastname =  isset($userAD['surname']) ? $userAD['surname'] : ' ';
    $userObj->email =  isset($userAD['mail']) ? $userAD['mail'] : ' ';
    $userObj->lang = 'es';

    $user = $DB->get_record('user', array('username' => $userPrincipalName));

    if(empty($user) || !$user) {
        $userObj->auth       = 'manual';
        $userObj->confirmed  = 1;
        $userObj->mnethostid = 1;
        //$DB->insert_record('user', $userObj);
    } else {
        $userObj->id = $user->id;
        $DB->update_record('user', $userObj);
    }
    $userObj->profile_field_origen = 'Qroma';
    $userObj->profile_field_tipo_empleado = isset($userAD['postalCode']) ? $userAD['postalCode'] : ' ';
    $userObj->profile_field_cargo = isset($userAD['jobTitle']) ? $userAD['jobTitle'] : ' ';
    $userObj->profile_field_celular = isset($userAD['mobilePhone']) ? $userAD['mobilePhone'] : ' ';
    $userObj->profile_field_codigo = isset($userAD['employeeID']) ? $userAD['employeeID'] : ' ';
    $userObj->profile_field_dni = isset($userAD['faxNumber']) ? $userAD['faxNumber'] : ' ';

    $departmentField = $userAD['department'];

    $profileFieldArea = '';
    $profileFieldDireccion = '';

    if(!empty($departmentField)) {
        if(strpos($departmentField,',') !== false) {
            $depValues = explode(',', $departmentField);
            $profileFieldArea = $depValues[0];
            $profileFieldDireccion = $depValues[1];
        } else if(strpos($departmentField,';') !== false) {
            $depValues = explode(';', $departmentField);
            $profileFieldArea = $depValues[0];
            $profileFieldDireccion = $depValues[1];
        } else {
            $profileFieldArea = $departmentField;
        }
    }

    $userObj->profile_field_area = $profileFieldArea;
    $userObj->profile_field_direccion = $profileFieldDireccion;

    profile_save_data($userObj);
}