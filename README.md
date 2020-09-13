# ListBundle

Symfony bundle for pagination, sorting and filtering list items.

Build-in paginators can paginate:
 
 * array
 * Doctrine\ORM\Query
 * Doctrine\ORM\QueryBuilder
 * Doctrine\ODM\MongoDB\Query\Query
 * Doctrine\ODM\MongoDB\Query\Builder
 * Doctrine\MongoDB\CursorInterface
 * MongoCursor

For other database queries you can create own paginators. See [#paginator-registration](register paginator).

## Installation

Add the following code to the `composer.json` file, to the `repositories` block

```json
{
    "repositories": [
        // ...
        {
            "type": "vcs",
            "url": "https://github.com/arturdoruch/ListBundle"
        }
    ]
}
```

and run command `composer require arturdoruch/list-bundle`.

## Configuration options

```yml
artur_doruch_list:
    query_parameter_names:
        page: page
        limit: limit
        sort: sort
    query_sort_direction:
        asc: asc
        desc: desc
        position: after # One of "before"; "after"
        separator: ':'
    pagination:
        item_limits: []
        page_items:
            prev_page_label: '&#8592; Prev'
            next_page_label: 'Next &#8594;'
    # A collection of paginator providers, with "query class: paginator class" pairs.
    paginator_providers: []
```

## Paginator registration

1. Create paginator for database query (like Doctrine\ORM\Query).
The paginator must implement the `ArturDoruch\ListBundle\Paginator\PaginatorInterface` interface.
2. Register paginator in bundle configuration at path `artur_doruch_list.paginator_providers`.

## Usage

Description of use on the example of a list of books.

Creating item list example:

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\BookFilterType;
use ArturDoruch\ListBundle\ItemList;
use ArturDoruch\ListBundle\Paginator;
use ArturDoruch\ListBundle\Request\QueryParameterBag;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;
use Symfony\Component\HttpFoundation\Request;

class BookController
{
    public function list(Request $request)
    {
        // Optionally create filter form.
        // Use named form to creating form with "filter" or other name, not depending on the FormType class.
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
        
        // Query book items.
        // $bookRepository - Doctrine or other data source.
        $books = $bookRepository->get($criteria, $sort);        
        
        $pagination = Paginator::paginate($books, $parameterBag->getPage(), $parameterBag->getLimit(100));
        // Optionally set item limits (overrides values form default config "pagination.item_limits").
        $pagination->setItemLimits([50, 100, 200]);

        // Optionally define sorting choice collection to display select field with sorting options.
        $sortChoiceCollection = new SortChoiceCollection();
        $sortChoiceCollection
            ->add('Lowest price', 'price', 'asc') // Sort books by price ascending.
            ->add('Highest price', 'price', 'desc'); // Sort books by price descending.

        return [
            'bookList' => new ItemList($pagination, $form),
        ];
    }
}
```

### Filter form (optional)

Create filter form type for filtering list items.
You can:
  * use the `ArturDoruch\ListBundle\Form\FilterType` class and add form filter fields in controller,
  * create own type class, and (optionally) extend the `ArturDoruch\ListBundle\Form\FilterType` class
  
Filter form requirements:

 * Must have method type of "GET".
 * `csrf_protection` option should be set to false.
 
Example of the filter form type class. 
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

### Sorting choice collection (optional)

Optionally define sorting choice collection to display select field with sorting options.

```php
<?php

use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;

$sortChoiceCollection = new SortChoiceCollection();
$sortChoiceCollection
    ->add('Lowest price', 'price', 'asc') // Sort books by price ascending.
    ->add('Highest price', 'price', 'desc'); // Sort books by price descending.
```

## Frontend

Import `css/styles.css` file with CSS styles to styling displayed items list.

Add JavaScript supports
todo

### Twig functions

#### Filtering

 * `arturdoruch_list_filter_form` - Renders the filter form.   
    <br>Arguments:
    * `formView` The Symfony\Component\Form\FormView object getting from ItemList object. For example "bookList.filterForm".
    * `removeQuerySortParameter` bool (default: `false`)
       Whether to remove query sort parameter from send form data. 
       In other words, whether to reset sorting after filtering the list items.

#### Pagination

 * `arturdoruch_list_pagination` - Renders pagination.
    <br>Arguments:
     * `pagination` The ArturDoruch\ListBundle\Pagination object getting from ItemList object. For example "bookList.pagination".
     
 * `arturdoruch_list_displayed_items` - Renders range of displayed list items.
    <br>Arguments:
    * `pagination` The ArturDoruch\ListBundle\Pagination object getting from ItemList object. For example "bookList.pagination".
    
 * `arturdoruch_list_items_limit_form` - Renders form and select field to change items limit (displayed number items per page).
    <br>Arguments:
    * `pagination` The ArturDoruch\ListBundle\Pagination object getting from ItemList object. For example "bookList.pagination".
      
 * `arturdoruch_list_items_and_pagination` - Renders all elements mentioned above: pagination, range of displayed list items
 select field changing items limit.
    <br>Arguments:
    * `pagination` The ArturDoruch\ListBundle\Pagination object getting from ItemList object. For example "bookList.pagination".
 
#### Sorting 
 
 * `arturdoruch_list_sort_link` - Renders a link (an anchor) sorting the list items.
    <br>Arguments:
    * `label` string 
    * `field` string The item field name used in repository for sorting items. For example Doctrine entity order field name.
    * `initialDirection` string (default: `asc`) Initial sort direction. One of the values: "asc", "desc".
    
 * `arturdoruch_list_sort_form` - Renders select element with options to sort list items.
    <br>Arguments:
    * `sortChoiceCollection` The ArturDoruch\ListBundle\Sorting\SortChoiceCollection object getting from ItemList object.
     For example "bookList.sortChoiceCollection".

### Templates

Example of template displaying filter form and item list table.

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

```twig
{# ajax_list.html.twig #}
{% block list %}{% endblock %}
```

```twig
{% extends app.request.xmlHttpRequest ?
    {# Update only list items getting with AJAX #}
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
