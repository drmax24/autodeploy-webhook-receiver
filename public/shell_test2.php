<?php

echo '<pre>';
//var_dump(shell_exec('pwd'));
//var_dump(shell_exec('ls -al /work/www/dev'));

//var_dump(shell_exec('ls -al /usr/local/bin/bash'));
//putenv('PATH=/usr/local/bin/git');

//shell_exec('export PATH=$PATH:/usr/local/bin/git');


var_dump(shell_exec('ls -al /usr/local/bin/git'));

var_dump(shell_exec('git status 2>&1'));



$cmd = 'find -H /work/www/dev -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4 -execdir /work/hook/try_install.sh x static.toyota.ru master \\; 2>&1';

var_dump(shell_exec($cmd));
//var_dump(shell_exec("sh install_release.sh x serviceportal.toyota.ru develop"));

echo '</pre>';
