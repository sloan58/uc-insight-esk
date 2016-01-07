<?php
return [
    'error' => [
        'cant-be-edited' => 'Cluster cannot be edited',
        'cant-be-deleted' => 'Cluster cannot be deleted',
        'cant-be-disabled' => 'Cluster cannot be disabled',
        'login-failed-cluster-disabled'=> 'That account has been disabled.',
        'perm_not_found' => 'Could not find permission #:id.',
        'cluster_not_found' => 'Could not find cluster #:id.',
    ],
    'page' => [
        'index' => [
            'title' => 'Clusters',
            'description' => 'List of CUCM Clusters',
            'table-title' => 'Clusters list',
        ],
        'create'            => [
            'title'            => 'Clusters | Create',
            'description'      => 'Creating a new cluster',
            'section-title'    => 'New Cluster'
        ],
    ],
    'columns' => [
        'id' => 'ID',
        'name' => 'Cluster Name',
        'ip' => 'Publisher IP Address',
        'user_type' => 'User Type',
        'version' => 'version',
        'verify' => 'Verify Peer',
        'username' => 'Username',
        'password' => 'Password',
        'active' => 'Active',
        'actions' => 'Actions',
    ],
    'button' => [
        'create' => 'Add New Cluster',
    ],
];