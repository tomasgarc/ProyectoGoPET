<?php

namespace Database\Seeders;

use App\Models\CareRequest;
use App\Models\Chat;
use App\Models\Dog;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        $users = [];

        $users['admin'] = User::create([
            'name' => 'Administrador GoPET',
            'email' => 'admin@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Users
        $users['juan'] = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['maria'] = User::create([
            'name' => 'María Gómez',
            'email' => 'maria@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['carlos'] = User::create([
            'name' => 'Carlos López',
            'email' => 'carlos@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['ana'] = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['elena'] = User::create([
            'name' => 'Elena Ruiz',
            'email' => 'elena@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Caretakers (now also users)
        $users['sofia'] = User::create([
            'name' => 'Sofía Rodríguez',
            'email' => 'sofia@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['diego'] = User::create([
            'name' => 'Diego Fernández',
            'email' => 'diego@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['laura'] = User::create([
            'name' => 'Laura Sánchez',
            'email' => 'laura@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $users['pablo'] = User::create([
            'name' => 'Pablo Muñoz',
            'email' => 'pablo@gopet.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // 2. Create Dogs
        $dogs = [];

        // Ensure dogs directory exists in public storage
        $dogsDir = storage_path('app/public/dogs');
        if (!file_exists($dogsDir)) {
            mkdir($dogsDir, 0755, true);
        }

        // Mock photos from Unsplash
        $photoData = [
            'max' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=400',
            'luna' => 'https://images.unsplash.com/photo-1505628346881-b72b27e84530?w=400',
            'rocky' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400',
            'toby' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400',
            'bella' => 'https://images.unsplash.com/photo-1531804055935-76f44d7c3621?w=400',
            'coco' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=400',
            'simba' => 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?w=400',
            'nala' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=400',
        ];

        foreach ($photoData as $key => $url) {
            $path = "dogs/{$key}.jpg";
            $fullPath = storage_path("app/public/{$path}");
            if (!file_exists($fullPath)) {
                try {
                    $ctx = stream_context_create([
                        'http' => [
                            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
                        ]
                    ]);
                    $content = file_get_contents($url, false, $ctx);
                    if ($content) {
                        file_put_contents($fullPath, $content);
                    }
                } catch (\Exception $e) {
                    // Silently ignore download errors
                }
            }
        }

        $dogs['max'] = Dog::create([
            'name' => 'Max',
            'breed' => 'Golden Retriever',
            'age' => 3,
            'size' => 'grande',
            'photo' => file_exists(storage_path('app/public/dogs/max.jpg')) ? 'dogs/max.jpg' : null,
            'sex' => 'macho',
            'user_id' => $users['juan']->id,
        ]);

        $dogs['luna'] = Dog::create([
            'name' => 'Luna',
            'breed' => 'Chihuahua',
            'age' => 1,
            'size' => 'pequeño',
            'photo' => file_exists(storage_path('app/public/dogs/luna.jpg')) ? 'dogs/luna.jpg' : null,
            'sex' => 'hembra',
            'user_id' => $users['maria']->id,
        ]);

        $dogs['rocky'] = Dog::create([
            'name' => 'Rocky',
            'breed' => 'Bulldog Francés',
            'age' => 4,
            'size' => 'mediano',
            'photo' => file_exists(storage_path('app/public/dogs/rocky.jpg')) ? 'dogs/rocky.jpg' : null,
            'sex' => 'macho',
            'user_id' => $users['maria']->id,
        ]);

        $dogs['toby'] = Dog::create([
            'name' => 'Toby',
            'breed' => 'Cocker Spaniel',
            'age' => 2,
            'size' => 'mediano',
            'photo' => file_exists(storage_path('app/public/dogs/toby.jpg')) ? 'dogs/toby.jpg' : null,
            'sex' => 'macho',
            'user_id' => $users['carlos']->id,
        ]);

        $dogs['bella'] = Dog::create([
            'name' => 'Bella',
            'breed' => 'Husky Siberiano',
            'age' => 5,
            'size' => 'grande',
            'photo' => file_exists(storage_path('app/public/dogs/bella.jpg')) ? 'dogs/bella.jpg' : null,
            'sex' => 'hembra',
            'user_id' => $users['ana']->id,
        ]);

        $dogs['coco'] = Dog::create([
            'name' => 'Coco',
            'breed' => 'Yorkshire Terrier',
            'age' => 6,
            'size' => 'pequeño',
            'photo' => file_exists(storage_path('app/public/dogs/coco.jpg')) ? 'dogs/coco.jpg' : null,
            'sex' => 'macho',
            'user_id' => $users['ana']->id,
        ]);

        $dogs['simba'] = Dog::create([
            'name' => 'Simba',
            'breed' => 'Pastor Alemán',
            'age' => 2,
            'size' => 'grande',
            'photo' => file_exists(storage_path('app/public/dogs/simba.jpg')) ? 'dogs/simba.jpg' : null,
            'sex' => 'macho',
            'user_id' => $users['elena']->id,
        ]);

        $dogs['nala'] = Dog::create([
            'name' => 'Nala',
            'breed' => 'Beagle',
            'age' => 3,
            'size' => 'mediano',
            'photo' => file_exists(storage_path('app/public/dogs/nala.jpg')) ? 'dogs/nala.jpg' : null,
            'sex' => 'hembra',
            'user_id' => $users['elena']->id,
        ]);

        // 3. Create Care Requests (Historic and Current)
        $requests = [];

        // Finalized
        $requests[1] = CareRequest::create([
            'user_id' => $users['juan']->id,
            'start_date' => Carbon::now()->subDays(30)->toDateString(),
            'end_date' => Carbon::now()->subDays(25)->toDateString(),
            'price' => 100.00,
            'description' => 'Necesito cuidador para Max durante un viaje de trabajo de 5 días. Le gusta correr por las mañanas.',
            'status' => 'finalized',
            'accepted_by' => $users['sofia']->id,
        ]);
        $requests[1]->dogs()->attach($dogs['max']->id);

        $requests[2] = CareRequest::create([
            'user_id' => $users['maria']->id,
            'start_date' => Carbon::now()->subDays(20)->toDateString(),
            'end_date' => Carbon::now()->subDays(18)->toDateString(),
            'price' => 50.00,
            'description' => 'Cuidado de fin de semana para mi pequeña Luna. Es muy cariñosa y tranquila.',
            'status' => 'finalized',
            'accepted_by' => $users['diego']->id,
        ]);
        $requests[2]->dogs()->attach($dogs['luna']->id);

        $requests[3] = CareRequest::create([
            'user_id' => $users['maria']->id,
            'start_date' => Carbon::now()->subDays(15)->toDateString(),
            'end_date' => Carbon::now()->subDays(12)->toDateString(),
            'price' => 90.00,
            'description' => 'Cuidado a domicilio para Rocky. Necesita paseos cortos debido a su respiración.',
            'status' => 'finalized',
            'accepted_by' => $users['laura']->id,
        ]);
        $requests[3]->dogs()->attach($dogs['rocky']->id);

        $requests[4] = CareRequest::create([
            'user_id' => $users['carlos']->id,
            'start_date' => Carbon::now()->subDays(10)->toDateString(),
            'end_date' => Carbon::now()->subDays(8)->toDateString(),
            'price' => 60.00,
            'description' => 'Cuidado para Toby. Tiene mucha energía y le encanta jugar a la pelota.',
            'status' => 'finalized',
            'accepted_by' => $users['sofia']->id,
        ]);
        $requests[4]->dogs()->attach($dogs['toby']->id);

        $requests[5] = CareRequest::create([
            'user_id' => $users['ana']->id,
            'start_date' => Carbon::now()->subDays(7)->toDateString(),
            'end_date' => Carbon::now()->subDays(2)->toDateString(),
            'price' => 150.00,
            'description' => 'Cuidado para mis dos perritos Bella y Coco. Se llevan muy bien y se cuidan mutuamente.',
            'status' => 'finalized',
            'accepted_by' => $users['pablo']->id,
        ]);
        $requests[5]->dogs()->attach([$dogs['bella']->id, $dogs['coco']->id]);

        // Active / Accepted (Currently in progress)
        $requests[6] = CareRequest::create([
            'user_id' => $users['juan']->id,
            'start_date' => Carbon::now()->subDays(2)->toDateString(),
            'end_date' => Carbon::now()->addDays(3)->toDateString(),
            'price' => 120.00,
            'description' => 'Cuidado para Max esta semana. Estará hospedado en casa de la cuidadora.',
            'status' => 'accepted',
            'accepted_by' => $users['sofia']->id,
        ]);
        $requests[6]->dogs()->attach($dogs['max']->id);

        $requests[7] = CareRequest::create([
            'user_id' => $users['maria']->id,
            'start_date' => Carbon::now()->addDays(5)->toDateString(),
            'end_date' => Carbon::now()->addDays(10)->toDateString(),
            'price' => 110.00,
            'description' => 'Cuidado para Luna y Rocky. Preferiblemente alguien con jardín.',
            'status' => 'accepted',
            'accepted_by' => $users['diego']->id,
        ]);
        $requests[7]->dogs()->attach([$dogs['luna']->id, $dogs['rocky']->id]);

        // Pending (Open Requests)
        $requests[8] = CareRequest::create([
            'user_id' => $users['carlos']->id,
            'start_date' => Carbon::now()->addDays(12)->toDateString(),
            'end_date' => Carbon::now()->addDays(15)->toDateString(),
            'price' => 100.00,
            'description' => 'Busco cuidador responsable para Toby durante el próximo puente festivo.',
            'status' => 'pending',
        ]);
        $requests[8]->dogs()->attach($dogs['toby']->id);

        $requests[9] = CareRequest::create([
            'user_id' => $users['ana']->id,
            'start_date' => Carbon::now()->addDays(18)->toDateString(),
            'end_date' => Carbon::now()->addDays(20)->toDateString(),
            'price' => 75.00,
            'description' => 'Cuidado para Coco de 2 días. Necesita que le den su medicación por la noche.',
            'status' => 'pending',
        ]);
        $requests[9]->dogs()->attach($dogs['coco']->id);

        $requests[10] = CareRequest::create([
            'user_id' => $users['elena']->id,
            'start_date' => Carbon::now()->addDays(25)->toDateString(),
            'end_date' => Carbon::now()->addDays(28)->toDateString(),
            'price' => 130.00,
            'description' => 'Cuidado para Simba. Es un Pastor Alemán muy noble pero tiene bastante fuerza.',
            'status' => 'pending',
        ]);
        $requests[10]->dogs()->attach($dogs['simba']->id);

        $requests[11] = CareRequest::create([
            'user_id' => $users['elena']->id,
            'start_date' => Carbon::now()->addDays(30)->toDateString(),
            'end_date' => Carbon::now()->addDays(32)->toDateString(),
            'price' => 120.00,
            'description' => 'Cuidado vacacional para Nala. Le gusta mucho jugar con otros perros.',
            'status' => 'pending',
        ]);
        $requests[11]->dogs()->attach($dogs['nala']->id);

        // Cancelled / Expired with Refund
        $requests[12] = CareRequest::create([
            'user_id' => $users['maria']->id,
            'start_date' => Carbon::now()->subDays(40)->toDateString(),
            'end_date' => Carbon::now()->subDays(38)->toDateString(),
            'price' => 40.00,
            'description' => 'Cuidado exprés para Luna.',
            'status' => 'pending', // Reverted to pending after refund
        ]);
        $requests[12]->dogs()->attach($dogs['luna']->id);

        // 4. Create Chats and Messages
        // Sofia chatting with Juan
        $chat1 = Chat::create(['care_request_id' => $requests[1]->id, 'user_id' => $users['sofia']->id, 'creator_id' => $users['juan']->id]);
        Message::create(['chat_id' => $chat1->id, 'sender_id' => $users['sofia']->id, 'content' => 'Hola Juan, estoy interesada en cuidar a Max. Tengo experiencia con Golden Retrievers.']);
        Message::create(['chat_id' => $chat1->id, 'sender_id' => $users['juan']->id, 'content' => '¡Hola Sofía! Estupendo. Max es muy dócil pero necesita ejercicio diario.']);
        Message::create(['chat_id' => $chat1->id, 'sender_id' => $users['sofia']->id, 'content' => 'Perfecto, me encanta pasear. ¿Quedamos antes de las fechas para que nos conozcamos?']);

        // Diego chatting with Maria
        $chat2 = Chat::create(['care_request_id' => $requests[2]->id, 'user_id' => $users['diego']->id, 'creator_id' => $users['maria']->id]);
        Message::create(['chat_id' => $chat2->id, 'sender_id' => $users['diego']->id, 'content' => 'Hola María, me ofrezco a cuidar a Luna. Vivo cerca de tu zona.']);
        Message::create(['chat_id' => $chat2->id, 'sender_id' => $users['maria']->id, 'content' => 'Hola Diego, me viene muy bien la cercanía. Ella es un poco tímida al principio.']);

        // Sofia chatting with Carlos
        $chat3 = Chat::create(['care_request_id' => $requests[4]->id, 'user_id' => $users['sofia']->id, 'creator_id' => $users['carlos']->id]);
        Message::create(['chat_id' => $chat3->id, 'sender_id' => $users['sofia']->id, 'content' => 'Hola Carlos, puedo cuidar a Toby. Tengo juguetes interactivos en casa.']);

        // Pablo chatting with Ana
        $chat4 = Chat::create(['care_request_id' => $requests[5]->id, 'user_id' => $users['pablo']->id, 'creator_id' => $users['ana']->id]);
        Message::create(['chat_id' => $chat4->id, 'sender_id' => $users['pablo']->id, 'content' => 'Hola Ana, puedo hacerme cargo de Bella y Coco. Tengo un espacio adaptado para ambos.']);

        // Laura chatting with Elena (pending request)
        $chat5 = Chat::create(['care_request_id' => $requests[10]->id, 'user_id' => $users['laura']->id, 'creator_id' => $users['elena']->id]);
        Message::create(['chat_id' => $chat5->id, 'sender_id' => $users['laura']->id, 'content' => 'Hola Elena, me encantaría cuidar de Simba. He tenido pastores alemanes antes.']);

        // 5. Create Payments
        // Finalized requests - released payments
        Payment::create([
            'care_request_id' => $requests[1]->id,
            'user_id' => $users['juan']->id,
            'receiver_id' => $users['sofia']->id,
            'amount' => 100.00,
            'fee' => 10.00,
            'net_amount' => 90.00,
            'status' => 'released',
            'card_last_four' => '4242',
            'transaction_id' => 'ch_released_1',
        ]);

        Payment::create([
            'care_request_id' => $requests[2]->id,
            'user_id' => $users['maria']->id,
            'receiver_id' => $users['diego']->id,
            'amount' => 50.00,
            'fee' => 5.00,
            'net_amount' => 45.00,
            'status' => 'released',
            'card_last_four' => '1111',
            'transaction_id' => 'ch_released_2',
        ]);

        Payment::create([
            'care_request_id' => $requests[3]->id,
            'user_id' => $users['maria']->id,
            'receiver_id' => $users['laura']->id,
            'amount' => 90.00,
            'fee' => 9.00,
            'net_amount' => 81.00,
            'status' => 'released',
            'card_last_four' => '2222',
            'transaction_id' => 'ch_released_3',
        ]);

        Payment::create([
            'care_request_id' => $requests[4]->id,
            'user_id' => $users['carlos']->id,
            'receiver_id' => $users['sofia']->id,
            'amount' => 60.00,
            'fee' => 6.00,
            'net_amount' => 54.00,
            'status' => 'released',
            'card_last_four' => '3333',
            'transaction_id' => 'ch_released_4',
        ]);

        Payment::create([
            'care_request_id' => $requests[5]->id,
            'user_id' => $users['ana']->id,
            'receiver_id' => $users['pablo']->id,
            'amount' => 150.00,
            'fee' => 15.00,
            'net_amount' => 135.00,
            'status' => 'released',
            'card_last_four' => '5555',
            'transaction_id' => 'ch_released_5',
        ]);

        // Active requests - escrowed payments
        Payment::create([
            'care_request_id' => $requests[6]->id,
            'user_id' => $users['juan']->id,
            'receiver_id' => $users['sofia']->id,
            'amount' => 120.00,
            'fee' => 12.00,
            'net_amount' => 108.00,
            'status' => 'escrow',
            'card_last_four' => '4242',
            'transaction_id' => 'ch_escrow_6',
        ]);

        Payment::create([
            'care_request_id' => $requests[7]->id,
            'user_id' => $users['maria']->id,
            'receiver_id' => $users['diego']->id,
            'amount' => 110.00,
            'fee' => 11.00,
            'net_amount' => 99.00,
            'status' => 'escrow',
            'card_last_four' => '9999',
            'transaction_id' => 'ch_escrow_7',
        ]);

        // Expired/Refunded request
        Payment::create([
            'care_request_id' => $requests[12]->id,
            'user_id' => $users['maria']->id,
            'receiver_id' => $users['laura']->id,
            'amount' => 40.00,
            'fee' => 4.00,
            'net_amount' => 36.00,
            'status' => 'refunded',
            'card_last_four' => '1111',
            'transaction_id' => 'ch_refunded_12',
        ]);

        // 6. Create Reviews
        Review::create([
            'care_request_id' => $requests[1]->id,
            'reviewer_id' => $users['juan']->id,
            'reviewee_id' => $users['sofia']->id,
            'rating' => 5,
            'comment' => '¡Sofía cuidó de Max estupendamente! Muy atenta y cariñosa. Totalmente recomendable.',
        ]);

        Review::create([
            'care_request_id' => $requests[2]->id,
            'reviewer_id' => $users['maria']->id,
            'reviewee_id' => $users['diego']->id,
            'rating' => 4,
            'comment' => 'Muy buen servicio, Diego es muy responsable y puntual.',
        ]);

        Review::create([
            'care_request_id' => $requests[3]->id,
            'reviewer_id' => $users['maria']->id,
            'reviewee_id' => $users['laura']->id,
            'rating' => 5,
            'comment' => 'Laura es encantadora, Rocky estuvo muy feliz jugando con sus perros.',
        ]);

        Review::create([
            'care_request_id' => $requests[4]->id,
            'reviewer_id' => $users['carlos']->id,
            'reviewee_id' => $users['sofia']->id,
            'rating' => 5,
            'comment' => 'Excelente cuidadora, muy atenta y comunicativa durante todo el proceso.',
        ]);

        Review::create([
            'care_request_id' => $requests[5]->id,
            'reviewer_id' => $users['ana']->id,
            'reviewee_id' => $users['pablo']->id,
            'rating' => 3,
            'comment' => 'El cuidado fue correcto, aunque la comunicación de incidencias fue algo lenta.',
        ]);

        // Update all seeded users and care requests to be in El Puerto de Santa María
        User::query()->update(['location' => 'El Puerto de Santa María']);
        CareRequest::query()->update(['location' => 'El Puerto de Santa María']);
    }
}
