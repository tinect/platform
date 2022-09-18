---
title: Change behaviour of getting path name to not generate it on every request
issue: NA
author: tinect
author_email: s.koenig@tinect.de
author_github: tinect
---

# Core
* Changed url-collection in private methos `getMediaUrls` of `Content/Mail/Service/MailService.php` to use static path property of media
* Added `path` as new StringField to `MediaDefinition` and `MediaThumbnailDefinition`
* Added `path` as new property to `MediaEntity` and `MediaThumbnailEntity`
* Changed `Content/Media/DataAbstractionLayer/MediaIndexer` to set field `path` of `media` and `media_thumbnail`
* Changed private function `getFilePath` in `Content/Media/File/FileLoader` to return static path
* Changed private functions `doRenameMedia` and `renameThumbnail` in `Content/Media/File/FileSaver` to use static path for currentMedia
* Changed private function `updateMediaEntity`in `Content/Media/File/FileSaver` to update `path`
* Changed private function `updateMediaEntity`in `Content/Media/File/FileSaver` to update `path`
* Changed relative paths of `Content/Media/Pathname/UrlGenerator` to use dedicated service `PathGenerator`
* Added `AbstractPathGenerator` and `PathGenerator` to generate paths for given media
* Changed private functions `handleMediaDeletion` and `handleThumbnailDeletion` of `Content/Media/Subscriber/MediaDeletionSubscriber` to use static path of entity
* Changed functions `updateThumbnails`, `createThumbnailsForSizes`, `ensureConfigIsLoaded` and `getImageResource` of `Content/Media/Thumbnail/ThumbnailService` to use static path of entity
