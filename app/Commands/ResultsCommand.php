<?php

namespace App\Commands;

use App\Enums\ResultEnum;
use App\Models\Result;
use App\Models\User;
use App\Responses\MessageSender;
use Illuminate\Support\Facades\DB;

class ResultsCommand extends BaseCommand
{

    protected static string $command_name_path = 'commands.results';


    public function execute(User $user, string $message): void
    {
        $response = new MessageSender(__('info.game_results.header'));
        $results = Result::select('result', DB::raw('count(*) as total'))
            ->where('user_id',$user->id)
            ->groupBy('result')->get();
        foreach ($results as $result){
            $text =match($result->result){
                    ResultEnum::WIN => __('info.game_results.win',['total'=>$result->total]),
                    ResultEnum::LOSE => __('info.game_results.lose',['total'=>$result->total]),
                    ResultEnum::DRAW => __('info.game_results.draw',['total'=>$result->total]),
                };
            $response->addText($text,PHP_EOL);
        }
        $response->send($user);
    }

}
