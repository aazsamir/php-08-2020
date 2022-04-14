<?php

namespace App\Http\Controllers;

use App\Library\BarTitles;
use App\Library\BazTitles;
use App\Library\FooTitles;
use App\Library\SystemTitles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTitles(Request $request): JsonResponse
    {
        $system_actions = [
            BarTitles::class,
            BazTitles::class,
            FooTitles::class
        ];

        $titles = [];

        foreach ($system_actions as $system_action) {
            /** @var SystemTitles */
            $handler = new $system_action;
            $system_titles = $handler->get();
            if ($system_titles && is_array($system_titles)) {
                $titles = array_merge($titles, $system_titles);
            } else {
                return response()->json([
                    'status' => 'failure',
                ]);
            }
        }

        return response()->json($titles);
    }
}
