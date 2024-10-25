<?php

use CRM_Ecgcheck_ExtensionUtil as E;

/*
* Settings metadata file
*/
return [
  'ecgcheck_api_batch_size' => [
    'group_name' => 'ecgcheck_config',
    'group' => 'ecgcheck_config',
    'name' => 'ecgcheck_api_batch_size',
    'type' => 'Integer',
    'default' => 100,
    'add' => '5.59',
    'title' => E::ts('Default api batch size'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Default api batch size',
  ],
  'ecgcheck_job_batch_size' => [
    'group_name' => 'ecgcheck_config',
    'group' => 'ecgcheck_config',
    'name' => 'ecgcheck_job_batch_size',
    'type' => 'Integer',
    'default' => 500,
    'add' => '5.59',
    'title' => E::ts('Default job batch size'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Default job batch size',
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
  'ecgcheck_check_live_time' => [
    'group_name' => 'ecgcheck_config',
    'group' => 'ecgcheck_config',
    'name' => 'ecgcheck_check_live_time',
    'type' => 'Integer',
    'default' => 6,
    'add' => '5.59',
    'title' => E::ts('Check status again after time'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Value in hours',
  ],
];
