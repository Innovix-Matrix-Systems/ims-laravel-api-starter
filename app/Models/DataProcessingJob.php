<?php

namespace App\Models;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataProcessingJob extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_id',
        'type',
        'status',
        'entity_type',
        'filters',
        'file_name',
        'file_path',
        'original_file_name',
        'total_rows',
        'processed_rows',
        'success_count',
        'error_count',
        'errors',
        'error_message',
        'started_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'type' => DataProcessingJobType::class,
        'status' => DataProcessingJobStatus::class,
        'filters' => 'array',
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', DataProcessingJobStatus::PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', DataProcessingJobStatus::PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', DataProcessingJobStatus::COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', DataProcessingJobStatus::FAILED);
    }

    public function scopeImports($query)
    {
        return $query->where('type', DataProcessingJobType::IMPORT);
    }

    public function scopeExports($query)
    {
        return $query->where('type', DataProcessingJobType::EXPORT);
    }

    public function isPending(): bool
    {
        return $this->status === DataProcessingJobStatus::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === DataProcessingJobStatus::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === DataProcessingJobStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === DataProcessingJobStatus::FAILED;
    }

    public function getProgressPercentage(): int
    {
        if (! $this->total_rows || $this->total_rows === 0) {
            return 0;
        }

        return min(100, (int) (($this->processed_rows / $this->total_rows) * 100));
    }

    public function getDownloadUrl(): ?string
    {
        if ($this->isCompleted() && $this->file_path) {
            return url('storage/' . $this->file_path);
        }

        return null;
    }
}
