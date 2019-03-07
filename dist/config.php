<?php
return [

    "app_title" => "Vue Log Manager",
    "app_secret" => "DQBtEzyaVNJDMw2YMSrGMch4fh27JDzy",
    "api_base_url" => "http://127.0.0.1:8800",

    "users" => [
        [
            "username" => "admin",
            "password" => "admin",
            "sources" => ["Hummingbird", "Whitelabel"]
        ],
        [
            "username" => "manager",
            "password" => "manager",
            "sources" => ["Whitelabel"]
        ]
    ],

    "database_sources" => [
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
    ],

    "log_levels" => [
        100 => "DEBUG",
        200 => "INFO",
        250 => "NOTICE",
        300 => "WARNING",
        400 => "ERROR",
        500 => "CRITICAL",
        550 => "ALERT",
        600 => "EMERGENCY"
    ]
];
