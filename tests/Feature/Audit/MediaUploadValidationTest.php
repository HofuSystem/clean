<?php

namespace Tests\Feature\Audit;

use Core\MediaCenter\Requests\Api\AddMediaCenterRequest;
use Illuminate\Http\UploadedFile;
use Tests\Support\IsolatedAuditTestCase;

class MediaUploadValidationTest extends IsolatedAuditTestCase
{
    private array $temporaryFiles = [];
    private int $acceptedRequests = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['router']->post('/_audit/media-upload', function (AddMediaCenterRequest $request) {
            $this->acceptedRequests++;
            return response()->json(['type' => $request->validated('type'), 'count' => count($request->file('files'))]);
        });
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) { if (is_file($file)) { unlink($file); } }
        parent::tearDown();
    }

    private function content(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'media-audit-');
        file_put_contents($path, $content);
        $this->temporaryFiles[] = $path;
        return new UploadedFile($path, $name, null, null, true);
    }

    private function upload(array $data)
    {
        return $this->post('/_audit/media-upload', $data, ['Accept' => 'application/json']);
    }

    public function test_valid_images_and_pdf_are_accepted_and_default_type_is_preserved(): void
    {
        $this->upload(['type' => 'image', 'files' => [UploadedFile::fake()->image('photo.png', 800, 600)]])->assertOk()->assertJson(['count' => 1]);
        $this->upload(['type' => 'pdf', 'files' => [$this->content('document.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF")]])->assertOk();
        $this->upload(['files' => [UploadedFile::fake()->image('photo.jpg')]])->assertOk()->assertJson(['type' => 'media']);
        $this->assertSame(3, $this->acceptedRequests);
    }

    public function test_scripts_svg_and_disguised_content_are_rejected_before_handler(): void
    {
        foreach ([
            ['malicious.jpg', '<?php echo 1;'],
            ['vector.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
            ['vector.png', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'],
            ['fake.pdf', '<html>Not a PDF</html>'],
        ] as [$name, $content]) {
            $response = $this->upload(['type' => 'file', 'files' => [$this->content($name, $content)]]);
            $response->assertStatus(422)->assertJson(['status' => false]);
            $this->assertNotEmpty($response->json('message'));
            $this->assertNotEmpty($response->json('data')['files.0'] ?? null);
        }
        $this->assertSame(0, $this->acceptedRequests);
    }

    public function test_wrong_category_unknown_type_and_empty_batch_are_rejected(): void
    {
        $this->upload(['type' => 'pdf', 'files' => [UploadedFile::fake()->image('photo.png')]])->assertStatus(422);
        $this->upload(['type' => '../images', 'files' => [UploadedFile::fake()->image('photo.png')]])->assertStatus(422);
        $this->upload(['type' => ['image'], 'files' => [UploadedFile::fake()->image('photo.png')]])->assertStatus(422);
        $this->upload(['type' => 'image', 'files' => []])->assertStatus(422);
        $this->assertSame(0, $this->acceptedRequests);
    }

    public function test_count_individual_size_and_combined_size_limits(): void
    {
        $images = fn ($count, $size) => array_map(fn ($i) => UploadedFile::fake()->image('photo'.$i.'.png')->size($size), range(1, $count));
        $this->upload(['type' => 'image', 'files' => $images(11, 1)])->assertStatus(422);
        $this->upload(['type' => 'image', 'files' => $images(1, 5121)])->assertStatus(422);
        $this->upload(['type' => 'image', 'files' => $images(5, 5120)])->assertStatus(422);
        $this->assertSame(0, $this->acceptedRequests);
        $this->upload(['type' => 'image', 'files' => $images(4, 5120)])->assertOk();
        $this->upload(['type' => 'image', 'files' => $images(10, 1)])->assertOk();
    }

    public function test_huge_image_dimensions_are_rejected_without_allocating_a_huge_bitmap(): void
    {
        $image = UploadedFile::fake()->image('small.png', 1, 1);
        $bytes = file_get_contents($image->getPathname());
        foreach ([[6001, 1], [5000, 4000]] as [$width, $height]) {
            $header = substr_replace($bytes, pack('NN', $width, $height), 16, 8);
            $this->upload(['type' => 'image', 'files' => [$this->content('huge.png', $header)]])->assertStatus(422);
        }
        $this->assertSame(0, $this->acceptedRequests);
    }
}
