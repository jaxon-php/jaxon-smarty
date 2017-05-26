Jaxon View for Smarty
=====================

Render Smarty templates in Jaxon applications.

Installation
------------

Install this package with Composer.

```json
"require": {
    "jaxon-php/jaxon-smarty": "~2.0"
}
```

Usage
-----

Foreach directory containing Smarty templates, add an entry to the `app.views` section in the configuration.

```php
    'app' => array(
        'views' => array(
            'demo' => array(
                'directory' => '/path/to/demo/views',
                'extension' => '.tpl',
                'renderer' => 'smarty',
            ),
        ),
    ),
```

In the application classes, this is how to render a view in this directory.

```php
    $this->view()->render('demo::/sub/dir/file');
```

Read the [views documentation](https://www.jaxon-php.org/docs/armada/views.html) to learn more about views.
