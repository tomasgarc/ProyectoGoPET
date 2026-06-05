<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\Dog;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DashboardController extends Controller
{
    /**
     * Display the platform and user dashboard.
     */
    public function index()
    {
        $stats = null;

        // Only load and generate stats if user is an admin
        if (auth()->user()->isAdmin()) {
            $analyticsPath = storage_path('app/analytics.json');
            
            // Try to read analytics if they exist, otherwise try to run the script
            if (File::exists($analyticsPath)) {
                $stats = json_decode(File::get($analyticsPath), true);
            } else {
                // Run the python script to generate the analytics
                $this->runPythonScript();
                if (File::exists($analyticsPath)) {
                    $stats = json_decode(File::get($analyticsPath), true);
                }
            }
        }

        return view('dashboard', compact('stats'));
    }

    /**
     * Update the analytics by running the python script.
     */
    public function updateAnalytics()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acción no autorizada.');
        }

        $output = $this->runPythonScript();

        if (str_contains($output, 'Error') && ! str_contains($output, 'Connecting to SQLite')) {
            return back()->with('error', 'Ocurrió un error al ejecutar el script de Python: '.$output);
        }

        // Check if fallback was written
        $stats = null;
        $analyticsPath = storage_path('app/analytics.json');
        if (File::exists($analyticsPath)) {
            $stats = json_decode(File::get($analyticsPath), true);
        }

        if ($stats && isset($stats['is_fallback']) && $stats['is_fallback']) {
            return back()->with('success', 'Análisis actualizado en modo de compatibilidad (PHP). El script de Python falló o no está instalado, pero los datos se calcularon correctamente.');
        }

        return back()->with('success', 'Estadísticas y gráficos recalculados con Python con éxito. 📊');
    }

    /**
     * Run the python ETL script.
     */
    private function runPythonScript(): string
    {
        $scriptPath = base_path('python/analyze_data.py');

        // Prepare list of python paths/executables to try
        $commands = [];

        // 1. Support custom PYTHON_PATH from .env (cache-safe via services config)
        if (config('services.python.path')) {
            $commands[] = config('services.python.path');
        }

        // 2. Standard system command paths
        $commands[] = 'python';
        $commands[] = 'python3';
        $commands[] = 'py';

        // 3. Dynamic Windows Anaconda/Miniconda/Programs paths based on USERPROFILE or HOMEDRIVE/HOMEPATH
        $userProfile = getenv('USERPROFILE') ?: ($_SERVER['USERPROFILE'] ?? null);
        if (! $userProfile) {
            $homeDrive = getenv('HOMEDRIVE') ?: ($_SERVER['HOMEDRIVE'] ?? null);
            $homePath = getenv('HOMEPATH') ?: ($_SERVER['HOMEPATH'] ?? null);
            if ($homeDrive && $homePath) {
                $userProfile = $homeDrive.$homePath;
            }
        }

        // Add specific user Anaconda path as high-priority fallback
        $commands[] = 'C:\\Users\\ytuqu\\anaconda3\\python.exe';

        if ($userProfile) {
            $userProfile = rtrim($userProfile, DIRECTORY_SEPARATOR);
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'anaconda3'.DIRECTORY_SEPARATOR.'python.exe';
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'miniconda3'.DIRECTORY_SEPARATOR.'python.exe';
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'AppData'.DIRECTORY_SEPARATOR.'Local'.DIRECTORY_SEPARATOR.'Programs'.DIRECTORY_SEPARATOR.'Python'.DIRECTORY_SEPARATOR.'Python313'.DIRECTORY_SEPARATOR.'python.exe';
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'AppData'.DIRECTORY_SEPARATOR.'Local'.DIRECTORY_SEPARATOR.'Programs'.DIRECTORY_SEPARATOR.'Python'.DIRECTORY_SEPARATOR.'Python312'.DIRECTORY_SEPARATOR.'python.exe';
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'AppData'.DIRECTORY_SEPARATOR.'Local'.DIRECTORY_SEPARATOR.'Programs'.DIRECTORY_SEPARATOR.'Python'.DIRECTORY_SEPARATOR.'Python311'.DIRECTORY_SEPARATOR.'python.exe';
            $commands[] = $userProfile.DIRECTORY_SEPARATOR.'AppData'.DIRECTORY_SEPARATOR.'Local'.DIRECTORY_SEPARATOR.'Programs'.DIRECTORY_SEPARATOR.'Python'.DIRECTORY_SEPARATOR.'Python310'.DIRECTORY_SEPARATOR.'python.exe';
        }

        $output = '';
        $success = false;
        $errors = [];

        foreach ($commands as $cmd) {
            try {
                $process = new Process([$cmd, $scriptPath], null, array_merge($_SERVER, [
                    'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                    'windir' => getenv('windir') ?: 'C:\\Windows',
                ]));
                $process->run();

                if ($process->isSuccessful()) {
                    $output = $process->getOutput();
                    $success = true;
                    break;
                } else {
                    $errors[$cmd] = trim($process->getErrorOutput());
                }
            } catch (\Exception $e) {
                $errors[$cmd] = $e->getMessage();
            }
        }

        if (! $success) {
            Log::error('Python ETL failed. Tried commands with errors: '.json_encode($errors, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            // If execution failed, let's write a mock/default JSON fallback so the app still renders
            $this->writeFallbackAnalytics();

            return 'Fallaron todos los ejecutables de Python.';
        }

        return $output;
    }

    /**
     * Generates a fallback JSON in case python is not installed.
     */
    private function writeFallbackAnalytics()
    {
        $analyticsPath = storage_path('app/analytics.json');

        // Count database metrics in PHP as a fallback
        $totalUsers = User::count();
        $totalDogs = Dog::count();
        $totalRequests = CareRequest::count();
        $activeRequests = CareRequest::whereIn('status', ['pending', 'accepted'])
            ->where('end_date', '>=', now()->toDateString())
            ->count();
        $totalReviews = Review::count();
        $avgRating = Review::avg('rating') ?? 0.0;

        $totalVolume = Payment::whereIn('status', ['released', 'escrow'])->sum('amount');
        $fees = Payment::whereIn('status', ['released', 'escrow'])->sum('fee');
        $escrow = Payment::where('status', 'escrow')->sum('amount');
        $released = Payment::where('status', 'released')->sum('amount');
        $refunded = Payment::where('status', 'refunded')->sum('amount');

        // Dog sizes distribution (case-insensitive normalization)
        $rawDogSizes = Dog::select('size', \DB::raw('count(*) as count'))
            ->groupBy('size')
            ->get();
        $dogSizes = [];
        foreach ($rawDogSizes as $row) {
            $normSz = strtolower(trim($row->size));
            $dogSizes[$normSz] = ($dogSizes[$normSz] ?? 0) + $row->count;
        }

        // Request status distribution
        $requestStatuses = CareRequest::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fallbackData = [
            'total_users' => $totalUsers,
            'total_dogs' => $totalDogs,
            'total_requests' => $totalRequests,
            'active_requests' => $activeRequests,
            'average_rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
            'total_volume' => round($totalVolume, 2),
            'platform_fees' => round($fees, 2),
            'escrow_amount' => round($escrow, 2),
            'released_amount' => round($released, 2),
            'refunded_amount' => round($refunded, 2),
            'dog_sizes' => $dogSizes,
            'request_statuses' => $requestStatuses,
            'charts_generated' => false, // fallback mode
            'is_fallback' => true,
        ];

        File::ensureDirectoryExists(dirname($analyticsPath));
        File::put($analyticsPath, json_encode($fallbackData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * List all users for administration.
     */
    public function usersIndex(Request $request)
    {
        $search = $request->query('search');

        $users = User::where('id', '!=', auth()->id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show a user's details and their dogs.
     */
    public function usersShow(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes administrar tu propia cuenta desde aquí.');
        }

        $user->load('dogs');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Ban or unban a user.
     */
    public function usersToggleBan(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes banearte a ti mismo.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'No puedes banear a otro administrador de la plataforma.');
        }

        if ($user->isBanned()) {
            $user->update(['banned_at' => null]);
            $message = "El usuario {$user->name} ha sido desbaneado con éxito.";
        } else {
            $user->update(['banned_at' => now()]);
            $message = "El usuario {$user->name} ha sido baneado con éxito y su sesión ha sido invalidada.";
        }

        return back()->with('success', $message);
    }
}
