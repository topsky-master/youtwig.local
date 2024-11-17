<?php

$scontent = file_get_contents('https://twig.d6r.ru/local/crontab/archive.tar.gz');
file_put_contents(__DIR__.'/archive.tar.gz',$scontent);