Install
```
composer require dennykuo/data-to-js
```

PHP
```
require __DIR__ . '/vendor/autoload.php';

use DataToJs\DataToJs;

...

$js = (new DataToJs)->put(['foo' => 'bar']);

(or)

$data = new stdClass();
$data->first = 'Peng';
$data->last  = 'Jie';

$js = (new DataToJs)->put(['foo' => 'bar', 'name' => $data]);

```

HTML
```
<script>
  <?php $js->output(); ?>
</script>
```

Options
```
$js = (new DataToJs($namespace = 'app'))->put(['foo' => 'bar']);

// console.log(app.foo) => 'bar'
```

