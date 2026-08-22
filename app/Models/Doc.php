<?php

namespace App\Models;

use App\Enums\CourseLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doc extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'file_path', 'original_filename', 'file_size',
        'course_level', 'required_access_level', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'course_level' => CourseLevel::class,
            'required_access_level' => 'integer',
            'file_size' => 'integer',
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
}
