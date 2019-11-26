<?php

$config = [

    /**
     * Database Config Section
     */
    "Database" => [
        /**
         * Database Address, without port
         */
        "address" => "127.0.0.1",

        /**
         * Database Port
         */
        "port" => 3306,
        
        /**
         * Database User
         */
        "username" => "user",

        /**
         * Database Password
         */
        "password" => "example",

    ],

    /**
     * Bot Config Section
     */

    "Bot" => [

        /**
         * Starting port
         * Port, where first bot start, new bots have automaticly port one more.
         * 
         * Example: D_Port = 9987, new bot have port 9988...
         */
        "D_Port" => 9987,

        /**
         * Bot install folder
         *
         */
        "Folder" => "/opt",

        /**
         * Use default password?
         *
         */
        "UseDP" => true,

        /**
         * Default password of bot, if UseDP is true
         *
         */
        "DPassword" => "example123456",

    ]
];