# Twig functions

Twig functions rendering the list components.

## Filtering

 * ### arturdoruch_list_filter_form
   Renders the filter form.  
   Arguments:
   
    * `formView` *Symfony\Component\Form\FormView* - object getting from the `ItemList` object returned by the controller.
    * `config` *array* - Form options overriding the [`artur_doruch_list.filter_form`](bundle_configuration.md#filter_form) configuration.            
       * `reset_sorting` *bool* - Whether to reset list sorting after filtering the list. 
       If true query "sort" parameter is removed from the request query.
       * `display_reset_button` *bool* - Whether to display button resetting the filter form elements.

## Pagination

 * ### arturdoruch_list_pagination
   Renders pagination.  
   Arguments:
   
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the `ItemList` object returned by the controller.
     
 * ### arturdoruch_list_displayed_items
    Renders the range of displayed list items.  
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the `ItemList` object returned by the controller.
    
 * ### arturdoruch_list_items_limit_form
    Renders a form with "select" field changing the limit of list items displayed per page.   
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the `ItemList` object returned by the controller.
      
 * ### arturdoruch_list_items_and_pagination
    Renders all pagination components:
      * pagination
      * range of displayed list items
      * "select" field changing the items limit.
      
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the `ItemList` object returned by the controller.
 
## Sorting 
 
 * ### arturdoruch_list_sort_link
    Renders a link ("href" element) sorting the list items.    
    Arguments:
    * `label` - The link label
    * `field` - The item field name used in repository for sorting items. For example Doctrine entity order field name.
    * `initialDirection` (default: `asc`) - Initial sort direction. One of the values: "asc", "desc".
    
 * ### arturdoruch_list_sort_form
    Renders a form with "select" element, with options to sort list items.    
    Arguments:
    * `sortChoiceCollection` *ArturDoruch\ListBundle\Sorting\SortChoiceCollection* - object getting from the `ItemList` object returned by the controller.

Note:  
**For the one item list, should be used only one of the functions: `arturdoruch_list_sort_link` or `arturdoruch_list_sort_form`.**