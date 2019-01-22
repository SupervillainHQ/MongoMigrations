<?php
/**
 * build-phar.php.
 *
 * TODO: Documentation required!
 */

/**
 * Build the Customers executable phar
 */
$stub = <<<STUB
#!/usr/bin/env php
<?php Phar::mapPhar("mm.phar");
include 'phar://mm.phar/bootstrap.php';
__HALT_COMPILER();
?>
STUB;


$phar = new Phar('bin/mm.phar');
//$phar->buildFromDirectory('src/'); // TODO: currently not using packed source files :) This should obviously change if the phar should work when moved outside the project
//$phar->addFile('build/config.php', 'config.php');
$phar->addFile('build/bootstrap.php', 'bootstrap.php');
//$phar->addFile('build/services.php', 'services.php');
$phar->setStub($stub);
