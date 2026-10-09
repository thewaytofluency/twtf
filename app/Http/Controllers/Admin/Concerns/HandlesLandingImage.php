<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Optional image handling shared by the landing page content controllers (courses, impact):
 * upload, replace and remove, with the old file deleted from disk each time.
 */
trait HandlesLandingImage
{
    /** @return array{image?: string|null} attributes to merge into the model data */
    protected function imageData(Request $request, ?Model $existing = null): array
    {
        if ($request->hasFile('image')) {
            $this->deleteImage($existing);

            return ['image' => $request->file('image')->store('landing', 'public')];
        }

        if ($request->boolean('remove_image')) {
            $this->deleteImage($existing);

            return ['image' => null];
        }

        return [];
    }

    protected function deleteImage(?Model $model): void
    {
        if ($model?->image) {
            Storage::disk('public')->delete($model->image);
        }
    }
}
