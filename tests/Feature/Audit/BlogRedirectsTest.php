<?php

namespace Tests\Feature\Audit;

use Tests\TestCase;

class BlogRedirectsTest extends TestCase
{
    /**
     * Test the 301 redirects for modified blog slugs.
     */
    public function test_blog_legacy_urls_redirect_correctly_with_301(): void
    {
        $redirects = [
            // Blog 1
            '/ar/blogs/the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer' 
                => '/ar/blogs/laundry-pickup-delivery-riyadh',

            // Blog 10
            '/ar/blogs/air-conditioner-cleaning-importance' 
                => '/ar/blogs/on-time-laundry-pickup-delivery-riyadh',

            // Blog 13
            '/ar/blogs/best-underground-water-tank-cleaning-company' 
                => '/ar/blogs/laundry-fabric-care-prices-riyadh',

            // Intermediate slugs from previous rename
            '/ar/blogs/pickup-delivery-on-time' 
                => '/ar/blogs/on-time-laundry-pickup-delivery-riyadh',
            '/ar/blogs/laundry-prices-riyadh' 
                => '/ar/blogs/laundry-fabric-care-prices-riyadh',
        ];

        foreach ($redirects as $source => $target) {
            $response = $this->get($source);

            $response->assertStatus(301);
            $this->assertStringEndsWith($target, $response->headers->get('Location'));
        }
    }
}
