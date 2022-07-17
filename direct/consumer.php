<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$routing_keys = array_slice($argv, 1);
foreach ($routing_keys as $key) {
    $channel->queue_bind($queue_name, 'direct_logs', $key);
}

echo " [Consumer] Waiting for logs. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();