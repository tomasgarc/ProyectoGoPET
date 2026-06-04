<?php

namespace App\Console\Commands;

use App\Models\CareRequest;
use App\Models\Dog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Clase CleanDigitalResidues
 * Comando Artisan personalizado para implementar el criterio de Sostenibilidad.
 * Se encarga de la gestión de residuos digitales y optimización del almacenamiento en servidor.
 */
class CleanDigitalResidues extends Command
{
    /**
     * El nombre y firma del comando en la consola.
     *
     * @var string
     */
    protected $signature = 'gopet:clean-residues {--dry-run : Muestra lo que se va a eliminar sin realizar cambios reales}';

    /**
     * La descripción técnica del comando para Artisan.
     *
     * @var string
     */
    protected $description = 'Gestión de Residuos Digitales: Limpia peticiones caducadas, fotos huérfanas y optimiza el almacenamiento.';

    /**
     * Ejecuta la lógica del comando de mantenimiento sostenible.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('--- MODO DE PRUEBA (DRY RUN) - No se aplicarán cambios reales ---');
        }

        $this->comment('Iniciando limpieza de residuos digitales en GoPET...');
        $totalBytesSaved = 0;

        // 1. Limpieza de peticiones pendientes caducadas en el tiempo
        $today = now()->toDateString();
        $expiredRequestsQuery = CareRequest::where('status', 'pending')
            ->where('end_date', '<', $today);

        $expiredCount = $expiredRequestsQuery->count();
        $this->info("Peticiones pendientes expiradas encontradas: {$expiredCount}");

        if ($expiredCount > 0 && ! $dryRun) {
            $expiredRequestsQuery->delete();
            $this->info('Peticiones pendientes expiradas purgadas de la base de datos con éxito.');
        }

        // 2. Limpieza de imágenes huérfanas en el disco físico del servidor
        $this->comment('Buscando imágenes huérfanas en storage/app/public/dogs...');
        $disk = Storage::disk('public');

        // Verificar si la carpeta existe en el disco antes de leer
        if ($disk->exists('dogs')) {
            $allFiles = $disk->allFiles('dogs');
            $dbPhotos = Dog::whereNotNull('photo')->pluck('photo')->toArray();

            $orphanCount = 0;
            foreach ($allFiles as $file) {
                // El campo photo guarda 'dogs/nombre_archivo.png' en base de datos
                if (! in_array($file, $dbPhotos)) {
                    $fileSize = $disk->size($file);
                    $orphanCount++;
                    $totalBytesSaved += $fileSize;

                    $sizeInKb = round($fileSize / 1024, 2);
                    $this->line("• Imagen huérfana detectada: {$file} ({$sizeInKb} KB)");

                    if (! $dryRun) {
                        $disk->delete($file);
                    }
                }
            }
            $this->info("Total de imágenes huérfanas eliminadas físicamente: {$orphanCount}");
        } else {
            $this->line('No se encontró el directorio de almacenamiento de perros dogs/. Saltando limpieza de ficheros.');
            $orphanCount = 0;
        }

        // 3. Optimización de la base de datos SQLite (VACUUM para liberar espacio en disco del SO)
        if (! $dryRun) {
            $this->comment('Ejecutando optimización nativa en base de datos (VACUUM)...');
            try {
                DB::statement('VACUUM');
                $this->info('Base de datos SQLite compactada y optimizada.');
            } catch (\Exception $e) {
                $this->error('No se pudo ejecutar VACUUM en este motor de BD: '.$e->getMessage());
            }
        }

        // Resumen final de Sostenibilidad y Ahorro Ecológico
        $savedKb = round($totalBytesSaved / 1024, 2);
        $savedMb = round($savedKb / 1024, 2);

        $this->info('====================================================');
        $this->info('📊 INFORME DE SOSTENIBILIDAD Y REDUCCIÓN DE RESIDUOS');
        $this->info("• Peticiones pendientes obsoletas eliminadas: {$expiredCount}");
        $this->info("• Archivos de imagen redundantes eliminados: {$orphanCount}");
        $this->info("• Almacenamiento físico del servidor liberado: {$savedKb} KB ({$savedMb} MB)");

        if (! $dryRun) {
            // Factor estimado de huella de carbono digital:
            // Ahorro medio de 0.00001 g CO2eq por KB/mes de almacenamiento local purgado
            $co2Saved = round($totalBytesSaved * 0.00000001, 8);
            $this->info("• Reducción de huella ecológica estimada: {$co2Saved} g CO2eq/año");
        }
        $this->info('====================================================');

        return Command::SUCCESS;
    }
}
