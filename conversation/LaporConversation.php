<?php
/**
 * Created by IntelliJ IDEA.
 * User: ASUS-ROG
 * Date: 19/05/2018
 * Time: 13:47
 */

require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '\botman3    \Lapor-Chatbot-dev\mysql\connect_db.php';

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class LaporConversation extends Conversation
{

    protected $state;

    protected $next_state;

    protected $data;

    public $con;

    public function __construct($state)
    {
        $this->state = $state;
    }


    public function asking(){
        $con = connect_db();
        $query = "SELECT text_conversation, next_state FROM conversation WHERE state='$this->state'";
        $result = $con->query($query);

        if($col = $result->fetch(PDO::FETCH_NUM)){

            $this->next_state = $col[1];
//            error_log(strcasecmp($this->next_state, 'konfirmasi'), 0);
            if(strcasecmp($this->next_state, 'konfirmasi') !== 0){
                $this->ask($col[0], function (Answer $answer){
                    $this->data = $answer->getText();
                    $this->bot->startConversation(new LaporConversation($this->next_state));
                });
            }else {
                error_log('0',0);
                $this->say($col[0]);
                error_log('1',0);
            }

        }

    }

    public function run()
    {
        $this->asking();
    }
}