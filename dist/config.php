<?php
return [

    "app_title" => "Vue Log Manager",
    "app_secret" => "DQBtEzyaVNJDMw2YMSrGMch4fh27JDzy",
    "api_base_url" => "http://127.0.0.1:8800",

    "users" => [
        [
            "username" => "admin",
            "password" => "admin",
            "connections" => ["Hummingbird", "Whitelabel"]
        ],
        [
            "username" => "manager",
            "password" => "manager",
            "connections" => ["Whitelabel"]
        ]
    ],

    "database_connections" => [
        "Hummingbird" => [
            "host" => "localhost",
            "name" => "my_logs",
            "table" => "hummingbird",
            "user" => "root",
            "pass" => "root"
        ],
        "Whitelabel" => [
            "host" => "localhost",
            "name" => "my_logs",
            "table" => "whitelabel",
            "user" => "root",
            "pass" => "root"
        ]
    ]
];
