<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PlayersIntegrityTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoaliePlayersExist ()
    {
/*
		Check there are players that have can_play_goalie set as 1
*/
		$result = User::where('user_type', 'player')->where('can_play_goalie', 1)->count();
		$this->assertTrue($result > 1);

    }
    public function testAtLeastOneGoaliePlayerPerTeam ()
    {
/*
	    calculate how many teams can be made so that there is an even number of teams and they each have between 18-22 players.
	    Then check that there are at least as many players who can play goalie as there are teams
*/

        $players = User::player()->orderBy('ranking')->get();
        $goalies = User::goalie()->orderBy('ranking')->get();

        $numberOfGoalies = count($goalies);
        $numberOfPlayers = count($players);

        $numberOfTeams = ceil(($numberOfGoalies+$numberOfPlayers)/22);
        if($numberOfTeams % 2 == 1) $numberOfTeams--;
        if($numberOfTeams>$numberOfGoalies)
        {
            ($numberOfGoalies % 2 == 1) ? $numberOfTeams = $numberOfGoalies-1 : $numberOfTeams = $numberOfGoalies;
        }

        $this->assertTrue($numberOfGoalies>=$numberOfTeams);

    }
}
