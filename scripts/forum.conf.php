<?php


$maxqueries = 20; // Max topic / post by pages
$minfloodtime = 15; // Minimum time beetween two post
$enablesidecheck = false; // if you dont use side specific forum, desactive it, because it will do one less query.

$forum_skeleton = [
    1 => [
        "name" => "Server Category",
        "forums" => [
            1 => [
                "name" => "News",
                "desc" => "News and infos about the server",
                "level_post_topic" => 3
            ],
            2 => [
                "name" => "General Talks",
                "desc" => "Talk about everything related to the server"
            ]
        ]
    ],
    2 => [
        "name" => "Game Category",
        "forums" => [
            3 => [
                "name" => "Bugs and problems",
                "desc" => "Ask here help from GM or Admin, not to beg money item or xp, thx.",
            ],
            4 => [
                "name" => "Horde and alliance forums",
                "desc" => "Talk about everything related to the game"
            ],
            5 => [
                "name" => "Horde forum only",
                "desc" => "Only horde players can see this",
                "side_access" => "H"
            ],
            6 => [
                "name" => "Alliance forum only",
                "desc" => "Only alliance players can see this",
                "side_access" => "A"
            ],
            7 => [
                "name" => "Admins forums only",
                "desc" => "Only admins can see this",
                "level_read" => "3",
                "level_post" => "3"
            ]
        ]
    ]
];
?>