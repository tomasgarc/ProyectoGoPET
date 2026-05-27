<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CareRequest;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the request owner can access the checkout page.
     */
    public function test_owner_can_access_checkout_page(): void
    {
        $owner = User::factory()->create();
        $caretaker = User::factory()->create();
        
        $careRequest = CareRequest::create([
            'user_id' => $owner->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 100.00,
            'description' => 'Test care request',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($owner)
            ->get(route('payments.checkout', [
                'care_request' => $careRequest,
                'caretaker_id' => $caretaker->id,
            ]));

        $response->assertOk();
        $response->assertViewIs('payments.checkout');
        $response->assertSee($caretaker->name);
        $response->assertSee('100');
    }

    /**
     * Test that non-owners are denied access to the checkout page.
     */
    public function test_non_owner_cannot_access_checkout_page(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $caretaker = User::factory()->create();
        
        $careRequest = CareRequest::create([
            'user_id' => $owner->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 100.00,
            'description' => 'Test care request',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->get(route('payments.checkout', [
                'care_request' => $careRequest,
                'caretaker_id' => $caretaker->id,
            ]));

        $response->assertStatus(403);
    }

    /**
     * Test that executing the checkout form creates a payment record in escrow.
     */
    public function test_payment_processing_creates_escrow_payment(): void
    {
        $owner = User::factory()->create();
        $caretaker = User::factory()->create();
        
        $careRequest = CareRequest::create([
            'user_id' => $owner->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 120.00,
            'description' => 'Test care request',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($owner)
            ->post(route('payments.process', $careRequest), [
                'caretaker_id' => $caretaker->id,
                'card_name' => 'John Doe',
                'card_number' => '4000 1234 5678 9010',
                'card_expiry' => '12/28',
                'card_cvv' => '123',
            ]);

        $response->assertRedirect(route('care-requests.show', $careRequest));
        $response->assertSessionHas('success');

        $careRequest->refresh();
        $this->assertEquals('accepted', $careRequest->status);
        $this->assertEquals($caretaker->id, $careRequest->accepted_by);

        $payment = Payment::where('care_request_id', $careRequest->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('escrow', $payment->status);
        $this->assertEquals(120.00, $payment->amount);
        $this->assertEquals(12.00, $payment->fee); // 10% platform fee
        $this->assertEquals(108.00, $payment->net_amount);
        $this->assertEquals('9010', $payment->card_last_four);
        $this->assertStringStartsWith('ch_', $payment->transaction_id);
    }

    /**
     * Test that releasing payment changes the status of both payment and request.
     */
    public function test_payment_release_finalizes_request_and_payout(): void
    {
        $owner = User::factory()->create();
        $caretaker = User::factory()->create();
        
        $careRequest = CareRequest::create([
            'user_id' => $owner->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 80.00,
            'description' => 'Test care request',
            'status' => 'accepted',
            'accepted_by' => $caretaker->id,
        ]);

        $payment = Payment::create([
            'care_request_id' => $careRequest->id,
            'user_id' => $owner->id,
            'receiver_id' => $caretaker->id,
            'amount' => 80.00,
            'fee' => 8.00,
            'net_amount' => 72.00,
            'status' => 'escrow',
            'card_last_four' => '1234',
            'transaction_id' => 'ch_test123',
        ]);

        $response = $this
            ->actingAs($owner)
            ->post(route('payments.release', $careRequest));

        $response->assertRedirect(route('care-requests.show', $careRequest));
        $response->assertSessionHas('success');

        $careRequest->refresh();
        $payment->refresh();

        $this->assertEquals('finalized', $careRequest->status);
        $this->assertEquals('released', $payment->status);
    }

    /**
     * Test that cancelling/refunding a request reverts it to pending.
     */
    public function test_payment_refund_resets_care_request_to_pending(): void
    {
        $owner = User::factory()->create();
        $caretaker = User::factory()->create();
        
        $careRequest = CareRequest::create([
            'user_id' => $owner->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 150.00,
            'description' => 'Test care request',
            'status' => 'accepted',
            'accepted_by' => $caretaker->id,
        ]);

        $payment = Payment::create([
            'care_request_id' => $careRequest->id,
            'user_id' => $owner->id,
            'receiver_id' => $caretaker->id,
            'amount' => 150.00,
            'fee' => 15.00,
            'net_amount' => 135.00,
            'status' => 'escrow',
            'card_last_four' => '9999',
            'transaction_id' => 'ch_test456',
        ]);

        $response = $this
            ->actingAs($owner)
            ->post(route('payments.refund', $careRequest));

        $response->assertRedirect(route('care-requests.show', $careRequest));
        $response->assertSessionHas('success');

        $careRequest->refresh();
        $payment->refresh();

        $this->assertEquals('pending', $careRequest->status);
        $this->assertNull($careRequest->accepted_by);
        $this->assertEquals('refunded', $payment->status);
    }

    /**
     * Test that the wallet calculates available, escrow and spent balances correctly.
     */
    public function test_wallet_calculates_balances_correctly(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 1. A payment made by the user
        $careRequest1 = CareRequest::create([
            'user_id' => $user->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 100.00,
            'description' => 'Cared by otherUser',
            'status' => 'accepted',
            'accepted_by' => $otherUser->id,
        ]);
        Payment::create([
            'care_request_id' => $careRequest1->id,
            'user_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'amount' => 100.00,
            'fee' => 10.00,
            'net_amount' => 90.00,
            'status' => 'escrow',
            'card_last_four' => '1111',
            'transaction_id' => 'ch_spent_escrow',
        ]);

        // 2. An escrow payment received by the user
        $careRequest2 = CareRequest::create([
            'user_id' => $otherUser->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 200.00,
            'description' => 'Cared by user',
            'status' => 'accepted',
            'accepted_by' => $user->id,
        ]);
        Payment::create([
            'care_request_id' => $careRequest2->id,
            'user_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'amount' => 200.00,
            'fee' => 20.00,
            'net_amount' => 180.00,
            'status' => 'escrow',
            'card_last_four' => '2222',
            'transaction_id' => 'ch_received_escrow',
        ]);

        // 3. A released payment received by the user
        $careRequest3 = CareRequest::create([
            'user_id' => $otherUser->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price' => 50.00,
            'description' => 'Cared by user, completed',
            'status' => 'finalized',
            'accepted_by' => $user->id,
        ]);
        Payment::create([
            'care_request_id' => $careRequest3->id,
            'user_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'amount' => 50.00,
            'fee' => 5.00,
            'net_amount' => 45.00,
            'status' => 'released',
            'card_last_four' => '3333',
            'transaction_id' => 'ch_received_released',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('payments.wallet'));

        $response->assertOk();
        $response->assertViewIs('payments.wallet');
        
        // Assert view variables
        $response->assertViewHas('availableBalance', 45.00); // 45.00 released
        $response->assertViewHas('escrowBalance', 180.00);   // 180.00 escrow
        $response->assertViewHas('totalSpent', 100.00);      // 100.00 spent
    }
}
