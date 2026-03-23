<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use QuantumTecnology\ModelBasicsExtension\BaseModel;

class Archive extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'main',
        'active',
        'order',
        'key',
        'disk',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function archivable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the file contents.
     */
    public function getContents(): string
    {
        return Storage::disk($this->disk)->get($this->key);
    }

    /**
     * Get the full URL for the archived file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->key);
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (Archive $archive) {
            if (Storage::disk($archive->disk)->exists($archive->key)) {
                Storage::disk($archive->disk)->delete($archive->key);
            }
        });
    }

    /**
     * Get the formated s3Key.
     */
    protected function UrlKey(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? $this->getUrlAttribute() : null,
        );
    }

    /**
     * Get the formated url.
     */
    protected function url(): Attribute
    {
        return new Attribute(
            get: fn () => $this->key ? $this->getUrlAttribute() : null,
        );
    }

    /**
     * Get the formated base64.
     */
    protected function temporaryUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->key ? Storage::temporaryUrl($this->key, Carbon::now()->addMinutes(60)) : null,
        );
    }

    /**
     * Get the formated mime.
     */
    protected function mime(): Attribute
    {
        return new Attribute(
            get: fn () => $this->key ? Storage::mimeType($this->key) : null,
        );
    }

    /**
     * Get the formated mime.
     */
    protected function size(): Attribute
    {
        return new Attribute(
            get: fn () => $this->key ? Storage::size($this->key) : null,
        );
    }

    /**
     * Get the formated base64.
     */
    protected function base64(): Attribute
    {
        return new Attribute(
            get: fn () => $this->key ? base64_encode($this->getContents()) : null,
        );
    }
}
