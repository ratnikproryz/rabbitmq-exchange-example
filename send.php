<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// get message from command line
$data = implode(' ', array_slice($argv, 1));
if(empty($data)) {
    $data="(Emty message!)";
}

// declare queue has name is hello
$channel->queue_declare('hello', false, false, false, false);
// publish message to queue "hello"
$msg = new AMQPMessage($data);
$channel->basic_publish($msg, '', 'hello');

echo "Producer sent message: '" .$data. "'\n" ;

$channel->close();
$connection->close();
// docker run -it --rm --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3.10-management