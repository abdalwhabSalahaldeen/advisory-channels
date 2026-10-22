<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Any enquiry submitted from the public site: a service request, a Saudi Gate
 * enquiry, or a general contact message.
 */
#[Fillable([
    'type', 'purpose', 'status', 'full_name', 'company', 'job_title', 'email', 'phone', 'country',
    'service_id', 'packages', 'timeline', 'message', 'attachment_path', 'notes', 'locale', 'source_url', 'consented_at',
])]
class Lead extends Model
{
    use HasFactory;

    /**
     * RFP documents and other attachments are private, like CVs.
     */
    public const ATTACHMENT_DISK = 'local';

    protected static function booted(): void
    {
        static::deleted(function (Lead $lead) {
            if ($lead->attachment_path) {
                Storage::disk(self::ATTACHMENT_DISK)->delete($lead->attachment_path);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'status' => LeadStatus::class,
            'packages' => 'array',
            'consented_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The Saudi Gate packages the visitor ticked, resolved from their slugs.
     *
     * @return Collection<int, GatePackage>
     */
    public function selectedPackages(): Collection
    {
        if (empty($this->packages)) {
            return collect();
        }

        return GatePackage::query()->whereIn('slug', $this->packages)->orderBy('sort_order')->get();
    }
}
