<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnershipRegistration extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'preferred_meeting_at' => 'datetime',
            'alternative_meeting_at' => 'datetime',
            'is_read' => 'boolean',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(PartnershipPackage::class, 'partnership_package_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function generateNumber(): string
    {
        return 'REG-'.now()->format('Ymd').'-'.str_pad((string) (static::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
