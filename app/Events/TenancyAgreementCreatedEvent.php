<?php

namespace App\Events;

use App\Models\TenancyAgreement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenancyAgreementCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tenancyAgreement;

    /**
     * Create a new event instance.
     */
    public function __construct(TenancyAgreement $tenancyAgreement)
    {
        $this->tenancyAgreement = $tenancyAgreement;
        \Log::info("Event dispatched");
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return array<int, \Illuminate\Broadcasting\Channel>
//     */
//    public function broadcastOn(): array
//    {
//        return [
//            new PrivateChannel('channel-name'),
//        ];
//    }
}
