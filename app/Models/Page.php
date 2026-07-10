<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'is_active',
        'meta_title', 'meta_description', 'og_image', 'is_indexable',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_indexable' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
            static::autoFillSeo($page);
        });

        static::updating(function (Page $page) {
            if (empty($page->meta_title) || $page->isDirty('title')) {
                static::autoFillSeo($page);
            }
        });
    }

    protected static function autoFillSeo(Page $page): void
    {
        if (empty($page->meta_title)) {
            $page->meta_title = mb_substr($page->title . ' | Patenli Ayakkabılar', 0, 70);
        }

        if (empty($page->meta_description)) {
            $source = strip_tags($page->content ?? '');
            $page->meta_description = mb_substr(Str::limit($source, 155), 0, 160);
        }
    }
}
