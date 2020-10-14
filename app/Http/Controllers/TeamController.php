<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TeamController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show()
    {

        $players = User::player()->orderBy('ranking')->get();
        $goalies = User::goalie()->orderBy('ranking')->get();

        $teams = User::teamPopulateAlgorithm(count($players), count($goalies), $players, $goalies);

        for($i=0; $i<count($teams); $i++)
        {
            $sumRanking = 0;
            foreach ($teams[$i]['users'] as $user)
            {
                $sumRanking = $sumRanking + $user['ranking'];
            }
            $teams[$i]['average']= round($sumRanking/count($teams[$i]['users']),2);
        }

        $data = [
            'teams' => $teams
        ];

        return view('welcome', $data);
    }
}
