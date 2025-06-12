<?php

namespace App\Commands;


abstract class BaseCommand
{
    protected static string $command_name_path;

    public function matches(string $message): bool
    {
        return in_array($this->cleanMessage($message),$this->getLowerCommands());
    }


    protected function cleanMessage(string $message): string
    {
        return mb_trim(mb_strtolower($message));
    }

    protected function getLowerCommands(): array
    {
        $text = __($this::$command_name_path);
        $array = explode('|',$text);
        return array_map('mb_strtolower', $array);
    }

    public static function getCommand(): string
    {
        $text = __(static::$command_name_path);
        $array = explode('|',$text);
        return current($array);
    }
}
