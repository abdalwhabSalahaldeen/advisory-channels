<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_opening_id', 'status', 'full_name', 'email', 'phone', 'linkedin_url',
    'cv_path', 'cover_letter', 'notes', 'locale', 'consented_at',
])]
class JobApplication extends Model
{
    use HasFactory;

    /**
     * CVs are personal data, so they live on the private disk and are only
     * reachable through the admin panel.
     */
    public const CV_DISK = 'local';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'consented_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<JobOpening, $this>
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }
}
