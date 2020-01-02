<?php

/**
 * Installation class for sinusbot
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use ZipArchive;
use mysqli;

class Install extends Database
{
	/**
     * Contains last error
     * 
     * @var string
     */
    public static $lasterror;

    public static $mysql_error;

    public static $ssh_error;

    /**
     * Validate form inputs from 1st part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_1($arr)
    {
        if (empty($arr["address"]) || empty($arr["port"]) || empty($arr["username"]) || empty($arr["password"]) || empty($arr["database"]) || empty($arr["prefix"])) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        }

        return true;
    }

    /**
     * Validate form inputs from 2nd part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_2($arr)
    {
        if (empty($arr["D_Port"]) || empty($arr["Folder"]) || empty($arr["UseDP"])) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["D_Port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        } else if ($arr["UseDP"] != "true" && $arr["UseDP"] != "false") {
            self::$lasterror = "Use default port value must be True/False";
            return false;
        } else if ($arr["UseDP"] == "true" && empty($arr["DPassword"])) {
            self::$lasterror = "If you can use default password, you must define it";
            return false;
        }
        return true;
    }


    /**
     * Validate form inputs from 3rd part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_3($arr)
    {
        if (empty($arr["address"]) || empty($arr["username"]) || empty($arr["password"])) {
            self::$lasterror = "Please fill form";
            return false;
        }
        return true;
    }

    /**
     * Validate form inputs from 5th part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_5($arr)
    {
        if (empty($arr["username"]) || empty($arr["password"])) {
            self::$lasterror = "Please fill form";
            return false;
        }
        return true;
    }


    /**
     * Generate random string
     * 
     * @param int $length Length of random string
     * 
     */
    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * Check compatibility
     * 
     */
    public static function checkVersion()
    {
        $errors = array();
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $errors[] = "Please use PHP 5.4.0+";
        }
        if (!extension_loaded("mysqli")) {
            $errors[] = "Please install mysqli extension";
        }
        if (!extension_loaded("curl")) {
            $errors[] = "Please install curl extension";
        }
        if (!extension_loaded("gd")) {
            $errors[] = "Please install gd extension";
        }
        if (!extension_loaded("zip")) {
            $errors[] = "Please install zip extension";
        }
        if (!`which unzip`) {
            $errors[] = "Please install unzip to your linux";
        }
        if (!extension_loaded("SPL")) {
            $errors[] = "Please install SPL extension";
        }
        if (!empty($errors)) {
            $return = "<pre>There was an error(s) checking the extensions and php version<ul>";
            foreach ($errors as $error) {
                $return .= "<li>{$error}</li>";
            }
            $return .= "</ul></pre>";
            die($return);
        }
    }

        /**
     * Check database connection
     * 
     * @param string $address  Address
     * @param int    $port     Port
     * @param string $username Username
     * @param string $password Password
     * @param string $database Database
     * 
     */
    public static function checkConnection($address, $port, $username, $password, $database)
    {

        $conn = @new mysqli($address . ":" . $port, $username, $password);
        if (isset($conn->connect_error)) {
            if ($conn->connect_error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
                $return = "Undefined host.";
            } else if (strpos($conn->connect_error, "access denied for user ") !== false) {
                $return = "Incorrect Login Data";
            } else if ($conn->connect_error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
                $return = "Please use valid host";
            } else if ($conn->connect_error == "php_network_getaddresses: getaddrinfo failed: name or service not known") {
                $return = "Please use valid host";
            } else if (strpos($conn->connect_error, "unknown database") !== false) {
                $return = "Database " . explode("'", $conn->connect_error . PHP_EOL)[1] . " not found! Please create it";
            }
            self::$mysql_error = $return;
            return false;
        }
        $conn->query("USE {$database};");
        if (!empty($conn->error)) {
            if ($conn->error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
                $return = "Undefined host.";
            } else if (strpos($conn->error, "access denied for user ") !== false) {
                $return = "Incorrect Login Data";
            } else if ($conn->error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
                $return = "Please use valid host";
            } else if ($conn->error == "php_network_getaddresses: getaddrinfo failed: name or service not known") {
                $return = "Please use valid host";
            } else if (strpos($conn->error, "unknown database") !== false) {
                $return = "Database " . explode("'", $conn->error . PHP_EOL)[1] . " not found! Please create it";
            }
            self::$mysql_error = $return;
            return false;
        }
        $conn->set_charset("utf8mb4");
        
        return true;
    }

    public static function checkSSH($host, $username, $password)
    {
        $error = null;
        $errorno = null;

        fsockopen($host, 22, $errorno, $error);

        if (!empty($error)) {
            self::$ssh_error = "Can't connect to {$host}:22";
            return false;
        }
        $connection = @ssh2_connect($host, 22);
        
        if (!@ssh2_auth_password($connection, $username, $password)) {
            self::$ssh_error = "Can't auth to {$username}@{$host}";
            return false;
        }
        return true;
    }

    /**
     * Install and prepare administration for use.
     * 
     * @param string $dir Root directory
     * 
     */
    public static function Install_bot($dir)
    {

        if (file_exists(__DIR__ . "/../sinusbot_latest.zip")) {
            unlink(__DIR__ . "/../sinusbot_latest.zip");
        }

        $init = Database::init();

        $main = new Main();

        echo "<script>$(\"#log\").text(\"Prepairing...\");</script>";
        flush();

        sleep(2);


        //-----------------------------

        echo "<script>$(\"#log\").text(\"Prepairing SQL..\");</script>";
        
        $config = Config::init();

        $content = file_get_contents(__DIR__ . "/installer/sql.txt");
        $content = str_replace([
                        "<%DATABASE%>",
                        "<%PREFIX%>",
                        "%bot_port_value%",
                        "%bot_folder%",
                        "%bot_usedp%",
                        "%bot_dpassword%",
                        "%ssh_host%",
                        "%ssh_username%",
                        "%ssh_password%"
                    ], [
                        $config->getConfig("Database/database"),
                        $config->getConfig("Database/prefix"),
                        $config->getConfig("Bot/d_port"),
                        $config->getConfig("Bot/folder"),
                        Main::booltostring($config->getConfig("Bot/usedp")),
                        $config->getConfig("Bot/dpassword"),
                        $config->getConfig("SSH/sshaddress"),
                        $config->getConfig("SSH/sshusername"),
                        $config->getConfig("SSH/sshpassword")
                    ],
        				$content
        		    );

        $sql = explode(";", $content);

        //-------------------------

        $i = 0;
        foreach ($sql as $command){
            $i++;
            echo "<script>$(\"#log\").text(\"Executing SQL commands ({$i})\");</script>";
            if (!empty($command)) {
                $init->execute($command);
            }
        }

        //-------------------------

        echo "<script>$(\"#log\").text(\"Downloading sinusbot_latest.zip...\");</script>";
        
        exec("wget https://proxy.patrick115.eu/bot/sinusbot_latest.zip");

        //--------------------
        echo "<script>$(\"#log\").text(\"Extracting sinusbot_latest.zip...\");</script>";
        

        $zip = new ZipArchive();

        $check = $zip->open(__DIR__ . "/../sinusbot_latest.zip");

        if ($check !== true) {
            parent::catchError("Can't open file " . __DIR__ . "/../sinusbot_latest.zip", debug_backtrace());
            echo "aaa";
        }
        
        $zip->extractTo(__DIR__ . "/../");
        $zip->close();

        //--------------------------------

        echo "<script>$(\"#log\").text(\"Deleting sinusbot_latest.zip\");</script>";
        

        unlink(__DIR__ . "/../sinusbot_latest.zip");

        $exec = [];

        //---------------------------------
        // Preparing commands

        echo "<script>$(\"#log\").text(\"Prepairing SSH commands..\");</script>";
        

        $sdir = Config::init()->getConfig("Bot/folder");

        $exec[] = "id -u sinusbot &>/dev/null || useradd --disable-login sinusbot";
        $exec[] = "mkdir -p {$sdir}";
        $exec[] = "chown -R sinusbot:sinusbot {$sdir}";
        $exec[] = "apt-get update";
        $exec[] = "apt-get install -y x11vnc xvfb libxcursor1 ca-certificates bzip2 libnss3 libegl1-mesa x11-xkb-utils libasound2 libpci3 libxslt1.1 libxkbcommon0 libxss1 curl";
        $exec[] = "update-ca-certificates";
        $exec[] = "apt-get install libglib2.0-0";
        $exec[] = "add-apt-repository universe";
        $exec[] = "apt-get update";
        $exec[] = "cp -r {$dir}/__Install__ {$sdir}/__DEFAULT__";
        $exec[] = "rm -rf {$dir}/__Install__";
        $exec[] = "chown -R sinusbot:sinusbot {$sdir}";

        // -------------------------
        $i = 0;
        foreach ($exec as $command) {
            $i++;
            echo "<script>$(\"#log\").text(\"Executing SSH commands ({$i})\");</script>";

            $main->SSHExecute($command);
        }

        //------------------------

        echo "<script>$(\"#log\").text(\"Creating new user..\");</script>";
        

        $main->createUser($_SESSION["data"]["user"]["username"], $_SESSION["data"]["user"]["password"], Main::getUserIP());

        //-----------------------------

        echo "<script>$(\"#log\").text(\"Cleaning temp files...\");</script>";
        
        foreach (glob(__DIR__ . "/../temp_*.txt") as $file) {
            #unlink($file);
        }

        $file = fopen(__DIR__ . "/installer/install.lock", "w");

        fwrite($file, "Installed at: " . date("H:i:s d.m.Y") . ". Version: " . Main::getVerBuild(true, true) . ".");
        
        fclose($file);

        echo "<script>$(\"#log\").text(\"Done\");</script>";

        Session::destroy();
    }

    public function run_reinstall()
    {

        $init = Database::init();

        $main = new Main();

        $config = Config::init();

        echo "<script>$(\"#install_log\").text(\"Prepairing...\");</script>";
        flush();

        sleep(2);

        $tables = [
            "bots",
            "settings",
            "users"
        ];
        echo "<script>$(\"#install_log\").text(\"Deleting tables...\");</script>";
        foreach ($tables as $table) {
            $init->execute("DROP TABLE IF EXISTS `" . $init->convertTableName($table) . "`");
        }
        echo "<script>$(\"#install_log\").text(\"Deleting sinusbot folder\");</script>";
        $main->SSHExecute("sudo -u sinusbot rm -rf " . $config->getConfig("Bot/folder"));
        echo "<script>$(\"#install_log\").text(\"Deleting lock file...\");</script>";
        unlink(MainDir . "/src/installer/install.lock");
        echo "<script>$(\"#install_log\").text(\"Deleting config file...\");</script>";
        unlink(MainDir . "/src/config/config.php");    
        echo "<script>$(\"#install_log\").text(\"Redirecting...\");</script>";

        Session::destroy();

    }
}