<?php

namespace App\Http\Controllers\Sources;

use App\Http\Controllers\Controller;
use App\Services\Sources\VkService;
use Illuminate\Http\Request;

class VkController extends Controller
{
    public function store(Request $request)
    {
        //echo 'ok';
        $type = $request->input('type');
        $object = $request->input('object');
        switch ($type) {
            case "confirmation":
                $this->confirmation();
                break;
            case "message_new":
                $this->finish();
                VkService::messageHandler($object);
                break;
            default:
                $this->finish();
        }
    }

    public function confirmation(){
        echo VkService::getGroupConfirmCode();
    }

    public function finish()
    {
        echo 'ok';
        \fastcgi_finish_request();
    }
}
