<?php declare(strict_types=1);

namespace src\Core\Content\Test\Media\Pathname;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\Pathname\PathGenerator;
use Shopware\Core\Content\Media\Pathname\PathnameStrategy\FilenamePathnameStrategy;
use Shopware\Core\Content\Media\Pathname\UrlGenerator;
use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
class UrlGeneratorTest extends TestCase
{
    public function testAbsoluteMediaUrl(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $urlGenerator = new UrlGenerator($pathGenerator, new RequestStack());
        static::assertSame(
            EnvironmentHelper::getVariable('APP_URL', 'http://localhost:8000') . '/media/d0/b3/24/file.jpg',
            $urlGenerator->getAbsoluteMediaUrl($mediaEntity)
        );
    }

    public function testMediaUrlWithEmptyRequest(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $requestStack = new RequestStack();
        $requestStack->push(new Request());
        $urlGenerator = new UrlGenerator($pathGenerator, $requestStack);
        static::assertSame(
            EnvironmentHelper::getVariable('APP_URL', 'http://localhost:8000') . '/media/d0/b3/24/file.jpg',
            $urlGenerator->getAbsoluteMediaUrl($mediaEntity)
        );
    }

    public function testAbsoluteThumbnailUrl(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
            ]
        );
        $mediaThumbnailEntity = new MediaThumbnailEntity();
        $mediaThumbnailEntity->assign(
            [
                'width' => 100,
                'height' => 100,
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $urlGenerator = new UrlGenerator($pathGenerator, new RequestStack());
        static::assertSame(
            EnvironmentHelper::getVariable('APP_URL', 'http://localhost:8000') . '/thumbnail/d0/b3/24/file.jpg_100x100',
            $urlGenerator->getAbsoluteThumbnailUrl($mediaEntity, $mediaThumbnailEntity)
        );
    }

    public function testRelativeMediaUrl(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $urlGenerator = new UrlGenerator($pathGenerator, new RequestStack());
        static::assertSame(
            'media/d0/b3/24/file.jpg',
            $urlGenerator->getRelativeMediaUrl($mediaEntity)
        );
    }

    public function testRelativehumbnailUrl(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
            ]
        );
        $mediaThumbnailEntity = new MediaThumbnailEntity();
        $mediaThumbnailEntity->assign(
            [
                'width' => 100,
                'height' => 100,
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $urlGenerator = new UrlGenerator($pathGenerator, new RequestStack());
        static::assertSame(
            'thumbnail/d0/b3/24/file.jpg_100x100',
            $urlGenerator->getRelativeThumbnailUrl($mediaEntity, $mediaThumbnailEntity)
        );
    }

    public function testResetUrlGenerator(): void
    {
        $mediaEntity = new MediaEntity();
        $mediaEntity->assign(
            [
                'id' => Uuid::randomHex(),
                'fileName' => 'file.jpg',
                'path' => 'my/pa/th/file.jpg',
            ]
        );

        $pathGenerator = new PathGenerator(new FilenamePathnameStrategy());
        $urlGenerator = new UrlGenerator($pathGenerator, new RequestStack());
        $urlGenerator->getAbsoluteMediaUrl($mediaEntity);
        $urlGeneratorAssert = new UrlGenerator($pathGenerator, new RequestStack());
        $urlGeneratorAssert->getAbsoluteMediaUrl($mediaEntity);
        $urlGeneratorAssertStaysUntouched = new UrlGenerator($pathGenerator, new RequestStack());

        // Both $fallbackBaseUrl should be same
        static::assertSame(print_r($urlGeneratorAssert, true), print_r($urlGenerator, true));

        $urlGenerator->reset();

        // Both $fallbackBaseUrl should be same
        static::assertSame(print_r($urlGeneratorAssertStaysUntouched, true), print_r($urlGenerator, true));

        // Both $fallbackBaseUrl should be different
        static::assertNotSame(print_r($urlGeneratorAssert, true), print_r($urlGenerator, true));
    }
}
