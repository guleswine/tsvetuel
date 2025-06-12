<?php

namespace App\Services\Game;

use App\Enums\SkillEnum;

class SkillService
{


    public static function getSkillsButtons(int $skills)
    {
        $skills_buttons = [];
        $counter = 0;
        foreach (SkillEnum::cases() as $skill){
            if($skill->isAvailable($skills)){

                $skills_buttons[intdiv($counter,2)][] = $skill->getName();
                $counter++;
            };
        }
        return $skills_buttons;
    }

}
