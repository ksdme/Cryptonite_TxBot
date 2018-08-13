<?php

// Cryptonite (XCN) Transaction Bot
// Testing daemon load and network speed
// (C) 2018 Pallas and the cryptonite developers

//bot version
$VERSION = '0.0.2';

// the path to the cryptonite cli executable (including the file itself)
$CLI_PATH = '/home/pallas/Cryptonite/src/cryptonite-cli';

// delay between transactions (range, milliseconds)
$DELAY = array(1000, 5000);
$NO_DELAY = false;

// amount of transactions (range, XCN)
$AMOUNT = array(0, 5);

// minimum wallet balance
$AMOUNT_MIN = 3 * 1000;

// address to send transactions to
$ADDRESS = 'CP6uhgcDnXzdgQhnz2q1xhSFMFinmqkQkh';

//*****************************************


echo "Starting Cryptonite TXBot $VERSION\n";

$getinfo = json_decode(`$CLI_PATH getinfo`);

if (!$getinfo) {
	echo "Can't run the cli utility! Check CLI_PATH at the start of this script\n";
	exit(1);
}

echo "Found daemon version $getinfo->version, balance $getinfo->balance\n";

$ep_flag = strstr($getinfo->balance, 'ep');
$count = 0;

while (1) {
	$balance = trim(`$CLI_PATH getbalance`);
	if ($ep_flag) $balance = str_replace('ep', '', $balance);
	//echo "Current balance: $balance\n";

	if ($balance < $AMOUNT_MIN) {
		echo "Balance lower than AMOUNT_MIN, stopping\n";
		exit(2);
	}

	$amount = rand($AMOUNT[0], $AMOUNT[1] - 1) + rand(0, 99999999) / 100000000;
	echo "Trying to send $amount XCN, tx number $count\n";
	if ($ep_flag) {
		$amount = number_format($amount, 10);
		$amount .= 'ep';
	}

	$tx = `$CLI_PATH sendtoaddress $ADDRESS $amount`;
	if (!$tx) {
		echo "Failed sending\n";
		exit(3);
	}
	$tx = trim($tx);
	//echo "Success, txid: $tx\n";
	echo "Success, ";

	if (!$NO_DELAY) {
		$sleep = rand($DELAY[0], $DELAY[1]);
		echo "Sleeping $sleep milliseconds\n";
		usleep($sleep * 1000);
	} else {
		echo "Not sleeping\n";
	}
	$count++;
}

?>
