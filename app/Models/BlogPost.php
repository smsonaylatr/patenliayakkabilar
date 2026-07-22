<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'is_indexable' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BlogPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
            static::autoFillSeo($post);
        });

        static::updating(function (BlogPost $post) {
            if (empty($post->meta_title) || $post->isDirty('title')) {
                static::autoFillSeo($post);
            }
        });
    }

    /**
     * Otomatik SEO meta verilerini üret
     */
    protected static function autoFillSeo(BlogPost $post): void
    {
        if (empty($post->meta_title)) {
            $post->meta_title = mb_substr($post->title . ' | Patenli Ayakkabılar', 0, 70);
        }

        if (empty($post->meta_description)) {
            $source = $post->excerpt ?: strip_tags($post->content ?? '');
            $post->meta_description = mb_substr(Str::limit($source, 155), 0, 160);
        }

        if (empty($post->image_alt)) {
            $post->image_alt = mb_substr($post->title, 0, 255);
        }
    }
}
