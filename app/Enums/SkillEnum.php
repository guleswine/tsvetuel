<?php

namespace App\Enums;

enum SkillEnum: int
{
    case VORTEX = 1 << 0; //Вихрь
    case ESPIONAGE = 1 << 1; //Шпионаж
    case DESTRUCTION = 1 << 2;//Уничтожение
    case RECOVERY = 1 << 3; //Восстановление
    case SKIP_FIGURE = 1 << 4; //Пропуск фигуры
    case MY_FIGURE = 1 << 5; //Выбор

    public static function total()
    {
        $all = 0;
        foreach (self::cases() as $skill){
            $all |= $skill->value;
        };
        return $all;

    }

    public static function getSkills(int $level): int
    {
        $skills = 0;
        if( $level > 1){
            $level_bit = 1<< ($level-2);
            foreach (self::cases() as $skill){
                if($skill->value<=$level_bit){
                    $skills = $skill->add($skills);
                }
            }
        }

        return $skills;
    }

    public function getName()
    {
        return __('commands.skills.'.mb_strtolower($this->name));
    }
    public function isAvailable(int $skills): bool
    {
        return ($skills & $this->value) === $this->value;
    }

    public function add(int $skills): int
    {
        return $skills | $this->value;
    }

    public function remove(int $skills): int
    {
        return $skills & (~$this->value);
    }
}



