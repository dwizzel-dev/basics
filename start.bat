@echo off

set serverName=test-dwizzel-0
set homesteadVagrant="C:\Users\Dwizzel\Desktop\dwizzel.dev\projects\laravel\environment\homestead\%serverName%"

cd /d %homesteadVagrant%

echo !! ATTENTION !!
echo Start La VM "%serverName%: 10.1.0.50" Laravel Homestead pour le dev!
echo.
echo.
SET /P ANSWER=On Continue (Y/N)?
if /i {%ANSWER%}=={y} (goto :yes)
if /i {%ANSWER%}=={n} (goto :no)

:yes
vagrant up
vagrant ssh
vagrant halt
exit

:no
echo Operation cancel.
exit