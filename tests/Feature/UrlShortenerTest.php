<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    #[Test]
    public function it_encodes_original_url()
    {
        $originalUrl = 'https://atarim.io';

        $response = $this->postJson('/api/encode', ['url' => $originalUrl]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['url']
                 ]);
    }
    #[Test]
    public function it_encode_large_url()
    {
        $longUrl = 'https://atarim.io/' . str_repeat('a', 5000);

        $response = $this->postJson('/api/encode', ['url' => $longUrl]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'message', 'data' => ['url']]);
    }
    #[Test]
    public function it_requires_a_valid_url_to_encode()
    {
        $response = $this->postJson('/api/encode', ['url' => 'invalid-url']);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Validation failed',
                     'errors' => ["The URL format is invalid."]
                 ]);
    }
    #[Test]
    public function it_encode_url_to_short_url_and_back_to_original_url()
    {
        $originalUrl = 'https://atarim.io/new-york/council/members';

        $encodeResponse = $this->postJson('/api/encode', ['url' => $originalUrl]);
        $encodedUrl = $encodeResponse->json('data.url') ?? null;

        $this->assertNotNull($encodedUrl, 'Encoded URL should not be null');

        $decodeResponse = $this->postJson('/api/decode', ['shortUrl' => $encodedUrl]);

        $decodeResponse->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Short URL decoded successfully',
                     'data' => ['url' => $originalUrl]
                 ]);
    }

    #[Test]
    public function it_returns_404_if_short_url_not_found()
    {
        $response = $this->postJson('/api/decode', ['shortUrl' => 'http://localhost/invalid']);

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Short URL not found or expired',
                     'data' => ['url' => null]
                 ]);
    }

    #[Test]
    public function it_returns_422_if_short_url_is_invalid()
    {
        $response = $this->postJson('/api/decode', ['shortUrl' => '']);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Validation failed',
                     'errors' => ["The Short URL field is required."]
                 ]);
    }

    #[Test]
    public function it_does_not_generate_duplicate_short_urls_for_the_same_url()
    {
        $url1 = 'https://atarim.io/about-us';
        $url2 = 'https://atarim.io/about-us';

        $response1 = $this->postJson('/api/encode', ['url' => $url1]);
        $response2 = $this->postJson('/api/encode', ['url' => $url2]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        $this->assertNotEquals($response1->json('data.url'), $response2->json('data.url'));
    }
}
