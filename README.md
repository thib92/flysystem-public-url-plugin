# Flysystem Public URL Plugin

This repo is a plugin for the [Flysystem](https://flysystem.thephpleague.com/docs/) PHP library.

It allows you to get a public URL to access a file.

## Getting started

Start by adding this plugin to your dependencies using Compose:
```bash
composer require thib92/flysystem-public-url-plugin
```

Then, add the plugin to your Flysystem filesystem:

```php
<?php
$adapter = new \League\Flysystem\Adapter\Local(__DIR__.'/path/to/root/');
$filesystem = new \League\Flysystem\Filesystem($adapter);
$filesystem->addPlugin(new \Thib\FlysystemPublicUrlPlugin\PublicUrlPlugin);
```

Finally, use it as a regular Flysystem plugin:

```php
<?php
$filesystem->getPublicUrl("/path/to/my/file");
```

## Supported adapters

The supported Flysystem adapters are:
* [Local](https://flysystem.thephpleague.com/docs/adapter/local/) : [LocalUrlAdapter](./src/Adapter/LocalUrlAdapter.php)
* [AWS S3 v3](https://flysystem.thephpleague.com/docs/adapter/aws-s3/) : [LocalUrlAdapter](./src/Adapter/AwsS3UrlAdapter.php)

## Adapters options

Some adapters need options.
For example, the `Local` Flysystem does not know if the upload directory is served by a webserver.

In order to set settings, you can use the `setParam` method of the `PublicUrlPlugin`. For example:
```php
<?php
$plugin = new \Thib\FlysystemPublicUrlPlugin\PublicUrlPlugin();
$plugin->setParam(\Thib\FlysystemPublicUrlPlugin\Adapter\LocalUrlAdapter::class, [
    "/path/to/webserver/root"
]);
```

The first argument of `setParam` is the class of the PublicUrlAdapter you use.<br>
The second one is a sequential array of constructor arguments for this adapter.
Refer to the individual adapter documentations below to know what to set

## Adapters reference

| Flysystem Adapter | Public URL Adapter | Constructor arguments        |
|-------------------|--------------------|------------------------------|
| Local             | LocalUrlAdapter    | * Public webserver root path |
| AWS S3 v3         | AwsS3UrlAdapter    | None                         |

## Adding your own adapter

If you need an adapter for another Flysystem adapter, two steps are required:

### 1. Create your Adapter class

Your Adapter class will need to extend the [AbstractPublicUrlAdapter](./src/Adapter/AbstractPublicUrlAdapter.php).
You will directly get a reference to the filesystem instance with `$this->filesystem`.
You will need to implement the `getPublicUrl(string $path): string` method.

### 2. Register it in the plugin

Then you need to use the `PublicUrlPlugin::addAdapter` method to register your adapter.
This method takes 3 arguments:
* The Flysystem adapter class that your PublicUrlAdapter works for
* Your PublicUrlAdapter class (the plugin will create a new instance on every call to `getPublicUrl()`)
* Optional: an array of constructor arguments, see [Adapter Options](#adapters-options)

## Contributing

I only created this plugin for the needs of a project I had. I only used the AWS S3 and the Local adapters.
Therefore, if you create an adapter, feel free to create a PR on this repo with your code.
Don't forget to add tests:
* Unit tests in the [tests/Adapter](./tests/Adapter) folder
* Integration tests in [tests/PublicUrlPluginTest](./tests/PublicUrlPluginTest.php) file for an integration test to the plugin