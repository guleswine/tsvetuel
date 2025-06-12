<?php

namespace App\Console\Commands;

use App\Commands\ResultsCommand;
use App\Enums\SkillEnum;
use App\Enums\SourceEnum;
use App\Enums\UserStatusEnum;
use App\Models\Player;
use App\Models\User;
use App\Services\Game\FieldService;
use App\Services\Game\GameService;
use App\Services\Game\SkillService;
use App\Services\MessageProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        var_dump(ResultsCommand::getCommand());
        die();
        $computer_first = User::firstOrCreate(
            ['first_name'=>'Компьютер 1','source_id'=>SourceEnum::COMPUTER],
            ['status'=>UserStatusEnum::GAME,'source_user_id'=>0,'active_at'=>now()]);
        $computer_second = User::firstOrCreate(
            ['first_name'=>'Компьютер 2','source_id'=>SourceEnum::COMPUTER],
            ['status'=>UserStatusEnum::GAME,'source_user_id'=>0,'active_at'=>now()]);
        for ($i=0;$i<1000;$i++) {
            $game = GameService::start($computer_first,$computer_second);
            while (!$game->isFieldFilled()){
                if ($game->isMyTurn($computer_first)){
                    $game->makeComputerStep($computer_first);
                }else{
                    $game->makeComputerStep($computer_second);
                }

            }
        }

       // $players = Player::all();
       // $player = $players->get(1);
       //var_dump($player->user);
        //$field = FieldService::makeField();
        //$keyboard = FieldService::convertForKeyboard($field);
        //$keyboard = MessageProcessingService::makeKeyboard($keyboard);
        //var_dump($keyboard);
        //$user = User::find(2);
        //MessageProcessingService::sendResponse($user,'test',$keyboard);
        //var_dump($keyboard);
        //die();
        $misa_test_token = 'vk1.a.rWP60dNapFjm05iAN0G3LkEXuZaILYemlte1RGBJ89co5DFxovR0DqimAD_4sbKDLTtwKnXrYZelm13m3C4WpRMJ-EeiM7MpFhcSDJ_P4UthYBzqIqoxYryzbnnfylFDtv6wW700xaC5LCiuLH62Blr0KPkrotKAaM1bDIz53a8fyG7gjXss1q1fuOYN6X3Pxot_-kF5BvFcskk7sn2Bdw';
        //Http::get('ya.ru');
        //Http::get('https://api.vk.com/method/messages.send?peer_id=408719448&message=test&random_id='.random_int(1,999999999).'&v=5.199&access_token='.$misa_test_token);
        //Http::get('https://api.vk.com/method/messages.send?peer_id=408719448&message=test&random_id='.random_int(1,999999999).'&v=5.199&access_token=vk1.a.zioYKHh5Uin405qc1guHL9Nc1wbjf7JkZZan_Qg-xWBIdjExC4H5_6oLq628jYMCl4iqv8iVBbY2xB9ne7oxyYbZTCYCN5ZTSaWt99P6oR8DZx9qkTFJa17dOKPsok5qMtQriCCXIg-EzMe_YRxRN9vDyvgJv1bZP1ngw0LODktq3dTiZ1QY2oZsINKRdVDMfz1c_deGD8Q-bg4ARQZHmQ');
    }
}
