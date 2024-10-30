<?php

use CRM_Ecgcheck_ExtensionUtil as E;

return [
  [
    'name' => 'OptionValue_civicrm_email',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'cg_extend_objects',
        'label' => E::ts('Email'),
        'value' => 'Email',
        'name' => 'civicrm_email',
        'weight' => 3,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_ecg_check',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'ecg_check',
        'title' => E::ts('ECG Check'),
        'extends' => 'Email',
        'style' => 'Inline',
        'help_pre' => '',
        'help_post' => '',
        'weight' => 47,
        'collapse_adv_display' => TRUE,
        'created_date' => '2024-09-30 12:51:36',
        'is_public' => FALSE,
        'table_name' => 'civicrm_value_ecg_check',
        'icon' => '',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_ecg_check_status',
    'entity' => 'OptionGroup',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'ecg_check_status',
        'title' => E::ts('ECG Status'),
        'data_type' => 'Integer',
        'is_reserved' => FALSE,
        'option_value_fields' => [
          'name',
          'label',
          'description',
        ],
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_ecg_check_status_OptionValue_pending',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'ecg_check_status',
        'label' => E::ts('Pending'),
        'value' => '1',
        'name' => 'pending',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_ecg_check_status_OptionValue_error',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'ecg_check_status',
        'label' => E::ts('Error'),
        'value' => '2',
        'name' => 'error',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_ecg_check_status_OptionValue_listed',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'ecg_check_status',
        'label' => E::ts('Listed'),
        'value' => '3',
        'name' => 'listed',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_ecg_check_status_OptionValue_not_listed',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'ecg_check_status',
        'label' => E::ts('Not Listed'),
        'value' => '4',
        'name' => 'not_listed',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_ecg_check_CustomField_status',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'ecg_check',
        'name' => 'status',
        'label' => E::ts('Status'),
        'data_type' => 'Int',
        'html_type' => 'Select',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'status',
        'option_group_id.name' => 'ecg_check_status',
        'is_view' => TRUE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_ecg_check_CustomField_last_check',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'ecg_check',
        'name' => 'last_check',
        'label' => E::ts('Last Check'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'text_length' => 255,
        'date_format' => 'dd.mm.yy',
        'time_format' => 2,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'last_check',
        'is_view' => TRUE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'Job_run_ecg_check_api_job',
    'entity' => 'Job',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'run_frequency' => 'Hourly',
        'name' => 'run_ecg_check_api_job',
        'api_entity' => 'Email',
        'api_action' => 'runEcgCheckApi',
        'parameters' => 'version=4',
        'is_active' => '0',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
];
