<?php

$container->setParameter('test_parameter1', 'original_value');
$container->loadFromExtension('framework', array(
    'secret' => 'secret',
));
