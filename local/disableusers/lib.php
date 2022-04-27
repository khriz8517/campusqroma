<?php
global $CFG;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

function execCurlD($data) {
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
    curl_close($curl);
    $responseData = json_decode($response,true);
    return $responseData;
}
function getADTokenD() {
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

    $responseData = execCurlD($data);
    return $responseData['access_token'];
}
function getADUsersD($key, $skipToken='') {
    if($key>0) {
        $skipToken = '&$skiptoken='.$skipToken;
    }

    $data = array(
        'url' => 'https://graph.microsoft.com/v1.0/users?$select=businessPhones,displayName,givenName,jobTitle,mail,mobilePhone,officeLocation,surname,userPrincipalName,id,department'.$skipToken,
        'httpMethod' => 'GET',
        'httpHeader' => array("Authorization: ". getADTokenD())
    );
    $responseData = execCurlD($data);
    return $responseData;
}
function disableusers_task() {
    global $DB;

    $key = 0;
    $skipToken = '';
    $usersValues = array();
    $allUsers = array();

    while(true) {
        if($key>1 && $skipToken=='') {
            break;
        }
        $allUsers[] = getADUsersD($key, $skipToken);
        $needle = '$skiptoken=';
        $skipToken = substr($allUsers[$key]['odata.nextLink'], strpos($allUsers[$key]['odata.nextLink'], $needle) + strlen($needle));
        $key++;
    }

    foreach($allUsers as $allUser) {
        foreach($allUser['value'] as $key=>$val) {
            $usersValues[$count] = $val;
            $count++;
        }
    }
    foreach($usersValues as $key=>$userAD) {
        $usersAdArr[] = $userAD['userPrincipalName'];
    }

    $users = $DB->get_records('user');
    //por definir indicador - samuel
    $indicadorDeNombreUsuario = 'por definir';

    foreach($users as $user) {
        //if(strpos($user->username, $indicadorDeNombreUsuario) !== false && !in_array($user->username, $usersAdArr)) {
        if($user->firstaccess == 0) {
            $userMainDataObj = new stdClass();
            $userMainDataObj->id = $user->id;
            $userMainDataObj->deleted = 1;
            $DB->update_record('user', $userMainDataObj);
        }
    }
}