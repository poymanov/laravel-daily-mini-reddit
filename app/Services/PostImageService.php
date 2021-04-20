<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PostImage;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use Str;

class PostImageService
{
    public function storeUploadedFile(UploadedFile $file, int $postId): void
    {
        $imageTypes = config('images.types');

        foreach ($imageTypes as $type => $config) {
            $resizeWidth = $config['width'] ? (int) $config['width'] : null;
            $path        = $this->storeImage($file, $postId, $resizeWidth);
            $this->createPostImage($type, $postId, $path);
        }
    }

    /**
     * Физическое сохранение изображения
     *
     * @param UploadedFile $file
     * @param int          $postId
     * @param int|null     $resizeWidth
     *
     * @return string
     */
    private function storeImage(UploadedFile $file, int $postId, ?int $resizeWidth = null): string
    {
        $image = ImageManagerStatic::make($file);

        if ($resizeWidth) {
            $image = $image->resize($resizeWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $image = $image->encode('jpg');

        $filename = Str::random() . '.jpg';

        $filepath = 'posts/' . $postId . '/' . $filename;

        Storage::disk('public')->put($filepath, $image->getEncoded());

        return $filename;
    }

    /**
     * Запись информации о файле в БД
     *
     * @param string $type
     * @param int    $postId
     * @param string $name
     *
     * @throws Exception
     */
    private function createPostImage(string $type, int $postId, string $name): void
    {
        $postImage          = new PostImage();
        $postImage->type    = $type;
        $postImage->post_id = $postId;
        $postImage->name    = $name;

        if (!$postImage->save()) {
            throw new Exception('Failed to save post image');
        }
    }
}
