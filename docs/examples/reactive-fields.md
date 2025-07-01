# Reactive Settings Fields

This package now supports reactive field behavior similar to FilamentPHP, allowing fields to be live-updating, conditionally hidden or visible based on other field values.

## Available Methods

### `live(bool $state = true)`
Makes the field reactive - when its value changes, other fields can respond to the change.

### `hidden(bool|callable $condition = true)`
Conditionally hides the field based on a boolean value or a closure that receives a `$get` function.

### `visible(bool|callable $condition = true)`
Conditionally shows the field based on a boolean value or a closure that receives a `$get` function.

## Usage Examples

### Basic Live Field
```php
use BagistoPlus\Visual\Settings\Select;
use BagistoPlus\Visual\Settings\Text;

Select::make('product_type')
    ->label('Product Type')
    ->options([
        'simple' => 'Simple Product',
        'configurable' => 'Configurable Product',
        'downloadable' => 'Downloadable Product',
    ])
    ->live(), // This field will trigger updates when changed

Text::make('download_link')
    ->label('Download Link')
    ->visible(fn ($get) => $get('product_type') === 'downloadable'),
```

### Conditional Visibility
```php
use BagistoPlus\Visual\Settings\Checkbox;
use BagistoPlus\Visual\Settings\Color;
use BagistoPlus\Visual\Settings\Range;

Checkbox::make('enable_custom_styling')
    ->label('Enable Custom Styling')
    ->live(),

Color::make('primary_color')
    ->label('Primary Color')
    ->visible(fn ($get) => $get('enable_custom_styling')),

Range::make('border_radius')
    ->label('Border Radius')
    ->min(0)
    ->max(50)
    ->unit('px')
    ->visible(fn ($get) => $get('enable_custom_styling')),
```

### Hidden Fields
```php
use BagistoPlus\Visual\Settings\Select;
use BagistoPlus\Visual\Settings\Number;

Select::make('layout_type')
    ->label('Layout Type')
    ->options([
        'grid' => 'Grid Layout',
        'list' => 'List Layout',
        'carousel' => 'Carousel Layout',
    ])
    ->live(),

Number::make('items_per_row')
    ->label('Items Per Row')
    ->default(3)
    ->hidden(fn ($get) => $get('layout_type') !== 'grid'),

Number::make('autoplay_speed')
    ->label('Autoplay Speed (ms)')
    ->default(3000)
    ->visible(fn ($get) => $get('layout_type') === 'carousel'),
```

### Complex Conditions
```php
use BagistoPlus\Visual\Settings\Select;
use BagistoPlus\Visual\Settings\Text;
use BagistoPlus\Visual\Settings\Textarea;

Select::make('content_source')
    ->label('Content Source')
    ->options([
        'manual' => 'Manual Entry',
        'cms_page' => 'CMS Page',
        'category' => 'Product Category',
    ])
    ->live(),

Text::make('manual_title')
    ->label('Title')
    ->visible(fn ($get) => $get('content_source') === 'manual'),

Textarea::make('manual_content')
    ->label('Content')
    ->visible(fn ($get) => $get('content_source') === 'manual'),

Select::make('cms_page_id')
    ->label('Select CMS Page')
    ->options([
        '1' => 'About Us',
        '2' => 'Privacy Policy',
        '3' => 'Terms of Service',
    ])
    ->visible(fn ($get) => $get('content_source') === 'cms_page'),
```

## Evaluating Fields with Values

You can manually evaluate fields with current values for testing or custom implementations:

```php
$field = Text::make('download_link')
    ->visible(fn ($get) => $get('product_type') === 'downloadable');

// Evaluate with current form values
$evaluated = $field->evaluateWithValues([
    'product_type' => 'downloadable'
]);

// $evaluated will contain the resolved 'visible' and 'hidden' states
```

## Using FieldEvaluator for Multiple Fields

```php
use BagistoPlus\Visual\Settings\Support\FieldEvaluator;

$fields = [
    Select::make('type')->live(),
    Text::make('title')->visible(fn ($get) => $get('type') === 'custom'),
    Color::make('color')->hidden(fn ($get) => $get('type') === 'default'),
];

$evaluator = new FieldEvaluator();
$evaluatedFields = $evaluator->evaluateFields($fields, [
    'type' => 'custom'
]);

// All fields will have their conditions evaluated
``` 