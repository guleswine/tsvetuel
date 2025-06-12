<?php

namespace App\Services\Game;

use App\Enums\ColorEnum;

class FieldService
{

    public static function getRows():int
    {
        return config('game.field.rows');
    }

    public static function getCols():int
    {
        return config('game.field.cols');
    }

    public static function totalCells(): int
    {
        return self::getRows()*self::getCols();
    }
    public static function makeField()
    {
        $field = [];
        $counter = 0;
        for ($r = 0; $r < self::getRows(); $r++) {
            for ($c = 0; $c < self::getCols(); $c++) {
                $counter++;
                $field[$r][$c][$counter] = 0;
            }
        }
        $field = self::shuffleField($field);
        return $field;
    }

    public static function iterateCells(array $field): \Generator
    {
        foreach ($field as $r=>$row) {
            foreach ($row as $c => $cell) {
                yield key($cell) => ColorEnum::from(current($cell));
            }
        }
    }

    public static function shuffleField($field)
    {
        foreach ($field as $str=>$row) {
            shuffle($row);
            $field[$str] = $row;
        }

        for ($i = 0; $i < self::getCols(); $i++) {
            $column = [];
            for ($k = 0; $k < self::getRows(); $k++) {
                $column[] = $field[$k][$i];
            }
            shuffle($column);
            for ($k = 0; $k < self::getRows(); $k++) {
                $field[$k][$i] = $column[$k];
            }
        }
        //shuffle($field);
        return $field;
    }

    public static function getFreeCellsCount($field):int
    {
        $counter = 0;
        foreach (self::iterateCells($field) as $cell=>$color){
            if ($color==ColorEnum::EMPTY){
                $counter++;
            }
        }
        return $counter;
    }

    public static function getNotFilledCells($field): array
    {
        $cells = [];
        foreach (self::iterateCells($field) as $cell=>$color){
            if ($color!=ColorEnum::FILLED){
                $cells[$cell]=$color;
            }
        }
        return $cells;
    }

    public static function getNotFilledCellsCount($field):int
    {
        $counter = 0;
        foreach (self::iterateCells($field) as $cell=>$color){
            if ($color!=ColorEnum::FILLED){
                $counter++;
            }
        }
        return $counter;
    }

    public static function mixColor($first_color,$second_color): ColorEnum
    {
        if ($first_color==ColorEnum::EMPTY){
            return $second_color;
        }elseif($first_color==$second_color and $first_color<>ColorEnum::EMPTY){
            return ColorEnum::FILLED;
        }elseif ($first_color==ColorEnum::FILLED){
            return ColorEnum::FILLED;
        }else{
            return ColorEnum::EMPTY;
        }
    }

    public static function convertToButtons($field)
    {
        $keyboard = [];
        foreach ($field as $r=>$row) {
            foreach ($row as $c=>$col) {
                $number = key($col);
                $color = current($col);
                $color_text = match ($color) {
                    ColorEnum::EMPTY->value=>'secondary',
                    ColorEnum::RED->value=>'negative',
                    ColorEnum::GREEN->value=>'positive',
                    ColorEnum::FILLED->value=>'primary',
                };
                $keyboard[$r][$c] =['text'=>$number, 'color'=>$color_text];
                //$keyboard[$row][$col] =['text'=>$number, 'color'=>$color_text];
            }
        }
        return $keyboard;
    }
}
