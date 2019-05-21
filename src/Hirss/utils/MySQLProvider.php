<?php

namespace Hirss\utils;

use Hirss\Main;
use Hirss\player\PlayerClass;
use pocketmine\utils\Config;

class MySQLProvider{

    //TODO: implement MySQL data saving for all data uses.

    private $plugin, $db, $users, $result;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->db = new \mysqli(!is_null($this->plugin->settings->get("server-name")) ? $this->plugin->settings->get("server-name") : "localhost", $this->plugin->settings->get("username"), $this->plugin->settings->get("db_name"), !is_null($this->plugin->settings->get("port")) ? ((int)$this->plugin->settings->get("port")) : 3306);
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->plugin->getServer();
    }

    /**
     *
     */
    public function process(){
        $this->getPlugin()->database->reload();
        $database = $this->getPlugin()->database->getAll();
        $total = count($database);
        $keys = array_keys($database);
        $i = 0;
        while($i < $total){
            $name = $database[$keys[$i]]["name"];
            $rank = $database[$keys[$i]]["rank"];
            $lang = $database[$keys[$i]]["lang"];
            if($database[$keys[$i]]["uploaded_to_sql"] !== true){
                $this->result = $this->db->query("INSERT INTO registered_players(name, rank, lang)
										VALUES ('$name', '$rank', '$lang')"
                );
                if($this->result === false){
                    $this->getPlugin()->getLogger()->info($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT_BAD)."Files weren't uploaded to SQL server!");
                    break;
                }
                $database[$keys[$i]]["uploaded_to_sql"] = true;
                $this->plugin->database->setAll($database);
                $this->plugin->database->save();
                $this->users++;
            }else{
                $this->getPlugin()->getLogger()->info($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Player ".$name." has already been uploaded to the SQL server");
            }
            $i++;
        }
        if($this->result){
            $this->getPlugin()->getLogger()->info($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT).(string) $this->users . " player files uploaded to SQL database.");
        }
    }

    /**
     * @param $name
     * @param string $data
     * $data is one of the MySQL database columns [name, password, rank, lang, uuid]
     * @return mixed
     */
    public function getDataFromSQL($name, string $data){
        //$name = $player->getName();
        $result = $this->db->query("SELECT * FROM registered_players where name='$name';");
        $array = $result->fetch_assoc();
        return $array[$data];
    }

    public function getStatsFromSQL($name, string $data){
        //TODO: stats for sql
    }

}