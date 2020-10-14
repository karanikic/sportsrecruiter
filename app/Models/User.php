<?php

namespace App\Models;

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model;

class User extends Model

{

    public function scopePlayer($query)
    {
        return $query
            ->where('user_type','=','player')
            ->where('can_play_goalie', 0);
    }

    public function scopeGoalie($query)
    {
        return $query
            ->where('user_type','=','player')
            ->where('can_play_goalie', 1);
    }

    public function createTeam()
    {
        $faker = Faker::create();
        return [
            'team_name' => $faker->company,
            'users' => [],
            'average' => 0,
        ];
    }

    public function generateTeams($numberOfTeams)
    {
        $teams = [];

        for ($i=0;$i<$numberOfTeams; $i++)
        {
            array_push($teams, $this->createTeam());
        }
        return $teams;
    }

    public function calculateNumberOfTeams($numberOfPlayers, $numberOfGoalies)
    {
        $numberOfTeams = ceil(($numberOfGoalies+$numberOfPlayers)/22);
        if($numberOfTeams % 2 == 1) $numberOfTeams--;
        if($numberOfTeams>$numberOfGoalies)
        {
            ($numberOfGoalies % 2 == 1) ? $numberOfTeams = $numberOfGoalies-1 : $numberOfTeams = $numberOfGoalies;
        }

        return $numberOfTeams;
    }

    public static function teamPopulateAlgorithm($numberOfPlayers,$numberOfGoalies, $players, $goalies)
    {
        $team = new self;
        $numberOfTeams = $team->calculateNumberOfTeams($numberOfPlayers, $numberOfGoalies);
        $teams = $team->generateTeams($numberOfTeams);

        $counter = 0;

        for($j=0, $z=$numberOfPlayers-1; $j<$numberOfTeams, $z>=$numberOfPlayers-$numberOfTeams; $j++, $z--) {
            array_push($teams[$counter]['users'], [
                'first_name' => $players[$j]['first_name'],
                'last_name' => $players[$j]['last_name'],
                'ranking' => $players[$j]['ranking'],
                'goalie' => $players[$j]['can_play_goalie']
            ],
            [
                'first_name' => $players[$z]['first_name'],
                'last_name' => $players[$z]['last_name'],
                'ranking' => $players[$z]['ranking'],
                'goalie' => $players[$z]['can_play_goalie']
            ]);
            $counter++;
            if($counter>=$numberOfTeams) {
                $counter=0;
            }
        }
        for($i=$numberOfTeams;$i<$numberOfPlayers-$numberOfTeams; $i++) {
            array_push($teams[$counter]['users'],[
                'first_name' => $players[$i]['first_name'],
                'last_name' => $players[$i]['last_name'],
                'ranking' => $players[$i]['ranking'],
                'goalie' => $players[$i]['can_play_goalie']
            ]);

            $counter++;
            if($counter>=$numberOfTeams) {
                $counter=0;
            }
        }

        for($i=0;$i<$numberOfGoalies; $i++) {
            array_push($teams[$counter]['users'],[
                'first_name' => $goalies[$i]['first_name'],
                'last_name' => $goalies[$i]['last_name'],
                'ranking' => $goalies[$i]['ranking'],
                'goalie' => $goalies[$i]['can_play_goalie']]);
            $counter++;
            if($counter>=$numberOfTeams) {
                $counter=0;
            }
        }
        return $teams;
    }



}
