<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "Shutdow/Error/Exception handler",
    "package"       => "Evo",
    "subpackage"    => "shutdown",
    "_buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/Shutdown/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "evo\\shutdown\\exception"  => [
            "subpackage"    => "exception",
            "buildpath"     =>  __DIR__.'/exception/',
            "extends"       => '\\ErrorException',
            "interfaces"    => [
                "ShutdownExceptionInterface"
            ],
            'implements' =>[
                    'evo\\shutdown\\exception\\ShutdownExceptionInterface'
            ],
            "exceptions" => [
                "900"   => "ShutdownException",
                "909"   => "RuntimeError",
                "999"   => "InvalidCallback"
            ]
        ]
    ]
];
