<?php

namespace App\Services\Game;

class FigureService
{

    public static function getFigures($count = 3,$first_small = false)
    {

        $forms = range(1, 18);
        shuffle($forms);
        $forms = array_slice($forms, 0, $count);
        $forms[0]=random_int(51,62);
        if($first_small) {
            $forms[0]=50;
            $forms[1]=random_int(51,62);
        }
        return implode(',', $forms);
    }

    public static function getSmallFigures(): array
    {
        return range(51,62);
    }

    public static function getFigure(array $current_figures = [],$strongest_chance = 0)
    {
        $chance = random_int(1,100);
        if ($chance <= $strongest_chance){
            return 0;
        }
        $figures = range(1, 18);
        shuffle($figures);
        $diff = array_diff($figures, $current_figures);
        return current($diff);
    }

    public static function printFigures(array $figures)
    {
        $text = '';
        $print = [];
        foreach ($figures as $figure) {
                $one_print = self::printFigure($figure);
                $print_rows = explode(PHP_EOL, $one_print);
                foreach ($print_rows as $row=>$print_row) {
                    $print[$row][] = $print_row;
                }

        }
        foreach ($print as $row=>$cols) {
            if ($text) {
                $text .= PHP_EOL;
            }
            $text .= implode('      ',$cols);
        }
        return $text;
    }

    public static function printFigure($figure)
    {
        $text = '';
        $emoji_form = [
            ['&#9898;', '&#9898;', '&#9898;'],
            ['&#9898;', '&#128070;', '&#9898;'],
            ['&#9898;', '&#9898;', '&#9898;'],
        ];
        $form = self::getFigurePosition($figure);
        foreach ($form as $cell) {
            $str = $cell['str'] + 1;
            $clm = $cell['clm'] + 1;
            $emoji_form[$str][$clm] = '&#9899;';
        }
        foreach ($emoji_form as $row) {
            if ($text) {
                $text .= PHP_EOL;
            }
            foreach ($row as $item) {

                $text .= $item;
            }
        }

        return $text;
    }

    public static function getFigurePosition($figure)
    {
        $form_cells = [];
        $form_cells[0] = [
            ['str'=>0, 'clm'=>0],
        ];
        $form_cells[1] = [
            ['str'=>-1, 'clm'=>0], //O X O
            ['str'=>0, 'clm'=>1],  //X O X
            ['str'=>0, 'clm'=>-1], //O O O
        ];
        $form_cells[2] = [
            ['str'=>1, 'clm'=>0],  //O O O
            ['str'=>0, 'clm'=>1],  //X O X
            ['str'=>0, 'clm'=>-1], //O X O
        ];
        $form_cells[3] = [
            ['str'=>1, 'clm'=>-1], //O O X
            ['str'=>-1, 'clm'=>1], //O O O
            ['str'=>1, 'clm'=>1],  //X O X

        ];
        $form_cells[4] = [
            ['str'=>1, 'clm'=>1],   //X O O
            ['str'=>-1, 'clm'=>-1], //O O O
            ['str'=>1, 'clm'=>-1],  //X O X
        ];
        $form_cells[5] = [
            ['str'=>0, 'clm'=>1],   //X O O
            ['str'=>-1, 'clm'=>-1], //O O X
            ['str'=>1, 'clm'=>-1],  //X O O
        ];
        $form_cells[6] = [
            ['str'=>1, 'clm'=>1],  //O O X
            ['str'=>-1, 'clm'=>1], //X O O
            ['str'=>0, 'clm'=>-1], //O O X
        ];
        $form_cells[7] = [
            ['str'=>-1, 'clm'=>0], //O X O
            ['str'=>1, 'clm'=>0],  //O O X
            ['str'=>0, 'clm'=>1],  //O X O
        ];
        $form_cells[8] = [
            ['str'=>-1, 'clm'=>0], //O X O
            ['str'=>1, 'clm'=>0],  //X O O
            ['str'=>0, 'clm'=>-1], //O X O
        ];
        $form_cells[9] = [
            ['str'=>-1, 'clm'=>0], //O X O
            ['str'=>0, 'clm'=>-1], //X O O
            ['str'=>1, 'clm'=>1],  //O O X
        ];
        $form_cells[10] = [
            ['str'=>-1, 'clm'=>0], //O X O
            ['str'=>0, 'clm'=>1],  //O O X
            ['str'=>1, 'clm'=>-1], //X O O
        ];
        $form_cells[11] = [
            ['str'=>1, 'clm'=>0],  //O O O
            ['str'=>0, 'clm'=>-1], //X O O
            ['str'=>1, 'clm'=>-1], //X X O
        ];
        $form_cells[12] = [
            ['str'=>-1, 'clm'=>0], //O X X
            ['str'=>-1, 'clm'=>1], //O O X
            ['str'=>0, 'clm'=>1],  //O O O
        ];
        $form_cells[13] = [
            ['str'=>0, 'clm'=>-1], //O O O
            ['str'=>0, 'clm'=>1],  //X O X
            ['str'=>1, 'clm'=>-1], //X O O
        ];
        $form_cells[14] = [
            ['str'=>-1, 'clm'=>1], //O O X
            ['str'=>0, 'clm'=>1],  //X O X
            ['str'=>0, 'clm'=>-1], //O O O
        ];
        $form_cells[15] = [
            ['str'=>1, 'clm'=>-1], //O O O
            ['str'=>1, 'clm'=>0],  //O O O
            ['str'=>1, 'clm'=>1],  //X X X
        ];
        $form_cells[16] = [
            ['str'=>-1, 'clm'=>-1], //X X X
            ['str'=>-1, 'clm'=>0],  //O O O
            ['str'=>-1, 'clm'=>1],  //O O O
        ];
        $form_cells[17] = [
            ['str'=>-1, 'clm'=>1], //O O X
            ['str'=>0, 'clm'=>1],  //O O X
            ['str'=>1, 'clm'=>1],  //O O X
        ];
        $form_cells[18] = [
            ['str'=>-1, 'clm'=>-1], //X O O
            ['str'=>0, 'clm'=>-1],  //X O O
            ['str'=>1, 'clm'=>-1],  //X O O
        ];
        $form_cells[50] = [
            ['str'=>0, 'clm'=>0],
        ];
        $form_cells[51] = [        //O O O
            ['str'=>0, 'clm'=>1],  //X O X
            ['str'=>0, 'clm'=>-1], //O O O
        ];
        $form_cells[52] = [         //O X O
            ['str'=>-1, 'clm'=>0],  //O O O
            ['str'=>1, 'clm'=>0],   //O X O
        ];
        $form_cells[53] = [         //X O O
            ['str'=>-1, 'clm'=>-1], //O O O
            ['str'=>1, 'clm'=>1],   //O O X
        ];
        $form_cells[54] = [         //O O X
            ['str'=>-1, 'clm'=>1],  //O O O
            ['str'=>1, 'clm'=>-1],  //X O O
        ];
        $form_cells[55] = [        //O O O
            ['str'=>0, 'clm'=>0],  //O X X
            ['str'=>0, 'clm'=>1],  //O O O
        ];
        $form_cells[56] = [         //O X O
            ['str'=>0, 'clm'=>0],   //O X O
            ['str'=>-1, 'clm'=>0],  //O O O
        ];
        $form_cells[57] = [         //O X O
            ['str'=>-1, 'clm'=>0],  //O O O
            ['str'=>1, 'clm'=>-1],  //X O O
        ];
        $form_cells[58] = [         //O X O
            ['str'=>-1, 'clm'=>0],  //O O O
            ['str'=>1, 'clm'=>1],   //O O X
        ];
        $form_cells[59] = [         //X O O
            ['str'=>-1, 'clm'=>-1], //O O X
            ['str'=>0, 'clm'=>1],   //O O O
        ];
        $form_cells[60] = [         //O O X
            ['str'=>-1, 'clm'=>1],  //X O O
            ['str'=>0, 'clm'=>-1],  //O O O
        ];
        $form_cells[61] = [         //O O X
            ['str'=>-1, 'clm'=>1],  //O X O
            ['str'=>0, 'clm'=>0],   //O O O
        ];
        $form_cells[62] = [         //X O O
            ['str'=>-1, 'clm'=>-1], //O X O
            ['str'=>0, 'clm'=>0],   //O O O
        ];

        return $form_cells[$figure];
    }
}
