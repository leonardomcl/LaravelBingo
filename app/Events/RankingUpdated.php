<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RankingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ranking;

    public function __construct($ranking)
    {
        $this->ranking = $ranking;
    }

    public function broadcastOn(): array
    {
        return [new Channel('bingo-channel')];
    }

    public function broadcastAs(): string
    {
        return 'RankingUpdated';
    }
}