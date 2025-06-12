<?php

namespace App\Responses;

use App\Enums\SourceEnum;
use App\Models\User;
use App\Services\Sources\VkService;

class MessageSender
{

    private bool $keyboard_onetime = false;
    private bool $keyboard_inline = false;
    public function __construct(
        private string $message = '',
        private array|null $keyboard = null,
        private array $attachments = [],
    )
    {

    }

    public function inline(): MessageSender
    {
        $this->keyboard_inline = true;
        return $this;
    }

    public function onetime(): MessageSender
    {
        $this->keyboard_onetime = true;
        return $this;
    }

    public function addButtons(array $buttons): MessageSender
    {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function mergeButtons(array $buttons): MessageSender
    {
        $this->keyboard = array_merge($this->keyboard, $buttons);
        return $this;
    }

    public function setButtons(array $buttons): MessageSender
    {
        $this->keyboard = $buttons;
        return $this;
    }

    public function addPrefixText(string $text,string $padding = ''): MessageSender
    {
        $this->message =$text.$padding.$this->message;
        return $this;
    }

    public function addText(string $text,string $padding = ''): MessageSender
    {
        $this->message .=$padding.$text;
        return $this;
    }

    public function addAttachments(array $attachments): MessageSender
    {
        $this->attachments[] = $attachments;
        return $this;
    }

    public function send(User $user):void
    {
        switch ($user->source_id){
            case SourceEnum::VK:
                VkService::sendMessage($user,$this->message,$this->convertButtons(),$this->attachments);
                break;
        }
    }

    public function convertButtons()
    {
        if ($this->keyboard===null){
            return null;
        }
        $buttons = [];
        foreach ($this->keyboard as $i=>$str) {
            foreach ($str as $k=>$key) {
                $buttons[$i][$k]['type'] = 'text';
                if (isset($key['text'])){
                    $buttons[$i][$k]['label'] = $key['text'];
                }else{
                    $buttons[$i][$k]['label'] = (is_string($key) ? $key : null);
                }
                if (!isset($key['payload'])) {
                    $buttons[$i][$k]['payload'] = null;
                }else{
                    $buttons[$i][$k]['payload'] = $key['payload'];
                }
                if (!isset($key['color'])) {
                    $buttons[$i][$k]['color'] = 'default';
                }else{
                    $buttons[$i][$k]['color'] = $key['color'];
                }
                //unset($keys[$i][$k]['text']);
            }
        }
        $keyboard['buttons'] = $buttons;
        $keyboard['one_time'] = $this->keyboard_onetime;
        $keyboard['inline'] = $this->keyboard_inline;
        return  $keyboard;
    }



}
