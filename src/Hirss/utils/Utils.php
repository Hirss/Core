<?php

namespace Hirss\utils;


use Hirss\Main;
use Hirss\player\PlayerClass;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use \ZipArchive;

class Utils{

    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    /**
     * @param $backupPath
     * @param $worldPath
     */
    public static function doBackup($backupPath, $worldPath){
        $zip = new \ZipArchive;
        $zip->open($backupPath, \ZipArchive::CREATE);
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($worldPath)) as $file){
            $zip->addFile($file, str_replace("\\", "/", ltrim(substr($file, strlen($worldPath)), "/\\")));
        }
    }

    /**
     * @param $src
     * @param $dst
     */
    public static function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !==($file = readdir($dir))){
            if(($file != '.') && ($file != '..' )){
                if(is_dir($src . '/' . $file)){
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }else{
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param $levelName
     */
    public static function resetLevel($levelName){
        if(!is_dir(Main::getInstance()->getDataFolder()."World_Backups/$levelName")){
            Main::getInstance()->getLogger()->critical("Map backup for '".$levelName."' is missing!");
            return;
        }
        $server = Server::getInstance();
        $main = Main::getInstance();
        $worldPath = $server->getDataPath() . "worlds/".$levelName;
        $server->unloadLevel($server->getLevelByName($levelName), true);
        self::file_delDir($worldPath);
        sleep(0.9);
        self::recurse_copy($main->getDataFolder()."World_Backups/".$levelName."/",$server->getDataPath()."worlds/".$levelName."/");
    }

    /**
     * @param $dir
     */
    public static function file_delDir($dir){
        $dir = rtrim($dir, "/\\") . "/";
        foreach(scandir($dir) as $file){
            if($file === "." or $file === ".."){
                continue;
            }
            $path = $dir . $file;
            if(is_dir($path)){
                self::file_delDir($path);
            }else{
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomInt($length = 10) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getChatMessages($key){
        $message = $this->getPlugin()->settings->get($key);
        return $message;
    }

}