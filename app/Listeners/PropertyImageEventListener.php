<?php

namespace App\Listeners;

use App\Events\PropertyImageUploadProcessed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class PropertyImageEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PropertyImageUploadProcessed $event)
    {
        Storage::delete($event->originalImage);
    }
}
