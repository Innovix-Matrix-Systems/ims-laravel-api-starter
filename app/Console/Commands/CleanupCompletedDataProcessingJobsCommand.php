<?php

namespace App\Console\Commands;

use App\Enums\DataProcessingJobStatus;
use App\Models\DataProcessingJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanupCompletedDataProcessingJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-processing:cleanup-completed 
                            {--days=30 : Number of days to keep completed jobs}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup completed data processing jobs older than specified days';

    /** Execute the console command. */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($days < 1) {
            $this->error('Days must be a positive number');

            return self::FAILURE;
        }

        $cutoffDate = Carbon::now()->subDays($days);

        $query = DataProcessingJob::where('status', DataProcessingJobStatus::COMPLETED)
            ->where('completed_at', '<', $cutoffDate);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No completed jobs found to cleanup');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("Would delete {$count} completed jobs older than {$days} days");

            // Show sample jobs that would be deleted
            $sampleJobs = $query->limit(5)->get(['job_id', 'type', 'completed_at']);
            if ($sampleJobs->isNotEmpty()) {
                $this->table(
                    ['Job ID', 'Type', 'Completed At'],
                    $sampleJobs->map(fn ($job) => [
                        $job->job_id,
                        $job->type->value,
                        $job->completed_at->format('Y-m-d H:i:s'),
                    ])
                );
            }

            return self::SUCCESS;
        }

        $this->info("Found {$count} completed jobs to cleanup (older than {$days} days)");

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $deletedCount = 0;
        $batchSize = 100;

        do {
            $jobsToDelete = $query->limit($batchSize)->get();

            if ($jobsToDelete->isEmpty()) {
                break;
            }

            foreach ($jobsToDelete as $job) {
                // Delete associated files if they exist
                if ($job->file_path && Storage::disk('public')->exists($job->file_path)) {
                    try {
                        Storage::disk('public')->delete($job->file_path);
                    } catch (\Exception $e) {
                        $this->warn("Could not delete file: {$job->file_path}");
                    }
                }

                $job->delete();
                $deletedCount++;
                $progressBar->advance();
            }
        } while ($jobsToDelete->count() === $batchSize);

        $progressBar->finish();
        $this->newLine();

        $this->info("Successfully deleted {$deletedCount} completed jobs");

        return self::SUCCESS;
    }
}
