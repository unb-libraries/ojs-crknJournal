<?php

$opts = getopt(NULL, ['ojs:', 'data:', 'create']);

if(empty($opts['ojs'])) {
  printUsage();
  exit;
}

define('INDEX_FILE_LOCATION', $opts['ojs'] . '/index.php');
$application = require($opts['ojs'] . '/lib/pkp/includes/bootstrap.inc.php');
$conn = DBConnection::getInstance();
$db = $conn->getDBConn();

if(isset($opts['create'])) {
  $sql = file_get_contents(dirname(__FILE__) . '/table.sql');
  print "Creating table...\n";
  $db->execute($sql);
}
elseif(!empty($opts['data'])) {
  print "Fetching latest data...\n";
  $content = file_get_contents($opts['data']);

  if($content == FALSE) {
    print "Failed to fetch data.\n";
    exit;
  }

  print "Removing old entries...\n";
  $db->execute('TRUNCATE TABLE crkn_ips;');

  print "Inserting new entries...\n";

  $xml = new SimpleXMLElement($content);
  $count = 0;
  $handle = $db->prepare('INSERT INTO crkn_ips (institution, ip, start, end) VALUES (?, ?, ?, ?);');
  foreach($xml->xpath('//listeip') as $item) {
    $start = $end = $item->ip;
    if(preg_match('/[a-z]/', $start)) {
      $start = $end = gethostbyname($start);
    }
    elseif(strpos($start, '*') != FALSE) {
      $start = str_replace('*', '0', $start);
      $end = str_replace('*', '255', $end);
    }

    $start = sprintf("%u", ip2long($start));
    $end = sprintf("%u", ip2long($end));
    if($db->execute($handle, [$item->abonne, $item->ip, $start, $end]) == false) {
      print $db->errorMsg() . "\n";
      exit;
    }
    $count++;
  }

  print "$count entries added.\n";

  print "Adding Erudit IPs...\n";
  $db->execute($handle, ['Erudit (Prod)', '132.219.138.146', 2228980370, 2228980370]);
  $db->execute($handle, ['Erudit (Test)', '132.219.138.147', 2228980371, 2228980371]);
}
else {
  printUsage();
}

function printUsage() {
  print "USAGE:\n";
  print "  # To initially create db tables\n";
  print "  update.php --ojs /path/to/ojs/ --create\n\n";
  print "  # To update table data\n";
  print "  update.php --ojs /path/to/ojs/ --data https://url.to/data.xml\n";
}
