<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "Shutdow/Error/Exception handler",
    "package"       => "Evo",
    "subpackage"    => "shutdown",
    "buildpath"     => __DIR__.DIRECTORY_SEPARATOR.'src',
    "support"       => "https://github.com/ArtisticPhoenix/Shutdown/issues",
    "version"       => "1.0.0",
    "_reserved"       => [1,2,[8,20]],
    "namespaces"     => [  
        "evo\\exception"  => [
            "subpackage"    => "exception",
            "buildpath"     =>  ["psr"=>4],
            "interfaces"    => [
                "EvoExceptionInterface"
            ]
        ],
        "evo\\shutdown\\exception" => [
            "buildpath"     =>  ["psr"=>4],
            "extends"       => '\\ErrorException',
            "interfaces"    => [
                "EvoShutdownExceptionInterface"
            ],
            'implements' =>[
                'evo\\shutdown\\exception\\EvoShutdownExceptionInterface',
                'evo\\exception\\EvoExceptionInterface'
            ],
            "exceptions" => [
                "900"   => "EvoShutdownError",
                "950"   => "EvoShutdownRuntimeError",
                "999"   => "EvoShutdownInvalidCallback"
            ]
        ] 
    ]
];
