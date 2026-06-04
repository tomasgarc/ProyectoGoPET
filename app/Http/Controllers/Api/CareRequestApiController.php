<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CareRequest;
use App\Models\Dog;
use Illuminate\Http\JsonResponse;

/**
 * Clase CareRequestApiController
 * Controlador de API REST para la gestión de recursos de GoPET.
 * Satisface los criterios del módulo Desarrollo Web en Entorno Servidor (DWES).
 */
class CareRequestApiController extends Controller
{
    /**
     * Retorna una lista estructurada en JSON de las peticiones de cuidado activas.
     */
    public function index(): JsonResponse
    {
        $requests = CareRequest::where('status', 'pending')
            ->where('end_date', '>=', now()->toDateString())
            ->with(['dogs:id,name,breed,size,age', 'user:id,name'])
            ->latest()
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'start_date' => $req->start_date,
                    'end_date' => $req->end_date,
                    'price' => (float) $req->price,
                    'description' => $req->description,
                    'owner' => [
                        'id' => $req->user->id,
                        'name' => $req->user->name,
                    ],
                    'dogs' => $req->dogs->map(function ($dog) {
                        return [
                            'id' => $dog->id,
                            'name' => $dog->name,
                            'breed' => $dog->breed ?? 'Mestizo',
                            'size' => $dog->size,
                            'age' => $dog->age,
                        ];
                    }),
                    'created_at' => $req->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $requests->count(),
            'data' => $requests,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Retorna la lista de todos los perros registrados de forma pública en la plataforma.
     */
    public function dogs(): JsonResponse
    {
        $dogs = Dog::with('user:id,name')
            ->latest()
            ->get()
            ->map(function ($dog) {
                return [
                    'id' => $dog->id,
                    'name' => $dog->name,
                    'breed' => $dog->breed ?? 'Mestizo',
                    'size' => $dog->size,
                    'age' => $dog->age,
                    'photo_url' => $dog->photo ? asset('storage/'.$dog->photo) : null,
                    'owner' => [
                        'id' => $dog->user->id,
                        'name' => $dog->user->name,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $dogs->count(),
            'data' => $dogs,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
