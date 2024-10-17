<?php

use CRM_Ecgcheck_ExtensionUtil as E;

/*
* Settings metadata file
*/
return [
  'ecgcheck_default_api_batch_size' => [
    'group_name' => 'ecgcheck_config',
    'group' => 'ecgcheck_config',
    'name' => 'ecgcheck_default_api_batch_size',
    'type' => 'Integer',
    'default' => 100,
    'add' => '5.59',
    'title' => E::ts('Default api batch size'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Default api batch size',
  ],
  'ecgcheck_api_key' => [
    'group_name' => 'ecgcheck_config',
    'group' => 'ecgcheck_config',
    'name' => 'ecgcheck_api_key',
    'type' => 'String',
    'default' => '',
    'add' => '5.59',
    'title' => E::ts('Api key'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Api key',
  ],
];
