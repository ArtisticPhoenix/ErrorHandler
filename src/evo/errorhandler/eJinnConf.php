<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "eJinn The Exception Genie",
    "package"       => "Evo",
    "subpackage"    => "ErrorHandler",
    "_buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "evo\\errorhandler\\Exception"  => [
            "subpackage"    => "Exception",
            "buildpath"     =>  __DIR__.'/Exception/',
            "extends"       => "\\ErrorException",
            "severity"      => E_ERROR,
            "interfaces"    => [
                "errorhandlerExceptionInterface"
            ],
            'implements' =>[
                 'evo\\errorhandler\\Exception\\errorhandlerExceptionInterface'
            ],
            "exceptions" => [
                900 => 'ShutdownError',
                909 => 'RuntimeError'
            ]
        ]
    ]
];
