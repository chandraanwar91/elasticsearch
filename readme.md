## Laravel, Lumen and Native php elasticseach query builder to build complex queries using an elegant syntax

- Keeps you away from wasting your time by replacing array queries with a simple and elegant syntax you will love.
- Elasticsearch data model for types and indices inspired from laravel eloquent.
- Feeling free to create, drop, mapping and reindexing through easy artisan console commands.
- Lumen framework support.
- Native php and composer based applications support.
- Can be used as a [laravel scout](https://laravel.com/docs/5.4/scout) driver.
- Dealing with multiple elasticsearch connections at the same time.
- Awesome pagination based on [LengthAwarePagination](https://github.com/illuminate/pagination).
- Caching queries using a caching layer over query builder built on [laravel cache](https://laravel.com/docs/5.4/cache).

## Requirements

- `php` >= 5.6.6 

- `laravel/laravel` >= 5.* or `laravel/lumen` >= 5.* or `composer application`

## Installation

### <u>Laravel Installation</u>


##### 1) Install package using composer.

```bash
$ composer require chandraanwar91/elasticsearch
```

##### 2) Add package service provider (< laravel 5.5).

```php
Chandraanwar91\Elasticsearch\ElasticsearchServiceProvider::class
```

##### 3) Add package alias (< laravel 5.5).

```php
'ES' => Chandraanwar91\Elasticsearch\Facades\ES::class
```
	
##### 4) Publishing.

```bash
$ php artisan vendor:publish --provider="Chandraanwar91\Elasticsearch\ElasticsearchServiceProvider"
```

### <u>Lumen Installation</u>

##### 1) Install package using composer.
```bash
$ composer require Chandraanwar91/elasticsearch
```

##### 2) Add package service provider in `bootstrap/app.php`.

```php
$app->register(Chandraanwar91\Elasticsearch\ElasticsearchServiceProvider::class);
```
	
##### 3) Copy package config directory `vendor/Chandraanwar91/elasticsearch/src/config` to root folder alongside with `app` directory.
	
	
##### 4) Making Lumen work with facades by uncommenting this line in `bootstrap/app.php`.

```php
$app->withFacades();
```

If you don't want to enable working with Lumen facades you can access the query builder using `app("es")`.

```php
app("es")->index("my_index")->type("my_type")->get();

# is similar to 

ES::index("my_index")->type("my_type")->get();
```   
   
### <u>Composer Installation</u>

You can install package with any composer-based applications

##### 1) Install package using composer.

```bash
$ composer require chandraanwar91/elasticsearch
```

##### 2) Creating a connection.

```php
require "vendor/autoload.php";

use Chandraanwar91\Elasticsearch\Connection;

$connection = Connection::create([
    'servers' => [
        [
            "host" => '127.0.0.1',
            "port" => 9200,
            'user' => '',
            'pass' => '',
            'scheme' => 'http',
        ],
    ],
    
	// Custom handlers
	// 'handler' => new MyCustomHandler(),

    'index' => 'my_index',
]);


# access the query builder using created connection

$documents = $connection->search("hello")->get();
```


## Configuration (Laravel & Lumen)

  
After publishing, two configuration files will be created.
  
  - `config/es.php` where you can add more than one elasticsearch server.

```php
# Here you can define the default connection name.

'default' => env('ELASTIC_CONNECTION', 'default'),

# Here you can define your connections.

'connections' => [
	'default' => [
	    'servers' => [
	        [
	            "host" => env("ELASTIC_HOST", "127.0.0.1"),
	            "port" => env("ELASTIC_PORT", 9200),
	            'user' => env('ELASTIC_USER', ''),
	            'pass' => env('ELASTIC_PASS', ''),
	            'scheme' => env('ELASTIC_SCHEME', 'http'),
	        ]
	    ],
	    
		// Custom handlers
		// 'handler' => new MyCustomHandler(),
		
		'index' => env('ELASTIC_INDEX', 'my_index')
	]
],
 
# Here you can define your indices.
 
'indices' => [
	'my_index_1' => [
	    "aliases" => [
	        "my_index"
	    ],
	    'settings' => [
	        "number_of_shards" => 1,
	        "number_of_replicas" => 0,
	    ],
	    'mappings' => [
	        'posts' => [
                'properties' => [
                    'title' => [
                        'type' => 'string'
                    ]
                ]
	        ]
	    ]
	]
]

```
  
  - `config/scout.php` where you can use package as a laravel scout driver.

## Elasticsearch data model

Each index type has a corresponding "Model" which is used to interact with that type.
Models allow you to query for data in your types or indices, as well as insert new documents into the type.


##### Basic usage
```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class Book extends Model
{
        
    protected $type = "books";
    
}
```

The above example will use the default connection and default index in `es.php`. You can override both in the next example.

```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class Post extends Model
{
    
    # [optional] Default: default elasticsearch driver
    # To override default conenction name of es.php file.
    # Assumed that there is a connection with name 'my_connection'
    protected $connection = "my_connection";
    
    # [optional] Default: default connection index
    # To override default index name of es.php file.
    protected $index = "my_index";
    
    protected $type = "books";
    
}
```

##### Retrieving Models

Once you have created a model and its associated index type, you are ready to start retrieving data from your index. For example:


```php
<?php

use App\Models\Book;

$books = Book::all();

foreach ($books as $book) {
    echo $book->name;
}

```

##### Adding Additional Constraints

The `all` method will return all of the results in the model's type. Each elasticsearch model serves as a query builder, you may also add constraints to queries, and then use the `get()` method to retrieve the results:

```php
$books = App\Book::where('status', 1)
               ->orderBy('created_at', 'desc')
               ->take(10)
               ->get();

```


##### Retrieving Single Models

```php
// Retrieve a model by document key...
$book = Book::find("AVp_tCaAoV7YQD3Esfmp");
```


##### Inserting Models


To create a new document, simply create a new model instance, set attributes on the model, then call the `save()` method:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Create a new post instance.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate the request...

        $book = new Book;

        $bo->title = $request->title;

        $post->save();
    }
}
```

##### Updating Models

The `save()` method may also be used to update models that already exist. To update a model, you should retrieve it, set any attributes you wish to update, and then call the save method.

```php
$post = App\Post::find(1);

$post->title = 'New Post Title';

$post->save();
```

##### Deleting Models

To delete a model, call the `delete()` method on a model instance:

```php
$post = App\Post::find(1);

$post->delete();
```

##### Query Scopes

Scopes allow you to define common sets of constraints that you may easily re-use throughout your application. For example, you may need to frequently retrieve all posts that are considered "popular". To define a scope, simply prefix an Eloquent model method with scope.

Scopes should always return a Query instance.

```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class Post extends Model
{
    /**
     * Scope a query to only include popular posts.
     *
     * @param \Chandraanwar91\Elasticsearch\Query $query
     * @return \Chandraanwar91\Elasticsearch\Query
     */
    public function scopePopular($query, $votes)
    {
        return $query->where('votes', '>', $votes);
    }

    /**
     * Scope a query to only include active posts.
     *
     * @param \Chandraanwar91\Elasticsearch\Query $query
     * @return \Chandraanwar91\Elasticsearch\Query
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
```

Once the scope has been defined, you may call the scope methods when querying the model. However, you do not need to include the scope prefix when calling the method. You can even chain calls to various scopes, for example:

```php
$posts = App\Post::popular(100)->active()->orderBy('created_at')->get();
```


##### Accessors & Mutators

###### Defining An Accessor
To define an `accessor`, create a getFooAttribute method on your model where `Foo` is the "studly" cased name of the column you wish to access. In this example, we'll define an accessor for the `title` attribute. The accessor will automatically be called by model when attempting to retrieve the value of the `title` attribute:


```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class post extends Model
{
    /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return ucfirst($value);
    }
}
```

As you can see, the original value of the column is passed to the accessor, allowing you to manipulate and return the value. To access the value of the accessor, you may simply access the `title` attribute on a model instance:

```php
$post = App\Post::find(1);

$title = $post->title;
```

Occasionally, you may need to add array attributes that do not have a corresponding field in your index. To do so, simply define an accessor for the value:

```php
public function getIsPublishedAttribute()
{
    return $this->attributes['status'] == 1;
}
```

Once you have created the accessor, just add the value to the `appends` property on the model:

```php
protected $appends = ['is_published'];
```

Once the attribute has been added to the appends list, it will be included in model's array.

###### Defining A Mutator

To define a mutator, define a `setFooAttribute` method on your model where `Foo` is the "studly" cased name of the column you wish to access. So, again, let's define a mutator for the `title` attribute. This mutator will be automatically called when we attempt to set the value of the `title`attribute on the model:

```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class post extends Model
{
    /**
     * Set the post title.
     *
     * @param  string  $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        return strtolower($value);
    }
}
```

The mutator will receive the value that is being set on the attribute, allowing you to manipulate the value and set the manipulated value on the model's internal `$attributes` property. So, for example, if we attempt to set the title attribute to `Awesome post to read`:

```php
$post = App\Post::find(1);

$post->title = 'Awesome post to read';
```

In this example, the setTitleAttribute function will be called with the value `Awesome post to read`. The mutator will then apply the strtolower function to the name and set its resulting value in the internal $attributes array.



##### Attribute Casting


The `$casts` property on your model provides a convenient method of converting attributes to common data types. The `$casts` property should be an array where the key is the name of the attribute being cast and the value is the type you wish to cast the column to. The supported cast types are: `integer`, `float`, `double`, `string`, `boolean`, `object` and `array`.


For example, let's cast the `is_published` attribute, which is stored in our index as an integer (0 or  1) to a `boolean` value:

```php
<?php

namespace App;

use Chandraanwar91\Elasticsearch\Model;

class Post extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];
}

```

Now the `is_published` attribute will always be cast to a `boolean` when you access it, even if the underlying value is stored in the index as an integer:


```php
$post = App\Post::find(1);

if ($post->is_published) {
    //
}
```



## Usage as a query builder

#### Creating a new index

```php
ES::create("my_index");
    
# or 
    
ES::index("my_index")->create();
```
    
##### Creating index with custom options (optional)
   
```php
ES::index("my_index")->create(function($index){
        
    $index->shards(5)->replicas(1)->mapping([
        'my_type' => [
            'properties' => [
                'first_name' => [
                    'type' => 'string',
                ],
                'age' => [
                    'type' => 'integer'
                ]
            ]
        ]
    ])
    
});
    
# or
    
ES::create("my_index", function($index){
  
      $index->shards(5)->replicas(1)->mapping([
          'my_type' => [
              'properties' => [
                  'first_name' => [
                      'type' => 'string',
                  ],
                  'age' => [
                      'type' => 'integer'
                  ]
              ]
          ]
      ])
  
});

```
#### Dropping index

```php
ES::drop("my_index");
    
# or
    
ES::index("my_index")->drop();
```
#### Running queries
```php
$documents = ES::connection("default")
                ->index("my_index")
                ->type("my_type")
                ->get();    # return a collection of results
```
You can rewrite the above query to

```php
$documents = ES::type("my_type")->get();    # return a collection of results
```

The query builder will use the default connection, index name in configuration file `es.php`. 
 
Connection and index names in query overrides connection and index names in configuration file `es.php`.

##### Getting document by id
```php
ES::type("my_type")->id(3)->first();
    
# or
    
ES::type("my_type")->_id(3)->first();
```
##### Sorting
```php 
ES::type("my_type")->orderBy("created_at", "desc")->get();
    
# Sorting with text search score
    
ES::type("my_type")->orderBy("_score")->get();
```
##### Limit and offset
```php
ES::type("my_type")->take(10)->skip(5)->get();
```
##### Select only specific fields
```php    
ES::type("my_type")->select("title", "content")->take(10)->skip(5)->get();
```
##### Where clause
```php    
ES::type("my_type")->where("status", "published")->get();

# or

ES::type("my_type")->where("status", "=", "published")->get();
```
##### Where greater than
```php
ES::type("my_type")->where("views", ">", 150)->get();
```
##### Where greater than or equal
```php
ES::type("my_type")->where("views", ">=", 150)->get();
```
##### Where less than
```php
ES::type("my_type")->where("views", "<", 150)->get();
```
##### Where less than or equal
```php
ES::type("my_type")->where("views", "<=", 150)->get();
```
##### Where like
```php
ES::type("my_type")->where("title", "like", "foo")->get();
```
##### Where field exists
```php
ES::type("my_type")->where("hobbies", "exists", true)->get(); 

# or 

ES::type("my_type")->whereExists("hobbies", true)->get();
```    
##### Where in clause
```php    
ES::type("my_type")->whereIn("id", [100, 150])->get();
```
##### Where between clause 
```php    
ES::type("my_type")->whereBetween("id", 100, 150)->get();

# or 

ES::type("my_type")->whereBetween("id", [100, 150])->get();
```    
##### Where not clause
```php    
ES::type("my_type")->whereNot("status", "published")->get(); 

# or

ES::type("my_type")->whereNot("status", "=", "published")->get();
```
##### Where not greater than
```php
ES::type("my_type")->whereNot("views", ">", 150)->get();
```
##### Where not greater than or equal
```php
ES::type("my_type")->whereNot("views", ">=", 150)->get();
```
##### Where not less than
```php
ES::type("my_type")->whereNot("views", "<", 150)->get();
```
##### Where not less than or equal
```php
ES::type("my_type")->whereNot("views", "<=", 150)->get();
```
##### Where not like
```php
ES::type("my_type")->whereNot("title", "like", "foo")->get();
```
##### Where not field exists
```php
ES::type("my_type")->whereNot("hobbies", "exists", true)->get(); 

# or

ES::type("my_type")->whereExists("hobbies", true)->get();
```    
##### Where not in clause
```php    
ES::type("my_type")->whereNotIn("id", [100, 150])->get();
```
##### Where not between clause 
```php    
ES::type("my_type")->whereNotBetween("id", 100, 150)->get();

# or

ES::type("my_type")->whereNotBetween("id", [100, 150])->get();
```
   
##### Search by a distance from a geo point 
```php  
ES::type("my_type")->distance("location", ["lat" => -33.8688197, "lon" => 151.20929550000005], "10km")->get();

# or

ES::type("my_type")->distance("location", "-33.8688197,151.20929550000005", "10km")->get();

# or

ES::type("my_type")->distance("location", [151.20929550000005, -33.8688197], "10km")->get();  
```
  
  
##### Search using array queries
      
```php
ES::type("my_type")->body([
    "query" => [
         "bool" => [
             "must" => [
                 [ "match" => [ "address" => "mill" ] ],
                 [ "match" => [ "address" => "lane" ] ]
             ]
         ]
     ]
])->get();

# Note that you can mix between query builder and array queries.
# The query builder will will be merged with the array query.

ES::type("my_type")->body([

	"_source" => ["content"]
	
	"query" => [
	     "bool" => [
	         "must" => [
	             [ "match" => [ "address" => "mill" ] ]
	         ]
	     ]
	],
	   
	"sort" => [
		"_score"
	]
     
])->select("name")->orderBy("created_at", "desc")->take(10)->skip(5)->get();

# The result query will be
/*
Array
(
    [index] => my_index
    [type] => my_type
    [body] => Array
        (
            [_source] => Array
                (
                    [0] => content
                    [1] => name
                )
            [query] => Array
                (
                    [bool] => Array
                        (
                            [must] => Array
                                (
                                    [0] => Array
                                        (
                                            [match] => Array
                                                (
                                                    [address] => mill
                                                )
                                        )
                                )
                        )
                )
            [sort] => Array
                (
                    [0] => _score
                    [1] => Array
                        (
                            [created_at] => desc
                        )
                )
        )
    [from] => 5
    [size] => 10
    [client] => Array
        (
            [ignore] => Array
                (
                )
        )
)
*/

```
  
##### Search the entire document
    
```php
ES::type("my_type")->search("hello")->get();
    
# search with Boost = 2
    
ES::type("my_type")->search("hello", 2)->get();

# search within specific fields with different weights

ES::type("my_type")->search("hello", function($search){
	$search->boost(2)->fields(["title" => 2, "content" => 1])
})->get();
```

##### Return only first record

```php    
ES::type("my_type")->search("hello")->first();
```
  
##### Return only count
```php    
ES::type("my_type")->search("hello")->count();
```
    
##### Scan-and-Scroll queries
    


```php
# These queries are suitable for large amount of data. 
# A scrolled search allows you to do an initial search and to keep pulling batches of results
# from Elasticsearch until there are no more results left.
# It’s a bit like a cursor in a traditional database
    
$documents = ES::type("my_type")->search("hello")
                 ->scroll("2m")
                 ->take(1000)
                 ->get();

# Response will contain a hashed code `scroll_id` will be used to get the next result by running

$documents = ES::type("my_type")->search("hello")
                 ->scroll("2m")
                 ->scrollID("DnF1ZXJ5VGhlbkZldGNoBQAAAAAAAAFMFlJQOEtTdnJIUklhcU1FX2VqS0EwZncAAAAAAAABSxZSUDhLU3ZySFJJYXFNRV9laktBMGZ3AAAAAAAAAU4WUlA4S1N2ckhSSWFxTUVfZWpLQTBmdwAAAAAAAAFPFlJQOEtTdnJIUklhcU1FX2VqS0EwZncAAAAAAAABTRZSUDhLU3ZySFJJYXFNRV9laktBMGZ3")
                 ->get();

# And so on ...
# Note that you don't need to write the query parameters in every scroll. All you need the `scroll_id` and query scroll time.
    
# To clear `scroll_id` 
  
ES::type("my_type")->scrollID("DnF1ZXJ5VGhlbkZldGNoBQAAAAAAAAFMFlJQOEtTdnJIUklhcU1FX2VqS0EwZncAAAAAAAABSxZSUDhLU3ZySFJJYXFNRV9laktBMGZ3AAAAAAAAAU4WUlA4S1N2ckhSSWFxTUVfZWpLQTBmdwAAAAAAAAFPFlJQOEtTdnJIUklhcU1FX2VqS0EwZncAAAAAAAABTRZSUDhLU3ZySFJJYXFNRV9laktBMGZ3")
        ->clear();
```
    
##### Paginate results with 5 records per page

```php   
$documents = ES::type("my_type")->search("hello")->paginate(5);
    
# Getting pagination links
    
$documents->links();

# Bootstrap 4 pagination

$documents->links("bootstrap-4");

# Simple bootstrap 4 pagination

$documents->links("simple-bootstrap-4");

# Simple pagination

$documents->links("simple-default");
```

These are all pagination methods you may use:

```php
$documents->count()
$documents->currentPage()
$documents->firstItem()
$documents->hasMorePages()
$documents->lastItem()
$documents->lastPage()
$documents->nextPageUrl()
$documents->perPage()
$documents->previousPageUrl()
$documents->total()
$documents->url($page)
```

##### Getting the query array without execution

```php
ES::type("my_type")->search("hello")->where("views", ">", 150)->query();
```

##### Getting the original elasticsearch response

```php
ES::type("my_type")->search("hello")->where("views", ">", 150)->response();
```

##### Ignoring bad HTTP response

```php      
ES::type("my_type")->ignore(404, 500)->id(5)->first();
```

##### Query Caching (Laravel & Lumen)

Package comes with a built-in caching layer based on laravel cache.

```php
ES::type("my_type")->search("hello")->remember(10)->get();
	
# Specify a custom cache key

ES::type("my_type")->search("hello")->remember(10, "last_documents")->get();
	
# Caching using other available driver
	
ES::type("my_type")->search("hello")->cacheDriver("redis")->remember(10, "last_documents")->get();
	
# Caching with cache key prefix
	
ES::type("my_type")->search("hello")->cacheDriver("redis")->cachePrefix("docs")->remember(10, "last_documents")->get();
```

##### Executing elasticsearch raw queries

```php
ES::raw()->search([
    "index" => "my_index",
    "type"  => "my_type",
    "body" => [
        "query" => [
            "bool" => [
                "must" => [
                    [ "match" => [ "address" => "mill" ] ],
                    [ "match" => [ "address" => "lane" ] ]
                ]
            ]
        ]
    ]
]);
```
   
##### Insert a new document
    
```php
ES::type("my_type")->id(3)->insert([
    "title" => "Test document",
    "content" => "Sample content"
]);
     
# A new document will be inserted with _id = 3.
  
# [id is optional] if not specified, a unique hash key will be generated.
```
  >
    
##### Bulk insert a multiple of documents at once.
     
```php
# Main query

ES::index("my_index")->type("my_type")->bulk(function ($bulk){

    # Sub queries

	$bulk->index("my_index_1")->type("my_type_1")->id(10)->insert(["title" => "Test document 1","content" => "Sample content 1"]);
	$bulk->index("my_index_2")->id(11)->insert(["title" => "Test document 2","content" => "Sample content 2"]);
	$bulk->id(12)->insert(["title" => "Test document 3", "content" => "Sample content 3"]);
	
});

# Notes from the above query:

# As index and type names are required for insertion, Index and type names are extendable. This means that: 

# If index() is not specified in subquery:
# -- The builder will get index name from the main query.
# -- if index is not specified in main query, the builder will get index name from configuration file.

# And

# If type() is not specified in subquery:
# -- The builder will get type name from the main query.

# you can use old bulk code style using multidimensional array of [id => data] pairs
 
ES::type("my_type")->bulk([
 
	10 => [
		"title" => "Test document 1",
		"content" => "Sample content 1"
	],
	 
	11 => [
		"title" => "Test document 2",
		"content" => "Sample content 2"
	]
 
]);
 
# The two given documents will be inserted with its associated ids
```

##### Update an existing document
```php     
ES::type("my_type")->id(3)->update([
   "title" => "Test document",
   "content" => "sample content"
]);
    
# Document has _id = 3 will be updated.
    
# [id is required]
```

```php
# Bulk update

ES::type("my_type")->bulk(function ($bulk){
    $bulk->id(10)->update(["title" => "Test document 1","content" => "Sample content 1"]);
    $bulk->id(11)->update(["title" => "Test document 2","content" => "Sample content 2"]);
});
```
   
##### Incrementing field
```php
ES::type("my_type")->id(3)->increment("views");
    
# Document has _id = 3 will be incremented by 1.
    
ES::type("my_type")->id(3)->increment("views", 3);
    
# Document has _id = 3 will be incremented by 3.

# [id is required]
```
   
##### Decrementing field
```php 
ES::type("my_type")->id(3)->decrement("views");
    
# Document has _id = 3 will be decremented by 1.
    
ES::type("my_type")->id(3)->decrement("views", 3);
    
# Document has _id = 3 will be decremented by 3.

# [id is required]
```
   
##### Update using script
       
```php
# increment field by script
    
ES::type("my_type")->id(3)->script(
    "ctx._source.$field += params.count",
    ["count" => 1]
);
    
# add php tag to tags array list
    
ES::type("my_type")->id(3)->script(
    "ctx._source.tags.add(params.tag)",
    ["tag" => "php"]
);
    
# delete the doc if the tags field contain mongodb, otherwise it does nothing (noop)
    
ES::type("my_type")->id(3)->script(
    "if (ctx._source.tags.contains(params.tag)) { ctx.op = 'delete' } else { ctx.op = 'none' }",
    ["tag" => "mongodb"]
);
```
   
##### Delete a document
```php
ES::type("my_type")->id(3)->delete();
    
# Document has _id = 3 will be deleted.
    
# [id is required]
```

```php
# Bulk delete

ES::type("my_type")->bulk(function ($bulk){
    $bulk->id(10)->delete();
    $bulk->id(11)->delete();
});
```
