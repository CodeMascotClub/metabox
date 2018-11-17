# CodeMascot MetaBox

This is a Composer library package to enhance the usage of **WordPress metabox API**.

## Table Of Contents

* [Coding styles and technique](#coding-styles-and-technique)
* [Installation](#installation)
* [Usage](#usage)
* [Crafted by Khan](#crafted-by-khan)
* [License](#license)
* [Contributing](#contributing)

## Coding styles and technique
* All input data escaped and validated.
* Developed as *Composer* package.
* **YODA** condition checked.
* Maintained *Right Margin* carefully. Usually that is 80 characters.
* Used `true`, `false` and `null` in stead of `TRUE`, `FALSE` and `NULL`.
* **INDENTATION:** *TABS* has been used in stead of *SPACES*.
* *PHP Codesniffer* checked.
* *WordPress VIP* coding standard followed mostly.

## Requirements
 * WordPress >= 3.8.0 (Tested)
 * PHP >= 5.6
 * Composer Package Manager

## Installation

The best way to use this package is through Composer:

```BASH
$ composer require codemascot/metabox
```

## Usage

##### Step 1
First we need to prepare the arguments like below-
```php
$prefix = 'codemascot_'; // Use any prefix you like to prefix the metabox name or id
$metabox_args = [
    [
        $prefix . '-section-title',
        __( 'Section Title', 'text-domain' ),
        'title',
        'required',
    ],
    [
        $prefix . '-checkbox',
        __( 'Input Checkbox', 'text-domain' ),
        'check',
        '',
        [
            'checkbox_key' => 'Checkbox Text',
        ],
    ],
    [
        $prefix . '-input',
        __( 'Input Text', 'text-domain' ),
        'input',
        'required',
    ],
    [
        $prefix . '-select',
        __( 'Select Dropdown', 'text-domain' ),
        'select',
        'required',
        [
            '-'     => '-',
            'red'   => 'Red',
            'blue'  => 'Blue',
            'green' => 'Green',
        ],
    ],
];
```
##### Step 2
Now you need to call the `MetaBox` class like below-
```php
new \CodeMascot\MetaBox\MetaBox(
    'section-id', // Section DOM ID
    __( 'Section Name', 'text-domain' ), // Section Name
     'post', // Post Type
     $metabox_args, // Arguments
     'advanced', // Context
     'high' // Priority
);
```
##### Data Sanitization & Validation
You can use `codeamscot_metabox_api_data_filter` for sanitizing and validating input through this package. It has two arguments, one is `$data` and another is `$field_id`.

```php
add_filter(
    'codemascot_metabox_api_data_filter',
    'codemascot_metabox_api_data_filter',
    10,
    2
);

/**
 * @param        $data
 * @param string $field_id
 */
function codemascot_metabox_api_data_filter( $data, $field_id ) {
    // Validate or Sanitize Data Here.
}
```

## Crafted by Khan

I'm Khan AKA CodeMascot a professional web developer and I crafted this package for my personal use. Feel free to use this for your projects too.

## License

Copyright (c) 2017 Khan M Rashedun-Naby, CodeMascot

Good news, this library is free for everyone! Since it's released under the [MIT License](LICENSE) you can use it free of charge on your personal or commercial website.

## Contribution

All feedback / bug reports / pull requests are welcome.
