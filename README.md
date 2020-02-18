Csv Driver for Cakephp3
========

An Csv datasource for CakePHP 3.5,3.6,3.7

## Installing via composer

Install [composer](http://getcomposer.org) and run:

```bash
composer require giginc/cakephp3-driver-csv
```

## Defining a connection
Now, you need to set the connection in your config/app.php file:

```php
 'Datasources' => [
...

    'csv' => [
        'className' => 'Giginc\Csv\Database\Connection',
        'driver' => 'Giginc\Csv\Database\Driver\Csv',
        'baseDir' => './', // local path on the server relative to CONFIG
    ],
],
```

## Models
After that, you need to load Giginc\Csv\ORM\Table in your tables class:

```php
//src/Model/Table/ProductsTable.php
namespace App\Model\Table;

use Giginc\Csv\ORM\Table;

class ProductsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setPrimaryKey('id');
        $this->setDelimiter(',');
        $this->setSchemaRow(1); // Schema row is 1 row.
        $this->setTable('products'); // load file is CONFIG/materials.csv
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'csv';
    }
}
```

## Controllers

```php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Pages Controller
 *
 * @property \App\Model\Table\PagesTable $Pages
 *
 * @method \App\Model\Entity\Review[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->loadModel('Products');
        $data = $this->Products->get(1);
    }
}
```

Let's see a quick example:

```csv
//config/products.csv
id,category,name,price
1,"iphone","iPhone",8000
2,"macbook_pro","Macbook Pro",150000
3,"redmi_3s","Redmi 3S Prime",12000
4,"redmi_4x":"Redmi 4X",15000
5,"macbook_air":"Macbook Air",110000
6,"macbook_air":"Macbook Air 1",81000
```

## LICENSE

[The MIT License (MIT) Copyright (c) 2020](http://opensource.org/licenses/MIT)

