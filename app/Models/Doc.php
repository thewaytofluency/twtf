<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Models\Concerns\Sequenced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doc extends Model
{
    use HasFactory, Sequenced;

    protected $fillable = [
        'title', 'description', 'file_path', 'original_filename', 'file_size',
        'course_level', 'required_access_level', 'sort_order', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'course_level' => CourseLevel::class,
            'required_access_level' => 'integer',
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAccessibleBy(?User $user): bool
    {
        $level = $user?->currentAccessLevel() ?? 0;

        return $level >= $this->required_access_level;
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->original_filename ?: $this->file_path, PATHINFO_EXTENSION));
    }

    /** PDFs and images can be read inside the page; everything else is download-only. */
    public function isPreviewable(): bool
    {
        return in_array($this->extension(), ['pdf', 'jpg', 'jpeg', 'png'], true);
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->file_size;

        return $bytes >= 1048576 ? number_format($bytes / 1048576, 1).' MB' : number_format(max($bytes, 1) / 1024, 0).' KB';
    }
}
