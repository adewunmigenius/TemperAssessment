<?php


namespace App\Http\Controllers;

use App\Services\UserInsightService;

class UserInsightController extends Controller
{
    public $userInsightService;

    public function __construct(UserInsightService $userInsightService)
    {
        $this->userInsightService = $userInsightService;
    }

    public function load_user_data(){
        $result = $this->userInsightService->load_user_data();
        return response()->json(['status' => true, 'data' => $result], 200);
    }
}