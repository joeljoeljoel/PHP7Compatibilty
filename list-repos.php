<?php
/*
* php list-repos.php | uniq | sort > all-repos.txt
*/
function send_request($uri, array $options = array()) {
    global $config;

    $curl_options = array(
        CURLOPT_USERAGENT => $config['user_agent'],
        CURLOPT_TIMEOUT => 5,
        CURLOPT_USERPWD => $config['username'] . ':' . $config['password'],
        CURLOPT_RETURNTRANSFER => true);

    if (!empty($options)) {
      $curl_options = $curl_options + $options;
    }

    $request = curl_init($uri);
    curl_setopt_array($request, $curl_options);
    $response = curl_exec($request);
    curl_close($request);

    return $response;
}

// config


$repos = array();
$i = 1;
do {
    $response = send_request($config['host'] . "/orgs/{$config['organization']}/repos?type=private&page=" . $i);
    $data = json_decode($response, true);

    $repos = array_merge($repos, $data);
    ++$i;
} while(count($data));

foreach ($repos as $repo) {
    echo $repo['name'], PHP_EOL;
}
