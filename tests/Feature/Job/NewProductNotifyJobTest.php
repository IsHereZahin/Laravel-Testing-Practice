<?php

namespace Tests\Feature\Job;

use App\Jobs\NewProductNotifyJob;
use App\Mail\NewProductCreatedMail;
use App\Models\User;
use App\Notifications\NewProductCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewProductNotifyJobTest extends TestCase
{

    use RefreshDatabase;

    public function test_product_create_job_notification_dispatched_successfully(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $product = [
            'name' => 'Test Product',
            'price' => 123,
            'image' => 'test.jpg',
        ];

        $response = $this->followingRedirects()->actingAs($admin)->post('/product/store', $product);
        $response->assertOk();

        Bus::assertDispatched(NewProductNotifyJob::class);
    }

    public function test_product_create_mail_send_successfully(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);

        $product = [
            'name' => 'Product Name',
            'price' => 123,
            'image' => 'test.jpg',
        ];

        $response = $this->followingRedirects()->actingAs($admin)->post('/product/store', $product);
        $response->assertStatus(200);

        Mail::assertSent(NewProductCreatedMail::class);
        Notification::assertSentTo($admin, NewProductCreatedNotification::class);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }
}
