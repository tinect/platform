<?php declare(strict_types=1);

namespace Shopware\Core\Content\Media\Pathname;

use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\Exception\EmptyMediaFilenameException;
use Shopware\Core\Content\Media\Exception\EmptyMediaIdException;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ResetInterface;

class UrlGenerator implements UrlGeneratorInterface, ResetInterface
{
    private AbstractPathGenerator $pathGenerator;

    private RequestStack $requestStack;

    private ?string $baseUrl;

    private ?string $fallbackBaseUrl = null;

    /**
     * @internal
     */
    public function __construct(
        AbstractPathGenerator $pathGenerator,
        RequestStack $requestStack,
        ?string $baseUrl = null
    ) {
        $this->pathGenerator = $pathGenerator;
        $this->requestStack = $requestStack;

        $this->baseUrl = $this->normalizeBaseUrl($baseUrl);
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getRelativeMediaUrl(MediaEntity $media): string
    {
        $this->validateMedia($media);

        if (empty($media->getPath())) {
            return $this->pathGenerator->generatePath($media);
        }

        return $media->getPath();
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getAbsoluteMediaUrl(MediaEntity $media): string
    {
        return $this->getBaseUrl() . '/' . $this->getRelativeMediaUrl($media);
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getRelativeThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string
    {
        $this->validateMedia($media);

        if (empty($thumbnail->getPath())) {
            return $this->pathGenerator->generatePath($media, $thumbnail);
        }

        return $thumbnail->getPath();
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getAbsoluteThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string
    {
        return $this->getBaseUrl() . '/' . $this->getRelativeThumbnailUrl($media, $thumbnail);
    }

    public function reset(): void
    {
        $this->fallbackBaseUrl = null;
    }

    private function createFallbackUrl(): string
    {
        $request = $this->requestStack->getMainRequest();
        if ($request && $request->getHttpHost() !== '' && $request->getHttpHost() !== ':') {
            $basePath = $request->getSchemeAndHttpHost() . $request->getBasePath();

            if (parse_url($basePath) === false) {
                return (string) EnvironmentHelper::getVariable('APP_URL');
            }

            return rtrim($basePath, '/');
        }

        return (string) EnvironmentHelper::getVariable('APP_URL');
    }

    private function normalizeBaseUrl(?string $baseUrl): ?string
    {
        if ($baseUrl === null) {
            return null;
        }

        return rtrim($baseUrl, '/');
    }

    private function getBaseUrl(): string
    {
        if (!$this->baseUrl) {
            return $this->fallbackBaseUrl ?? $this->fallbackBaseUrl = $this->createFallbackUrl();
        }

        return $this->baseUrl;
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    private function validateMedia(MediaEntity $media): void
    {
        if (empty($media->getId())) {
            throw new EmptyMediaIdException();
        }

        if (empty($media->getFileName())) {
            throw new EmptyMediaFilenameException();
        }
    }
}
