<?php

namespace Idylla\Installer;

use Composer\Script\CommandEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

class Install
{
    /**
     * execute Idylla installation functions
     *
     * @param CommandEvent $event
     */
    public static function run(CommandEvent $event)
    {
        (new ConsoleOutput)->writeln('<info>Installation started.</info>');
    }
}
