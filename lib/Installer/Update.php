<?php

namespace Idylla\Installer;

use Composer\Script\CommandEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

class Update
{
    /**
     * execute Idylla update and downgrade functions
     *
     * @param CommandEvent $event
     */
    public static function run(CommandEvent $event)
    {
        (new ConsoleOutput)->writeln('<info>Update started.</info>');
    }
}
