<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerWon implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playerName;

    /**
     * @param string $playerName
     */
    public function __construct($playerName)
    {
        $this->playerName = $playerName;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('bingo-channel'),
        ];
    }

   
}