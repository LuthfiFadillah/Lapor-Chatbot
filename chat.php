<?php
require_once 'vendor/autoload.php';
require 'conversation/LaporConversation.php';
require_once 'mysql/connect_db.php';

$con = connect_db();

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\DoctrineCache;
use Doctrine\Common\Cache\FilesystemCache;

$dotenv = new Dotenv\Dotenv(realpath($_SERVER["DOCUMENT_ROOT"]) . '/Lapor-Chatbot-dev/');
$dotenv->load();

$config = [
    // Your driver-specific configuration
     "telegram" => [
        "token" => getenv('TELEGRAM_TOKEN')
     ]
];

DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

$doctrineCacheDriver = new FilesystemCache(__DIR__.'/cache');
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

$botman->hears('{command}', function (BotMan $bot, $command) use ($con) {
    $query = "SELECT next_state FROM command WHERE command_pattern='$command'";
    $result = $con->query($query);
    if($col = $result->fetch(PDO::FETCH_NUM)){
        //create laporan entry in database
        $laporanid = mt_rand(1, 999999);
        $query = "INSERT INTO laporan (id) VALUES ($laporanid)";
        $result = $con->query($query);

        //start conversation
        $bot->startConversation(new LaporConversation($col[0],$laporanid));
    } else {
        //no command matches in database
        $bot->reply('Salah memasukkan perintah!');
    }
});

$botman->listen();