# Uploads and images

File storage goes through Flysystem; thumbnails go through LiipImagine when it is installed.

## Configuration

```php
'uploads' => [
    'storage' => 'lag_admin.media_storage',
    'media_directory' => param('kernel.project_dir').'/public/admin/media/uploads',
],
```

The bundle prepends a Flysystem storage named after `uploads.storage`, backed by a `local`
adapter on `uploads.media_directory`. Point `storage` at any storage of your own to push media
to S3 or anywhere else Flysystem supports:

```yaml
# config/packages/flysystem.yaml
flysystem:
    storages:
        app.media_storage:
            adapter: 'aws'
            options:
                client: 'App\Aws\S3Client'
                bucket: 'my-bucket'
```

```php
'uploads' => ['storage' => 'app.media_storage'],
```

## The image model

An uploadable image is a small entity of your own implementing
`LAG\AdminBundle\Image\ImageInterface`:

```php
interface ImageInterface
{
    public function getType(): ?string;
    public function setType(?string $type): void;
    public function getFile(): ?File;          // the uploaded file, not persisted
    public function setFile(?File $file): void;
    public function hasFile(): bool;
    public function getPath(): ?string;        // the stored path, persisted
    public function setPath(?string $path): void;
    public function getOwner(): mixed;
    public function setOwner(?object $owner): void;
}
```

`LAG\AdminBundle\Image\ImageTrait` provides a default implementation.

The owning entity declares its images with one of two interfaces:

| Interface | For | Trait |
|---|---|---|
| `ImageAwareInterface` | one image | `ImageAwareTrait` |
| `ImagesAwareInterface` | a collection of images | `ImagesAwareTrait` |

```php
#[ORM\Entity]
class ProductImage implements ImageInterface
{
    use ImageTrait;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'images')]
    private ?Product $product = null;
}

#[ORM\Entity]
class Product implements ImagesAwareInterface
{
    use ImagesAwareTrait;

    #[LAG\Image]
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    private Collection $images;
}
```

## What happens on save

`UploadImageListener` listens to `lag_admin.resource.data_process` (priority 250, so before
persistence) and, for data implementing either interface, hands each image to `ImageUploader`:

1. skip images with no uploaded file;
2. generate a storage path with `ImagePathGeneratorInterface`;
3. stream the file into the Flysystem storage;
4. delete the previous file, if the image had one;
5. set the new path on the image.

Nothing to call yourself — declaring the interface is enough.

Override the naming scheme by replacing `ImagePathGeneratorInterface`:

```php
$services->set(App\Upload\Generator\HashedPathGenerator::class);
$services->alias(LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface::class, App\Upload\Generator\HashedPathGenerator::class);
```

## Displaying an image

```php
new Image(
    name: 'thumbnail',
    propertyPath: 'images.first',
    imageFilter: 'product_thumbnail',
    upload: false,
)
```

| Option | Default | Description |
|---|---|---|
| `imageFilter` | *(none)* | LiipImagine filter set applied to the path |
| `storage` | the default | storage holding the file |
| `upload` | `true` | whether the property accepts uploads in forms |

`ImageDataTransformer` (from the LiipImagine bridge, wired as the default transformer of
`Image`) turns the stored path into a filtered URL. Without LiipImagine, drop the transformer
and render `data.path` yourself in a template.

Set `upload: false` when the property is display-only — a thumbnail column in a grid should not
turn into a file input on the form.

## Filter sets

The bundle prepends two:

```php
'liip_imagine' => [
    'filter_sets' => [
        'lag_admin_thumbnail' => ['filters' => ['thumbnail' => ['size' => [100, 100]]]],
        'lag_admin_full' => [],
    ],
    'loaders' => [
        'lag_admin' => ['flysystem' => ['filesystem_service' => '%lag_admin.media_storage%']],
    ],
    'data_loader' => 'lag_admin',
],
```

Add your own the usual way — they are merged with these:

```yaml
liip_imagine:
    filter_sets:
        product_thumbnail:
            filters:
                thumbnail: { size: [300, 300], mode: outbound }
```

## Uploading from a form

`Image\ImageType` renders a file input with a preview and a removal checkbox;
`Media\GalleryType` lets the user pick an already uploaded media. Both are declared in your form
type like any other field:

```php
$builder->add('images', CollectionType::class, [
    'entry_type' => ImageType::class,
    'allow_add' => true,
    'allow_delete' => true,
]);
```
