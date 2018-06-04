<?php
/**
 * Created by IntelliJ IDEA.
 * User: ASUS-ROG
 * Date: 19/05/2018
 * Time: 13:47
 */

require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/Lapor-Chatbot/mysql/connect_db.php';

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use GuzzleHttp\Client;

class LaporConversation extends Conversation
{
    protected $state;

    protected $laporanid;

    protected $next_state;

    protected $data;

    public $con;

    public function __construct($state, $laporanid)
    {
        $this->state = $state;
        $this->laporanid = $laporanid;
        $this->con = connect_db();
    }

    public function asking(){
        //find any information for this state (text for conversation, pattern for this conversation answer, etc)
        $query = "SELECT text_conversation, next_state, pattern, datatype FROM conversation WHERE state='$this->state'";
        $result = $this->con->query($query);

        if($col = $result->fetch(PDO::FETCH_NUM)){

            $lapid = $this->laporanid;
            if(strcasecmp($this->state, 'konfirmasi') !== 0){
                //conversation
                $this->ask($col[0], [
                    [
                        'pattern' => $col[2],
                        'callback' => function (Answer $answer) use ($col,$lapid) {
                            $this->data = $answer->getText();
                            $query = "UPDATE laporan SET ".$col[3]." = '".$this->data."' WHERE id=".$lapid;
                            $result = $this->con->query($query);
                            $this->bot->startConversation(new LaporConversation($col[1], $lapid));
                        }
                    ],
                    [
                        'pattern' => '.*',
                        'callback' => function () use ($col) {
                            $this->say('Data yang dimasukkan salah / tidak sesuai format!');
                            $this->repeat($col[0]);
                        }
                    ]
                ]);
            }else if (strcasecmp($this->state, 'konfirmasi') == 0) {
                $konfirmasi = $col[0];

                //preparing for send xml response
                $query = "SELECT nama, phone, email, laporan FROM laporan WHERE id='$this->laporanid'";
                $result = $this->con->query($query);
                if($col = $result->fetch(PDO::FETCH_NUM)){

                    //create xml
                    $input_xml = '<?xml version="1.0" encoding="UTF-8"?>
<lapor token="{E8F10E96-E8F7-A7A2-3968-A337E4FB5481}">
    <pelapor>
        <email>'.$col[2].'</email>
        <telpon>'.$col[1].'</telpon>
        <namadepan>'.$col[0].'</namadepan>
        <namabelakang>_</namabelakang>
    </pelapor>
    <laporan rahasia="1" anonim="1">
        <isi>'.$col[3].'</isi>
    </laporan>
</lapor>';

                    //setting up connection and request connection
                    $client = new Client();
                    $uri = 'https://www.lapor.go.id/api/stream/addstream';
                    $request = new \GuzzleHttp\Psr7\Request('POST',$uri,['Content-Type'=>'text/xml; charset=UTF8'],$input_xml);

                    //getting response & convert it into object
                    $response = $client->send($request);
                    $output_xml = simplexml_load_string($response->getBody()) or die("Error: Cannot create object");

                    //update query tracking id and transaksi id for laporan
                    $query = "UPDATE laporan SET trackingid = '".$output_xml->trackingid."', transaksiid = '".$output_xml->transaksiid."' WHERE id=".$this->laporanid;
                    $result = $this->con->query($query);
                    $this->say($konfirmasi.$output_xml->trackingid);
                }
            }
        }
    }

    public function run()
    {
        $this->asking();
    }

    public function __sleep()
    {
        return array();
    }

    public function __wakeup()
    {
        $this->con = connect_db();
    }
}
