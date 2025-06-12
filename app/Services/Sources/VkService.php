<?php

namespace App\Services\Sources;

use App\Enums\SourceEnum;
use App\Enums\UserStatusEnum;
use App\Handlers\MainHandler;
use App\Models\User;
use VK\Client\VKApiClient;

class VkService
{


    public static function messageHandler($object)
    {
        $message=$object['message'];
        $text = $message['text']??'';
        $user_array = self::getUser($message['from_id']);
        MainHandler::processing($user_array['user'], $text,$user_array['first_time']);
    }


    public static function attachmentProcessing(VKApiClient $vk, $attachments, $peer_id = null, $type = 'message')
    {
        $vk_attachs = [];
        foreach ($attachments as $attach) {
            switch ($attach['type']) {
                case 'photo':
                    switch ($type) {
                        case 'message':
                            $address = $vk->photos()->getMessagesUploadServer(VkService::getGroupToken(), ['peer_id'=>$peer_id]);
                            $photo = $vk->getRequest()->upload($address['upload_url'], 'photo', public_path($attach['path']));
                            if (!isset($photo['server'])) {
                                $address = $vk->photos()->getMessagesUploadServer(VkService::getGroupToken());
                                $photo = $vk->getRequest()->upload($address['upload_url'], 'photo', public_path($attach['path']));
                            }
                            try {
                                $response_save_photo = $vk->photos()->saveMessagesPhoto(VkService::getGroupToken(), [
                                    'server' => $photo['server'],
                                    'photo' => $photo['photo'],
                                    'hash' => $photo['hash'],
                                ]);
                            } catch (\Exception) {
                                $response_save_photo = $vk->photos()->saveMessagesPhoto(VkService::getGroupToken(), [
                                    'server' => $photo['server'],
                                    'photo' => $photo['photo'],
                                    'hash' => $photo['hash'],
                                ]);
                            }

                            break;
                    }

                    $file = array_pop($response_save_photo);
                    $vk_attachs[] = "photo$file[owner_id]_$file[id]";
                    break;
            }

        }

        return implode(',', $vk_attachs);
    }

    public static function keyboardProcessing($keyboard)
    {
        foreach ($keyboard['buttons'] as $skey => $str) {
            foreach ($str as $ikey => $item) {
                $new_key = [];
                $new_key['action']['label'] = $item['label'];
                $new_key['action']['type'] = $item['type'];

                $new_key['action']['payload'] = $item['payload'];
                if ($new_key['action']['payload']) {
                    $new_key['action']['payload'] = json_encode($item['payload']);
                }
                $new_key['color'] = $item['color'];
                $keyboard['buttons'][$skey][$ikey] = $new_key;
            }
        }

        return json_encode($keyboard);
    }

    public static function sendMessage(User $user, string $text,  $keyboard = null,$attachments = null)
    {
        $vk = new VKApiClient();
        $params = [];
        $params['peer_id'] = $user->source_user_id;
        $params['random_id'] = random_int(1, 100000000);
        $params['message'] = $text;
        if ($keyboard) {
            $params['keyboard'] = self::keyboardProcessing($keyboard);
        }
        if ($attachments) {
            $params['attachment'] = self::attachmentProcessing($vk, $attachments, $user->source_user_id);
        }

        $resp = $vk->messages()->send(VkService::getGroupToken(), $params);

    }

    public static function getUser($source_user_id)
    {
        $first_time = false;
        $user = User::where('source_id', SourceEnum::VK)
            ->where('source_user_id', $source_user_id)
            ->first();
        if (!$user) {
            $vk = new VKApiClient();
            $users_get_response = $vk->users()->get(self::getGroupToken(), ['user_id'=>$source_user_id, 'fields' =>'city']);
            if (!$users_get_response) {
                return false;
            }
            $vkuser = array_pop($users_get_response);
            $user = new User();
            $user->source_id = 1;
            $user->first_name = $vkuser['first_name'];
            $user->last_name = $vkuser['last_name'];
            $user->status = UserStatusEnum::LOBBY;
            //  $user->disable = 0;
            //  $user->role = 0;
            $user->source_user_id = $source_user_id;
            $user->active_at =now();
            $user->save();
            $user->refresh();
            $first_time = true;
        }

        return ['user'=>$user, 'first_time'=>$first_time];
    }

    public static function getGroupToken()
    {
        return env('VK_GROUP_TOKEN');
    }

    public static function getGroupConfirmCode()
    {
        return env('VK_GROUP_CONFIRM_CODE');
    }

}
