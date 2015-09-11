# Imagecraft

[![Build Status](https://travis-ci.org/coldume/imagecraft.svg)](https://travis-ci.org/coldume/imagecraft)

Imagecraft is a reliable and extensible PHP image manipulation library. It can
edit and compose images in multiple layers and supports watermark, resize and
text. Furthermore, it keeps GIF animation, optimizes memory usage, catches
memory exhausted error, and gives an user-friendly/translated feedback.

Imagecraft is intended to be an image abstraction & manipulation layer, which
offers a PDO-like API. It currently supports GD extension, and will support
ImageMagick in version 2.0. If you have any suggestions, comments or feedback,
let me know. Thanks.

## Requirement

*   PHP >= 5.4.0
*   PHP GD extension

## Installation

[Composer](https://getcomposer.org) is the recommended way to install
Imagecraft, simply add a dependency to your project's composer.json file:

````json
{
    "require": {
        "coldume/imagecraft": "~1.0"
    }
}
````

Or, you can install from an archive file: [imagecraft_1.0.4.zip](https://docs.google.com/uc?id=0B5ruhRHby-kbQlVzcHN3TFhNNzQ&export=download)

## Usage

### Provide the User with a Hint on What He Needs to Upload

````php
use Imagecraft\ImageBuilder;

$options = ['engine' => 'php_gd'];
$builder = new ImageBuilder($options);
$context = $builder->about();
if (!$context->isEngineSupported()) {
    echo 'Sorry, image processing service is currently not available.'.PHP_EOL;
} else {
    $formats = $context->getSupportedImageFormatsToString();
    echo 'Make sure that you\'re using one of the following image formats: '.$formats.'.'.PHP_EOL;
    $formats = $context->getSupportedFontFormatsToString();
    echo 'We accept the following font formats: '.$formats.'.'.PHP_EOL;
}
````

### Build an Image in Fluent Pattern

1.  Client uploads an [image](https://cloud.githubusercontent.com/assets/5236124/9806426/4b49fe20-5812-11e5-8a44-bdbdfff2787a.gif) from URL.
2.  Server performs resize and [watermark](https://cloud.githubusercontent.com/assets/5236124/9806423/4b465d4c-5812-11e5-8dc9-f0f7257e7cda.png) operations.
5.  Server returns a message in English if an error occured, such as data size
    exceeds allowable limit (2MB), timeout expired (20s), file not found, etc.

````php
use Imagecraft\ImageBuilder;

$options = ['engine' => 'php_gd', 'locale' => 'en'];
$builder = new ImageBuilder($options);
$image = $builder
    ->addBackgroundLayer()
        ->http('www.imagecraft.cc/web/images/pikachu.gif', 2048, 20)
        ->resize(400, 400, 'shrink')
        ->done()
    ->addImageLayer()
        ->filename(__DIR__.'/pikachu_what_to_do_logo_by_mnrart-d5h998b.png')
        ->move(-20, -20, 'bottom_right')
        ->done()
    ->save()
;
if ($image->isValid()) {
    file_put_contents(__DIR__.'/output.'.$image->getExtension(), $image->getContents());
} else {
    echo $image->getMessage().PHP_EOL;
}
````

![Fluent Pattern Output](https://cloud.githubusercontent.com/assets/5236124/9806425/4b49d224-5812-11e5-92d6-2f3514ebd660.gif)

### Build an Image in Classic Pattern

1.  Client uploads an [image](https://cloud.githubusercontent.com/assets/5236124/9806422/4b4582dc-5812-11e5-9e85-8974e48dfdfd.gif) directly.
2.  Server performs thumbnail and [text](https://drive.google.com/open?id=0B5ruhRHby-kbQmxKVDVEaEc3Zkk) operations.
5.  Server returns a message in traditional Chinese if an error occured.

````php
use Imagecraft\ImageBuilder;

$options = ['engine' => 'php_gd', 'locale' => 'zh_TW'];
$builder = new ImageBuilder($options);

$layer = $builder->addBackgroundLayer();
$layer->filename(__DIR__.'/yotsuba_koiwai.gif');
$layer->resize(150, 150, 'fill_crop');

$layer = $builder->addTextLayer();
$layer->font(__DIR__.'/minecraftia.ttf', 12, '#FFF');
$layer->label(date('F j, Y, g:i a'));
$layer->move(-10, -10, 'bottom_right');

$image = $builder->save();
if ($image->isValid()) {
    file_put_contents(__DIR__.'/output.'.$image->getExtension(), $image->getContents());
} else {
    echo $image->getMessage().PHP_EOL;
}
````

![Classic Pattern Output](https://cloud.githubusercontent.com/assets/5236124/9806424/4b485480-5812-11e5-818c-a2aa8b2c2c9d.gif)

### When Debugging is Easier than Expected

````php
use Imagecraft\ImageBuilder;

// Build an image

if ($image->isValid()) {
    file_put_contents(__DIR__.'/output.'.$image->getExtension(), $image->getContents());
    print_r($image->getExtras());
} else {
    echo $image->getMessage().PHP_EOL;
    print_r($image->getVerboseMessage());
}
````

## Cheat Sheet

### Options

| Name              | Default   | Available       | Description                           |
| :---------------- | :-------- | :-------------- | :------------------------------------ |
| `engine`          | `php_gd`  | `php_gd`        | image library to be used              |
| `cache_dir`       | `n/a`     | `n/a`           | project-specific cache directory      |
| `debug`           | `true`    | `true, false`   | debug mode of your project            |
| `locale`          | `en`      | `en, zh_TW, ..` | error message language                |
| `jpeg_quality`    | `100`     | `[0, 100]`      | quality of the JPEG image             |
| `png_compression` | `100`     | `[0, 100]`      | compression level of the PNG image    |
| `memory_limit`    | `-10`     | `[-∞, ∞]`       | maximum memory to use in MB           |
| `gif_animation`   | `true`    | `true, false`   | whether to maintain the GIF animation |
| `output_format`   | `default` | `jpeg, png, ..` | output image format                   |

### Layers

| Method                                                                                                          | Compatible                      |
| :-------------------------------------------------------------------------------------------------------------- | :------------------------------ |
| `http(`![Hint][http_url]`$url, `![Hint][http_dataLimit]`$dataLimit = -1, `![Hint][http_timeout]`$timeout = -1)` | `BackgroundLayer`, `ImageLayer` |
| `filename($filename)`                                                                                           | `BackgroundLayer`, `ImageLayer` |
| `contents($contents)`                                                                                           | `BackgroundLayer`, `ImageLayer` |
| `resize($width, $height, `![Hint][resize_option]`$option = 'shrink')`                                           | `BackgroundLayer`, `ImageLayer` |
| `move($x, $y, `![Hint][move_gravity]`$gravity = 'center')`                                                      | `ImageLayer`, `TextLayer`       |
| `font($filename, `![Hint][font_size]`$size = 12, $color = '#FFF')`                                              | `TextLayer`                     |
| `label($label)`                                                                                                 | `TextLayer`                     |
| `angle(`![Hint][angle_angle]`$angle)`                                                                           | `TextLayer`                     |
| `lineSpacing($lineSpacing)`                                                                                     | `TextLayer`                     |
| `box(array `![Hint][box_paddings]`$paddings, `![Hint][box_color]`$color = null)`                                | `TextLayer`                     |

[http_url]:       http://www.imagecraft.cc/web/images/tooltip.png "The URL begins with http://, https:// or nothing."
[http_dataLimit]: http://www.imagecraft.cc/web/images/tooltip.png "The data limit in KB. If set to -1, no data limit is imposed."
[http_timeout]:   http://www.imagecraft.cc/web/images/tooltip.png "The timeout in second. If set to -1, no timeout is imposed"
[resize_option]:  http://www.imagecraft.cc/web/images/tooltip.png "The resize option. Predefined values: shrink, fill_crop."
[move_gravity]:   http://www.imagecraft.cc/web/images/tooltip.png "The move gravity. Predefined values: top_left, top_center, top_right, center_left, center, center_right, bottom_left, bottom_center, bottom_right."
[font_size]:      http://www.imagecraft.cc/web/images/tooltip.png "The font size to use in points."
[angle_angle]:    http://www.imagecraft.cc/web/images/tooltip.png "The angle in degrees."
[box_paddings]:   http://www.imagecraft.cc/web/images/tooltip.png "The four paddings of the box."
[box_color]:      http://www.imagecraft.cc/web/images/tooltip.png "The color of the box. null means transparent."

*   Letting your mouse hover over ![Hint](http://www.imagecraft.cc/web/images/tooltip.png "Yes, you got it.")
    should cause a tooltip to appear.
*   Method `http()`, `filename()` or `contents()` is required for `BackgroundLayer`
    and `ImageLayer`.
*   Methods `font()` and `label()` are required for `TextLayer`.

## Tips

1.  In addition to the default English error message, Imagecraft can be switched
    to other languages. You can help with existing translations or to add
    another language. The translation files are located at:

    *   https://github.com/coldume/imc-stream/tree/master/src/Resources/translations
    *   https://github.com/coldume/imagecraft/tree/master/src/Resources/translations

## Resources

*   Mike Flickinger (2005) "What's In A GIF".

    http://giflib.sourceforge.net/whatsinagif/index.html.

*   David C. Kay (1994) "Graphic File Formats".

    http://www.w3.org/Graphics/GIF/spec-gif89a.txt.
