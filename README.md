# ListBundle

Symfony bundle for pagination, sorting and filtering of list items.

Bundle contains paginators supporting:
 
 * array
 * Doctrine\ORM\Query
 * Doctrine\ORM\QueryBuilder
 * Doctrine\ODM\MongoDB\Query\Query
 * Doctrine\ODM\MongoDB\Query\Builder
 * Doctrine\MongoDB\CursorInterface
 * MongoCursor

For other database queries or cursors you can create own paginators. See [paginators](Resources/doc/bundle_configuration.md#paginators) configuration option.

## Installation

Run composer command
 
```sh
composer require arturdoruch/list-bundle
```

and register list-bundle and required other bundles in application Kernel class.

In Symfony 3
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // Other bundles
        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        new Symfony\Bundle\TwigBundle\TwigBundle(),
        new ArturDoruch\ListBundle\ArturDoruchListBundle(),
    ];
}    
```

In Symfony >= 4
```php
// config/bundles.php
return [
    // Other bundles
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    ArturDoruch\ListBundle\ArturDoruchListBundle::class => ['all' => true],
];
```

### JavaScript support

For JavaScript support install (with `yarn` or `npm`) package [@arturdoruch/list](https://github.com/arturdoruch/js-list). 
Package contains also file with CSS styles, styling the filter form and item list.

## Bundle configuration

See [bundle configuration options](Resources/doc/bundle_configuration.md).

## Usage

In short:

1. [Create controller action getting the item list](#creating-controller-action-getting-the-item-list)
2. [Create template displaying item list](#creating-template-displaying-item-list)

### Controller

#### Creating controller action getting the item list

The controller action requirements:

 * **The route method must be type of `GET`.**
 * **To the twig template must be passed the `ArturDoruch\ListBundle\ItemList` object.** 

Full example of the controller action getting book list:
```php
<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\BookFilterType;
use ArturDoruch\ListBundle\ItemList;
use ArturDoruch\ListBundle\Paginator;
use ArturDoruch\ListBundle\Request\QueryParameterBag;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController
{
    /**
     * @Route(
     *     "/",
     *     methods={"GET"}
     * )
     * @Template("@App/book/list.html.twig")
     */
    public function list(Request $request)
    {
        // (optional) Create filter form.
        // Info: 
        // The request URL query parameter name with filtering parameters, is created based on the form name.
        // Because of that use Symfony\Component\Form\FormFactory::createNamed() method
        // for creating form with own name (e.g. "filter").
        $form = $this->get('form.factory')->createNamed('filter', BookFilterType::class);
        $form->handleRequest($request);

        // Filtering criteria.
        $criteria = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData();
        }
               
        // Get request query parameters (page, limit, sort).
        $parameterBag = new QueryParameterBag($request);        
        // Array with sorting field and order, pair ["field" => "order"]
        $sort = $parameterBag->getSort();
        
        // Get book items - array, query or cursor depend on database type.
        $bookRepository = '';
        $books = $bookRepository->get($criteria, $sort);        
        
        $pagination = Paginator::paginate($books, $parameterBag->getPage(), $parameterBag->getLimit(100));
        // (optional) Set item limits (overrides values form default config "pagination.item_limits").
        $pagination->setItemLimits([50, 100, 200]);

        // (optional) Define SortChoiceCollection to display "select" field with sorting options.
        // Alternatively you can render sorting links in twig template with "arturdoruch_list_sort_link" function.
        $sortChoiceCollection = new SortChoiceCollection();
        $sortChoiceCollection
            ->add('Lowest price', 'price', 'asc') // Sort books by price ascending.
            ->add('Highest price', 'price', 'desc'); // Sort books by price descending.

        return [
            'bookList' => new ItemList($pagination, $form, $sortChoiceCollection),
        ];
    }
}
```

#### Pagination item limits

The default limits of list items displayed per page are specified in bundle configuration
 at path [pagination.item_limits](Resources/doc/bundle_configuration.md#item_limits).
To setting different item limits for a specific list call `ArturDoruch\ListBundle\Pagination::setItemLimits()` method
with a custom values.

Example:
```php
<?php

use ArturDoruch\ListBundle\Paginator;

// In controller
$pagination = Paginator::paginate($items, $page, $limit);
$pagination->setItemLimits([50, 100, 200]);
```

#### Filter form

In order to filtering list items, you must create `FormType` class.

  * You can use the `ArturDoruch\ListBundle\Form\FilterType` class and add the custom filter fields to the form in controller,
  * or create own form type class and (optionally) extend the `ArturDoruch\ListBundle\Form\FilterType` class.
  
**The filter form must have method type of `GET` and `csrf_protection` option should be set to false.**  
Created `FormType` class pass in the constructor of the `ArturDoruch\ListBundle\ItemList` object.  
 
Example of the filter form type class:
```php
<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BookCategory;
use ArturDoruch\ListBundle\Form\FilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class BookFilterType extends FilterType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('category', EntityType::class, [
                'placeholder' => '-- all --',
                'class' => BookCategory::class,
                'choice_label' => 'category',
                'choice_value' => 'id'
            ])
            ->add('author')
            ->add('title');
    }
}
```

#### Sort choice collection

If you want to render an HTML "select" field with sorting options create `ArturDoruch\ListBundle\Sorting\SortChoiceCollection`
object and specify the sorting choices. Then pass the `SortChoiceCollection` object in the constructor
of the `ArturDoruch\ListBundle\ItemList` object.

**This is an alternative for sorting links rendered in twig template with `arturdoruch_list_sort_link` function.**

Example:
```php
<?php

use ArturDoruch\ListBundle\ItemList;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;

// In controller action.
$sortChoiceCollection = new SortChoiceCollection();
$sortChoiceCollection
    ->add('Cheapest first', 'price', 'asc') // Sort books by price ascending.
    ->add('Expensive first', 'price', 'desc'); // Sort books by price descending.
    
new ItemList($pagination, $form, $sortChoiceCollection); 
```

### View

#### Twig functions

See [Twig functions](Resources/doc/twig_functions.md) rendering the list components.

#### Creating template displaying item list

```twig
{# base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        {% block content %}
        {% endblock %}
    </body>
</html>
```

Template for use with AJAX request.

```twig
{# ajax_list.html.twig #}
{% block list %}{% endblock %}
```

Example of template displaying all of the list components.

```twig
{# book/list.html.twig #}
{# Update only list table (block list) when is AJAX request. #}
{% extends app.request.xmlHttpRequest ?
    '@App/ajax_list.html.twig':
    '@App/base.html.twig'
%}

{% block content %}
    {{ arturdoruch_list_filter_form(bookList.filterForm) }}

    <div id="book-list-container">
    {% block list %}
        {% if bookList.count > 0 %}
        {{ arturdoruch_list_items_and_pagination(bookList.pagination) }}
        {{ arturdoruch_list_sort_form(bookList.sortChoiceCollection) }}

        <table class="table" id="book-list">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>{{ arturdoruch_list_sort_link('Author', 'author') }}</th>
                    <th>{{ arturdoruch_list_sort_link('Title', 'title') }}</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
            {% for book in bookList %}
                <tr>
                    <td>{{ book.category }}</td>
                    <td>{{ book.author }}</td>>
                    <td>{{ book.title }}</td>
                    <td>{{ book.price }}</td>                                      
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {% else %}
            <h4>No books with the specified criteria.</h4>
        {% endif %}
    {% endblock %}
    </div>
{% endblock %}
```