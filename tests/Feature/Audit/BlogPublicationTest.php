<?php

namespace Tests\Feature\Audit;

use Core\Blog\Models\Blog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\IsolatedAuditTestCase;

class BlogPublicationTest extends IsolatedAuditTestCase
{
    public function test_only_published_due_posts_are_public_and_slug_filters_still_apply(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('status');
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
        });
        foreach ([
            ['published', 'publish', '2000-01-01'],
            ['undated', 'publish', null],
            ['draft', 'pending', '2000-01-01'],
            ['scheduled', 'publish', '2999-01-01'],
        ] as [$slug, $status, $date]) {
            DB::table('blogs')->insert(['slug' => $slug, 'status' => $status, 'published_at' => $date]);
        }
        $this->assertSame(['published', 'undated'], Blog::published()->orderBy('id')->pluck('slug')->all());
        $this->assertSame(['undated'], Blog::published()->where('slug', 'undated')->pluck('slug')->all());
        $this->assertNull(Blog::published()->where('slug', 'draft')->first());
        $this->assertNull(Blog::published()->where('slug', 'missing')->first());
    }

    public function test_meta_fields_resolve_from_the_selected_translation(): void
    {
        $blog = new Blog;
        $blog->translateOrNew('en')->meta_title = 'Laundry guide';
        $blog->translateOrNew('en')->meta_description = 'Care instructions';
        $blog->setDefaultLocale('en');
        $this->assertSame('Laundry guide', $blog->meta_title);
        $this->assertSame('Care instructions', $blog->meta_description);
    }
}
