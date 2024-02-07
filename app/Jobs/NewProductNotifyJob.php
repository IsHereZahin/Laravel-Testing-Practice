<?php

namespace App\Jobs;

use App\Models\Products;
use App\Models\User;
use App\Mail\NewProductCreatedMail;
use App\Notifications\NewProductCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewProductNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Products $products)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('Sending notification to everyone about new product' . $this->products->name);

        // Get the admin user
        $admin = User::where('is_admin', 1)->first();

        // Send email notification
        Mail::to($admin->email)->send(new NewProductCreatedMail($this->products));

        // Send notification
        $admin->notify(new NewProductCreatedNotification($this->products));
    }
}
