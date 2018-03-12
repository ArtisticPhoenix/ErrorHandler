<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "Exception/Error/Shutdown handler",
    "package"       => "Evo",
    "subpackage"    => "ErrorHandler",
    "buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "evo\\Shutdown\\Exception"  => [
            "subpackage"    => "Exception",
            "interfaces"    => [
                "eJinnExceptionInterface"
            ],
            'implements' =>[
                'evo\\Shutdown\\Exception\\ShutdownExceptionInterface'
            ],
            "exceptions" => [
                2000 => 'Shutdown'
            ]
        ]
    ]
];